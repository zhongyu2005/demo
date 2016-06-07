<?php
/**
 * 201605晒娃活动
 * @author zhong
 * @version 2016-5-17
 */
if(!class_exists('AutoBaseAction')){
	require dirname(__FILE__).'/../Auth/AutoBaseAction.class.php';
}
class BaoBaoAction extends AutoBaseAction{

	const SMS_CODE=12;//短信的key
	const SIGN_KEY='act_bb_sign_key_';
	const VOTE_KEY='act_bb_vote_key_';
	const VISIT_KEY='act_bb_visit_key_';

    private $baseTab='sfy_baobao';
    private $voteTab='sfy_baobao_vote';
	private $user=null;//
	private $active_statu='start';//活动状态

	public function __construct(){
 		parent::__construct();
 		unset($_GET['a'],$_GET['m']);
		$this->checkUser();
		//$this->active_statu='start';//这里是测试
		if($this->openid=='og26Wt_YX9EFvxqvQ13ym-ZJ8ar8'){
		   // $this->active_statu='finish';//这里是测试
		}
		$script='';
		if($this->active_statu=='finish'){
		    $script=<<<EOF
<div id="temp_show" class="txzl_opo xq_txzl_opo hide">
	<div class="txzl_opo_bg">
		<p>活动已结束，感谢您的参与！</p>
		<a href="javascript:;" onclick="$(this).parent().parent().remove();" class="zdle_btn xq_zdle_btn index_zdle_btn"><img src="baobao/img/zdle_btn.png" alt="" /></a>
	</div>
</div>
<script>
$(function(){
    $("#temp_show").removeClass("hide");
})
</script>
EOF;
		    
		}
		$this->assign('tongji_party_script',$script);
		//记录访问量
 		$cache=Factory::getCacher();
 		method_exists($cache,'getRedis') && $cache->getRedis()->lPush(self::VISIT_KEY,date("ymd"));
	}

	/**
	 * index
	 */
	public function index(){
		$type=I('get.type','all','trim');
		$subSql='';//",(SELECT count(*) as num from sfy_baobao s where s.vote_times>sfy_baobao.vote_times LIMIT 1)+1 as bank";
		$field="id,number,name,vote_times,img1".$subSql;
		$where=" deleted=0 AND is_check=1  ";
		$db=M("Const")->_db();
		$kw=I('get.kw','','trim');
		if(!empty($kw)){
			$type=$_GET['type']='all';
			if(is_numeric($kw)){
				$where.=sprintf(" AND number='%u' ",$kw);
			}else{
				$where.=sprintf(" AND name='%s' ",$kw);
			}
		}
		if(isAjax()){
			//异步加载数据
			$lastId=I('post.lastId',1,'intval');
			$sql=sprintf("SELECT %s FROM %s WHERE %s "
				,$field,$this->baseTab,$where);
			if($type=='all'){
				$sql.=" ORDER BY is_top DESC,id DESC ";
				$limit=($lastId*30).",30 ";
			}elseif($type=='new'){
				$sql.=" ORDER BY vote_times DESC ";
				$limit=($lastId*30).",30 ";
			}else{
				$sql.=" ORDER BY vote_times DESC ";
				$limit=" 10 ";
			}
			$sql.=" LIMIT ".$limit;
			$list=$db->getAll($sql);
			if(empty($list)){
				$list=[];
			}else{
				foreach($list as &$v){
					$sql="SELECT count(*) as num from sfy_baobao s where deleted=0 AND is_check=1 AND s.vote_times>".$v['vote_times']." LIMIT 1";
					$v['bank']=($db->getOne($sql)) + 1 ;
				}
				unset($v);
			}
			printJson(compact('list'),200,"操作成功.");
		}

		if($type=='all'){
			//全部-根据置顶，在根据id desc
			$sql=sprintf("SELECT %s FROM %s WHERE %s "
				,$field,$this->baseTab,$where);
			$sql.=" ORDER BY is_top DESC,id DESC ";
			$sql.=" LIMIT 30 ";
			$list=$db->getAll($sql);
		}elseif($type=='new'){
			//根据排行来显示所有
			$sql=sprintf("SELECT %s FROM %s WHERE %s "
				,$field,$this->baseTab,$where);
			$sql.=" ORDER BY vote_times DESC ";
			$sql.=" LIMIT 30 ";
			$list=$db->getAll($sql);
		}else{
			//top
			$sql=sprintf("SELECT %s FROM %s WHERE %s "
				,$field,$this->baseTab,$where);
			$sql.=" ORDER BY vote_times DESC ";
			$sql.=" LIMIT 10 ";
			$list=$db->getAll($sql);
		}
		//显示已报名，投票，访问量
		if(!empty($list)){
			foreach($list as &$v){
				$sql="SELECT count(*) as num from sfy_baobao s where deleted=0 AND is_check=1 AND s.vote_times>".$v['vote_times']." LIMIT 1";
				$v['bank']=($db->getOne($sql)) + 1 ;
			}
			unset($v);
		}

		$base=$this->getConstNum();//['sign'=>100,'vote'=>2345,'visit'=>67811];
		$this->assign('list',$list);
		$this->assign('baseVar',$base);
		$this->assign('title','童享六月，成长添彩萌娃秀');
		$new=[
			'title'=>'主办方童享六月，萌娃秀活动',
			'desc'=>'为宝贝筑梦童年，成长添彩，还能赢取价值5000元儿童桌椅等礼品哦！',
			'picurl'=>'http://pic.baidu.com/upload/uedit/images/201605/25-0351541347.jpg'
		];
		$this->_share($new);
		$this->display();
	}

