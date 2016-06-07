<?php

/**
 * 父类action
 * @author zhong
 * @version 2015-02-01
 */
class BaseAction{
	
	protected $user='';
	
	public function __construct(){
		$this->_setTpl();
	}

	/**
	 * 设置页面参数
	 */
	protected function _setTpl(){
		$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		View::assign('tpl_args',array(
				'root'		=> $url,
				'action'	=> CONST_ACTION,
				'method'	=> CONST_METHOD,
				'self' => $url.sprintf("?a=%s&m=%s",CONST_ACTION,CONST_METHOD),
		));
		View::assign('user',$this->user);
	}

	/**
	 * 跳走
	 */
	protected function redirect($url){
		header("Location:".$url);
		exit;
	}

	/**
	 * 处理模板
	 */
	protected function assign($key,$val){
		View::assign($key,$val);
	}

	/**
	 * 处理模板
	 */
	protected function display($tpl=''){
		View::display($tpl);
	}


	/**
	* 操作没有权限
	*/
	protected function deny(){
		$this->display('common/deny');
	}

}