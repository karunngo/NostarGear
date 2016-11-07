<?php
/*define('FFMPEG_BINARY', '/usr/local/bin/ffmpeg'); 
define('FFMPEG_FLVTOOLS_BINARY', '/usr/bin/flvtool2'); 
require_once 'ffmpeg.php'; 
*/
require_once("mysql.php");
require_once("cloud_vision.php");

function func($var,$text) {
	return array( 'name' => $var, 'message' => $text);
}

$mysql =new MySQL;
$cloud_vision =new Cloud_vision;

$save_url ="";
$label_arr="";
$label_arr_j="";
$image = "";
$thing_name="";
$message="";



if($_SERVER["REQUEST_METHOD"]=="POST"){
	echo "post到着(NostalGear.php) POST=";
	print var_dump($_POST);
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
			$vision_path = $sql_result["path"];
			return $vision_path;
		}
	}

	if ($_POST["type"]=="upload"){
		//ダブってるなー　改良したい
	echo "type==uploadスタート";
		$label_arr_j = $cloud_vision ->get_label($_FILES["picture"]["tmp_name"]);
                $label_arr = json_decode($label_arr_j,true);

                $thing_name = $label_arr["responses"][0]["labelAnnotations"][0]["description"];
	echo "\nthing_nameGet完了 名前は".$thing_name;
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
		$vision_path = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/" . $file_name;
	echo "\nffmpeg動いたよ vision_path = ".$vision_path;

		//ファイルが書き込めたか確認
		if(file_exists($vision_path)){
                        $sql = "INSERT INTO  NostalGear (name, path) VALUES (\'" . $thing_name ."\',\'".$vision_path."\')";
                        $mysql->query($sql);

                 	//DBへの書き込みが出来たか確認       
			if(empty($mysql->error)){
				$result = func($thing_name,"");
				echo "\nmessage=",$message." \nthing_name=".$thing_name;
                        	return $result;
			}else{
				//ここどうにかしたいな…要改良
				$result = func($thing_name,"MySQLクエリでエラー発生(mysql.class)<br>".$mysql->mysql_errno().":".$mysql->mysql_error());
				$message = "MySQLクエリでエラー発生(mysql.class)<br>".$mysql->mysql_errno().":".$mysql->mysql_error();
				echo "\nmessage=",$message." \nthing_name=".$thing_name;
				return $result;

			}
		}else{
			$result = func($thing_name,"動画を保存できませんでした");
                        $message = "動画を保存できませんでした。";
			echo "\nmessage=",$message." \nthing_name=".$thing_name;
			return $result;
		}
	}
	echo "素通りしました…";
}else{
	echo "error! post以外の通信です";
}
?>

