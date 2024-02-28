<?php
// ini_set("display_errors", "On"); //PHP偵錯

try{
  if($_SERVER["HTTP_HOST"] == "localhost" || $_SERVER["HTTP_HOST"] == "127.0.0.1") {
    // 開發環境
    require_once("connectPxzoo.php");
    header("Access-Control-Allow-Origin: *"); // 允許跨域存取，* 表示允許所有網域的前端頁面
    header("Access-Control-Allow-Headers: Content-Type"); // 允許使用 Content-Type 這個請求標頭
    header("Content-Type: application/json; charset=UTF-8"); // 回傳給前端的資料類型及字元編碼
  }else{
    // 生產環境
    require_once("connect_chd104g4.php");
  }

  // 開始事務
  $pdo->beginTransaction();

  // -----------------------------
  
  // SQL 查詢
  // CASE...: 符合條件的 od.ordQty 會被 sum 統計，不符合 的 od.ordQty 替代為0。
  $peopleSQL="SELECT DATE_FORMAT(o.ord_tidate, '%Y / %c') time, IFNULL( SUM(CASE WHEN o.ord_status = '已用票' THEN od.ordQty ELSE 0 END), 0 ) AS digitalTicket, IFNULL( SUM(CASE WHEN o.ord_status = '已取票' THEN od.ordQty ELSE 0 END), 0 ) AS entityTicket, IFNULL( SUM(CASE WHEN o.ord_status = '已用票' OR o.ord_status = '已取票' THEN od.ordQty ELSE 0 END), 0 ) AS peopleTotal FROM orders o JOIN (SELECT ord_id, SUM(ord_detail_qty) AS ordQty FROM orders_detail GROUP BY ord_id) od ON o.ord_id=od.ord_id WHERE MONTH(o.ord_tidate) IN (MONTH(CURDATE()), MONTH(CURDATE() - INTERVAL 1 MONTH), MONTH(CURDATE() - INTERVAL 2 MONTH)) AND YEAR(o.ord_tidate) IN (YEAR(CURDATE()), YEAR(CURDATE() - INTERVAL 1 MONTH), YEAR(CURDATE() - INTERVAL 2 MONTH)) GROUP BY time ORDER BY o.ord_tidate ASC;  ";

  // 準備 SQL 查詢
  // query: 執行 SQL 查詢並返回一個 PDOStatement 對象，且應是完整的、可執行的 SQL 查詢(非查詢語句、有參數的，應使用 prepare)
  $peopleStatement = $pdo->query($peopleSQL);

  if(!$peopleStatement){
    die(json_encode(["error"=>"SQL Error", "message" => "peopleStatement" . $pdo->error()]));
    // die(): 主要目的是終止腳本的執行，通常與一條錯誤消息一起使用，以提供有關發生問題的信息。
  }
  $peopleRows = $peopleStatement->fetchAll(PDO::FETCH_NUM);
  // fetchAll 是 PDOStatement 對象的一個方法，用於檢索所有結果行
  // PDO::FETCH_NUM 是 fetch 方法的一個常量參數，表示以數字索引的方式檢索每一行，並將其以數字索引的形式存儲在 $peopleRows
  // 因此 $peopleRows 將包含數值索引的結果及數據

  // -----------------------------

  // 當資料有缺漏時，補上空值
  $twoMonthsAgo = date("Y / n", strtotime("-2 months"));
  $lastMonth = date("Y / n", strtotime("-1 months"));
  $currentMonth = date("Y / n");
  $hasTwoMonthsAgo = false; // 檢查是否有上上月的資料
  $hasLastMonth = false; // 檢查是否有上月的資料
  $hasCurrentMonth = false; // 檢查是否有本月的資料

  if( count($peopleRows) == 0 ){
    array_push( $peopleRows, [$twoMonthsAgo, 0,0,0] );
    array_push( $peopleRows, [$lastMonth, 0,0,0] );
    array_push( $peopleRows, [$currentMonth, 0,0,0] );
  }else if( count($peopleRows)<3 ){
    foreach($peopleRows as $data){
      $month = $data[0]; // 取得月份
  
      if($month === $twoMonthsAgo){
          $hasTwoMonthsAgo = true;
      }else if($month === $lastMonth){
          $hasLastMonth = true;
      }else if($month === $currentMonth){
          $hasCurrentMonth = true;
      }
    }

    // 檢查並補充缺失的月份
    if(!$hasTwoMonthsAgo) {
      array_unshift( $peopleRows, [$twoMonthsAgo, 0,0,0] );
    }
    if(!$hasLastMonth) {
      // array_splice($arr, 1, 0, $newData); 
      // 參數: 目標陣列, 要插入的索引位置, 刪除數量, 加進來的資料
      array_splice( $peopleRows, 1, 0, [[$lastMonth, 0, 0, 0]] );
    }
    if(!$hasCurrentMonth) {
      array_push( $peopleRows, [$currentMonth, 0,0,0] );
    }
  }
  
  // -----------------------------

  // 如果一切正常，提交事務
  $pdo->commit();

  $result = $peopleRows;

  // 記錄成功的日誌
  error_log("PeopleData selected successfully");

}catch(PDOException $e){
  $result=["error"=>true, "errMsg"=>"執行失敗，無法產生人數統計資料，請聯繫系統管理員。原因: " . $e->getMessage()]; 
  // 記錄錯誤的日誌
  error_log($result["errMsg"]);
}
echo json_encode($result);
// json_encode 函數是 PHP 提供的一個功能，將 PHP 陣列轉換為 JSON 格式的字串
exit;
// exit: 確保在返回 JSON 資料後停止腳本的執行
?>