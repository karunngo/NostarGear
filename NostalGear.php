<?php
/*define('FFMPEG_BINARY', '/usr/local/bin/ffmpeg'); 
define('FFMPEG_FLVTOOLS_BINARY', '/usr/bin/flvtool2'); 
require_once 'ffmpeg.php'; 
*/
require_once("mysql.php");
require_once("cloud_vision.php");

$mysql =new MySQL;
$cloud_vision =new Cloud_vision;
//$ffmgeg =new ffmpeg();
//$ffmpeg->on_error_die = FALSE; 

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
		//ダブってるなー　改良したい
		$label_arr_j = $cloud_vision ->get_label($_FILES["picture"]["tmp_name"]);
                $label_arr = json_decode($label_arr_j,true);

                $thing_name = $label_arr["responses"][0]["labelAnnotations"][0]["description"];

		$file_name =$thing_name . date('ymdHi') .".gif";

		/*/動画変換&保存
		$video_output_dir = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/"; 
		$bitrate = 64; 
		$samprate = 44100; 
		
		$ok = $ffmpeg->setInputFile($input_dir.$file); 
    		if(!$ok) { 
        		$message = $ffmpeg->getLastError()."<br />rn"; 
        		$ffmpeg->reset(); 
        		continue; 
    		} 

		
 		$ffmpeg->setVideoOutputDimensions(320, 240); 

		*/

		shell_exec("ffmpeg -ss 00:00:05 -i ".$_FILES["movie"]["tmp_name"]." -frames:v 300 image/". $file_name);		

		//DBに書き込み
		$sql = "INSERT INTO  NostalGear (name, path) VALUES (\'" . $thing_name ."\',\'".$file_name."\')";
                $mysql->query($sql);
                $sql_result = $mysql->fetch();

		//iPhoneに返す
	}
}else{
echo "error! post以外の通信です";
}
?>

