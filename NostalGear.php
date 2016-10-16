<?php

require_once("mysql.php");
require_once("cloud_vision.php");

$mysql =new MySQL;
$cloud_vison =new Cloud_vision;

$save_url ="";
$label_arr="";

if($_SERVER["REQUEST_METHOD"]=="POST"){
        //テスト用if ($_POST["type"]=="vision"){
		$image = $_FILES["file"]["name"];

		//ファイル一時保存
		//move_uploaded_file($_FILES["file"]["tmp_name"], $save_url);


		//テスト用
		$save_url = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/apple/1016";

	
		//名前をgetする
		$label_arr = json_decode($cloud_vision->get_label($save_url),true);


		echo $label_arr;

		$thing_name = $label_arr[`responses`][`labelAnnotations`][0][`description`]; 		


        	//sqlでパス取得
		$sql = `SELECT path from NostalGear WHERE name = "` . $thing_name .`" ORDER BY date DESC LIMIT 1`;
        	$mysql->query($sql);
		$sql_result = $mysql->fetch();

		echo $sql_result;
	
		if($mysql->rows() == 0){
			echo "error! " . $things_name ."の思い出はありません";
		}else{
			$vision_path = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/" . $$sql_result[0][`path`];
			return $vision_path;
		}
	//テスト用}


}else{
echo "error! post以外の通信です";
}
?>

