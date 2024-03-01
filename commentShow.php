<?php
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

    // SQL 查詢
    $sql = "SELECT c.*, m.mem_name
            FROM comment c JOIN member m ON c.mem_id = m.mem_id
            ORDER BY c.com_date DESC";
    
    // 準備 SQL 查詢
    $comment = $pdo->prepare($sql);

    // 執行 SQL 查詢
    $comment->execute();

    // 檢查是否有資料
    if ($comment->rowCount() > 0) {
        $commentData = $comment->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($commentData);
    } else {
        echo json_encode(["errMsg" => "沒有找到資料"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
