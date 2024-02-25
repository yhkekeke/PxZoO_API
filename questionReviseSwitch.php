<?php
try {
// 根據當前環境選擇數據庫連接
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    header("Access-Control-Allow-Origin: *"); // 允許跨域存取
    header("Access-Control-Allow-Headers: Content-Type");//設定回傳資料的解析方式
    header("Content-Type: application/json; charset=UTF-8");//設定回傳的資料類型和編碼

    // 如果是在開發環境
    require_once("connectPxzoo.php");
} else {
    // 如果是在生產環境
    require_once("connect_chd104g4.php");
}
// 檢查是否有提交表單
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 從表單中獲取數據
     // 從 HTTP 請求中獲取 JSON 格式的輸入數據
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 將 JSON 字符串轉換為 PHP 關聯數組

    $question_id = $input["question_id"];
    $question_status = $input["question_status"];

    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sql = "UPDATE questions SET question_status = ?
    WHERE question_id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $question_status);
    $stmt->bindParam(2, $question_id);

    $stmt->execute();
    // 檢查更新操作是否成功
    if ($stmt->rowCount() > 0) {
        echo json_encode(["successMsg" => "更新成功"]);
    } else {
        echo json_encode(["errMsg" => "更新失敗"]);
    }


}
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