	/**
	 * 规则
	 */
	public function rule(){
	    $url=url(__ACTION_NAME__,'res');
	    redirect($url);
		$this->assign('title','活动奖品');
		$new=[
			'title'=>'主办方童享六月，萌娃秀活动',
			'desc'=>'为宝贝筑梦童年，成长添彩，还能赢取价值5000元儿童桌椅等礼品哦！',
			'picurl'=>'http://pic.baidu.com/upload/uedit/images/201605/25-0351541347.jpg'
		];
		$this->_share($new);
		$this->display();
	}
	
	/**
	 * 中奖结果页面
	 */
	public function res(){
	    $this->assign('tongji_party_script','');
	    $this->assign('title','获奖名单');
	    $this->display();
	}

	/**
	 * 详细
	 */
	public function bao(){

		$bb_id=I('get.bb_id',0,'intval');
		$sn=I('get.sn','','trim');

		$field='id,number,name,sex,month,vote_times,vote_nums,intro,img1,img2,img3,hobby,is_check,openid';
		$where=sprintf("deleted=0 AND id='%u' ",$bb_id);
		$bao=$this->getBaoBao($field,$where);
		if(!empty($bao)){
			//宝宝待审核
			/*
			if($bao['is_check']!='1' && $this->openid!=$bao['openid']){
				$this->display();
				exit;
			}
			*/
		}
		if(empty($bao)){
			$url=url(__ACTION_NAME__,'index');
			redirect($url);
		}
		$bao['img']=array_filter([$bao['img1'],$bao['img2'],$bao['img3']],function($v){
			if(!filter_var($v, FILTER_VALIDATE_URL)){
				return false;
			}
			return true;
		});
		if(empty($bao['img'])){
			$bao['img']=['Active/baobao/img/mb_pic_1.png'];
		}
		$bao['sexp']=$bao['sex']=='2' ? 'nv' : 'nan';
		if(!empty($bao['hobby'])){
			$bao['hob']=explode(',',$bao['hobby']);
		}else{
			$bao['hob']=['可爱'];
		}
		//查询排名,查询距离上个排行，
		$bao['bank']=$this->getBank($bao['vote_times']);
		$bao['prev']=$this->getPrevTimes($bao['vote_times']);
		$bao['month']=$this->getAge($bao['month']);
		if(isset($bao['month']['str'])){
			$bao['month']=$bao['month']['str'];
		}

		$this->assign('title',$bao['name'].' 小萌娃的详细介绍');
		$this->assign('bao',$bao);
		$new=[
			'title'=>'童享六月，成长添彩萌娃秀',
			'desc'=>'我的宝贝'.$bao['name'].' 正在参与主办方萌娃秀活动，请大家帮忙投票哦~',
			'picurl'=>$bao['img'][0],
			'url'=>url(__ACTION_NAME__,__ACTION_METHOD__,compact('bb_id'))
		];
		$this->_share($new);
		$this->display();
	}

