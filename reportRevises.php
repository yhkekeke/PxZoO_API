<?php
//下面這個if則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 開發環境
    //這是本地端的mySQL資料庫帳號密碼檔案
    require_once("connectPxzoo.php");
        //允許跨域存取
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header("Content-Type: application/json; charset=UTF-8");
} else {
    // 生產環境  
    //這裡則是我們網站上線後要偵測緯育資料庫的帳號密碼檔案
    require_once("connect_chd104g4.php");
}

// 確保任何來自 OPTIONS 方法的請求都會被接受
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 從 HTTP 請求中獲取 JSON 格式的輸入數據
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 將 JSON 字符串轉換為 PHP 關聯數組

    // 從解析過後的資料中提取特定屬性值
    $report_id = $_POST['report_id'];
    $report_type = $_POST['report_type'];
    $report_text = $_POST['report_text'];
    // $com_id = $_POST['com_id'];
    // $com_text = $_POST['com_text'];
    // $com_pic = $_POST['com_pic'];
    $report_status = $_POST['report_status'];

    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sql = "UPDATE report SET report_type = ?, 
        report_text = ? ,
        -- com_id = ? ,
        -- com_text = ? ,
        -- com_pic = ? 
        report_status = ? 
        WHERE report_id = ?";

    // 預處理 SQL 語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數到預處理語句
    $stmt->bindParam(1, $report_type);
    $stmt->bindParam(2, $report_text);
    // $stmt->bindParam(3, $com_id);
    // $stmt->bindParam(4, $com_text);
    // $stmt->bindParam(5, $com_pic);
    $stmt->bindParam(3, $report_status);
    $stmt->bindParam(4, $report_id); // 為 WHERE 條件也綁定參數

    // 執行 SQL 語句
    $stmt->execute();
    // 檢查更新操作是否成功
    if ($stmt->rowCount() > 0) {
        header('Content-Type: application/json');
        echo json_encode(["successMsg" => "更新成功"]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["errMsg" => "更新失敗"]);
    }
}
