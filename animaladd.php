<?php
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
// 檢查是否有提交表單
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 從表單中獲取數據
    $animal_species = $_POST["animal_species"] ?? '';
    $animal_name = $_POST["animal_name"] ?? '';

    $category_name = $_POST["category_name"] ?? '';//location table
    $location_name = $_POST["location_name"] ?? '';//fk
    $animal_enterdate = $_POST["animal_enterdate"] ?? '';

    $animal_lifespan = $_POST["animal_lifespan"] ?? '';
    $animal_area = $_POST["animal_area"] ?? '';
    $animal_food = $_POST["animal_food"] ?? '';
    $animal_features = $_POST["animal_features"] ?? '';
    $animal_description = $_POST["animal_description"] ?? '';

    $animal_pic_a = $_FILES["animal_pic_a"]["name"] ?? '';
    $animal_pic_b = $_FILES["animal_pic_b"]["name"] ?? '';
    $animal_pic_c = $_FILES["animal_pic_c"]["name"] ?? '';
    $animal_icon = $_FILES["animal_icon"]["name"] ?? '';
    $animal_sound = $_FILES["animal_sound"]["name"] ?? '';
    $animal_small_pic = $_FILES["animal_small_pic"]["name"] ?? '';
      
      echo json_encode($filesInfo);
    //--------------取得上傳檔案
    // if ($_FILES["animal_icon"]["error"] === 0) {
    //     $dir = "../images/animal/animal_icon/";
    //     if (!file_exists($dir)) {
    //         mkdir($dir);
    //     }
    //     $from = $_FILES["animal_icon"]["tmp_name"];
    //     $to = $dir . $animal_icon;
    //     if (move_uploaded_file($from, $to)) {
    //         // 檔案成功移動
    //     } else {
    //         $result = ["error" => true, "msg" => "檔案移動失敗"];
    //     }
    // } else {
    //     $result = ["error" => true, "msg" => "檔案上傳失敗"];
    // }

    // // 將上傳的圖片移動到指定的資料夾中
    move_uploaded_file($_FILES["animal_pic_a"]["tmp_name"], "../images/animal/animal_pic/" . $_FILES["animal_pic_a"]["name"]);
    if(isset($_FILES['animal_pic_b']) && !empty($_FILES['animal_pic_b'])) {
        // 處理 animal_pic_b 的上傳圖片
        move_uploaded_file($_FILES["animal_pic_b"]["tmp_name"], "../images/animal/animal_pic/" . $_FILES["animal_pic_b"]["name"]);
    }
    if(isset($_FILES['animal_pic_c']) && !empty($_FILES['animal_pic_c'])) {
        // 處理 animal_pic_c 的上傳圖片
        move_uploaded_file($_FILES["animal_pic_c"]["tmp_name"], "../images/animal/animal_pic/" . $_FILES["animal_pic_c"]["name"]);
    }

    move_uploaded_file($_FILES["animal_icon"]["tmp_name"], "../images/animal/animal_icon/" . $_FILES["animal_icon"]["name"]);
    move_uploaded_file($_FILES["animal_small_pic"]["tmp_name"], "../images/animal/small_pic/" . $_FILES["animal_small_pic"]["name"]);
    move_uploaded_file($_FILES["animal_sound"]["tmp_name"], "../images/animal/audio/" . $_FILES["animal_sound"]["name"]);


    $pdo->beginTransaction();
    // 插入 animal 資料
    $sqlAnimal = "INSERT INTO animal (animal_species, animal_name,
    location_name, animal_enterdate, animal_lifespan, animal_area, animal_food, animal_features, animal_description, animal_pic_a, animal_pic_b, animal_pic_c, animal_icon, animal_sound, animal_small_pic, animal_status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

    $stmtAnimal = $pdo->prepare($sqlAnimal);
    $stmtAnimal->bindParam(1, $animal_species);
    $stmtAnimal->bindParam(2, $animal_name);
    $stmtAnimal->bindParam(3, $location_name);
    $stmtAnimal->bindParam(4, $animal_enterdate);
    $stmtAnimal->bindParam(5, $animal_lifespan);
    $stmtAnimal->bindParam(6, $animal_area);
    $stmtAnimal->bindParam(7, $animal_food);
    $stmtAnimal->bindParam(8, $animal_features);
    $stmtAnimal->bindParam(9, $animal_description);
    $stmtAnimal->bindParam(10, $animal_pic_a);
    $stmtAnimal->bindParam(11, $animal_pic_b);
    $stmtAnimal->bindParam(12, $animal_pic_c);
    $stmtAnimal->bindParam(13, $animal_icon);
    $stmtAnimal->bindParam(14, $animal_sound);
    $stmtAnimal->bindParam(15, $animal_small_pic);

    $stmtAnimal->execute();
    // if ($stmtAnimal->execute()) {
    //     echo "新增 animal 記錄成功";
    // } else {
    //     echo "錯誤: " . $stmtAnimal->errorInfo()[2];
    // }

    // 獲取新 animal_id
    $lastInsertedId = $pdo->lastInsertId();

    // 更新 location 表中的 animal_id
    $sqlLocation = "UPDATE location SET animal_id = ? WHERE location_name = ?";
    $stmtLocation = $pdo->prepare($sqlLocation);
    $stmtLocation->bindParam(1, $lastInsertedId);
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
}
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