	/**
	 * 我的宝宝
	 * 存在则查看，不存在则添加
	 */
	public function myBao(){
		$where=sprintf("openid='%s'",$this->openid);
		$baobao=$this->getBaoBao('id',$where);
		if(!empty($baobao)){
			$url=url(__ACTION_NAME__,'bao',['bb_id'=>$baobao['id']]);
			redirect($url);
		}
		$url=url(__ACTION_NAME__,'newBao');
		$this->assign('title','童享六月-还没有报名');
		$this->display();
	}


	/**
	 * 添加宝宝
	 */
	public function newBao(){
		$where=sprintf("openid='%s'",$this->openid);
		$baobao=$this->getBaoBao('id',$where);
		
		if(isAjax()){
		    if($this->active_statu!='start'){
		        if($this->active_statu=='not start'){
		            printJson(null,1,"活动还未开始");
		        }else{
		            printJson(null,1,"活动已经结束");
		        }
		    }
			if(!empty($baobao)){
				printJson(null,1,"您已经添加过萌娃");
			}
			$vcode=I('post.vcode');
			$name=I('post.name');
			$sex=I('post.sex');
			$age=I('post.age');
			$hobby=I('post.hobby');
			$img=I('post.img');
			$intro=I('post.intro');
			$parent_name=I('post.parent_name');
			$parent_phone=I('post.parent_phone');
			$city=I('post.city');

			//提交参数判断.
			if(empty($name) || empty($sex) || empty($age) || empty($hobby) || empty($img) || empty($intro)
				|| empty($parent_name) || empty($parent_phone) || empty($city) ){
				printJson(null,1,"填写参数不合法,请检查");
			}
			if(!in_array($sex,['1','2'])){
				printJson(null,1,"请选择宝宝性别");
			}
			if($age<=0 || $age>168){
				printJson(null,1,"萌娃参赛年龄最小为1个月，最大为14周岁");
			}
			if(mb_strlen($intro,'utf-8')>100){
				printJson(null,1,"宝宝参赛宣言最多100字");
			}
			$pat=array("options"=>array("regexp"=>"/^1\d{10}$/"));
			if(filter_var($parent_phone, FILTER_VALIDATE_REGEXP,$pat)===false){
				printJson(null,1,"家长手机号码格式不正确");
			}
			$img_ar=explode(',',$img);
			$city_ar=explode(',',$city);
			if(!isset($img_ar[0])){
				printJson(null,1,"请至少添加一张萌娃图片");
			}
			if(!isset($city_ar[0])){
				printJson(null,1,"请选择所在城市");
			}
			array_map(function($v){
				if(filter_var($v, FILTER_VALIDATE_URL)===false){
					printJson(null,1,"请上传合法的萌娃图片");
				}
			},$img_ar);
			$img1=$img_ar[0];
			$img2=isset($img_ar[1]) ? $img_ar[1] : '';
			$img3=isset($img_ar[2]) ? $img_ar[2] : '';

			$prov=$city_ar[0];
			$city=isset($city_ar[1]) ? $city_ar[1] : '';
			$dist=isset($city_ar[2]) ? $city_ar[2] : '';
//			list($img1,$img2,$img3)=explode(',',$img);
//			list($prov,$city,$dist)=explode(',',$city);

			//查看验证码
			if(!M('Api.SendSMS')->checkVcode($this->openid,$vcode,self::SMS_CODE)){
				printJson(null,1,"您输入的验证码有误，请重新输入!");
			}

			$time=time();
			$set=[
				'number'=>'','name'=>$name,'sex'=>$sex,'month'=>$age,
				'vote_times'=>0,'vote_nums'=>0,'is_top'=>1,'is_check'=>0,
				'parent_name'=>$parent_name,'parent_phone'=>$parent_phone,
				'openid'=>$this->openid,'prov'=>$prov,'city'=>$city,'dist'=>$dist,
				'intro'=>$intro,'img1'=>$img1,'img2'=>$img2,'img3'=>$img3,'hobby'=>$hobby,
				'create_at'=>$time,'deleted'=>0
			];

			$id=M("Const")->add('sfy_baobao',$set);
			if(empty($id)){
				Logger::error("active添加萌娃失败",$set);
				printJson(null,1,"网络连接失败,请重试");
			}
			$number=10000+$id*1;
			M("Const")->update('sfy_baobao',sprintf("id='%u'",$id),['number'=>$number]);
			//记录报名的人数
			$cache=Factory::getCacher();
			method_exists($cache,'getRedis') && $cache->getRedis()->lPush(self::SIGN_KEY,date("ymd"));
			//进行自动绑定
			$this->autoBind($parent_phone);
			$url=url(__ACTION_NAME__,'newBaoSucc');
			printJson(compact('url'),200,'操作成功');
		}
		if(!empty($baobao)){
			$url=url(__ACTION_NAME__,'myBao');
			redirect($url);
		}
		if($this->active_statu!='start'){
		    $url=url(__ACTION_NAME__,'rule');
		    redirect($url);
		}

		$this->assign('title',' 萌娃资料填写（1/2）');
		$this->assign('baobao',$baobao);
		$new=[
			'title'=>'我正在参加主办方童享六月，成长添彩萌娃秀，快来给我的宝贝投一票吧',
			'picurl'=>'http://pic.baidu.com/upload/uedit/images/201605/25-0351541347.jpg'
		];
		$this->_share($new);
		$this->assign('closeShare',200);
		$this->display();
	}

