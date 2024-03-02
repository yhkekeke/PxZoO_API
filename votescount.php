

    <?php
    // //下面這個if則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
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

    // // 獲取用戶IP
    // $user_ip = $_SERVER['REMOTE_ADDR'];

    // // 查詢投票活動資料
    // $sql = "SELECT vote_activity_id, animal_id FROM vote_activity WHERE user_ip = '$user_ip'";
    // $result = $conn->query($sql);

    // if ($result->num_rows > 0) {
    //     // 找到投票活動
    //     $row = $result->fetch_assoc();
    //     $vote_activity_id = $row["vote_activity_id"];
    //     $animal_id = $row["animal_id"];

    //     // 更新動物的累計得票數
    //     $update_sql = "UPDATE vote SET vote_count = vote_count + 1 WHERE animal_id = '$animal_id'";
    //     if ($conn->query($update_sql) === TRUE) {
    //         echo "動物編號為 " . $animal_id . " 的累計得票數已更新成功！";
    //     } else {
    //         echo "更新失敗：" . $conn->error;
    //     }
    // } else {
    //     echo "找不到符合條件的投票活動。";
    // }

    // // 關閉連接
    // $conn->close();



    // 假設這是你的投票處理後端
    
    // 獲取用戶IP地址
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    // 假設今天的日期是2024-02-24
    $current_date = date('Y-m-d');
    
    // 檢查用戶今天的投票次數
    $sql = "SELECT COUNT(*) as vote_count FROM vote_activity WHERE user_ip = '$user_ip' AND vote_date = '$current_date'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $vote_count = $row["vote_count"];
        if ($vote_count >= 3) {
            // 今天已經投了三票，不允許再投票
            echo "今天你已經投了三票，請明天再來！";
        } else {
            // 允許投票，更新投票次數
            $insert_sql = "INSERT INTO vote_activity (user_ip, vote_date) VALUES ('$user_ip', '$current_date')";
            if ($conn->query($insert_sql) === TRUE) {
                // 投票次數更新成功
                echo "投票成功！";
            } else {
                // 更新失敗
                echo "投票失敗：" . $conn->error;
            }
        }
    } else {
        // 如果沒有投票記錄，則允許投票並新增一條投票記錄
        $insert_sql = "INSERT INTO vote_activity (user_ip, vote_date) VALUES ('$user_ip', '$current_date')";
        if ($conn->query($insert_sql) === TRUE) {
            // 投票次數更新成功
            echo "投票成功！";
        } else {
            // 更新失敗
            echo "投票失敗：" . $conn->error;
        }
    }
    
    // 關閉連接
    $conn->close();
    ?>
    


  



