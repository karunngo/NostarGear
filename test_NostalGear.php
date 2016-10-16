<?php

require_once("mysql.php");
require_once("cloud_vision.php");

$mysql =new MySQL;
$cloud_vison = new Cloud_vision;


if($_SERVER["REQUEST_METHOD"]=="POST"){
        if ($_POST["type"]=="vision"){
		$image = $_FILES["file"]["name"];

		//一旦保存する
		$save_url = "";		
		move_uploaded_file($_FILES["file"]["tmp_name"], $save_url);
	
		//名前をgetする
		$label_arr = json_decode($cloud_vision->get_label($save_url),true);

		echo $label_arr;

		$thing_name = $label_arr[`responses`][`labelAnnotations`][0][`description`]; 		


        	//sqlでパス取得
		$sql = "SELECT path from NostalGear ORDER BY date DESC LIMIT 1 WHERE name = " . $thing_name;
        	$mysql->query($sql);
		$sql_result = $mysql->fetch();

		echo $sql_result;
	
		if($mysql->rows() == 0){
			//echo "error! " . $things_name ."の思い出はありません";
		}else{
			//$vision_path = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/" . $$sql_result[0][`path`];
			//return $vision_path
		}
	}


}else{
echo "error! post以外の通信です"";
}
?>

