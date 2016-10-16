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

/*テスト用
if($_SERVER["REQUEST_METHOD"]=="POST"){
	if ($_POST["type"]=="vision"){
*/
		$image = $_FILES["file"]["name"];

		//ファイル一時保存
		//move_uploaded_file($_FILES["file"]["tmp_name"], $save_url);


		//テスト用
		$save_url = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/apple/1016";

	
		//名前をgetする
		$label_arr_j = $cloud_vision ->get_label($save_url);
		echo $label_arr_j;
		$label_arr = json_decode($label_arr_j,true);

		$thing_name = $label_arr["responses"][0]["labelAnnotations"][0]["description"]; 		
		echo "thing_name=" . $thing_name;

        	//sqlでパス取得
		$sql = "SELECT path from NostalGear WHERE name = \"" . $thing_name ."\" ORDER BY date DESC LIMIT 1";
		echo "sql=" . $sql;
        	$mysql->query($sql);
		$sql_result = $mysql->fetch();

		print_r($sql_result);
	
		if($mysql->rows() == 0){
			echo "error! " . $things_name ."の思い出はありません";
		}else{
			$vision_path = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/" . $sql_result["path"];
			return $vision_path;
		}
/*テスト用
	}
}else{
echo "error! post以外の通信です";
}
*/
?>

