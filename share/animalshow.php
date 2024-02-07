

    <?php
    //允許跨域存取
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header("Content-Type: application/json; charset=UTF-8");

    try {
        require_once("../pxzoo/connectPxzoo.php");

        // SQL 查詢
        $sql = "SELECT * FROM animal";  // 修改為您的 SQL 查詢

        // 準備 SQL 查詢
        $animal = $pdo->prepare($sql);

        // 執行 SQL 查詢
        $animal->execute();

        // 檢查是否有資料
        if ($animal->rowCount() > 0) {
            $questionsData = $animal->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($questionsData);
        } else {
            echo json_encode(["errMsg" => "沒有找到票務資料"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
    }
    ?>