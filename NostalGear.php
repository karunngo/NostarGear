<?php
/**
 * Created by PhpStorm.
 * User: fabius
 * Date: 2016/11/07
 * Time: 21:02
 */

require_once("cloud_vision.php");
require_once("mysql.php");

Class NostalGear
{
    public function upload($image,$movie) {

	$upload_result = array(
	  "name"=>"",
	  "error"=>""
	);

	$is_image_saved = false; //画像がサーバに保存されたか
	$is_gifmovie_saved = false; //動画がgif変換されてサーバに保存されたか


	//画像の保存
        if (is_uploaded_file($image["tmp_name"])) {

	    $is_image_saved = move_uploaded_file($image["tmp_name"],"/home/karu/public_html/orf/images/".$image["name"]);

            if ($is_image_saved==false) {
                $upload_result["error"] .= "画像ファイルのアップロードができません。<br>";
            }

        } else {
           $upload_result["error"] .= "画像ファイルが選択されていません。<br>";

        }
	
	//動画の保存
	if (is_uploaded_file($movie["tmp_name"])) {
	
	    $is_movie_saved = move_uploaded_file($movie["tmp_name"],"/home/karu/public_html/orf/movies/". $movie["name"]);

            if ($is_movie_saved) {

		//gif変換
		$saved_movie_name =$movie["name"].date('ymdHi').".gif";
		shell_exec("ffmpeg -ss 00:00:00 -i movies/".$movie["name"]." -frames:v 300 movies/". $saved_movie_name);

		//ここで保存出来たか確認したいのに、できない…
		//$is_gitmovie_saved=$file_exists('movies'$file_name);
		$is_gifmovie_saved = true;

	    } else {
                $upload_result["error"] .= "動画ファイルをアップロードできません。<br>";
            }

        } else {

            $upload_result["error"] .= "動画ファイルが選択されていません。";

        }
	
	
	//画像と動画、両方保存できたら
	if ($is_image_saved && $is_gifmovie_saved){

		$movie_url = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/movies/".$saved_movie_name;	

		//画像をCloud Visionに投げ、判別結果を取得
                $cloud_vision = NEW Cloud_vision();
                $cv_result = $cloud_vision->post_image("http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/images/".$image["name"]);
                $upload_result["name"] = $cv_result["responses"][0]["labelAnnotations"][0]["description"];

		//DBに保存
		$mysql = NEW MySQL();
		$sql = 'INSERT INTO  NostalGear (name, path) VALUES (\'' . $object_name .'\',\''.$movie_url.'\')';
                $mysql->query($sql);

                if(isset($mysql->error)){
                   $upload_result["error"] .= "\nMySQLクエリでエラー発生(mysql.class)<br>".$mysql->error;
		}

	}

	//物体の名前とメッセージを返す
	$json =json_encode($upload_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	echo $json;
    }

    public function vision($image) {

    }
}

$type  = $_POST['request_type'];
$image = $_FILES['object_image'];
$movie = $_FILES['object_movie'];

$nostalgear = new NostalGear();

if ($type == 'upload') {
    $nostalgear->upload($image,$movie);
} else if ($type == 'vision'){
    $nostalgear->vision($image);
} else {
    echo 'no type!';
    echo $type;
}
