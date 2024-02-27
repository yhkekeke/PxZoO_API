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



try {


        // 執行 SQL 語句
        $sql = "SELECT animal_id ,animal_name, animal_small_pic, animal_vote
                FROM animal
                ORDER BY animal_vote DESC
                LIMIT 3";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }catch (Exception $e) {
    echo "發生錯誤：" . $e->getMessage();
}
?>
