<?php
/**
* php document test
*/


$url='https://www.zhihu.com/question/47261726';
$data='';

$html=http($url,$data);

$doc = new DOMDocument();
@$doc->loadHTML($html);

/*
//use by tag
$a=$doc->getElementsByTagName('a');

foreach ($a as $v) {
    var_dump($v->nodeValue);
    print_r($v->getAttribute('href'));
    echo '<hr />';
}
*/
//use xpath
$domxpath = new DOMXPath($doc);
$result = $domxpath->query("//a");

foreach($result as $v){
    var_dump($v->nodeValue,$v->getAttribute("href"));
}

