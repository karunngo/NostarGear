<?php
/**
 * Created by PhpStorm.
 * User: fabius
 * Date: 2016/11/07
 * Time: 21:02
 */

require_once("cloud_vision.php");

Class NostalGear
{
    public function upload($image,$movie) {

        echo "ファイルの保存を試みています";
        if (is_uploaded_file($image["tmp_name"])) {
            if (move_uploaded_file($image["tmp_name"],"/home/karu/public_html/orf/images/".$image["name"])) {
                echo "\n画像: " . $image["name"] . "をアップロードしました";

        	//画像に移っていた物体の判別結果を取得
        	$cloud_vision = NEW Cloud_vision();
		$cv_result = $cloud_vision->post_image("http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/images/".$image["name"]);
		var_dump($cv_result);
		$object_name = $cv_result["responses"][0]["labelAnnotations"][0]["description"];
		echo "\n画像に移っている物体:".$object_name;

            } else {
                echo "画像ファイルをアップロードできません。";
            }
        } else {
            echo "画像ファイルが選択されていません。";
        }
	
	if (is_uploaded_file($movie["tmp_name"])) {
            if (move_uploaded_file($movie["tmp_name"],"/home/karu/public_html/orf/movies/". $movie["name"])) {
                echo "\n動画: " . $movie["name"] . "をアップロードしました";
            } else {
                echo "動画ファイルをアップロードできません。";
            }
        } else {
            echo "動画ファイルが選択されていません。";
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
    var_dump($image);
    Var_dump($movie);
    $nostalgear->upload($image,$movie);
} else if ($type == 'vision'){
    echo "vision中";
    $nostalgear->vision($image);
} else {
    echo 'no type!';
    echo $type;
}
