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
    $input = json_decode($inputJSON, TRUE); // 將 JSON 字串轉換為 PHP 關聯陣列

    // 從解析後的數據中提取特定屬性值
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $question_option_a = $_POST['question_option_a'];
    $question_option_b = $_POST['question_option_b'];
    $question_option_c = $_POST['question_option_c'];
    $question_option_d = $_POST['question_option_d'];
    $question_correctanswer = $_POST['question_correctanswer'];
    $question_answer_illustrate = $_POST['question_answer_illustrate'];

    // 檢查新的圖片 URL，如果有的話
    $question_img_a = $_POST['question_img_a'] ?? '';
    $question_img_b = $_POST['question_img_b'] ?? '';
    $question_img_c = $_POST['question_img_c'] ?? '';
    $question_img_d = $_POST['question_img_d'] ?? '';

    $new_question_img_a = $_FILES['question_img_a']["name"] ?? '';
    $new_question_img_b = $_FILES['question_img_b']["name"] ?? '';
    $new_question_img_c = $_FILES['question_img_c']["name"] ?? '';
    $new_question_img_d = $_FILES['question_img_d']["name"] ?? '';

    // 如果沒有上傳新圖片，則使用原始圖片名稱
    if (empty($new_question_img_a)) {
        $new_question_img_a = $question_img_a;
    }
    if (empty($new_question_img_b)) {
        $new_question_img_b = $question_img_b;
    }
    if (empty($new_question_img_c)) {
        $new_question_img_c = $question_img_c;
    }
    if (empty($new_question_img_d)) {
        $new_question_img_d = $question_img_d;
    }

    // 目標路徑
    $target_directory = '../images/school/animal/';
    if (!empty($new_question_img_a)) {
        $question_img_a = $target_directory . basename($new_question_img_a);
        move_uploaded_file($_FILES["question_img_a"]["tmp_name"], $question_img_a);
    }
    if (!empty($new_question_img_b)) {
        $question_img_b = $target_directory . basename($new_question_img_b);
        move_uploaded_file($_FILES["question_img_b"]["tmp_name"], $question_img_b);
    }
    if (!empty($new_question_img_c)) {
        $question_img_c = $target_directory . basename($new_question_img_c);
        move_uploaded_file($_FILES["question_img_c"]["tmp_name"], $question_img_c);
    }
    if (!empty($new_question_img_d)) {
        $question_img_d = $target_directory  . basename($new_question_img_d);
        move_uploaded_file($_FILES["question_img_d"]["tmp_name"], $question_img_d);
    }

    // 準備 SQL 更新語句，請根據您的資料庫實際情況進行調整
    $sql = "UPDATE questions SET question_text = ?, 
          question_option_a = ?, 
          question_img_a = ? ,
          question_option_b = ? ,
          question_img_b = ? ,
          question_option_c = ? ,
          question_img_c = ? ,
          question_option_d = ? ,
          question_img_d = ? ,
          question_correctanswer = ? ,  
          question_answer_illustrate = ? 
          WHERE question_id = ?";

    // 預備 SQL 語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數到預備語句
    $stmt->bindParam(1, $question_text);
    $stmt->bindParam(2, $question_option_a);
    $stmt->bindParam(3, $new_question_img_a); // 將檔案名稱綁定到資料庫
    $stmt->bindParam(4, $question_option_b);
    $stmt->bindParam(5, $new_question_img_b); // 將檔案名稱綁定到資料庫
    $stmt->bindParam(6, $question_option_c);
    $stmt->bindParam(7, $new_question_img_c); // 將檔案名稱綁定到資料庫
    $stmt->bindParam(8, $question_option_d);
    $stmt->bindParam(9, $new_question_img_d); // 將檔案名稱綁定到資料庫
    $stmt->bindParam(10, $question_correctanswer);
    $stmt->bindParam(11, $question_answer_illustrate);
    $stmt->bindParam(12, $question_id); // 為 WHERE 條件也綁定參數

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
