<?php
header("Access-Control-Allow-Origin: *"); // 允許所有來源
header("Content-Type: application/json; charset=UTF-8");


try {
    //連線到demo資料庫
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        require_once("../pxzoo/connectPxzoo.php");
    } else {
        // 生產環境
        require_once("connectPxzoo.php");
    }

    
    $sql = "insert into staff(sta_pos , sta_email , sta_acc , sta_psw) values (:sta_pos,:sta_email , :sta_acc , :sta_psw)";
    // 編譯sql指令
$mem = $pdo->prepare($sql);
    //將資料放入並執行之
    $mem->bindValue(":sta_pos",$_POST["sta_pos"]);
    $mem->bindValue(":sta_email",$_POST["sta_email"]);
    $mem->bindValue(":sta_acc",$_POST["sta_acc"]);
    $mem->bindValue(":sta_psw",$_POST["sta_psw"]);
    $mem->execute();
    //準備要回傳給前端的資料
    $result = ["error" => false, "msg" => "success"];
} catch (PDOException $e) {
	//準備要回傳給前端的資料
    $result = ["error" => true, "msg" => $e->getMessage()];

}
echo json_encode($result);
?>