<?php
// 下面這個 if 判斷是我們設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 開發環境
    // 這是本地端的 mySQL 資料庫帳號密碼檔案
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
// SQL 查詢語句
$sql = "SELECT animal.animal_id, animal.animal_name, animal.animal_small_pic 
        FROM animal 
        LEFT JOIN location ON animal.location_name = location.location_name";
// 執行查詢
$result = $pdo->query($sql);

// 檢查查詢結果是否有數據
if ($result->rowCount() > 0) {
    // 输出每一行數據
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "animal_id: " . $row["animal_id"] . " - animal_name: " . $row["animal_name"] . " - animal_small_pic: " . $row["animal_small_pic"] . " - category_name: " . $row["category_name"] . "<br>";
    }
} else {
    echo "0 個結果";
}
}
// 關閉資料庫連線
$pdo = null;
