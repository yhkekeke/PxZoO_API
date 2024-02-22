<?php
ini_set("display_errors", "On"); 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    require_once("connectPxzoo.php");
} else {
    require_once("connectPxzoo.php"); 
}
try {
    $com_id=$_POST['com_id'];

    
	$sql = "DELETE FROM comment WHERE  com_id = :com_id";

    $news = $pdo->prepare( $sql );

    $news->bindValue(":com_id", $com_id);

    $news->execute();

    $result=array("error" => false, "msg" => "刪除成功");
    echo json_encode($result);

} catch (PDOException $e) {
	$result = ["error" => true, "msg" => $e->getMessage()];
	echo json_encode($result);
}


?>