	/**
	 * 添加宝宝成功
	 */
	public function newBaoSucc(){
		$where=sprintf("openid='%s'",$this->openid);
//		$where="id='1'";
		$baobao=$this->getBaoBao('id,vote_times',$where);
		if(empty($baobao)){
			$url=url(__ACTION_NAME__,'index');
			redirect($url);
		}
		$baobao['bank']=$this->getBank($baobao['vote_times']);
		$baobao['prev']=$this->getPrevTimes($baobao['vote_times']);
		$this->assign('title','萌娃报名成功');
		$this->assign('baobao',$baobao);
		$this->display();
	}

	/**
	 * 投票
	 */
	public function vote(){
		$bb_id=I('post.bb_id',0,'intval');

		$url=url(__ACTION_NAME__,'index');
		if(!isAjax()){
			redirect($url);
		}
		if($this->active_statu!='start'){
			if($this->active_statu=='not start'){
				printJson(null,1,"活动还未开始");
			}else{
				printJson(null,1,"活动已结束，感谢您的参与！");
			}
		}
		$time=time();
		$cache_id='active_baobao_vote_'.$this->openid;
		$row=Factory::getCacher()->get($cache_id);
		if(empty($row)){
			$id=$this->voteOne($bb_id);
			if(empty($id)){
				printJson(null,1,"网络连接失败，请稍后再试.");
			}
			Factory::getCacher()->set($cache_id,[0=>$time,1=>1],86400);
			printJson(null,200,"恭喜你，投票成功！您今天还有1次投票机会哦，请1小时后再来投。");
		}
		list($date,$times)=$row;
		$isSameDay=date("Ymd",$time)==date("Ymd",$date);
		//判断1个小时才可以投票一次，一天投票三次.(第二天又可以投了)
		if( $time-$date <3600 || ($times>=2 && $isSameDay ) ){
			$msg=$times>=2 ? '哦啊~今天的票投完了，请明天再来吧！' : '请间隔1小时后再来投。';
			printJson(null,1,$msg);
		}
		//又加一票
		$id=$this->voteOne($bb_id);
		if(empty($id)){
			printJson(null,1,"网络连接失败，请稍后再试.");
		}
		$times++;
		$msg='恭喜你，投票成功！您今天还有'.(2-$times).'次投票机会哦，请1小时后再来投。';
		if($times>=2){
			$msg="恭喜你，投票成功！请明天再来投";
		}
		if(!$isSameDay){
			//第二天重置为1
			$times=1;
			$msg='恭喜你，投票成功！您今天还有1次投票机会哦，请1小时后再来投。';
		}
		Factory::getCacher()->set($cache_id,[0=>$time,1=>$times],86400);
		printJson(null,1,$msg);
	}





