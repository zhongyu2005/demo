<?php
/**
 * Hobby功能
 * @author zhong
 * @version 2015-9-14
 * #ActionName#,#TableName#
 * #SelectField#
 */
class #ActionName#Action extends AdminAction
{
    private $baseTab='#TableName#'; 

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 列表
	 */
	public function index(){

	    $mod=M('Const');
	    $page=I('p',0,'intval')-1;
        $page=$page<=0 ? 0 : $page;
        $pageSize = defined('Config::PAGE_LISTROWS') ? Config::PAGE_LISTROWS : 10; //每页显示记录数
	    //得到总记录数
	    $list = array ();
	    $where =$_set= null;
	    
	    $where=" deleted=0 ";
	    /* 
	    $title=trim(I('get.keyword',''));
	    if($title!=''){
	        $where.=" AND title like '%".$title."%' ";
	        $_set['title']=$title;
	    }
	     */
	    $_where=array(
	        'field'=>"count(*) num",
	        'where'=>$where,
	        "table"=>$this->baseTab,
	        'type'=>'one',
	    );
	    $row_num=$mod->query($_where);
	    $echoAjax=I('echoAjax');
	    if(!empty($row_num)){
	        $total=$row_num['num'];
	        unset($_where['type']);
	        $_where['field']='id,#SelectField#,create_at';
	        $_where['limit'] = ($page*$pageSize).','.($pageSize);
	        $_where['order'] = "id DESC";
	        $list = $mod->query($_where);
	        if(!empty($list)){
	            foreach ($list as &$v){
	                $v['create_date']=date('Y-m-d H:i',$v['create_at']);
	            }
	            unset($v);
	            /* 
	            $pageObj=array(
	                'page'=>$page+1,'total'=>$total,'pageSize'=>$pageSize
	            );
	            if(isAjax() && $echoAjax=='true'){
	                //针对异步分页
	                $pageObj['list']=$list;
	                printJson($pageObj);
	            }	  
	             */              
	            $this->assign('list', $list);
	            $this->assign('pageObj',$pageObj);
	            
	            //分页
	            $pageObj = new Page($total, $pageSize);
	            $pageHtml = $pageObj->show();
	            $this->assign('pageHtml',$pageHtml);
	        }
	    }
	    $this->assign('where',$_set);
	    $this->display();
	}

	/**
	 * 添加
	 */
	public function add(){
	    if( isAjax() && isset($_POST['submit']) && $_POST['submit']=='submit'){
	        if(empty($_POST['data'])){
	            printJson(null,1,"无指定数据");
	        }
	        $data=$_POST['data'];
	        $mod=M('Const');
	        //统一验证参数
	        $this->_check($data);
	        $data['create_at']=$data['update_at']=time();
	        $id=$mod->add($this->baseTab,$data);
	        if($id<=0){
	            printJson('',1,'操作失败.');
	        }
	        printJson(200);
	    }
		$this->display();
	}


	/**
	 * 编辑
	 */
	public function edit(){
		$id = I('id',0,'intval');
		$mod=M('Const');
		if(!empty($id)){
		    $where=sprintf("id='%s' AND  deleted=0",$id);
		    //查询
		    $_where=array(
		        'field'=>"id,#SelectField#",
		        'where'=>$where,
		        "table"=>$this->baseTab,
		        'type'=>'one'
		    );
		    $row=$mod->query($_where);
		    $this->assign('row',$row);
		}
		if( isAjax() && isset($_POST['submit']) && $_POST['submit']=='submit'){
			if(empty($row) || empty($_POST['data'])){
				printJson(null,1,"无指定数据");
			}
			$data=$_POST['data'];
			//统一验证参数
			$this->_check($data);
			$data['update_at']=time();
			$id=$mod->update($this->baseTab,$where,$data);
			printJson(200);
		}
		$this->display();
	}

	
	/**
	 * 排序
	 */
	public function sort(){
	    if(!isAjax()){
	        exit;
	    }
	    $vals=$_POST['vals'];
	    if(!is_array($vals) || count($vals)<=0 ){
	        printJson(null,1,"提交参数不合法");
	    }
	    $mod=M('Const');
	    foreach ($vals as $val){
	        $where=sprintf("id='%u'",$val['id']);
	        $mod->update($this->baseTab,$where,array('sort'=>$val['val']));
	    }
	    printJson(200);
	}
	
	/**
	 * 删除业务动态
	 */
	public function delete(){
	    $ids=$_POST['ids'];
	    if(!is_array($ids)){
	        printJson(null,1,"提交参数不合法");
	    }
	    $str_id=implode(',', $ids);
	    $where="id in (".$str_id.")";
	    $time=time();
	    M('Const')->update($this->baseTab,$where,array('deleted'=>1,'update_at'=>$time));
	    printJson(1,0,"操作成功");
	}
	
	
	
	/**
	 * 验证提交参数
	 */
	protected function _check(&$data){
	    if(empty($data)){
	        return false;
	    }
        //非空,or手机号验证
	    return true;
	}
	
}

