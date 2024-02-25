<?php
// ini_set("display_errors", "On"); // PHP 調試模式開啟

try {
    // 連接數據庫
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        require_once("connectPxzoo.php");
        header("Access-Control-Allow-Origin: *"); //允許跨域存取
    } else {
        // 生產環境
        require_once("connect_chd104g4.php");
    }

    // 從 URL 參數中獲取 mem_id 和 ord_id
    $mem_id = isset($_GET['mem_id']) ? $_GET['mem_id'] : null;
    $ord_id = isset($_GET['ord_id']) ? $_GET['ord_id'] : null;

    // 驗證 mem_id 和 ord_id 是否獲得
    if ($mem_id && $ord_id) {
        // 準備更新 SQL 語句
        $sql = "UPDATE orders SET ord_status = '已用票',ord_altertime = NOW(),sta_id = 1 WHERE mem_id = :mem_id AND ord_id = :ord_id";

        // 預處理 SQL 語句
        $stmt = $pdo->prepare($sql);

        // 綁定參數
        $stmt->bindParam(':mem_id', $mem_id, PDO::PARAM_INT);
        $stmt->bindParam(':ord_id', $ord_id, PDO::PARAM_INT);

        // 執行 SQL 語句
        $stmt->execute();

        // 檢查更新是否成功
        if ($stmt->rowCount() > 0) {
            echo json_encode(["successMsg" => "訂單狀態更新成功"]);
        } else {
            echo json_encode(["errMsg" => "訂單狀態更新失敗"]);
        }
    } else {
        echo json_encode(["errMsg" => "缺少必要的參數"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "執行失敗: " . $e->getMessage()]);
}
?>
