

    <?php

    try {
        //下面這個if則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
            // 開發環境
            //這是本地端的mySQL資料庫帳號密碼檔案
            require_once("connectPxzoo.php");

                //允許跨域存取
            header("Access-Control-Allow-Origin: *"); // 允許所有來源
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS,DELETE");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header("Content-Type: application/json; charset=UTF-8");
        } else {
            // 生產環境  
            //這裡則是我們網站上線後要偵測緯育資料庫的帳號密碼檔案
            require_once("connect_chd104g4.php");
        }

        $searchTerm = $_GET['searchTerm'];
        // SQL 查詢，模糊查詢
        $sql = "SELECT a.*, l.category_name
        FROM animal a JOIN location l ON a.location_name = l.location_name 
        WHERE a.animal_species LIKE :searchTerm OR a.animal_name LIKE :searchTerm OR l.category_name LIKE :searchTerm";
        // 修改為您的 SQL 查詢
        $animal = $pdo->prepare($sql);
        // 準備 SQL 查詢
        $searchTerm = "%$searchTerm%"; // 在搜尋條件的兩側添加 %
        $animal->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);

        // 執行 SQL 查詢
        $animal->execute();

        // 檢查是否有資料
        if ($animal->rowCount() > 0) {
            $animalData = $animal->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($animalData);
        } else {
            echo json_encode(["errMsg" => "沒有找到動物資料"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
    }
    ?>