<?php


/**
 * sign方法
 * @author zhong
 * @version 2015-02-01
 */
class IndexAction extends BaseAction{

	public function __construct(){
		parent::__construct();		
	}
	
	public function index(){
	    $url=url('SimpleSql','index');
	    $this->redirect($url);
	}
	
	/**
	 * 做一个login
	 */
	public function login(){
		if(IS_AJAX && 'submit'==I('post.submit')){
			//login 操作
			$username=I('post.username');
			$userpass=I('post.userpass');
			//表单令牌
			if(token_check()==false){
				printJson(array('tk'=>form_token()),1,'请求超时,请重试');
			}
			$mod=Factory::getModel('bt_user');
			$where=sprintf("username='%s' AND deleted=0",$username);
			$row=$mod->field('id,userpass,salt')->where($where)->find();
			if(empty($row)){
				printJson(array('tk'=>form_token()),1,'账号不存在');
			}
			if($row['userpass']!=md5($userpass.$row['salt'])){
				printJson(array('tk'=>form_token()),1,'账号或者密码不正确');
			}
			$row['username']=$username;
			session_regenerate_id();
			$user_cls=load_class('UserModel');
			$user_cls->setSessionUser($row);
			printJson(1);
		}
		$turl=urldecode(I('get.url',url('DiskTop','index')));
		$this->assign('turl',$turl);
		$this->display();
		
	}

	/**
	 * logout
	 */
	public function logout(){
		unset($_SESSION);
		session_destroy();
		$url=url(CONST_ACTION,'login');
		$this->redirect($url);
	}
}