<?php
class Cloud_vision{
	$api_key = "";

	function get_label($path){
		echo "get_labelが動いたよ";
		$api_key = "AIzaSyBsmdrzulnPMj2B9Zf9yEmy3e6kEgqgkPM";	
		$image_path = $path;
		//$image_path = "http://life-cloud.ht.sfc.keio.ac.jp/~karu/orf/image/toriaezu.jpg" ;
		// リクエスト用のJSONを作成
        	$json = json_encode( array(
        	        "requests" => array(
        	                array(
        	                        "image" => array(
        	                                "content" => base64_encode( file_get_contents( $image_path ) ) ,
        	                        ) ,
       		                         "features" => array(
        	                                array(
        	                                        "type" => "LABEL_DETECTION" ,
        	                                        "maxResults" => 1 ,
        	                                ) ,
        	                        ) ,
        	                ) ,
        	        ) ,
        	) ) ;

	        // リクエストを実行
	        $curl = curl_init() ;
	        curl_setopt( $curl, CURLOPT_URL, "https://vision.googleapis.com/v1/images:annotate?key=" . $api_key ) ;
	        curl_setopt( $curl, CURLOPT_HEADER, true ) ;
	        curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "POST" ) ;
	        curl_setopt( $curl, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ) ) ;
	        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ) ;
	        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ) ;
	        if( isset($referer) && !empty($referer) ) curl_setopt( $curl, CURLOPT_REFERER, $referer ) ;
	        curl_setopt( $curl, CURLOPT_TIMEOUT, 15 ) ;
	        curl_setopt( $curl, CURLOPT_POSTFIELDS, $json ) ;
	        $res1 = curl_exec( $curl ) ;
	        $res2 = curl_getinfo( $curl ) ;
	        curl_close( $curl ) ;
	
	        // 取得したデータ
	        $json = substr( $res1, $res2["header_size"] ) ;                         // 取得したJSON
	        $header = substr( $res1, 0, $res2["header_size"] ) ;            // レスポンスヘッダー
		return $json;
	}
}

