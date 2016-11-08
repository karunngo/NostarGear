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

	$object_name="";//画像から判別した物の名前
	$message="";//エラーメッセージ

	$is_image_saved = false; //画像がサーバに保存されたか
	$is_gifmovie_saved = false; //動画がgif変換されてサーバに保存されたか

        echo "ファイルの保存を試みています";

	//画像の保存
        if (is_uploaded_file($image["tmp_name"])) {

	    $is_image_saved = move_uploaded_file($image["tmp_name"],"/home/karu/public_html/orf/images/".$image["name"]);

            if ($is_image_saved) {
                echo "\n画像: " . $image["name"] . "をアップロードしました";
            } else {
                echo "画像ファイルをアップロードができません。";
            }

        } else {

            echo "画像ファイルが選択されていません。";

        }
	
	//動画の保存
	if (is_uploaded_file($movie["tmp_name"])) {
	
	    $is_movie_saved = move_uploaded_file($movie["tmp_name"],"/home/karu/public_html/orf/movies/". $movie["name"]);

            if ($is_movie_saved) {
                echo "\n動画: " . $movie["name"] . "をアップロードしました";

		//gif変換
		$file_name =$movie["name"].date('ymdHi').".gif";
		shell_exec("ffmpeg -ss 00:00:00 -i movies/".$movie["name"]." -frames:v 300 movies/". $file_name);

		//ここで保存出来たか確認したいのに、できない…
		//$is_gitmovie_saved=$file_exists('movies'$file_name);
		$is_gifmovie_saved = true;

	    } else {
                echo "動画ファイルをアップロードできません。";
            }

        } else {

            echo "動画ファイルが選択されていません。";

        }
	
	
	//画像と動画、両方保存できたら
	if ($is_image_saved && $is_gifmovie_saved){

		$movie_url = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/movies/".$file_name;	

		//画像をCloud Visionに投げ、判別結果を取得
                $cloud_vision = NEW Cloud_vision();
                $cv_result = $cloud_vision->post_image("http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/images/".$image["name"]);
                $object_name = $cv_result["responses"][0]["labelAnnotations"][0]["description"];
                echo "\n画像に移っている物体:".$object_name;

		//DBに保存
		$mysql = NEW MySQL();
		echo "\nmovie_url:".$movie_url;
		$sql = 'INSERT INTO  NostalGear (name, path) VALUES (\'' . $object_name .'\',\''.$movie_url.'\')';
		echo "\nsql:".$sql;
                $mysql->query($sql);

                if(empty($mysql->error)){
			echo"\nMySQLクエリ成功";
                }else{
                	echo "\nMySQLクエリでエラー発生(mysql.class)<br>".$mysql->mysqli_error;
		}

	//物体の名前とメッセージを返す

	}



    }

    public function vision($image) {

    }
}

$type  = $_POST['request_type'];
$image = $_FILES['object_image'];
$movie = $_FILES['object_movie'];

$nostalgear = new NostalGear();

if ($type == 'upload') {
    echo "upload中";
    $nostalgear->upload($image,$movie);
} else if ($type == 'vision'){
    echo "vision中";
    $nostalgear->vision($image);
} else {
    echo 'no type!';
    echo $type;
}
