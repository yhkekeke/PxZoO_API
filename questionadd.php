<?php
// 允許跨域存取
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once("../../g4/api/connectPxzoo.php");

// $dbname = "pxzoo";   // 資料庫名稱改為 pxzoo
// $user = "root";
// $password = "";
// $port = 3306;

// $dsn = "mysql:host=localhost;port={$port};dbname=$dbWname;charset=utf8";

// 建立 PDO 物件
$pdo = new PDO($dsn, $user, $password, $options);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

// 檢查是否有提交表單
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 從表單中獲取數據
    $question_text = $_POST["question_text"] ?? '';
    $question_option_a = $_POST["question_option_a"] ?? '';
    $question_img_a = $_FILES["question_img_a"]["name"] ?? '';
    $question_option_b = $_POST["question_option_b"] ?? '';
    $question_img_b = $_FILES["question_img_b"]["name"] ?? '';
    $question_option_c = $_POST["question_option_c"] ?? '';
    $question_img_c = $_FILES["question_img_c"]["name"] ?? '';
    $question_option_d = $_POST["question_option_d"] ?? '';
    $question_img_d = $_FILES["question_img_d"]["name"] ?? '';
    $question_correctanswer = $_POST["question_correctanswer"] ?? '';
    $question_answer_illustrate = $_POST["question_answer_illustrate"] ?? '';

    // 將上傳的圖片移動到指定的資料夾中
    move_uploaded_file($_FILES["question_img_a"]["tmp_name"], "../images/school/animal/" . $_FILES["question_img_a"]["name"]);
    move_uploaded_file($_FILES["question_img_b"]["tmp_name"], "../images/school/animal/" . $_FILES["question_img_b"]["name"]);
    move_uploaded_file($_FILES["question_img_c"]["tmp_name"], "../images/school/animal/" . $_FILES["question_img_c"]["name"]);
    move_uploaded_file($_FILES["question_img_d"]["tmp_name"], "../images/school/animal/" . $_FILES["question_img_d"]["name"]);


    // SQL 插入語句
    $sql = "INSERT INTO questions (question_text, question_option_a, question_img_a, question_option_b, question_img_b, question_option_c, question_img_c, question_option_d, question_img_d, question_correctanswer, question_answer_illustrate, question_status) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

    // 預備語句
    $stmt = $pdo->prepare($sql);
    // 綁定參數
    $stmt->bindParam(1, $question_text);
    $stmt->bindParam(2, $question_option_a);
    $stmt->bindParam(3, $question_img_a);
    $stmt->bindParam(4, $question_option_b);
    $stmt->bindParam(5, $question_img_b);
    $stmt->bindParam(6, $question_option_c);
    $stmt->bindParam(7, $question_img_c);
    $stmt->bindParam(8, $question_option_d);
    $stmt->bindParam(9, $question_img_d);
    $stmt->bindParam(10, $question_correctanswer);
    $stmt->bindParam(11, $question_answer_illustrate);

    // 執行 SQL 插入語句
    if ($stmt->execute()) {
        echo "新增記錄成功";
    } else {
        echo "錯誤: " . $stmt->errorInfo();
    }

    $stmt->closeCursor();
}

$pdo = null; // 關閉資料庫連接
