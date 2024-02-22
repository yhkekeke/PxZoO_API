

    <?php

    try {
        //下面這個if則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
            // 開發環境
            //這是本地端的mySQL資料庫帳號密碼檔案
            require_once("../api/connectPxzoo.php");
            //允許跨域存取
            header("Access-Control-Allow-Origin: *"); // 允許所有來源
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS,DELETE");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header("Content-Type: application/json; charset=UTF-8");
        } else {
            // 生產環境  
            //這裡則是我們網站上線後要偵測緯育資料庫的帳號密碼檔案
            require_once("connect_chd104g4.php");
        }

        $searchTerm = $_GET['searchTerm']; //有問題
        // SQL 查詢，模糊查詢
        $sql = "SELECT *   FROM questions 
        WHERE question_text  LIKE :searchTerm 
        OR question_option_a LIKE :searchTerm 
        OR question_option_b LIKE :searchTerm
        OR question_option_c LIKE :searchTerm
        OR question_option_d LIKE :searchTerm
        OR question_correctanswer LIKE :searchTerm
        OR question_answer_illustrate LIKE :searchTerm";
        // 修改為您的 SQL 查詢
        $questions = $pdo->prepare($sql);
        // 準備 SQL 查詢
        $searchTerm = "%$searchTerm%"; // 在搜尋條件的兩側添加 %
        $questions->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);

        // 執行 SQL 查詢
        $questions->execute();

        // 檢查是否有資料
        if ($questions->rowCount() > 0) {
            $questionsData = $questions->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($questionsData);
        } else {
            echo json_encode(["errMsg" => "沒有找到動物資料"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
    }
    ?>