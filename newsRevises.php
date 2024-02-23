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
    $news_id = $_POST['news_id'];
    $news_title = $_POST['news_title'];
    $news_type = $_POST['news_type'];
    $news_date = $_POST['news_date'];
    $news_text_1 = $_POST['news_text_1'];
    $news_text_2 = $_POST['news_text_2'];
    $news_text_3 = $_POST['news_text_3'];
    $news_text_4 = $_POST['news_text_4'];


    // 新的圖片 URL，如果有的話
    $news_typepic = $_POST['news_typepic'] ?? '';
    $news_pic = $_POST['news_pic'] ?? '';

    $new_news_typepic = $_FILES['news_typepic']["name"] ?? '';
    $new_news_pic = $_FILES['news_pic']["name"] ?? '';

    // 如果沒有上傳新圖片，則使用原始圖片名稱
    if (empty($new_news_typepic)) {
        $new_news_typepic = $news_typepic;
    }
    if (empty($new_news_pic)) {
        $new_news_pic = $news_pic;
    }

    // 目標路徑
    $target_directory = '../images/news/';

    // 更新圖片 URL 並移動檔案
    if (!empty($new_news_typepic)) {
        $news_typepic = $target_directory . 'newsFrame/' . basename($new_news_typepic);
        move_uploaded_file($_FILES["news_typepic"]["tmp_name"], $news_typepic);
    } else {
        $news_typepic = $news_typepic; // 使用已有的圖片名稱
    }
    if (!empty($new_news_pic)) {
        $news_pic = $target_directory . basename($new_news_pic);
        move_uploaded_file($_FILES["news_pic"]["tmp_name"], $news_pic);
    } else {
        $news_pic = $news_pic; // 使用已有的圖片名稱
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
    $stmt->bindParam(3, $new_news_typepic);
    $stmt->bindParam(4, $news_date);
    $stmt->bindParam(5, $new_news_pic);
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
