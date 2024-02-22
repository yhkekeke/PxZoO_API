

    <?php
    //允許跨域存取
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header("Content-Type: application/json; charset=UTF-8");

    require_once("../../g4/api/connectPxzoo.php");

    // 獲取用戶IP
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // 查詢投票活動資料
    $sql = "SELECT vote_activity_id, animal_id FROM vote_activity WHERE user_ip = '$user_ip'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 找到投票活動
        $row = $result->fetch_assoc();
        $vote_activity_id = $row["vote_activity_id"];
        $animal_id = $row["animal_id"];

        // 更新動物的累計得票數
        $update_sql = "UPDATE vote SET vote_count = vote_count + 1 WHERE animal_id = '$animal_id'";
        if ($conn->query($update_sql) === TRUE) {
            echo "動物編號為 " . $animal_id . " 的累計得票數已更新成功！";
        } else {
            echo "更新失敗：" . $conn->error;
        }
    } else {
        echo "找不到符合條件的投票活動。";
    }

    // 關閉連接
    $conn->close();
    ?>



