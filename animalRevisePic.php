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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $animal_pic_a = $_FILES["animal_pic_a"]["name"] ?? '';
        $animal_pic_b = $_FILES["animal_pic_b"]["name"] ?? '';
        $animal_pic_c = $_FILES["animal_pic_c"]["name"] ?? '';
        $animal_icon = $_FILES["animalIcon"]["name"] ?? '';
        $animal_sound = $_FILES["animalSound"]["name"] ?? '';
        $animal_small_pic = $_FILES["animal_small_pic"]["name"] ?? '';
        $animal_id = $_POST["animalId"];

    //取原始值
    $sqlSelect = "SELECT animal_pic_a, animal_pic_b, animal_pic_c, animal_icon, animal_sound, animal_small_pic FROM animal WHERE animal_id = ?";
    $stmtSelect = $pdo->prepare($sqlSelect);
    $stmtSelect->execute([$animal_id]);
    $row = $stmtSelect->fetch(PDO::FETCH_ASSOC);

    $original_pic_a = $row['animal_pic_a'];
    $original_pic_b = $row['animal_pic_b'];
    $original_pic_c = $row['animal_pic_c'];
    $original_icon = $row['animal_icon'];
    $original_sound = $row['animal_sound'];
    $original_small_pic = $row['animal_small_pic'];

 
    if (!empty($_FILES['animal_pic_a'])) {
        $animal_pic_a = $_FILES["animal_pic_a"]["name"] ?? '';
    } else {
        // 如果没有上傳新的 animal_pic_a，保持原始值不變
        $animal_pic_a = $original_pic_a ?? '';
    }

    if (!empty($_FILES['animal_pic_b'])) {
        $animal_pic_b = $_FILES["animal_pic_b"]["name"] ?? '';
    } else {
        $animal_pic_b = $original_pic_b ?? '';
    }

    if (!empty($_FILES['animal_pic_c'])) {
        $animal_pic_c = $_FILES["animal_pic_c"]["name"] ?? '';
    } else {
        $animal_pic_c = $original_pic_c ?? '';
    }

    if (!empty($_FILES['animalIcon'])) {
        $animal_icon = $_FILES["animalIcon"]["name"] ?? '';
    } else {
        $animal_icon = $original_icon ?? '';
    }

    if (!empty($_FILES['animalSound'])) {
        $animal_sound = $_FILES["animalSound"]["name"] ?? '';
    } else {
        $animal_sound = $original_sound ?? '';
    }

    if (!empty($_FILES['animal_small_pic'])) {
        $animal_small_pic = $_FILES["animal_small_pic"]["name"] ?? '';
    } else {
        $animal_small_pic = $original_small_pic ?? '';
    }

    // 更新圖片 URL
    if(isset($_FILES['animal_pic_a']) && !empty($_FILES['animal_pic_a'])) {
        // 處理 animal_pic_a 的上傳圖片
        move_uploaded_file($_FILES["animal_pic_a"]["tmp_name"], "../images/animal/animal_pic/" . $_FILES["animal_pic_a"]["name"]);
    }
    if(isset($_FILES['animal_pic_b']) && !empty($_FILES['animal_pic_b'])) {
        // 處理 animal_pic_a 的上傳圖片
        move_uploaded_file($_FILES["animal_pic_b"]["tmp_name"], "../images/animal/animal_pic/" . $_FILES["animal_pic_b"]["name"]);
    }
    if(isset($_FILES['animal_pic_c']) && !empty($_FILES['animal_pic_c'])) {
        // 處理 animal_pic_a 的上傳圖片
        move_uploaded_file($_FILES["animal_pic_c"]["tmp_name"], "../images/animal/animal_pic/" . $_FILES["animal_pic_c"]["name"]);
    }


    if(isset($_FILES['animalIcon']) && !empty($_FILES['animalIcon'])) {
        // 處理 animal_pic_a 的上傳圖片
        move_uploaded_file($_FILES["animalIcon"]["tmp_name"], "../images/animal/animal_icon/"  . $_FILES["animalIcon"]["name"]);
    }
    if(isset($_FILES['animalSound']) && !empty($_FILES['animalSound'])) {
        // 處理 animal_pic_a 的上傳圖片
        move_uploaded_file($_FILES["animalSound"]["tmp_name"], "../images/animal/audio/" . $_FILES["animalSound"]["name"]);
    }
    if(isset($_FILES['animal_small_pic']) && !empty($_FILES['animal_small_pic'])) {
        // 處理 animal_pic_a 的上傳圖片
        move_uploaded_file($_FILES["animal_small_pic"]["tmp_name"], "../images/animal/small_pic/" . $_FILES["animal_small_pic"]["name"]);
    }



    $pdo->beginTransaction();
    // 準備 SQL 更新語句，請根據您的數據庫實際情況進行調整
    $sqlAnimal = "UPDATE animal SET 
    animal_pic_a = ?,
    animal_pic_b = ?, 
    animal_pic_c = ?, 
    animal_icon = ?, 
    animal_sound = ?, 
    animal_small_pic = ?
    WHERE animal_id = ?";

    // 預處理 SQL 語句
    $stmtAnimal = $pdo->prepare($sqlAnimal);

    $stmtAnimal->bindParam(1, $animal_pic_a);
    $stmtAnimal->bindParam(2, $animal_pic_b);
    $stmtAnimal->bindParam(3, $animal_pic_c);
    $stmtAnimal->bindParam(4, $animal_icon);
    $stmtAnimal->bindParam(5, $animal_sound);
    $stmtAnimal->bindParam(6, $animal_small_pic);
    $stmtAnimal->bindParam(7, $animal_id);
    
    // 執行 SQL 語句
    $stmtAnimal->execute();

    // 檢查更新操作是否成功
    if ($stmtAnimal->rowCount() > 0) {
        echo json_encode(["successMsg" => "更新成功"]);
    } else {
        echo json_encode(["errMsg" => "更新失敗"]);
    }
}
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
