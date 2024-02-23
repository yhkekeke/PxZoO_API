<?php
// 允許跨域存取
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

// 驗證 POST 請求中的資料是否存在
if(isset($_POST['question_id']) && isset($_POST['question_status'])) {
    // 獲取要更新狀態的問題ID和狀態值
    $question_id = $_POST['question_id'];
    $question_status = $_POST['question_status']; // 從前端獲取 question_status 屬性
    
    // 將 question_status 屬性轉換為0或1
    $question_status = $question_status ? 1 : 0;

    try {
        // 使用參數化查詢更新問題狀態，防止 SQL 注入
        $stmt = $conn->prepare("UPDATE questions SET question_status = :status WHERE question_id = :id");
        $stmt->bindParam(':status', $question_status);
        $stmt->bindParam(':id', $question_id);
        
        if ($stmt->execute()) {
            // 成功更新狀態
            echo json_encode(array('success' => true, 'message' => '問題狀態更新成功'));
        } else {
            // 更新狀態失敗
            echo json_encode(array('success' => false, 'error' => '更新問題狀態時出錯'));
        }
    } catch (PDOException $e) {
        // 資料庫操作出現異常
        echo json_encode(array('success' => false, 'error' => '資料庫操作失敗: ' . $e->getMessage()));
    }
} else {
    // 缺少必要的參數
    echo json_encode(array('success' => false, 'error' => '缺少必要的參數'));
}

// 關閉資料庫連接
$conn = null;
?>
