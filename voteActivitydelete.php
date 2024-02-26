<?php
//下面這個if則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 開發環境
    //這是本地端的mySQL資料庫帳號密碼檔案
    require_once("connectPxzoo.php");
    //允許跨域存取
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Allow-Headers: Content-Type"); // 允許 'Content-Type' 標頭
    header("Content-Type: application/json; charset=UTF-8");
} else {
    // 生產環境  
    //這裡則是我們網站上線後要偵測緯育資料庫的帳號密碼檔案
    require_once("connect_chd104g4.php");
}

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
