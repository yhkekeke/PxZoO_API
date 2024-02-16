<?php
// ini_set("display_errors", "On"); // 開啟 PHP 偵錯模式

try {
    // 根據當前環境選擇數據庫連接
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        header("Access-Control-Allow-Origin: *"); // 允許跨域存取
        header("Access-Control-Allow-Headers: Content-Type");//設定回傳資料的解析方式
        header("Content-Type: application/json; charset=UTF-8");//設定回傳的資料類型和編碼

        // 如果是在開發環境
        require_once("../pxzoo/connectPxzoo.php");
    } else {
        // 如果是在生產環境
        require_once("connect_chd104g4.php");
    }

    // 從 HTTP 請求中獲取 JSON 格式的輸入數據
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 將 JSON 字符串轉換為 PHP 關聯數組
 
    // 從解析過後的資料中提取特定屬性值
    $animal_id = $input['animal_id'];
    $animal_species = $input['animal_species'];
    $animal_name = $input['animal_name'];
    // $category_name = $input['category_name'];
    // $location_name = $input['location_name'];
    $animal_enterdate = $input['animal_enterdate'];
    $animal_lifespan = $input['animal_lifespan'];
    $animal_area = $input['animal_area'];
    $animal_food = $input['animal_food'];
    $animal_features = $input['animal_features'];
    $animal_description = $input['animal_description'];

    $animal_pic_a = $input['animal_pic_a'];
    $animal_pic_b = $input['animal_pic_b'];
    $animal_pic_c = $input['animal_pic_c'];
    $animal_icon = $input['animal_icon'];
    $animal_sound = $input['animal_sound'];
    $animal_small_pic = $input['animal_small_pic'];

    
     // 新的圖片 URL，如果有的話
    $new_animal_pic_a= $input['new_animal_pic_a'];
    $new_animal_pic_b= $input['new_animal_pic_b'];
    $new_animal_pic_c= $input['new_animal_pic_c'];
    $new_animal_icon= $input['new_animal_icon'];
    $new_animal_sound= $input['new_animal_sound'];
    $new_animal_small_pic= $input['new_animal_small_pic'];

    // 更新圖片 URL
    if (!empty($new_animal_pic_a)) {
        $animal_pic_a = $new_animal_pic_a;
    }
    if (!empty($new_animal_pic_b)) {
        $animal_pic_b = $new_animal_pic_b;
    }
    if (!empty($new_animal_pic_c)) {
        $animal_pic_c = $new_animal_pic_c;
    }
    if (!empty($new_animal_icon)) {
        $animal_icon = $new_animal_icon;
    }
    if (!empty($new_animal_sound)) {
        $animal_sound = $new_animal_sound;
    }
    if (!empty($new_animal_small_pic)) {
        $animal_small_pic = $new_animal_small_pic;
    }

    
    

    // $sqlLocation = 

    // if ($stmtLocation->execute()) {
    // echo "插入 location 資料成功";
    // } else {
    // echo "錯誤: " . $stmtLocation->errorInfo()[2];
    // }


    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sqlAnimal = "UPDATE animal SET animal_species = ?, 
    animal_name = ?, 
    -- location_name = ?,
    animal_enterdate = ?,
    animal_lifespan = ?, 
    animal_area = ?, 
    animal_food = ?, 
    animal_features = ?, 
    animal_description = ?, 
    animal_pic_a = ?,
    animal_pic_b = ?, 
    animal_pic_c = ?, 
    animal_icon = ?, 
    animal_sound = ?, 
    animal_small_pic = ?
    WHERE animal_id = ?";

    // 預處理 SQL 語句
    $stmtAnimal = $pdo->prepare($sqlAnimal);

    // 綁定參數到預處理語句
    $stmtAnimal->bindParam(1, $animal_species);
    $stmtAnimal->bindParam(2, $animal_name);
    // $stmtAnimal->bindParam(3, $location_name);
    $stmtAnimal->bindParam(3, $animal_enterdate);
    $stmtAnimal->bindParam(4, $animal_lifespan);
    $stmtAnimal->bindParam(5, $animal_area);
    $stmtAnimal->bindParam(6, $animal_food);
    $stmtAnimal->bindParam(7, $animal_features);
    $stmtAnimal->bindParam(8, $animal_description);
    $stmtAnimal->bindParam(9, $animal_pic_a);
    $stmtAnimal->bindParam(10, $animal_pic_b);
    $stmtAnimal->bindParam(11, $animal_pic_c);
    $stmtAnimal->bindParam(12, $animal_icon);
    $stmtAnimal->bindParam(13, $animal_sound);
    $stmtAnimal->bindParam(14, $animal_small_pic);
    $stmtAnimal->bindParam(15, $animal_id);
// 為 WHERE 條件也綁定參數

    // 執行 SQL 語句
    $stmtAnimal->execute();

    // 檢查更新操作是否成功
    if ($stmtAnimal->rowCount() > 0) {
        echo json_encode(["successMsg" => "更新成功"]);
    } else {
        echo json_encode(["errMsg" => "更新失敗"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
