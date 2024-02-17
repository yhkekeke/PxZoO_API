<?php
//允許跨域存取
header("Access-Control-Allow-Origin: *"); // 允許所有來源
header("Content-Type: application/json; charset=UTF-8");

try {
    // 連線 MySQL
    // if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    //     // 開發環境
    //     require_once("../pxzoo/connectPxzoo.php");
    // } else {
    //     // 生產環境
    //     require_once("https://tibamef2e.com/chd104/g4/api/connectPxzoo.php");
    // }
    require_once("../pxzoo/connectPxzoo.php");
    // SQL 查詢
    $sql = "SELECT * FROM comment";  // 修改為您的 SQL 查詢
    // $sql = "SELECT p.product_no, pc.product_class_name, pt.product_tag_name, p.product_name, p.product_price, p.product_status 
    //         FROM product p JOIN product_class pc ON p.product_class_no = pc.product_class_no LEFT JOIN product_tag pt ON p.product_tag_no = pt.product_tag_no";
    
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
