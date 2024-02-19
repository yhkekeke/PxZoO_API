<?php
// 包含用於數據庫連接的文件
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

    // 檢查是否收到動物 ID
    if(isset($_GET['id'])) {
        $animalId = $_GET['id'];

        // 準備 SQL 查詢
        $sql = "SELECT * FROM animal 
        WHERE animal_id = :animal_id";

        // 使用 PDO 預備語句來防止 SQL 注入攻擊
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':animal_id', $animalId, PDO::PARAM_INT);

        // 執行查詢
        if ($stmt->execute()) {
            // 檢查是否有找到動物
            if ($stmt->rowCount() > 0) {
                // 獲取動物的詳細信息
                $animalDetail = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // 返回 JSON 格式的動物詳細信息
                echo json_encode($animalDetail);
            } else {
                // 如果未找到動物，返回空數據或錯誤消息
                echo json_encode(array('message' => 'Animal not found'));
            }
        } else {
            // 如果查詢失敗，返回錯誤消息
            echo json_encode(array('message' => 'Query failed'));
        }
    }
    if((isset($_GET['type']) && $_GET['type'] === 'speciesname')){
        $sql = "SELECT a.animal_species, l.category_name
        FROM animal a JOIN location l ON a.location_name = l.location_name";  // 修改為您的 SQL 查詢

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
    if((isset($_GET['type']) && $_GET['type'] === 'animalList')){
        $ani_sql = "SELECT a.animal_id, a.animal_species, a.animal_small_pic, l.category_name
        FROM animal a JOIN location l ON a.location_name = l.location_name"; 

        $animalList = $pdo->prepare($ani_sql);
        $animalList->execute();
        if ($animalList->rowCount() > 0) {
            $animalListData = $animalList->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($animalListData);
        } else {
            echo json_encode(["errMsg" => "沒有找到動物資料"]);
        }

    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
