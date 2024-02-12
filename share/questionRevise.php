<?php
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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

    $question_id = $input['question_id'];
    $question_text = $input['question_text'];
    $question_option_a = $input['question_option_a'];
    $question_img_a = $input['question_img_a'];
    $question_option_b = $input['question_option_b'];
    $question_img_b = $input['question_img_b'];
    $question_option_c = $input['question_option_c'];
    $question_img_c = $input['question_img_c'];
    $question_option_d = $input['question_option_d'];
    $question_img_d = $input['question_img_d'];
    $question_correctanswer = $input['question_correctanswer'];
    $question_answer_illustrate = $input['question_answer_illustrate'];


    // 新的圖片 URL，如果有的話
    $new_question_img_a = $input['new_question_img_a'];
    $new_question_img_b = $input['new_question_img_b'];
    $new_question_img_c = $input['new_question_img_c'];
    $new_question_img_d = $input['new_question_img_d'];

    // 更新圖片 URL
    if (!empty($new_question_img_a)) {
        $question_img_a = $new_question_img_a;
    }
    if (!empty($new_question_img_b)) {
        $question_img_b = $new_question_img_b;
    }
    if (!empty($new_question_img_c)) {
        $question_img_c = $new_question_img_c;
    }
    if (!empty($new_question_img_d)) {
        $question_img_d = $new_question_img_d;
    }

    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sql = "UPDATE questions SET question_text = ?, 
      question_option_a = ?, 
      question_img_a = ? ,
      question_option_b = ? ,
      question_img_b = ? ,
      question_option_c = ? ,
      question_img_c = ? ,
      question_option_d = ? ,
      question_img_d = ? ,
      question_correctanswer = ? ,  question_answer_illustrate = ? 
      WHERE question_id = ?";

    // 預處理 SQL 語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數到預處理語句
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
    $stmt->bindParam(12, $question_id); // 為 WHERE 條件也綁定參數

    // 執行 SQL 語句
    $stmt->execute();
    // 檢查更新操作是否成功
    if ($stmt->rowCount() > 0) {
        echo json_encode(["successMsg" => "更新成功"]);
    } else {
        echo json_encode(["errMsg" => "更新失敗"]);
    }
}
