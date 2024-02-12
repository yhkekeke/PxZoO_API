<?php
// 允許跨域存取
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once("../pxzoo/connectPxzoo.php");

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
