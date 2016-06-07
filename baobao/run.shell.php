<?php
/**
 * crontab 每分钟执行
 * @author zhongyu
 * @desc 还可以吧exec进行函数封装.近一步简化逻辑
 */
header("content-type:text/html;charset=utf-8");
date_default_timezone_set("PRC");

$time=time();
$hour=intval(date('H',$time));
$minute=intval(date('i',$time));
$second=date('s',$time);
$php='/usr/bin/php';
$path = dirname(__FILE__);
$syncIndex = $path.'/SyncIndex.shell.php';

if($hour%2=='0' && $minute=='0'){
    #每两个小时运行一次
}

if($hour=='1'){

}elseif($hour=='2'){

}elseif($hour == 0 && $minute == 0) {
	//0点0分的时候执行
}elseif($hour == 1 && $minute == 0) {
	//1点0分的时候执行
}

//每分钟执行一次

$file=$path.'/SyncIndex.shell.php';
$args='orderTips';
$cmd=$php.' '.$file." '{$args}' > /dev/null &";
$obj=exec($cmd);


$file=$path.'/SyncIndex.shell.php';
$args='supplyOrder';
$cmd=$php.' '.$file." '{$args}' > /dev/null &";
$obj=exec($cmd);
