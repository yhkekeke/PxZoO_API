<?php
// 引入連接資料庫的文件，假設是 db_connect.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once("../pxzoo/connectPxzoo.php");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 確保收到了刪除請求
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    // 解析 JSON 請求主體，其中包含要刪除的資料的 ID
    $data = json_decode(file_get_contents("php://input"));

    // 確保收到了有效的 ID
    if (isset($data->id)) {
        $questionId = $data->id;

        // 使用 PDO 或其他適當的資料庫操作庫執行刪除操作
        $stmt = $pdo->prepare("DELETE FROM vote_activity WHERE vote_activity_id = ?");
        $stmt->execute([$questionId]);

        // 檢查是否刪除成功
        if ($stmt->rowCount() > 0) {
            // 成功刪除
            http_response_code(200);
            echo json_encode(array("message" => "資料已成功刪除"));
        } else {
            // 資料不存在或刪除失敗
            http_response_code(404);
            echo json_encode(array("message" => "無法找到要刪除的資料"));
        }
    } else {
        // 如果沒有提供有效的 ID，返回錯誤狀態碼
        http_response_code(400);
        echo json_encode(array("message" => "未提供有效的資料 ID"));
    }
} else {
    // 如果不是 DELETE 請求，返回錯誤狀態碼
    http_response_code(405);
    echo json_encode(array("message" => "僅支援 DELETE 請求"));
}