	function ajaxPageShare(){
	    $openid=$this->openid;//I("get.openid");
	    if(!empty($openid)){
	        $shareType = I("post.shareType");
	        $data=array(
	            'openid'=>$openid,'share_key'=>'baobao2016','share_type'=>$shareType,
	            'article_id'=>0,'material_id'=>0,'create_at'=>time(),'share_num'=>1
	        );
	        M("Const")->add('wx_share',$data);
	    }
	}
	
	/**
	 * 监听用户分享
	 */
	private function _share($new){
	    $fx_callback=$fx_img=$fx_link=$fx_title=$fx_desc='';
	
	    $fx_title=$fx_desc=$new['title'];
	    if(!empty($new['desc'])){
	        $fx_desc=$new['desc'];
	    }
	    $fx_img=$new['picurl'];
	
	    $fx_callback=empty($new['callback']) ? url(__ACTION_NAME__,'ajaxPageShare') : $new['callback'];
	    $fx_link=empty($new['url']) ? url(__ACTION_NAME__,'index') : $new['url'];
	
	    $this->assign('fx_callback',$fx_callback);
	    $this->assign('fx_img',$fx_img);
	    $this->assign('fx_link',$fx_link);
	    $this->assign('fx_title',$fx_title);
	    $this->assign('fx_desc',$fx_desc);
	
	
	    $config=js_getConfig(false,false);
	    $fx_config=json_encode($config);
	    $this->assign('fx_config',$fx_config);
	    $this->assign('isShare',true);
	}

	/**
	 * 上传图片
	 */
	public function upload(){
// 	    printJson('http://pic.baidu.com/upload/web/images/201603/56f9e14d508141496.jpg');
		$file=$_FILES['file'];
		if(empty($file)){
			printJson(null,1,"未上传文件.请重试");
		}
		$suffix = pathinfo($file['name'],PATHINFO_EXTENSION);
		$suffix=strtolower($suffix);
		if(!in_array($suffix, array('jpg','png','bmp','gif','jpeg'))){
			printJson(null,1,"上传文件格式不被支持");
            }
		$file=file_get_contents($file['tmp_name']);
		$url="http://pic.baidu.com/task/task.php?opt=uploadPic";
		$res=RequestClient::http($url,null,array('block'=>'actbao','pic'=>base64_encode($file)));
		Logger::debug("上传comment结果返回".$res,RequestClient::getError());
		if(empty($res) || $res=='500'){
			printJson(null,1,"上传失败,请重试");
		}
		$res=json_decode($res,true);
		if(!isset($res['url'])){
			printJson(null,1,"上传失败.请重试");
		}
		printJson($res['url']);
	}

	/**
	 * 发送短信
	 */
	public function sendSMS(){
		if(isAjax() && I('submit')=='submit'){
			$phone=I("phone",'','trim');
			$reg=array("options"=>array("regexp"=>"/^1\d{10}$/"));
			if(filter_var($phone,FILTER_VALIDATE_REGEXP,$reg)===false){
				printJson(null,1,"您输入的手机号有误，请重新输入!");
			}
			$mod=M('Api.SendSMS');
			//检查是否可以发送
			if(!$mod->checkSend($this->openid,self::SMS_CODE)){
				printJson(null,1,"您已经成功发送短信");
			}
			//进行验证码的发送.
			$msg='';
			$flg=$mod->sendTo($phone,$msg,$this->openid,self::SMS_CODE);
			if($flg===false){
				printJson(null,1,"服务暂时不可用.请稍后再试.");
			}
			printJson(200,200);
		}
		printJson(null,1,"发送短信失败");
	}


