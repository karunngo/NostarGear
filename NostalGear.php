<?php

require_once("mysql.php");
require_once("cloud_vision.php");

$mysql =new MySQL;
$cloud_vision =new Cloud_vision;

$save_url ="";
$label_arr="";
$label_arr_j="";
$image = "";
$thing_name="";
$message="";


if($_SERVER["REQUEST_METHOD"]=="POST"){
	if ($_POST["type"]=="vision"){
		//名前をgetする
		$label_arr_j = $cloud_vision ->get_label($_FILES["picture"]["tmp_name"]);
		$label_arr = json_decode($label_arr_j,true);


		$thing_name = $label_arr["responses"][0]["labelAnnotations"][0]["description"]; 		

        	//sqlでパス取得
		$sql = "SELECT path from NostalGear WHERE name = \"" . $thing_name ."\" ORDER BY date DESC LIMIT 1";
        	$mysql->query($sql);
		$sql_result = $mysql->fetch();

	
		if($mysql->rows() == 0){
			$message = "error! " . $things_name ."の思い出はありません";
			echo $message;
			return $message;
		}else{
			$vision_path = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/" . $sql_result["path"];
			return $vision_path;
		}
	}

	if ($_POST["type"]=="upload"){


	}
}else{
echo "error! post以外の通信です";
}
?>

