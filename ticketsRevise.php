<?php
// ini_set("display_errors", "On"); // 開啟 PHP 偵錯模式

try {
    // 根據當前環境選擇數據庫連接
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        header("Access-Control-Allow-Origin: *"); // 允許跨域存取
        header("Access-Control-Allow-Headers: Content-Type");//設定回傳資料的解析方式
        header("Content-Type: application/json; charset=UTF-8");//設定回傳的資料類型和編碼

        // 如果是在開發環境
        require_once("../pxzoo/connectPxzoo.php");
    } else {
        // 如果是在生產環境
        require_once("connect_chd104g4.php");
    }

    // 從 HTTP 請求中獲取 JSON 格式的輸入數據
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 將 JSON 字符串轉換為 PHP 關聯數組

    // 從解析過後的資料中提取特定屬性值
    $ticketsID = $input['ticketsID'];
    $ticketsName = $input['ticketsName'];
    $ticketsPrice = $input['ticketsPrice'];
    $ticketsRule = $input['ticketsRule'];

    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sql = "UPDATE tickets SET tickets_name = ?, tickets_price = ?, tickets_rule = ? ,tickets_changetime = NOW() WHERE tickets_id = ?";

    // 預處理 SQL 語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數到預處理語句
    $stmt->bindParam(1, $ticketsName);
    $stmt->bindParam(2, $ticketsPrice);
    $stmt->bindParam(3, $ticketsRule);
    $stmt->bindParam(4, $ticketsID); // 為 WHERE 條件也綁定參數

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
