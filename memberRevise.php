<?php
// ini_set("display_errors", "On"); // 開啟 PHP 偵錯模式

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

    // 從 HTTP 請求中獲取 JSON 格式的輸入數據
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 將 JSON 字符串轉換為 PHP 關聯數組

    // 從解析過後的資料中提取特定屬性值
    $mem_id = $input['mem_id'];
    $mem_name = $input['mem_name'];
    $mem_title = $input['mem_title'];
    $mem_birthday = $input['mem_birthday'];
    $mem_email = $input['mem_email'];
    $mem_phone = $input['mem_phone'];
    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sql = "UPDATE member SET  mem_name = ?, mem_title = ? , mem_birthday = ? , mem_email = ? , mem_phone = ? WHERE mem_id = ?";

    // 預處理 SQL 語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數到預處理語句
    $stmt->bindParam(1, $mem_name);
    $stmt->bindParam(2, $mem_title);
    $stmt->bindParam(3, $mem_birthday);
    $stmt->bindParam(4, $mem_email);
    $stmt->bindParam(5, $mem_phone);
    $stmt->bindParam(6, $mem_id);

    // 執行 SQL 語句
    $stmt->execute();

    // 檢查更新操作是否成功
    if ($stmt->rowCount() > 0) {
        echo json_encode(["successMsg" => "更新成功"]);
    } else {
        echo json_encode(["errMsg" => "更新失敗"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
