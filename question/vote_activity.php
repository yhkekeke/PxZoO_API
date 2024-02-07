<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>顯示問題</title>
</head>

<body>
    <?php
    try {
        require_once("./connectPxzoo.php");

        // 處理圖片上傳
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
            $uploadDirectory = "/image/animal/";

            // 檢查上傳目錄是否存在，不存在則創建
            if (!file_exists($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            // 檔案名稱
            $fileName = $_FILES["file"]["name"];
            // 檔案臨時路徑
            $tmpFilePath = $_FILES["file"]["tmp_name"];
            // 目標檔案路徑
            $targetFilePath = $uploadDirectory . $fileName;

            // 將檔案移動到目標位置
            if (move_uploaded_file($tmpFilePath, $targetFilePath)) {
                // 上傳成功後，將圖片路徑儲存到資料庫中
                $sql = "UPDATE questions SET question_img_a = :question_img_a WHERE question_id = :question_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':question_img_a', $targetFilePath, PDO::PARAM_STR);
                $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT); // 假設question_id為問題的ID
                $stmt->execute();
                echo "圖片上傳成功並存儲到資料庫。";
            } else {
                echo "圖片上傳失敗。";
            }
        }

        // 準備 SQL 語句
        $sql = "SELECT * FROM questions WHERE question_id = :question_id";

        // 準備 SQL 語句以防止 SQL 注入攻擊
        $stmt = $pdo->prepare($sql);

        // 綁定參數
        $questions = 1; // 假設要搜尋的問題 ID 為 1
        $stmt->bindParam(':question_id', $questions, PDO::PARAM_INT);

        // 執行查詢
        $stmt->execute();

        // 檢查是否有結果
        if ($stmt->rowCount() > 0) {
            // 迭代結果集
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // 輸出每一行數據
                echo "<div>";
                echo "<p>" . $row['question_text'] . "</p>";
                echo "<img src='" . $row['question_img_a'] . "' alt='Option A'>";
                echo "<img src='" . $row['question_img_b'] . "' alt='Option B'>";
                echo "<img src='" . $row['question_img_c'] . "' alt='Option C'>";
                echo "<img src='" . $row['question_img_d'] . "' alt='Option D'>";
                echo "<p>Correct Answer: " . $row['question_correctanswer'] . "</p>";
                echo "<p>Answer Illustration: " . $row['question_answer_illustrate'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "沒有找到相應的問題。";
        }
    } catch (PDOException $e) {
        echo "執行失敗：" . $e->getMessage();
    }
    ?>
</body>

</html>