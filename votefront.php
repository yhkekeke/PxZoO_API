<?php
try {
    // 下面這個 if 則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        // 這是本地端的 MySQL 資料庫帳號密碼檔案
        require_once("connectPxzoo.php");
        // 允許跨域存取
        header("Access-Control-Allow-Origin: *"); // 允許所有來源
        header("Content-Type: application/json; charset=UTF-8");
    } else {
        // 生產環境  
        // 這裡則是我們網站上線後要偵測緯育資料庫的帳號密碼檔案
        require_once("connect_chd104g4.php");
    }
    
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
        
        // 使用參數化查詢動態獲取不同館別的資料
        $sql = "SELECT a.animal_id, a.animal_name, a.animal_vote, a.animal_small_pic, l.category_name FROM animal a JOIN location l ON a.location_name = l.location_name WHERE l.category_name = ?";
        
        // 預備語句
        $stmt = $pdo->prepare($sql);

        // 綁定參數並執行查詢
        $stmt->execute([$category]);

        // 檢索所有結果
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 將數據轉換為 JSON 格式
        $json_data = json_encode($data);

        // 設置 HTTP 響應頭部，通知瀏覽器返回的是 JSON 格式的數據
        header('Content-Type: application/json');

        // 返回 JSON 數據
        echo $json_data;
    }
} catch (PDOException $e) {
    // 錯誤處理
    echo "錯誤: " . $e->getMessage();
}
?>