	/**
	 * 判断是否关注
	 */
	private function checkUser(){
		//判断是否活动时间
		$s_date=strtotime("2016-05-24 12:00:00");
		$e_date=strtotime("2016-06-06 17:00:00");
		$time=time();
		if($time<$s_date){
			$this->active_statu='not start';
		}elseif($time<$e_date){
			$this->active_statu='start';
		}else{
			$this->active_statu='finish';
		}


		$openid=$this->openid;
		$sql=" SELECT id FROM wx_user WHERE user='{$openid}' AND subscribe=1 LIMIT 1 ";
		$user=M('Const')->_db()->getRow($sql);
		$this->user=$user;
		if(!empty($user)){
			return false;
		}
		if(isAjax()){
			printJson(null,1,"请关注公众号后重试!");
		}
		$url='mp-url';
		echo '<script>alert("请先关注主办方家居 微信公众号后，点击“我要定制-晒萌娃”参与活动");location="'.$url.'"</script>';
		exit;
	}
	/*********************************private func**********************************/

	/**
	 * 获取当前宝宝
	 */
	private function getBaoBao($field,$where){
		$sql=sprintf(" SELECT %s FROM %s WHERE deleted=0 AND %s ",
			$field,$this->baseTab,$where);
		$row=M("Const")->_db()->getRow($sql,1);
		return $row;

	}
	/**
	 * 查询当前宝宝的排名
	 */
	public function getBank($times){
		$where=sprintf("vote_times > %u AND deleted=0 AND is_check=1",$times);
		$sql="SELECT count(*) num FROM %s WHERE %s ";
		$sql=sprintf($sql,$this->baseTab,$where);
		$num=M("Const")->_db()->getOne($sql);
		return intval($num)+1;
	}

	/**
	 * 查询前一名分数
	 */
	public function getPrevTimes($times){
		$where=sprintf("vote_times > %u AND deleted=0 AND is_check=1",$times);
		$sql="SELECT vote_times FROM %s WHERE %s  ORDER BY vote_times asc LIMIT 1";
		$sql=sprintf($sql,$this->baseTab,$where);
		$num=M("Const")->_db()->getOne($sql);
		if(empty($num)){
			return 0;
		}
		return intval($num)-$times;
	}

	/**
	 * 计算宝宝的年龄
	 */
	public function getAge($month){
		if($month<=11){
			return $month;
		}
		$year=intval($month/12);//岁
		$month=$month-$year*12;//月
		$str=$year.'岁'.$month;
		return compact('year','month','str');
	}

	/**
	 * 得到已报名,已投票,访问量
	 */
	public function getConstNum(){
		if(!method_exists(Factory::getCacher(),'getRedis')){
			$sign=6888;
			$vote=18888;
			$visit=68888;
		}else{
			$redis=Factory::getCacher()->getRedis();
			$sign=intval($redis->get(self::SIGN_KEY.'2016')) + $redis->lSize(self::SIGN_KEY);
			$vote=intval($redis->get(self::VOTE_KEY.'2016')) + $redis->lSize(self::VOTE_KEY);
			$visit=intval($redis->get(self::VISIT_KEY.'2016')) + $redis->lSize(self::VISIT_KEY);
		}
		return compact('sign','vote','visit');
	}


	/**
	 * 投票
	 */
	private function voteOne($bb_id){
		//判断bb_id是否存在
		$sql=sprintf("SELECT 1 FROM ".$this->baseTab." WHERE id='%u' AND deleted=0",$bb_id);
		$bb=M("Const")->_db()->getOne($sql,$bb_id);
		if(empty($bb)){
			Logger::debug("投票的宝宝不存在",$bb_id);
			return false;
		}
		//判断是否是第一次投票.
		$sql="SELECT create_at FROM ".$this->voteTab." WHERE ".
			sprintf("openid='%s' AND bb_id='%u' AND deleted=0 ",$this->openid,$bb_id)
			.' ORDER BY id DESC ';
		$num=M("Const")->_db()->getOne($sql);
		$time=time();
		if($time - $num < 3600){
			//数据库也做一层判断
			printJson(null,1,'请间隔1小时后再来投');
		}
		/*
		if($num>=strtotime(date("Y-m-d"))){
			printJson(null,1,"每人每天对同一个萌娃仅限投1票");
		}
		*/
		$set=[
			'openid'=>$this->openid,'bb_id'=>$bb_id,
			'create_at'=>$time,'update_at'=>$time,'deleted'=>0
		];
		$id=M("Const")->add($this->voteTab,$set);
		if(empty($id)){
			Logger::debug("晒娃活动投票失败",$set);
		}else{
			//投票.
			$sql="UPDATE ".$this->baseTab." SET vote_times=vote_times+1 ";//次数
			if(empty($num)){
				$sql.=",vote_nums=vote_nums+1";//人次
				//记录投票的人数
				$cache=Factory::getCacher();
				method_exists($cache,'getRedis') && $cache->getRedis()->lPush(self::VOTE_KEY,$bb_id);
			}
			$sql.=" WHERE ".sprintf("id='%u'",$bb_id);
			M("Const")->_db()->query($sql);
		}
		return $id;
	}

