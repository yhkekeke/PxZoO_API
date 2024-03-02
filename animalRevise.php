<?php
// ini_set("display_errors", "On"); // 開啟 PHP 偵錯模式

try {
    // 根據當前環境選擇數據庫連接
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        header("Access-Control-Allow-Origin: *"); // 允許跨域存取
        header("Access-Control-Allow-Headers: Content-Type"); // 設定回傳資料的解析方式
        header("Content-Type: application/json; charset=UTF-8"); // 設定回傳的資料類型和編碼

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
    $oldLocation_name = $_POST['oldLocationName'];
    $location_name = $_POST['locationName'];
    $animal_enterdate = $_POST['animalEnterdate'];
    $animal_lifespan = $_POST['animalLifespan'];
    $animal_area = $_POST['animalArea'];
    $animal_food = $_POST['animalFood'];
    $animal_features = $_POST['animalFeatures'];
    $animal_description = $_POST['animalDescription'];

    $pdo->beginTransaction();

    // 準備 SQL 更新語句
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

    // 更新舊位置的 animal_id 為 NULL
    $sqlLocationOld = "UPDATE location SET animal_id = NULL WHERE location_name = ?";
    $stmtLocationOld = $pdo->prepare($sqlLocationOld);
    $stmtLocationOld->bindParam(1, $oldLocation_name);
    $stmtLocationOld->execute();

    // 更新新位置的 animal_id
    $sqlLocationNew = "UPDATE location SET animal_id = ? WHERE location_name = ?";
    $stmtLocationNew = $pdo->prepare($sqlLocationNew);
    $stmtLocationNew->bindParam(1, $animal_id);
    $stmtLocationNew->bindParam(2, $location_name);
    $stmtLocationNew->execute();

    // 提交事務
    $pdo->commit();

    echo json_encode(["successMsg" => "動物資訊及位置更新成功"]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
