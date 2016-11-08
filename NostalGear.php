<?php
/**
 * Created by PhpStorm.
 * User: fabius
 * Date: 2016/11/07
 * Time: 21:02
 */
Class NostalGear
{

   // private $save_path = "/home/karu/public_html/orf/images/tmp,jpg";

    public function upload($image) {

        echo "ファイルの保存を試みています";
        if (is_uploaded_file($image["tmp_name"])) {
            if (move_uploaded_file($image["tmp_name"],"/home/karu/public_html/orf/images/". $image["name"])) {
                echo "画像: " . $image["name"] . "をアップロードしました";
            } else {
                echo "ファイルをアップロードできません。";
            }
        } else {
            echo "ファイルが選択されていません。";
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
    $nostalgear->upload($image);
} else if ($type == 'vision'){
    echo "vision中";
    $nostalgear->vision($image);
} else {
    echo 'no type!';
    echo $type;
}
