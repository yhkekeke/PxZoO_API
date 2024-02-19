<?php
//允許跨域存取
header("Access-Control-Allow-Origin: *"); // 允許所有來源
header("Content-Type: application/json; charset=UTF-8");

try {
    // 連線 MySQL
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        require_once("connectPxzoo.php");
    } else {
        // 生產環境
        require_once("connect_chd104g4.php");
    }

    // SQL 查詢
    $sql = "SELECT * FROM staff";  // 修改為您的 SQL 查詢

    // 準備 SQL 查詢
    $staff = $pdo->prepare($sql);

    // 執行 SQL 查詢
    $staff->execute();

    // 檢查是否有資料
    if ($staff->rowCount() > 0) {
        $staffData = $staff->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($staffData);
    } else {
        echo json_encode(["errMsg" => "查無此資料"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
