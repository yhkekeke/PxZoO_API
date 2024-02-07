

    <?php
    //允許跨域存取
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header("Content-Type: application/json; charset=UTF-8");

    try {
        require_once("../pxzoo/connectPxzoo.php");

        // SQL 查詢
        $sql = "SELECT * FROM votes";  // 修改為您的 SQL 查詢

        // 準備 SQL 查詢
        $votes = $pdo->prepare($sql);

        // 執行 SQL 查詢
        $votes->execute();

        // 檢查是否有資料
        if ($votes->rowCount() > 0) {
            $votesData = $votes->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($votesData);
        } else {
            echo json_encode(["errMsg" => "沒有找到票務資料"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
    }
    ?>