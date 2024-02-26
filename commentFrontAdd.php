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
    $com_text = $_POST["com_text"] ?? '';
    $com_pic = $_FILES["com_pic"]["name"] ?? '';

    // 將上傳的圖片移動到指定的資料夾中
    move_uploaded_file($_FILES["com_pic"]["tmp_name"], "../images/comm/" . $_FILES["com_pic"]["name"]);

    // SQL 插入語句
    $sql = "INSERT INTO comment (com_text, mem_id, com_pic, com_status) 
    VALUES (?, 2, ?, 1)";

    // 預備語句
    $stmt = $pdo->prepare($sql);
    // 綁定參數
    $stmt->bindParam(1, $com_text);
    // $stmt->bindParam(2, $mem_id);
    $stmt->bindParam(2, $com_pic);

    // 執行 SQL 插入語句
    if ($stmt->execute()) {
        echo "新增記錄成功";
        
    } else {
        echo "錯誤: " . $stmt->errorInfo();
    }

    $stmt->closeCursor();
}

$pdo = null; // 關閉資料庫連接
