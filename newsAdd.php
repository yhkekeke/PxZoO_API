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

// 建立 PDO 物件
$pdo = new PDO($dsn, $user, $password, $options);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

// 檢查是否有提交表單
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 從表單中獲取數據
    $news_title = $_POST["news_title"] ?? '';
    $news_type = $_POST["news_type"] ?? '';
    $news_typepic = $_POST["news_typepic"] ?? '';
    $news_date = $_POST["news_date"] ?? '';
    $news_pic = $_FILES["news_pic"]["name"] ?? '';
    $news_text_1 = $_POST["news_text_1"] ?? '';
    $news_text_2 = $_POST["news_text_2"] ?? '';
    $news_text_3 = $_POST["news_text_3"] ?? '';
    $news_text_4 = $_POST["news_text_4"] ?? '';

    // 將上傳的圖片移動到指定的資料夾中
    move_uploaded_file($_FILES["news_pic"]["tmp_name"], "../images/news/" . $_FILES["news_pic"]["name"]);

    // SQL 插入語句
    $sql = "INSERT INTO news (news_title, news_type, news_typepic, news_date, news_pic, news_text_1, news_text_2, news_text_3, news_text_4,  news_status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

    // 預備語句
    $stmt = $pdo->prepare($sql);
    // 綁定參數
    $stmt->bindParam(1, $news_title);
    $stmt->bindParam(2, $news_type);
    $stmt->bindParam(3, $news_typepic);
    $stmt->bindParam(4, $news_date);
    $stmt->bindParam(5, $news_pic);
    $stmt->bindParam(6, $news_text_1);
    $stmt->bindParam(7, $news_text_2);
    $stmt->bindParam(8, $news_text_3);
    $stmt->bindParam(9, $news_text_4);


    // 執行 SQL 插入語句
    if ($stmt->execute()) {
        echo "新增記錄成功";
        
    } else {
        echo "錯誤: " . $stmt->errorInfo();
    }

    $stmt->closeCursor();
}

$pdo = null; // 關閉資料庫連接
