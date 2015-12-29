<?php
/*
*获取文章15 来自微信 每日2000篇文章
*by xtz   http://localhost/2/getCont.php
*/
	require_once('function.php');
	require_once('mysqlService.php');
	class find{
		public $db;
		public function _Db_ini(){
			if(!$this->db){
				$this->db=new MysqlAction('121.40.144.140','root','xtzlyp');
				$this->db->con();
				$this->db->database('xtzlyp');
				$this->db->query("set names 'utf8'");//写库 
			}
		}
		
		public function getlist($url,$m){
			$content=httpGet($url);
			$p='/http:\/\/mp.weixin.qq.com\/s(.*?)6#rd/is';
			preg_match_all($p,$content,$arr);
			$arr=array_unique($arr[0]);
			if(!$arr){
				die();
			}
			foreach($arr as $v){
				$ckSql="select * from csm_ul where url='".trim($v)."'";
				$flag=$this->db->query($ckSql);
				if(!$flag){
					$store=$this->db->query("insert into csm_ul (url)values('".$v."')");
				}
			}
			$upSql="update csm_ul_num set num=".($m+1)." where dat=".date('Ymd',time());
			$this->db->query($upSql);
		}
		
		
		public function getcontent($url,$pid){
			$data=httpGet($url);
			preg_match('/<title>(.*)<\/title>/', $data, $title);
			preg_match('/<div class=\"rich_media_content \" id=\"js_content\">(.*)<div class=\"rich_media_area_extra\">/is', $data, $content);
			
			$title=$title[1];
			$content=$content[0];
			$content=str_replace('http://mmbiz.qpic.cn','http://img01.store.sogou.com/net/a/04/link?appid=100520031&w=900&h=105&url=http://mmbiz.qpic.cn',$content);
			$content=str_replace('fieldset','p',$content);
			$content=str_replace('data-src','src',$content);
			$content=explode('<script',$content);
			$content=$content[0];
			if(!$title||!$content){
				echo 'mmmm';
				die();
			}
			$sql="insert into csm_a_use (title,content,times,p_id,froms) values ('".$title."','".$content."','".time()."','".$pid."','weixin');";
			$this->db->query($sql);
			$upSql="update csm_ul set type=2 where id=".$pid;
			$this->db->query($upSql);
		}
	}
	$find=new find();
	$find->_Db_ini();
	if($_GET['type']==1){
		$numSql="select * from csm_ul where type=0 order by rand() limit 1";
		$num=$find->db->query($numSql);
		$url=$num[0]['url'];
		$find->getcontent($url,$num[0]['id']);
	}else{
		$numSql="select * from csm_ul_num where dat='".date('Ymd',time())."'";
		$num=$find->db->query($numSql);
		if($num[0]){
			if($num[0]['num']>20){
				echo 'over';
				die();
			}else{
				$m=$num[0]['num'];
			}
		}else{
			$sql="insert into csm_ul_num (num,dat) values ('0','".date('Ymd',time())."');";
			$find->db->query($sql);
			$m=0;
		}
		$i=1;
		for($i;$i<16;$i++){
			$url='http://weixin.sogou.com/pcindex/pc/pc_'.$m.'/'.$i.'.html';
			$find->getlist($url,$m);
			sleep(1);
		} /**/
	}
	
	
?>
