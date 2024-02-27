<?php
// 判斷開發環境還是生產環境
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 開發環境
    require_once("connectPxzoo.php"); // 本地 MySQL 資料庫帳號密碼檔案
    header("Access-Control-Allow-Origin: *"); // 允許跨域存取
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Content-Type: application/json; charset=UTF-8");
} else {
    // 生產環境
    require_once("connect_chd104g4.php"); // 網站上線後緯育資料庫帳號密碼檔案
}

// 解析 JSON 格式的請求數據
$data = json_decode(file_get_contents("php://input"));

try {
    // 確保 animal_id 變量正確從請求中獲取到
    $animal_id = isset($data->animal_id) ? $data->animal_id : null;

    // 確認 animal_id 是否存在，以及是否為有效值
    if ($animal_id !== null && is_numeric($animal_id)) {
   

        // 更新票數
        $sql = "UPDATE animal SET animal_vote = animal_vote + 1 WHERE animal_id = :animal_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);

        // 執行 SQL 語句
        if ($stmt->execute()) {
            // 查詢更新後的總票數
            $sql = "SELECT animal_vote FROM animal WHERE animal_id = :animal_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($result);
        } else {
            echo "更新投票數據時出錯";
        }

        // 關閉資料庫連接
        $conn = null;
    } else {
        echo "無效的 animal_id";
    }
} catch (Exception $e) {
    echo "發生錯誤：" . $e->getMessage();
}
?>

