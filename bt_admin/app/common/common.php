<?php

/**
 * js，css，生成器
 * @param $type=css,js
 */
function dom_help($url){
	if(empty($url)){
		return false;
	}
	if(is_string($url)){
		$url=array($url);
	}
	if(!is_array($url)){
		return false;
	}
	$_arr=array();
	foreach ($url as $v){
		$arr=pathinfo($v);
		$ext=strtolower($arr['extension']);
		if(!in_array($ext, array('js','css'))){
			continue;
		}
		switch($ext){
			case 'js':
				$_arr[]='<script type="text/javascript" src="'.$v.'"></script>';
				break;
			case 'css':
				$_arr[]='<link rel="stylesheet" type="text/css" href="'.$v.'" />';
				break;
		}
	}
	$str='';
	if(!empty($_arr)){
		$str=implode("\n", $_arr);
	}
	return $str;
}

/**
 * 生成url
 */
function url($action,$method,$args=null){
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
	$action[0]=strtoupper($action[0]);
	$url.='?a='.$action.'&m='.$method;
	if(!empty($args)){
		$url.='&'.http_build_query($args);
	}
	return $url;
}


/**
 * 加载类库
 */
function load_class($className){
	static $_ar=array();
	if(isset($_ar[$className])){
		return $_ar[$className];
	}
	if(class_exists($className)){
		$_ar[$className]=new $className();
		return $_ar[$className];
	}
	return false;
}

/**
* 简单分页
*/
function pageShow($page=array()){
	if(empty($page)){
		return false;
	}
	$prev_dis=$next_dis='disabled';
	$prev_url=$next_url='javascript:;';
	if(!empty($page['prev'])){
		$prev_url=$page['prev'];
		$prev_dis='';
	}
	if(!empty($page['next'])){
		$next_url=$page['next'];
		$next_dis='';
	}
	$html=<<<EOF
	<nav>
  <ul class="pager">
    <li class="previous {$prev_dis}"><a href="{$prev_url}"><span aria-hidden="true">&larr;</span> 上一页</a></li>
    <li class="next {$next_dis}"><a href="{$next_url}">下一页 <span aria-hidden="true">&rarr;</span></a></li>
  </ul>
</nav>
EOF;
	return $html;
}