

    <?php
    //允許跨域存取
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header("Content-Type: application/json; charset=UTF-8");

    try {
        require_once("../../g4/api/connectPxzoo.php");


        // SQL 查詢
        $sql = "SELECT * FROM questions ORDER BY RAND() LIMIT 10";  // 修改為您的 SQL 查詢

        // 準備 SQL 查詢
        $questions = $pdo->prepare($sql);

        // 執行 SQL 查詢
        $questions->execute();

        // 檢查是否有資料
        if ($questions->rowCount() > 0) {
            $questionsData = $questions->fetchAll(PDO::FETCH_ASSOC);
            // 在返回的陣列中加入題號，以及任何其他需要的欄位
            foreach ($questionsData as $key => $question) {
                $questionsData[$key]['question_number'] = $key + 1;
            }
            echo json_encode($questionsData);
        } else {
            echo json_encode(["errMsg" => "沒有找到題目資料"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
    }
    ?>