<?php

// ini_set("display_errors", "On");//php偵錯
try {
    // 連線 MySQL
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        require_once("connectPxzoo.php");
        header("Access-Control-Allow-Origin: *"); //允許跨域存取
    } else {
        // 生產環境
        require_once("connect_chd104g4.php");
    }

    // SQL 查詢
    $sql = "SELECT * FROM coupon";  // 修改為您的 SQL 查詢

    // 準備 SQL 查詢
    $coupons = $pdo->prepare($sql);

    // 執行 SQL 查詢
    $coupons->execute();

    // 檢查是否有資料
    if ($coupons->rowCount() > 0) {
        $couponsData = $coupons->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($couponsData);
    } else {
        echo json_encode(["errMsg" => "沒有找到票務資料"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
