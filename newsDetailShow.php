<?php
// 包含用於數據庫連接的文件
try {
    //下面這個if則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
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

    // 檢查是否收到消息 ID
    if(isset($_GET['id'])) {
        $newsId = $_GET['id'];

        // 準備 SQL 查詢
        $sql = "SELECT * FROM news 
        WHERE news_id = :news_id";

        // 使用 PDO 預備語句來防止 SQL 注入攻擊
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':news_id', $newsId, PDO::PARAM_INT);

        // 執行查詢
        if ($stmt->execute()) {
            // 檢查是否有找到消息
            if ($stmt->rowCount() > 0) {
                // 獲取消息的詳細信息
                $newsDetail = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // 返回 JSON 格式的消息詳細信息
                echo json_encode($newsDetail);
            } else {
                // 如果未找到消息，返回空數據或錯誤消息
                echo json_encode(array('message' => 'News not found'));
            }
        } else {
            // 如果查詢失敗，返回錯誤消息
            echo json_encode(array('message' => 'Query failed'));
        }
    }
    if((isset($_GET['type']) && $_GET['type'] === 'speciesname')){
        $sql = "SELECT * FROM news";  // 修改為您的 SQL 查詢

        // 準備 SQL 查詢
        $newscategory = $pdo->prepare($sql);

        // 執行 SQL 查詢
        $newscategory->execute();

        // 檢查是否有資料
        if ($newscategory->rowCount() > 0) {
            $newscategoryData = $newscategory->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($newscategoryData);
        } else {
            echo json_encode(["errMsg" => "沒有找到消息位置資料"]);
        }
    } 
    if((isset($_GET['type']) && $_GET['type'] === 'newsList')){
        $news_sql = "SELECT * FROM news"; 

        $newsList = $pdo->prepare($news_sql);
        $newsList->execute();
        if ($newsList->rowCount() > 0) {
            $newsListData = $newsList->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($newsListData);
        } else {
            echo json_encode(["errMsg" => "沒有找到消息資料"]);
        }

    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
