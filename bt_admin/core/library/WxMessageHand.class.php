<?php

/**
 * 微信信息接收处理
 * @author zhong
 * @version 2015-01-28
 */
class WxMessageHand{
	
	const C_GET='tk';
	const C_SALT = 'ZHP,MXG,ZHPENG,GRH,ZHX,ZHTP';
	
	private $start_consum='';
	private $end_consum='';
	private $wx_message='';
	
	private $is_encrypt=false;//是否aes加密
	
	public function __construct(){
		$this->start_consum=microtime(true);
		//验证是否加密传输.
		$encrypt_type=isset($_GET['encrypt_type']) ? trim($_GET['encrypt_type']) : '';
		if($encrypt_type=='aes'){
			$this->is_encrypt=TRUE;
		}
		
	}
	public function __destruct(){
		$this->end_consum=microtime(true);
		$runtime=sprintf('%0.8f',($this->end_consum-$this->start_consum));
		Factory::getSystemLog('wx_log')->push('wx time consuming'.$runtime,$this->wx_message);
	}
	
	public function run(){
		//解析,验证get参数
		$g_get=$this->_parseGet();
		if(!$g_get || !$g_get['token']){
			$this->_exit();
		}
		//判断来源是否是微信
		if(!$this->_checkSignature($g_get['token'])){
			$this->_exit();
		}
		//判断是否初次接入
		$this->_isAccess();
		//读取信息
		if(!$this->_fetchData() || empty($this->wx_message)){
			$this->_exit();
		}
		//调用插件
		$this->_callApp();
	}
	
	/**
	 * 调用插件进行处理
	 */
	private function _callApp(){
		$wxApp = Conf::get('WX_APP');
		
		if (! $wxApp) {
			Error::setError('131', '没有对应的app处理wx消息');
			return false;
		}
		
		$execType = $wxApp['EXEC_TYPE'];
		$className = $wxApp['CLASS_NAME'];
		$methodName = $wxApp['METHOD_NAME'];
		$classType = $wxApp['CLASS_TYPE'];
		
		$return = false;
		
		switch ($execType) {
			//本地加载插件方式
			case 'local':
				if (! class_exists($className) || ! method_exists($className, $methodName)) {
					Error::setError('132', 'call app 类或方法不存在');
					return false;
				}
		
				if ('instance' == $classType) {
					$obj = new $className();
					$return=call_user_func(array($obj,$methodName),$this->wx_message);
				} elseif ('static' == $classType) {
					$return = call_user_func(array($className, $methodName),$this->wx_message);
				}
				break;
		}
		
		//执行返回结果.
		if(!empty($return['echo']) && !empty($return['xml']) ){
			$messageXML=$this->_msgEnCrypt($return['xml']);
			echo $messageXML;
		}
	}
	
