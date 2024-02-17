<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // 添加這行來支持帶有身份驗證信息的請求

// 確保任何來自 OPTIONS 方法的請求都會被接受
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once("../pxzoo/connectPxzoo.php");

    // 從 HTTP 請求中獲取 JSON 格式的輸入數據
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 將 JSON 字符串轉換為 PHP 關聯數組

    // 從解析過後的資料中提取特定屬性值

    $news_id = $input['news_id'];
    $news_title = $input['news_title'];
    $news_type = $input['news_type'];
    $news_typepic = $input['news_typepic'];
    $news_date = $input['news_date'];
    $news_pic = $input['news_pic'];
    $news_text_1 = $input['news_text_1'];
    $news_text_2 = $input['news_text_2'];
    $news_text_3 = $input['news_text_3'];
    $news_text_4 = $input['news_text_4'];


    // 新的圖片 URL，如果有的話
    $new_news_typepic = $input['new_news_typepic'];
    $new_news_pic = $input['new_news_pic'];

    // 更新圖片 URL
    if (!empty($new_news_typepic)) {
        $news_typepic = $new_news_typepic;
    }
    if (!empty($new_news_pic)) {
        $news_pic = $new_news_pic;
    }

    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sql = "UPDATE news SET news_title = ?, 
        news_type = ?, 
        news_typepic = ? ,
        news_date = ? ,
        news_pic = ? ,
        news_text_1 = ? ,
        news_text_2 = ? ,
        news_text_3 = ? ,
        news_text_4 = ? 
        WHERE news_id = ?";

    // 預處理 SQL 語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數到預處理語句
    $stmt->bindParam(1, $news_title);
    $stmt->bindParam(2, $news_type);
    $stmt->bindParam(3, $news_typepic);
    $stmt->bindParam(4, $news_date);
    $stmt->bindParam(5, $news_pic);
    $stmt->bindParam(6, $news_text_1);
    $stmt->bindParam(7, $news_text_2);
    $stmt->bindParam(8, $news_text_3);
    $stmt->bindParam(9, $news_text_4);
    $stmt->bindParam(10, $news_id); // 為 WHERE 條件也綁定參數

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
