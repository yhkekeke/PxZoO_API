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
    $report_type = $_POST["report_type"] ?? '';
    $com_id = $_POST["com_id"] ?? '';
    // $mem_id = $_POST["mem_id"] ?? '';
    $report_text = $_POST["report_text"] ?? '';

    // SQL 插入語句
    $sql = "INSERT INTO report (report_type, com_id, mem_id, report_text, report_status) 
    VALUES (?, ?, 3, ?, '未審核')";

    // 預備語句
    $stmt = $pdo->prepare($sql);
    // 綁定參數
    $stmt->bindParam(1, $report_type);
    $stmt->bindParam(2, $com_id);
    // $stmt->bindParam(3, $mem_id);
    $stmt->bindParam(3, $report_text);

    // 執行 SQL 插入語句
    if ($stmt->execute()) {
        echo "新增記錄成功";
        
    } else {
        echo "錯誤: " . $stmt->errorInfo();
    }

    $stmt->closeCursor();
}

$pdo = null; // 關閉資料庫連接
