<?php
//允許跨域存取
header("Access-Control-Allow-Origin: *"); // 允許所有來源
header("Content-Type: application/json; charset=UTF-8");

try {
    // 連線 MySQL
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        require_once("../pxzoo/connectPxzoo.php");
    } else {
        // 生產環境
        require_once("https://tibamef2e.com/chd104/g4/api/connectPxzoo.php");
    }

    // SQL 查詢
    $sql = "SELECT * FROM tickets";  // 修改為您的 SQL 查詢

    // 準備 SQL 查詢
    $tickets = $pdo->prepare($sql);

    // 執行 SQL 查詢
    $tickets->execute();

    // 檢查是否有資料
    if ($tickets->rowCount() > 0) {
        $ticketsData = $tickets->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ticketsData);
    } else {
        echo json_encode(["errMsg" => "沒有找到票務資料"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
