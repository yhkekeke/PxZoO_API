

    <?php
    //允許跨域存取
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header("Content-Type: application/json; charset=UTF-8");

    try {
        require_once("connectPxzoo.php");


        // SQL 查詢
        $sql = "SELECT * FROM vote_activity";  // 修改為您的 SQL 查詢

        // 準備 SQL 查詢
        $vote_activity = $pdo->prepare($sql);

        // 執行 SQL 查詢
        $vote_activity->execute();

        // 檢查是否有資料
        if ($vote_activity->rowCount() > 0) {
            $vote_activityData = $vote_activity->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($vote_activityData);
        } else {
            echo json_encode(["errMsg" => "沒有找到票務資料"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
    }
    ?>