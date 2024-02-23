<?php
ini_set("display_errors", "On"); //PHP偵錯

try{
  if($_SERVER['HTTP_HOST']=='localhost' || $_SERVER['HTTP_HOST']=='127.0.0.1'){
    // 開發環境
    require_once('connectPxzoo.php');
    header('Access-Control-Allow-Origin: *'); // 允許跨網域存取，*表示允許所有網域的前端頁面
    header("Access-Control-Allow-Methods: POST");
    header('Access-Control-Allow-Headers: Content-Type'); // 允許使用 Content-Type 這個請求標頭
    header('Content-Type: application/json; charset=UTF-8'); // 回傳給前端的資料類型與字元編碼
  }else{
    // 生產環境
    require_once('connect_chd104g4.php');
  }

  // 解析 JSON 請求數據
  $data = json_decode(file_get_contents('php://input'), true);

  // 檢查請求方式&&有數據
  if(
    $_SERVER['REQUEST_METHOD'] == 'POST' && 
      isset($data)
  ){
    // 開始事務
    $pdo->beginTransaction();

    // -----------------------------

    // SQL 新增指令
    $newOrd='INSERT INTO orders values( null, :mem_id, :cou_id, null, NOW(), :ord_tidate, :ord_tiprice, :ord_couprice, :ord_payprice, :ord_payway, :ord_ticktype, :ord_cardid, :ord_status, null, null);'; 
    
    // 準備 SQL 指令
    $newOrdSQLStatement =  $pdo->prepare($newOrd);

    // 綁定資料
    $newOrdSQLStatement->bindValue(':mem_id', $data['mem_id']);
    $newOrdSQLStatement->bindValue(':cou_id', $data['cou_id']);
    $newOrdSQLStatement->bindValue(':ord_tidate', $data['ord_tidate']);
    $newOrdSQLStatement->bindValue(':ord_tiprice', $data['ord_tiprice']);
    $newOrdSQLStatement->bindValue(':ord_couprice', $data['ord_couprice']);
    $newOrdSQLStatement->bindValue(':ord_payprice', $data['ord_payprice']);
    $newOrdSQLStatement->bindValue(':ord_payway', $data['ord_payway']);
    $newOrdSQLStatement->bindValue(':ord_ticktype', $data['ord_ticktype']);
    $newOrdSQLStatement->bindValue(':ord_cardid', $data['ord_cardid']);
    $newOrdSQLStatement->bindValue(':ord_status', $data['ord_status']);

    // 執行 SQL
    $newOrdSQLStatement->execute();

    //取得新的ord_id
    $lastOrdId = $pdo->lastInsertId(); 

    // -----------------------------

    // 取得 ord_detail_tick
    if( is_object($data['ord_detail_tick']) ){
        // is_object 為 PHP 內建函數
        $ord_detail_tick = get_object_vars($data['ord_detail_tick']);
    }else{
        $ord_detail_tick = $data['ord_detail_tick'];
    }

    // 每筆資料都要run一次
    foreach($data['ord_detail_tick'] as $key => $value){
      if(isset($value['ord_detail_qty']) && $value['ord_detail_qty'] > 0){ // 購買數量須大於 0
        // 訂單明細SQL 新增指令
        $newOrdDeSQL='INSERT INTO orders_detail(ord_id, tickets_id, ord_detail_price, ord_detail_qty) VALUES (:ord_id, :tickets_id, :tickets_price, :ord_detail_qty);';

        // 準備 SQL 指令
        $newOrdDeSQLStatement = $pdo->prepare($newOrdDeSQL);

        // 綁定資料
        $newOrdDeSQLStatement->bindValue(':ord_id', $lastOrdId);
        $newOrdDeSQLStatement->bindValue(':tickets_id', $value['tickets_id']);
        $newOrdDeSQLStatement->bindValue(':tickets_price', $value['tickets_price']);
        $newOrdDeSQLStatement->bindValue(':ord_detail_qty', $value['ord_detail_qty']);
        
        // 執行 SQL
        $newOrdDeSQLStatement->execute(); 
      }
    }

    // -----------------------------

    // 優惠券SQL 修改指令
    $CouDeSQL = "UPDATE coupon_detail 
    SET ord_id = :ord_id WHERE mem_id = :mem_id AND cou_detail_id = :cou_detail_id;";

    // 準備 SQL 指令
    $CouDeSQLStatement = $pdo->prepare($CouDeSQL);

    // 綁定資料
    $CouDeSQLStatement->bindValue(':ord_id', $lastOrdId);
    $CouDeSQLStatement->bindValue(':mem_id', $data['mem_id']);
    $CouDeSQLStatement->bindValue(':cou_detail_id', $data['cou_detail_id']);

    // 執行 SQL
    $CouDeSQLStatement->execute();

    // -----------------------------

    // 如果一切正常，提交事務
    $pdo->commit();

    $result = ['error' => false, 'errMsg' => 'Order inserted & Order_detail inserted & cou_detail updated successfully.'];
    error_log($result['errMsg']);
  }else{
    $result = ['error' => true, 'errMsg' => '執行失敗，無法新增訂單資料，請聯繫系統管理員。原因: ' . $e->getMessage()];
  }
}catch(PDOException $e){
  // 捕捉一個特定型別的例外狀況(即 PDOException)
  // 在這個情境中，PDOException 是與 PDO 有關的例外狀況，通常發生在與資料庫的連線、查詢等操作中。
  // 發生異常時回滾事務
  $pdo->rollBack();

  // 設定錯誤的結果
  $result = ['error' => true, 'errMsg' => '執行失敗，無法新增訂單資料，請聯繫系統管理員。原因: ' . $e->getMessage()];

  // 記錄錯誤的日誌
  error_log($result['errMsg']);
}
echo json_encode($result);
exit; // 新增這一行，確保在返回 JSON 資料後停止腳本的執行
?>