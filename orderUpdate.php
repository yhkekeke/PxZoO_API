<?php

ini_set("display_errors", "On"); //PHP偵錯

try{
    if($_SERVER['HTTP_HOST']=='localhost' || $_SERVER['HTTP_HOST']=='127.0.0.1'){
        // 開發環境
        require_once('connectPxzoo.php');
        header('Access-Control-Allow-Origin: *'); // 允許跨網域存取，*表示允許所有網域的前端頁面
        header('Access-Control-Allow-Headers: Content-Type'); // 允許使用 Content-Type 這個請求標頭
        header('Content-Type: application/json; charset=UTF-8'); // 回傳給前端的資料類型與字元編碼
    }else{
        // 生產環境
        require_once('connect_chd104g4.php');
    }

    // SQL 修改指令
    $alterOrderSQL = 'UPDATE orders SET sta_id=:sta_id, ord_status=:ord_status, ord_altertime=now() WHERE ord_id=:ord_id;' ;

    // 準備 SQL 查詢
    $alterOrderStatement = $pdo->prepare($alterOrderSQL);

    // 解析 JSON 請求數據
    $data = json_decode(file_get_contents("php://input"), true);

    $alterOrderStatement->bindValue(":sta_id", $data['sta_id']);
    $alterOrderStatement->bindValue(":ord_status", $data['ord_status']);
    $alterOrderStatement->bindValue(':ord_id', $data['ord_id']);
    $alterOrderStatement->execute();

    // 記錄成功的 log
    error_log('Order altered successfully');

    $result = ['error'=>false, 'msg'=>"異動成功"];

}catch(PDOException $e){
    
    // 記錄錯誤的 log
    error_log('Error altering order: ' . $e->getMessage());

    $result=['error'=>true, "msg"=>$e->getMessage()];
}
echo json_encode($result);
?>