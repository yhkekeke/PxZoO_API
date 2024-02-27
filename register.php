<?php
header("Access-Control-Allow-Origin: *"); // 允許所有來源
header("Content-Type: application/json; charset=UTF-8");


try {
    //連線到demo資料庫
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        require_once("connectPxzoo.php");
    } else {
        // 生產環境
        require_once("connect_chd104g4.php");
    }

    
    $sql = "insert into member(mem_name, mem_acc, mem_psw , mem_status) values (:mem_name,:mem_acc,:mem_psw , 1)";
    // 編譯sql指令
$mem = $pdo->prepare($sql);
    //將資料放入並執行之
    $mem->bindValue(":mem_name",$_POST["mem_name"]);
    $mem->bindValue(":mem_acc",$_POST["mem_acc"]);
    $mem->bindValue(":mem_psw",$_POST["mem_psw"]);
    $mem->execute();
    //準備要回傳給前端的資料
    $result = ["error" => false, "msg" => "success"];
} catch (PDOException $e) {
	//準備要回傳給前端的資料
    $result = ["error" => true, "msg" => $e->getMessage()];

}
echo json_encode($result);
?>