	/**
	 * 自动绑定
	 */
	private function autoBind($phone){
		if(empty($phone))
		{
			return false;
		}
		//-进行绑定操作
		$sql=" SELECT id FROM sfy_user_bind WHERE openid='".$this->openid."' LIMIT 1 ";
		$id=M('Const')->_db()->getOne($sql);
		$time=time();
		if(empty($id)){
			//add
			$data=array(
				'openid'=>$this->openid,'type'=>1,'phone'=>$phone,
				'isbind'=>1,'update_at'=>$time,'create_at'=>$time
			);
			$id=M('Const')->add('sfy_user_bind',$data);
		}else{
			//update
			$data=array(
				'type'=>1,'phone'=>$phone,
				'isbind'=>1,'update_at'=>$time
			);
			M('Const')->update('sfy_user_bind',sprintf("id='%u'",$id),$data);
		}
		try {
			//进行手机订单关联
			$bindKey = M('Api.SmartQuery')->getBindByPhone($phone);
			if ($bindKey !== false && strlen($bindKey) > 3) {
				$data = array('bind_key' => $bindKey);
				M('Const')->update('sfy_user_bind', sprintf("id='%u'", $id), $data);
				//员工信息回传
				$data = array(
					'phone' => $phone, 'account_id' => $bindKey,
					'order_phone' => '', 'order_id' => ''
				);
				M('Api.ApiPassBack')->passBindOpenid($this->openid, $data);
				//关联到订单门店
				$this->unionStore($bindKey);
			}
		}catch (Exception $e){
			Logger::error("act_baobao 绑定个人信息失败",$e->getMessage());
		}
		return false;
	}

	/**
	 * 绑定订单后，关联门店粉丝
	 */
	private function unionStore($bindKey){
		//查询是否是粉丝
		$where=sprintf("openid='%s' AND deleted=0 ",$this->openid);
		$sql=" SELECT id FROM sfy_store_user WHERE {$where} ";
		$id=M("Const")->_db()->getOne($sql);
		if(!empty($id)){
			Logger::debug("绑定订单后，查询已存在粉丝,则不关联",[$this->openid,$bindKey]);
			return false;
		}
		$order=M('Api.SmartQuery')->getOrderById($bindKey);
		if(empty($order)){
			Logger::debug("绑定订单后，查询没有订单,则不关联",[$this->openid,$bindKey]);
			return false;
		}
		$shopId=$order[0]['shopId'];
		$sql=" SELECT id,(select scene_id FROM wx_qr_code_hd WHERE wx_qr_code_hd.id=sfy_store.qr_code_id)as qr_code_id 
 FROM sfy_store WHERE org_id='".$shopId."' ";
		$row=M("Const")->_db()->getRow($sql,1);
		if(empty($row)){
			//门店粉丝不存在
			Logger::debug("绑定订单后，查询订单的门店不存在,则不关联",[$this->openid,$bindKey,$sql]);
			return false;
		}
		$sid=$row['id'];
		$qr_id=$row['qr_code_id'];
		//进行粉丝关联
		$time=time();
		$set=[
			'sid'=>$sid,'uid'=>1,'openid'=>$this->openid,
			'scene_id'=>$qr_id,'create_at'=>$time,'update_at'=>$time
		];
		$id=M("Const")->add('sfy_store_user',$set);
		Logger::debug("绑定订单后，关联门店粉丝成功",$id);
	}

}