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
$conn = new PDO($dsn, $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

// 檢查是否有提交表單
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 從表單中獲取數據
    $vote_activity_name = $_POST["vote_activity_name"] ?? '';
    $vote_activity_content = $_FILES["vote_activity_content"]["name"] ?? '';
    $vote_activity_date = $_POST["vote_activity_date"] ?? '';
    $animal_id_1 = $_POST["animal_id_1"] ?? '';
    $animal_id_2 = $_POST["animal_id_2"] ?? '';
    $animal_id_3 = $_POST["animal_id_3"] ?? '';

    // SQL 插入語句
    $sql = "INSERT INTO vote_activity (vote_activity_name, vote_activity_content, vote_activity_date, animal_id_1, animal_id_2, animal_id_3) 
  VALUES (?, ?, ?, ?, ?, ?)";

    // 預備語句
    $stmt = $conn->prepare($sql);
    // 綁定參數
    $stmt->bindParam(1, $vote_activity_name);
    $stmt->bindParam(2, $vote_activity_content);
    $stmt->bindParam(3, $vote_activity_date);
    $stmt->bindParam(4, $animal_id_1);
    $stmt->bindParam(5, $animal_id_2);
    $stmt->bindParam(6, $animal_id_3);
    // 執行 SQL 插入語句
    if ($stmt->execute()) {
        echo "新增記錄成功";
    } else {
        echo "錯誤: " . $stmt->errorInfo();
    }

    $stmt->closeCursor();
}

$conn = null; // 關閉資料庫連接
