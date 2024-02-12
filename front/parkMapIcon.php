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

    // 準備 SQL 查詢語句
    $sql = "SELECT l.category_name, l.location_name, a.animal_species, a.animal_name, a.animal_icon FROM location l LEFT JOIN animal a ON l.animal_id = a.animal_id";

    // 預處理 SQL 查詢語句
    $stmt = $pdo->prepare($sql);

    // 執行 SQL 查詢
    $stmt->execute();

    // 獲取結果集
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 輸出 JSON 格式的結果
    echo json_encode($result);
} catch (PDOException $e) {
    // 捕獲異常並輸出錯誤信息
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
