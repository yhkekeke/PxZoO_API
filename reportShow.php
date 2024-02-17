<?php
//允許跨域存取
header("Access-Control-Allow-Origin: *"); // 允許所有來源
header("Content-Type: application/json; charset=UTF-8");

try {
    // 連線 MySQL
    // if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    //     // 開發環境
    //     require_once("../pxzoo/connectPxzoo.php");
    // } else {
    //     // 生產環境
    //     require_once("https://tibamef2e.com/chd104/g4/api/connectPxzoo.php");
    // }
    require_once("../pxzoo/connectPxzoo.php");
    // SQL 查詢
    $sql = "SELECT * FROM report";  // 修改為您的 SQL 查詢

    // 準備 SQL 查詢
    $report = $pdo->prepare($sql);

    // 執行 SQL 查詢
    $report->execute();

    // 檢查是否有資料
    if ($report->rowCount() > 0) {
        $reportData = $report->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($reportData);
    } else {
        echo json_encode(["errMsg" => "沒有找到資料"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