	/**
	 * 读取数据
	 */
	private function _fetchData(){
		$msgStr = trim(file_get_contents('php://input'));
		if (! $msgStr) {
			Error::setError('121', 'fetch php://input is null');
			return false;
		}
		//-----增加消息体加密
		if($this->is_encrypt){
			$msgStr=$this->_msgCrypt($msgStr);
		}
		//解析消息体xml数据
		$msgXmlObj = simplexml_load_string($msgStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		if (! $msgXmlObj) {
			Error::setError('121', 'fetch php://input is null');
			return false;
		};
		$message =$msgXmlObj;
		if (! $message || ! is_object($message)) {
			Error::setError('125', 'parse data faild');
			return false;
		}
		$this->wx_message= $message;
		return $message;
	}
	
	/**
	 * 判断来源是否是微信
	 */
	private function _checkSignature($token){
		$signature = isset($_GET["signature"]) ? trim($_GET["signature"]) : '';
		$timestamp = isset($_GET["timestamp"]) ? trim($_GET["timestamp"]) : '';
		$nonce = isset($_GET["nonce"]) ? trim($_GET["nonce"]) : '';
		
		$tmpArr = array (
				$token,
				$timestamp,
				$nonce
		);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		
		if ($tmpStr == $signature) {
			return true;
		} else {
			Error::setError('111', '微信签名认证失败.');
			return false;
		}
		
	}
	
	/**
	 * 解析，验证get
	 */
	private function _parseGet(){
		$param=isset($_GET[self::C_GET]) ? trim($_GET[self::C_GET]) : null;
		if(empty($param)){
			Error::setError('101', '解析get参数失败');
			return false;
		}
		$token = base64_decode($param);
		if(empty($token)){
			Error::setError('102', '解析get参数失败,base64_decode');
			return false;
		}
		$tokenArr = explode(';', $token);
		if (! $tokenArr){
			Error::setError('103', '解析get参数失败,explode');
			return false;
		}
		$tokenParam = array ();
		$tokenCheck = array ();
		foreach ($tokenArr as $k => $v) {
			$oneArr = explode('=', $v);
			if ($oneArr && isset($oneArr[0]) && isset($oneArr[1]) ) {
				$tokenParam[$oneArr[0]] = $oneArr[1];
				if ($oneArr[0] != 'sig') {
					array_push($tokenCheck, $v);
				}
			}
		}
		$tokenSig = isset($tokenParam['sig']) ? trim($tokenParam['sig']) : '';
		if (! $tokenSig){
			Error::setError('104', '解析sig参数失败');
			return false;
		}
		
		unset($tokenParam['sig']);
		$newSig = md5(implode(';', $tokenCheck) . self::C_SALT);
		if ($tokenSig != $newSig) {
			Error::setError('105', '解析sig参数失败,签名失败.');
			return false;
		}
		return $tokenParam;
		
	}
	
	
	
	/**
	 * 消息体解密处理
	 * @param crypt $msg
	 * @return string encrypt str
	 */
	private function _msgCrypt($msg){
		if(!$this->is_encrypt){
			return $msg;
		}
		$timeStamp=trim($_GET['timestamp']);
		$nonce=trim($_GET['nonce']);
		$msg_sign=trim($_GET['msg_signature']);
		$pc =$this->_getCryptObject();
		$ret_msg='';
		$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $msg, $ret_msg);
		if($errCode==0){
			return $ret_msg;
		}
		Error::setError('123', '消息解密失败');
		$this->_exit();
	}
	
	/**
	 * 消息体加密处理.
	 * @param unknown $msg
	 * @return unknown|string
	 */
	private function _msgEnCrypt($msg){
		if(!$this->is_encrypt){
			return $msg;
		}
		$timeStamp=trim($_GET['timestamp']);
		$nonce=trim($_GET['nonce']);
		$pc =$this->_getCryptObject();
		$ret_msg='';
		$errCode = $pc->encryptMsg($msg, $timeStamp, $nonce, $ret_msg);
		if($errCode==0){
			return $ret_msg;
		}
		Error::setError('124', '消息加密失败');
		$this->_exit();
	}
	
	/**
	 * 获取加密的对象
	 * decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg) 解密方法
	 * encryptMsg($send_xml, $timeStamp, $nonce, $encryptMsg) 加密方法
	 */
	private function _getCryptObject(){
		static $_ar=array();
		if(!isset($_ar[0])){
			$_ar[0]=new WXBizMsgCrypt(Conf::get('APP_TOKEN'),Conf::get('APP_AESKey'),Conf::get('APP_ID'));
		}
		return $_ar[0];
	}
	
	
	/**
	 * 退出中断程序
	 */
	private function _exit($str='',$data=null){
		if(!empty($str)){
			Factory::getSystemLog('wx_log')->push('system error'.$str,$data);
		}
		exit;
	}
	
	/**
	 * 初次接入
	 */
	private function _isAccess(){
		if (isset($_GET["echostr"]) && ! empty($_GET["echostr"])) {
			echo $_GET["echostr"];
			exit();
		}
	}
}