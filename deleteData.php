<?php
ini_set("display_errors", "On"); // 开启 PHP 错误显示

header("Access-Control-Allow-Origin: *"); // 允许所有来源
header("Content-Type: application/json; charset=UTF-8");

// 包含连接数据库的文件
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 开发环境
    require_once("connectPxzoo.php");
} else {
    // 生产环境
    require_once("connect_chd104g4.php"); // 确保 connectPxzoo.php 文件位于本地文件系统中
}
try {
    $sta_id=$_POST['sta_id'];

    
	$sql = "DELETE FROM staff WHERE sta_id = :sta_id";

    $news = $pdo->prepare( $sql );

    $news->bindValue(":sta_id", $sta_id);

    $news->execute();

    $result=array("error" => false, "msg" => "刪除成功");
    echo json_encode($result);

} catch (PDOException $e) {
	$result = ["error" => true, "msg" => $e->getMessage()];
	echo json_encode($result);
}


?>


