<?php
/*
*获取品牌字符串
*http://www.nz86.com/brands/p286/（p1-p286）
*by xtz
*287
*/
	require_once('function.php');
	$start=90;
	for($i=$start;$i<$start+20;$i++){
		$url='http://www.nz86.com/brands/p'.$i.'/';
		getlist($url);
	}
	
	function getlist($url){
	$content=httpGet($url);
	$p='/<span class=\"tit\">(.*?)<\/span>/is';
	preg_match_all($p,$content,$arr);
	if($arr[1]){
		foreach($arr[1] as $k=>$v){
			$pre=trim($v);
			$st=explode('title="',$pre);
			$st=explode('">',$st[1]);
			echo trim($st[0]).'
';
		}
	}
		
	}


?>
