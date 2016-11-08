<?php

require_once("mysql.php");
require_once("cloud_vision.php");


$mysql =new MySQL;
$cloud_vision =new Cloud_vision;

$save_url =""; // 画像を一時保存するパス
$label_arr=""; // CloudVisionから帰ってきたタグの配列
$label_arr_j=""; //
$image = "";
$thing_name="";
$message="";
$save_url = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/tmp/toriaezu";


if($_SERVER["REQUEST_METHOD"]=="POST"){
	echo "post到着(NostalGear.php) POST=";
	print var_dump($_POST);
	echo "fileのサイズ＝".$_SERVER["CONTENT_LENGTH"];
	echo "\nFILE=";
	print print_r($_FILES["picture"]);
	print print_r($_FILES["movie"]);
	if($_POST["type"]=="vision"){
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
			$vision_path = $sql_result["path"];
			return $vision_path;
		}
	}

	if ($_POST["type"]=="upload"){
//		//ダブってるなー　改良したい
//	echo "type==uploadスタート";
//		if(empty($FILES["picture"]["tmp_name"])){
//		$message= "\n画像のアップロードできてないよ！";
//		echo $message;
//		return $message;}
//
//		if(file_exists($save_url)){
//		unlink($save_url);
//		}
//
//		move_uploaded_file($_FILES["picture"]["tmp_name"], $save_url);
//
//		$label_arr_j = $cloud_vision ->get_label($save_url);
//                $label_arr = json_decode($label_arr_j,true);
//
//                $thing_name = $label_arr["responses"][0]["labelAnnotations"][0]["description"];
//	echo "\nthing_nameGet完了 名前は".$thing_name;
//		$file_name =$thing_name . date('ymdHi') .".gif";
//
//		shell_exec("ffmpeg -ss 00:00:05 -i ".$_FILES["movie"]["tmp_name"]." -frames:v 300 image/". $file_name);
//		$vision_path = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/" . $file_name;
//	echo "\nffmpeg動いたよ vision_path = ".$vision_path;
//
//		//ファイルが書き込めたか確認
//		if(file_exists($vision_path)){
//                        $sql = "INSERT INTO  NostalGear (name, path) VALUES (\'" . $thing_name ."\',\'".$vision_path."\')";
//                        $mysql->query($sql);
//
//                 	//DBへの書き込みが出来たか確認
//			if(empty($mysql->error)){
//				$result = func($thing_name,"");
//				echo "\nmessage=",$message." \nthing_name=".$thing_name;
//                        	return $result;
//			}else{
//				//ここどうにかしたいな…要改良
//				$result = func($thing_name,"MySQLクエリでエラー発生(mysql.class)<br>".$mysql->mysql_errno().":".$mysql->mysql_error());
//				$message = "MySQLクエリでエラー発生(mysql.class)<br>".$mysql->mysql_errno().":".$mysql->mysql_error();
//				echo "\nmessage=",$message." \nthing_name=".$thing_name;
//				return $result;
//
//			}
//		}else{
//			$result = func($thing_name,"動画を保存できませんでした");
//                        $message = "動画を保存できませんでした。";
//			echo "\nmessage=",$message." \nthing_name=".$thing_name;
//			return $result;
//		}
//	}
} else {
	echo "MUST BE POST";
}

