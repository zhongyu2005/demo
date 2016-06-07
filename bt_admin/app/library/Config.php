<?php
$_log=dirname(__BT__).'/log_app/';
$config=array(


		'DEBUG'=>false,


		'DEFAULT_DB'=>array(
				'DB_HOST'=>'127.0.0.1',
				'DB_NAME'=>'test',
				'DB_USER'=>'root',
				'DB_PWD'=>'',
		),
		'LOG_PATH'=>$_log,
		//cache
		'DEFAULT_CACHE'=>'redis',

		'CACHE_REDIS_HOST'=>'127.0.0.1',
		'CACHE_REDIS_PORT'=>'6379',
);
/*
$file=__APP__.'/common/config.php';
if(is_file($file)){
	$cfg=include $file;
	if(!empty($cfg)){
		$config['APP_CONFIG'] = array_merge($config['APP_CONFIG'],$cfg);
// 		foreach ($cfg as $key=>$val){
// 			$config['APP_CONFIG'][$key]=$val;
// 		}
	}
}
*/
return $config;
