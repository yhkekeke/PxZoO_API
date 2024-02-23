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
    $animal_id = $_POST['animal_id'];
    $animal_species = $_POST['animal_species'];
    $animal_name = $_POST['animal_name'];
    // $category_name = $_POST['category_name'];
    $location_name = $_POST['location_name'];
    $animal_enterdate = $_POST['animal_enterdate'];
    $animal_lifespan = $_POST['animal_lifespan'];
    $animal_area = $_POST['animal_area'];
    $animal_food = $_POST['animal_food'];
    $animal_features = $_POST['animal_features'];
    $animal_description = $_POST['animal_description'];

    $animal_pic_a = $_POST['animal_pic_a'] ??'';
    $animal_pic_b = $_POST['animal_pic_b'] ??'';
    $animal_pic_c = $_POST['animal_pic_c'] ??'';
    $animal_icon = $_POST['animal_icon'] ??'';
    $animal_sound = $_POST['animal_sound'] ??'';
    $animal_small_pic = $_POST['animal_small_pic'] ??'';

    
     // 新的圖片 URL，如果有的話
    $new_animal_pic_a= $input['new_animal_pic_a']["name"] ?? '';
    $new_animal_pic_b= $input['new_animal_pic_b']["name"] ?? '';
    $new_animal_pic_c= $input['new_animal_pic_c']["name"] ?? '';
    $new_animal_icon= $input['new_animal_icon']["name"] ?? '';
    $new_animal_sound= $input['new_animal_sound']["name"] ?? '';
    $new_animal_small_pic= $input['new_animal_small_pic']["name"] ?? '';

    // 如果沒有上傳新圖片，則使用原始圖片名稱
    if (empty($new_animal_pic_a)) {
        $new_animal_pic_a = $animal_pic_a;
    }
    if (empty($new_animal_pic_b)) {
        $new_animal_pic_b = $animal_pic_b;
    }
    if (empty($new_animal_pic_c)) {
        $new_animal_pic_c = $animal_pic_c;
    }
    if (empty($new_animal_icon)) {
        $new_animal_icon =  $animal_icon;
    }
    if (empty($new_animal_sound)) {
        $new_animal_sound = $new_animal_sound;
    }
    if (empty($new_animal_small_pic)) {
        $new_animal_small_pic = $animal_small_pic;
    }

    // 目標路徑
    $target_directory1 = '/images/animal/animal_pic/';
    $target_directory2 = '/images/animal/animal_icon/';
    $target_directory3 = '/images/animal/small_pic/';
    $target_directory4 = '/images/animal/audio/';

    // 更新圖片 URL
    if (!empty($new_animal_pic_a)) {
        $animal_pic_a = $target_directory1 .  basename($_FILES["new_animal_pic_a"]["name"]);
        move_uploaded_file($_FILES["animal_pic_a"]["tmp_name"], $animal_pic_a);
        if (! move_uploaded_file($_FILES["new_animal_pic_a"]["tmp_name"], $animal_pic_a)) {
            echo json_encode(["errMsg" => "無法將 new_animal_pic_a 檔案複製到目標資料夾"]);
            exit;
        }
    }
    if (!empty($new_animal_pic_b)) {
        $animal_pic_b = $target_directory1 .  basename($_FILES["new_animal_pic_b"]["name"]);
        move_uploaded_file($_FILES["animal_pic_b"]["tmp_name"], $animal_pic_b);
        if (! move_uploaded_file($_FILES["new_animal_pic_b"]["tmp_name"], $animal_pic_b)) {
            echo json_encode(["errMsg" => "無法將 new_animal_pic_b 檔案複製到目標資料夾"]);
            exit;
        }
    }
    if (!empty($new_animal_pic_c)) {
        $animal_pic_c = $target_directory1 .  basename($_FILES["new_animal_pic_c"]["name"]);
        move_uploaded_file($_FILES["animal_pic_c"]["tmp_name"], $animal_pic_c);
        if (! move_uploaded_file($_FILES["new_animal_pic_c"]["tmp_name"], $animal_pic_c)) {
            echo json_encode(["errMsg" => "無法將 new_animal_pic_c 檔案複製到目標資料夾"]);
            exit;
        }
    }
    if (!empty($new_animal_icon)) {
        $animal_icon = $target_directory2 .  basename($_FILES["new_animal_icon"]["name"]);
        move_uploaded_file($_FILES["animal_icon"]["tmp_name"], $animal_icon);
        if (! move_uploaded_file($_FILES["new_animal_icon"]["tmp_name"], $animal_icon)) {
            echo json_encode(["errMsg" => "無法將 new_animal_icon 檔案複製到目標資料夾"]);
            exit;
        }
    }
    if (!empty($new_animal_sound)) {
        $animal_sound = $target_directory4 .  basename($_FILES["new_animal_sound"]["name"]);
        move_uploaded_file($_FILES["$animal_sound"]["tmp_name"], $animal_sound);
        if (! move_uploaded_file($_FILES["new_animal_sound"]["tmp_name"], $animal_sound)) {
            echo json_encode(["errMsg" => "無法將 new_animal_sound 檔案複製到目標資料夾"]);
            exit;
        }
    }
    if (!empty($new_animal_small_pic)) {
        $animal_small_pic = $target_directory3 .  basename($_FILES["new_animal_small_pic"]["name"]);
        move_uploaded_file($_FILES["$animal_small_pic"]["tmp_name"], $animal_small_pic);
        if (! move_uploaded_file($_FILES["new_animal_small_pic"]["tmp_name"], $animal_small_pic)) {
            echo json_encode(["errMsg" => "無法將 new_animal_small_pic 檔案複製到目標資料夾"]);
            exit;
        }
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
    location_name = ?,
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
    //加上館別名稱
    $stmtAnimal->bindParam(3, $location_name);
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
