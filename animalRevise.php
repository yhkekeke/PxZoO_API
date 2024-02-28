<?php
// ini_set("display_errors", "On"); // 開啟 PHP 偵錯模式

try {
    // 根據當前環境選擇數據庫連接
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        header("Access-Control-Allow-Origin: *"); // 允許跨域存取
        header("Access-Control-Allow-Headers: Content-Type");//設定回傳資料的解析方式
        header("Content-Type: application/json; charset=UTF-8");//設定回傳的資料類型和編碼

        // 如果是在開發環境
        require_once("connectPxzoo.php");
    } else {
        // 如果是在生產環境
        require_once("connect_chd104g4.php");
    }

    // 從 HTTP 請求中獲取 JSON 格式的輸入數據
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 將 JSON 字符串轉換為 PHP 關聯數組
 
    // 從解析過後的資料中提取特定屬性值
    $animal_id = $_POST["animalId"];
    $animal_species = $_POST['animalSpecies'];
    $animal_name = $_POST['animalName'];
    $category_name = $_POST['categoryName'];
    $location_name = $_POST['locationName'];
    $animal_enterdate = $_POST['animalEnterdate'];
    $animal_lifespan = $_POST['animalLifespan'];
    $animal_area = $_POST['animalArea'];
    $animal_food = $_POST['animalFood'];
    $animal_features = $_POST['animalFeatures'];
    $animal_description = $_POST['animalDescription'];

    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sqlAnimal = "UPDATE animal SET 
    animal_species = ?, 
    animal_name = ?, 
    location_name = ?,
    animal_enterdate = ?,
    animal_lifespan = ?, 
    animal_area = ?, 
    animal_food = ?, 
    animal_features = ?, 
    animal_description = ? 
    WHERE animal_id = ?";


    $pdo->beginTransaction();
    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sqlAnimal = "UPDATE animal SET 
    animal_species = ?, 
    animal_name = ?, 
    location_name = ?,
    animal_enterdate = ?,
    animal_lifespan = ?, 
    animal_area = ?, 
    animal_food = ?, 
    animal_features = ?, 
    animal_description = ? 
    WHERE animal_id = ?";


    // 預處理 SQL 語句
    $stmtAnimal = $pdo->prepare($sqlAnimal);

    // 綁定參數到預處理語句
    $stmtAnimal->bindParam(1, $animal_species);
    $stmtAnimal->bindParam(2, $animal_name);
    //加上館別名稱
    $stmtAnimal->bindParam(3, $location_name);
    $stmtAnimal->bindParam(4, $animal_enterdate);
    $stmtAnimal->bindParam(5, $animal_lifespan);
    $stmtAnimal->bindParam(6, $animal_area);
    $stmtAnimal->bindParam(7, $animal_food);
    $stmtAnimal->bindParam(8, $animal_features);
    $stmtAnimal->bindParam(9, $animal_description);
    $stmtAnimal->bindParam(10, $animal_id);

    // 執行 SQL 語句
    $stmtAnimal->execute();

    // 更新 location 表中的 animal_id
    $sqlLocation = "UPDATE location SET animal_id = ? WHERE location_name = ?";
    $stmtLocation = $pdo->prepare($sqlLocation);
    $stmtLocation->bindParam(1, $animal_id);
    $stmtLocation->bindParam(2, $location_name);

    // 執行 location 更新指令
    if ($stmtLocation->execute()) {
        echo "更新 location 成功";
    } else {
        echo json_encode(["errMsg" => "錯誤: " . $stmtLocation->errorInfo()[2]]);
        $pdo->rollBack(); // 回滾事物
        exit; // 停止執行代碼
    }

    // 提交事务
    $pdo->commit();

} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
