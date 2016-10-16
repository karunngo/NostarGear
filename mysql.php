<?php
//mySQLクラスを作る
class MySQL{
	var $m_con;
	var $m_HostName ="";
	var $m_UserName ="";
	var $m_PassWord ="";
	var $m_DataBase="";
	var $m_Rows="";

	//コンストラクタ
	function MySQL(){
		$filename="/home/karu/common/orf_mysql.ini";
		if(!file_exists($filename)){
			die("mysql.iniファイルが存在しません");
		}else{
			$fp=fopen($filename,"r");

			if(!$fp){die("mysql.iniファイルが存在しません(2)");

			}else{
				$this ->m_HostName=trim(fgets($fp));
				$this ->m_UserName=trim(fgets($fp));
				$this ->m_PassWord=trim(fgets($fp));
				$this ->m_DataBase=trim(fgets($fp));
			}
			fclose($fp);
		}
		$this->m_con =mysqli_connect($this->m_HostName,$this->m_UserName,$this->m_PassWord,$this->m_DataBase);

	if ($this->m_con == false){
		die(" mySQLの接続失敗");
	}
}


//クエリ処理
	function query($sql){
		$this->m_Rows =mysqli_query($this->m_con,$sql);
		if(!$this->m_Rows){
			die("MySQLクエリでエラー発生(mysql.class)<br>".mysql_errno().":".mysql_error());
		}
		return $this->m_Rows;
	}

//検索結果をfetch

	function fetch(){
		return mysqli_fetch_array($this->m_Rows);
	}

//変更された行の行数ゲット
	function affected_rows(){
		return mysqli_affected_rows($this->m_Rows);
	}

//列数
	function cols(){
		return mysqli_num_fields($this->m_Rows);
	}
//行数
	function rows(){
		return mysqli_num_rows($this->m_Rows);
	}
//検索結果の開放
	function free(){
		mysqli_free_result($this->m_Rows);
	}
//mySQLをクローズ
	function close(){
		mysqli_close($this->m_Rows);
	}

//エラー文用メソッド
	function errors(){
		return mysqli_errno().":".mysqli_error();
	}

	function errorno(){
		return mysql_errno();
	}
}
?>
