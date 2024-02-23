<?php

try {
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        header("Access-Control-Allow-Origin: *"); 
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json; charset=UTF-8");

        require_once("connectPxzoo.php");
    } else {
        require_once("connect_chd104g4.php");
    }

    // 檢查是否有收到會員ID
    if (isset($_GET['mem_id'])) {
        $mem_id = $_GET['mem_id'];

        $sql = 'SELECT * FROM comment WHERE mem_id = ?;';
        
        $memOrder = $pdo->prepare($sql);

        $memOrder->bindValue(1, $mem_id);

        $memOrder->execute();

        $result = $memOrder->fetchAll(PDO::FETCH_ASSOC);

        if ($memOrder->rowCount() > 0) {
            
            echo json_encode($result);
        } else {
            
            echo json_encode(["errMsg" => "尚無留言"]);
        }
    } else {
        echo json_encode(["errMsg" => "為提供ID"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>

