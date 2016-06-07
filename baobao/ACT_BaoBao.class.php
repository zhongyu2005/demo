<?php

/**
 * 晒娃活动，每天定时任务维护数据一致
 * @author zhongyu
 * @version 2016-5-22
 *
 每天晚上2点
1、先判断所有的投票的人，是否还关注服务号。若是不关注,请求微信再判断一次；
更新不关注的人的投票；
2、更新每一个宝宝的所有的票数，和人次（宝宝表和宝宝投票表）
3、更新总的已报名的，已投票的人次，和访问量的设置的；

 */
class ACT_BaoBao{

	private $lock_key='shell_lock_actbaobao_';
	private $log_type='actBaoBao';

	const SIGN_KEY='act_bb_sign_key_';
	const VOTE_KEY='act_bb_vote_key_';
	const VISIT_KEY='act_bb_visit_key_';

	/**
	 * 执行的具体
	 */
	private function todo(){
		$h=date("H");
		if($h!='1'){
			//定时任务 在1点10分执行一次
			//$this->end();
		}

		return false;//活动已经结束

		$this->isFollow();

		$this->updateVote();

		$this->updateCache();


		$this->end();
	}


	/**
	 * 判断投票用户是否还关注服务号
	 */
	private function isFollow(){
		$db=M("Const")->_db();
		$sql="SELECT DISTINCT(openid) FROM `sfy_baobao_vote` WHERE deleted=0 ";
		$list=$db->getAll($sql);
		if(empty($list)){
			return false;
		}
		$where=sprintf("user in('%s') AND subscribe=0 "
			,implode("','",array_map(function($v){
				return $v['openid'];
			},$list))
		);
		$sql="SELECT user as openid FROM wx_user WHERE ".$where;
		$list=$db->getAll($sql);
		if(empty($list)){
			return false;
		}
		//如果是取消，则全部更新
		$set=[
			'update_at'=>time(),'deleted'=>1
		];
		$where=sprintf("openid in ('%s') AND deleted=0"
			,implode("','",array_map(function($v){
				return $v['openid'];
			},$list)) );
		M("Const")->update('sfy_baobao_vote',$where,$set);
	}

	/*
	 * 更细每一个宝宝的所有票数和人次
	 */
	public function updateVote(){
		$db=M("Const")->_db();
		$sql="SELECT id,vote_times,vote_nums FROM sfy_baobao WHERE deleted=0 AND is_check=1 ";

		$list=$db->getAll($sql);

		foreach($list as &$v){
			//查询宝宝的投票人数和次数
			$id=$v['id'];
			$where=sprintf("bb_id='%u' AND deleted=0",$id);
			$sql="SELECT count(DISTINCT(openid)) as num FROM `sfy_baobao_vote` WHERE ".$where;//人
			$nums=$db->getOne($sql);
			$sql="SELECT count(*) as num FROM `sfy_baobao_vote` WHERE ".$where;
			$times=$db->getOne($sql);
			if($nums!=$v['vote_nums'] || $times!=$v['vote_times'] ){
				$where=sprintf("id='%u'",$id);
				$set=['vote_times'=>$times,'vote_nums'=>$nums];
				$db->update('sfy_baobao',$where,$set);
			}

		}
		unset($v);

	}

	/*
	 * 更新已报名，已投票人次，访问量
	 */
	public function updateCache(){
		if(!method_exists(Factory::getCacher(),'getRedis')){
			//没有redis,更新啥
			return false;
		}
		$db=M("Const")->_db();
		$sql="SELECT count(*) as num  FROM `sfy_baobao` WHERE deleted=0 ";
		$sign=$db->getOne($sql);
		//已投票人次
		$sql="SELECT count(DISTINCT(openid)) as num FROM `sfy_baobao_vote` WHERE deleted=0 ";//人
		$vote=$db->getOne($sql);
		//访问量
		$redis=Factory::getCacher()->getRedis();
		$visit=intval($redis->get(self::VISIT_KEY.'2016')) + $redis->lSize(self::VISIT_KEY);

		$sign_r=intval($redis->get(self::SIGN_KEY.'2016')) + $redis->lSize(self::SIGN_KEY);
		$vote_r=intval($redis->get(self::VOTE_KEY.'2016')) + $redis->lSize(self::VOTE_KEY);

		Logger::debug("act_baobao 统计已报名，已投票，访问量为",compact('sign','vote','visit','sign_r','vote_r'));

		$redis->set(self::VISIT_KEY.'2016',$visit);//归纳到一起了
		$redis->del(self::VISIT_KEY);//删除这个队列了

		//更新已报名
		$redis->set(self::SIGN_KEY.'2016',$sign);
		$redis->del(self::SIGN_KEY);

		//更新已经投票
		$redis->set(self::VOTE_KEY.'2016',$vote);
		$redis->del(self::VOTE_KEY);

	}


	/**
	 * main方法
	 */
	public function run(){
		$this->lock_key.=Config::APP_ID.date("Ymd");//设置lock-key
		$this->check_lock();
		//进行群发操作
		$this->todo();
	}
	/**
	 * 结束当前操作.
	 */
	private function end(){
		Factory::getCacher('redis')->clear($this->lock_key);
		SS_Logger::flush();
		exit;
	}
	/*
	 * 检查是否重复运行.
	*/
	private function check_lock(){
		$lock_id=$this->lock_key;
		$is_lock=Factory::getCacher('redis')->get($lock_id);
		if('lock'==$is_lock){
			SS_Logger::debug($this->log_type,"cron act_baobao is lock.wait...");
			SS_Logger::flush();
			exit;
		}
		Factory::getCacher('redis')->set($lock_id, 'lock',600);
	}
	
}