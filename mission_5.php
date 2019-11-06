<?php
/***
投稿内容：comment
id name comment pass
***/

/*エラー内容出力*/
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*データベースに接続*/
try {
$pdo = new PDO('mysql:host="ユーザー名";dbname="データベース名";charset=utf8','tb-210589','パスワード',
array(PDO::ATTR_EMULATE_PREPARES => false));
} catch (PDOException $e) {
 exit('データベース接続失敗。'.$e->getMessage());
}

 /*投稿内容テーブル作成*/
try {
	// SQL作成
	$sql = 'CREATE TABLE IF NOT EXISTS comment (
		id INT(11) AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(20),
		comment TEXT,
		registry_datetime TEXT,
		pass VARCHAR(8)
	) engine=innodb default charset=utf8';

	// SQL実行
	$res = $pdo->query($sql);
	print_r($pdo->errorInfo());

} catch(PDOException $e) {

	echo $e->getMessage();
	die();
}

$Array = array();

/*Arrayの要素数を取得する*/
$hensu = "mission_3-5.txt"; //ファイルの読み込み
$Array = file($hensu);
$ArrayDelete = array();
$ArrayEdit = array();

$hensu1 = "mission_3-5.txt"; //ファイルの読み込み
$Array = file($hensu1); //投稿内容
$hensu2 = "mission_3-5delete.txt"; //ファイルの読み込み
$ArrayDelete = file($hensu2); //削除番号
$hensu3 = "mission_3-5edit.txt"; //ファイルの読み込み
$ArrayEdit = file($hensu3); //編集内容
$ArrayEditNum = array();
$ArrayPass = array();

/*コメントが送信されたら*/
if(isset($_POST["submit1"])){ //送信ボタンが押されたら
$pass = $_POST["pass"];
	if($pass!=""){ //パスワードが設定されていたら
		$comment = $_POST["comment"];
		$name = $_POST["name"];
		$pass = $_POST["pass"];
		if($comment!=""){
			$now1 = date('Y/m/d H:i:s');
			$num1 = count($Array)+1;
			$sql = "INSERT INTO comment (name, comment, registry_datetime, pass) VALUES (:name, :comment, now(), :pass)";
			$stmt = $pdo -> prepare($sql);
			print_r($pdo->errorInfo());
			$params = array(':name' => $name, ':comment' => $comment, 'pass' => $pass);
			$stmt->execute($params);
			print_r($pdo->errorInfo());
					}else{
			echo "投稿内容がありません<br>";
		}
	}else{
		echo "パスワードが設定されていません<br>";
	}
}
/*削除番号が送信されたら*/
if(isset($_POST["submit2"])){
	$pass = $_POST["pass"];
	$num2 = $_POST["delete"];
	if($num2!=""){ //削除番号が設定されていたら
		if($pass!=""){ //パスワードが設定されていたら
			$sql = "SELECT * FROM comment WHERE id = :id";
			$stmt = $pdo->prepare($sql);
			$params = array(':id' => $num2);
			$result = $stmt->execute($params);
			//$row = mysqli_num_rows($result);
			if(!$result){
				echo "投稿番号".$num2."は投稿がありません。<br>";		
			}else{
				$sql = "SELECT * FROM comment WHERE id = :id AND pass = :pass";
				$stmt = $pdo -> prepare($sql);
				$params = array(':id' => $num2, ':pass' => $pass);
				$result = $stmt->execute($params);
				//$row = mysqli_num_rows($result);
				if(!$result){
					echo "パスワードが違います。<br>";		
				}else{
					$sql = "DELETE FROM  comment WHERE id = :id AND pass = :pass";
					$stmt = $pdo -> prepare($sql);
					$params = array(':id' => $num2, ':pass' => $pass);
					$result = $stmt->execute($params);
					if(!$result){
						echo "削除に失敗しました。<br>";
					}
				}
			}
		}else{
			echo "パスワードを設定してください。<br>";
		}
	}else{
		echo "削除番号を設定してください。<br>";
	}
}

/*編集番号が送信されたら*/
if(isset($_POST["submit3"])){
	$comment = $_POST["comment"];
	$name = $_POST["name"];
	$num3 = $_POST["edit"];
	$pass = $_POST["pass"];
	if($num3!=""){ //編集番号が設定されていたら
		if($pass!=""){ //パスワードが設定されていたら
			if($comment!=""){
				$sql = "SELECT * FROM comment WHERE id = :id";
				$stmt = $pdo->prepare($sql);
				$params = array(':id' => $num3);
				$result = $stmt->execute($params);
				$row = mysqli_num_rows($result);
				if($row==0){
					echo "投稿番号".$num3."は投稿がありません。<br>";		
				}else{
					$sql = "SELECT * FROM comment WHERE id = :id AND pass = :pass";
					$stmt = $pdo -> prepare($sql);
					$params = array(':id' => $num3, ':pass' => $pass);
					$result = $stmt->execute($params);
					$row = mysqli_num_rows($result);
					if($row==0){
						echo "パスワードが違います。<br>";		
					}else{
						$sql = "UPDATE toukou SET comment = :comment WHERE  id = :id AND pass = :pass";
						$stmt = $pdo -> prepare($sql);
						$params = array(':id' => $num3, ':pass' => $pass);
						$result = $stmt->execute($params);
						if(!$result){
							echo "編集に失敗しました。<br>";
						}
					}
				}
			}else{
				echo "編集内容がありません。<br>";
			}
		}else{
			echo "パスワードを設定してください。<br>";
		}
	}else{
		echo "編集番号を設定してください。<br>";
	}
}
/*出力*/
$stmt = $pdo->query('SELECT * FROM comment');
$result = $stmt->fetchAll();
foreach($result as $output){
	echo $output['id']." ".$output['name']." ".$output['comment']." ".$output['registry_datetime']."<br>";
}

// 接続を閉じる
$pdo = null;
?>
</body>
</html>