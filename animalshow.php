<?php

try {
    //下面這個if則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        //這是本地端的mySQL資料庫帳號密碼檔案
        require_once("connectPxzoo.php");
            //允許跨域存取
        header("Access-Control-Allow-Origin: *"); // 允許所有來源
        header("Content-Type: application/json; charset=UTF-8");
    } else {
        // 生產環境  
        //這裡則是我們網站上線後要偵測緯育資料庫的帳號密碼檔案
        require_once("connect_chd104g4.php");
    }

    if (isset($_GET['type']) && $_GET['type'] === 'animals') {
            // SQL 查詢
        $sql = "SELECT a.*, l.category_name
        FROM animal a JOIN location l ON a.location_name = l.location_name";  // 修改為您的 SQL 查詢

        // 準備 SQL 查詢
        $animal = $pdo->prepare($sql);

        // 執行 SQL 查詢
        $animal->execute();

        // 檢查是否有資料
        if ($animal->rowCount() > 0) {
            $animalData = $animal->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($animalData);
        } else {
            echo json_encode(["errMsg" => "沒有找到動物資料"]);
        }
    }elseif((isset($_GET['type']) && $_GET['type'] === 'categories')){
        $sql = "SELECT DISTINCT category_name FROM location";  // 修改為您的 SQL 查詢

        // 準備 SQL 查詢
        $animalcategory = $pdo->prepare($sql);

        // 執行 SQL 查詢
        $animalcategory->execute();

        // 檢查是否有資料
        if ($animalcategory->rowCount() > 0) {
            $animalcategoryData = $animalcategory->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($animalcategoryData);
        } else {
            echo json_encode(["errMsg" => "沒有找到動物位置資料"]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>