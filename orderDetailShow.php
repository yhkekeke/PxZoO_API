<?php
ini_set("display_errors", "On"); // PHP偵錯

try {
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        // 開發環境
        require_once('../pxzoo/connectPxzoo.php');
        header('Access-Control-Allow-Origin: *'); // 允許跨域存取，* 表示允許所有網域的前端頁面
        header("Content-Type: application/json; charset=UTF-8"); // 回傳給前端的資料類型及字元編碼
    }else{
        // 生產環境
        require_once('connect_chd104g4.php');
    }

    // SQL 查詢
    $orderDetailSQL = 'SELECT od.ord_id, t.tickets_name, od.ord_detail_qty FROM orders_detail od JOIN tickets t ON od.tickets_id = t.tickets_id WHERE od.ord_id = ?;';
    
    // 準備 SQL 查詢
    $orderDetailStatement = $pdo->prepare($orderDetailSQL);

    // 將 SQL 語句中的第一個問號 ? 綁定到 $_GET["ord_id"] 的值
    $orderDetailStatement->bindValue(1, $_GET['ord_id']);

    // 執行 SQL
    $orderDetailStatement->execute();

    // 檢查是否有資料
    if($orderDetailStatement->rowCount()>0){
        $orderDetailData = $orderDetailStatement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($orderDetailData);
    }else{
        echo json_encode(['errMsg'=>'沒有找到訂單明細資料']);
    }

}catch(PDOException $e){
    echo json_encode(['errMsg'=>'執行失敗:'.$e->getMessage()]);
}
?>