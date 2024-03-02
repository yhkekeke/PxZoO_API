<?php
// ini_set("display_errors", "On"); // PHP偵錯

try {
  if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 開發環境
    require_once('connectPxzoo.php');
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

  // 對輸入的參數進行適當的過濾和驗證，以確保它不會被濫用，特別是防止 SQL 注入攻擊
  // filter_input(外部變數來源, 變數名稱, 過濾器類型) ，這個函數可以對輸入進行過濾，確保其符合預期的類型
  $ord_id = filter_input(INPUT_GET, 'ord_id', FILTER_VALIDATE_INT);
  
  if ($ord_id === false || $ord_id === null) {
    echo json_encode(['errMsg' => 'Invalid order ID']);
    exit;
  }

  // 將 SQL 語句中的第一個問號 ? 綁定到 $_GET["ord_id"] 的值
  $orderDetailStatement->bindValue(1, $ord_id, PDO::PARAM_INT);

  // 執行 SQL
  $orderDetailStatement->execute();

  // 檢查是否有資料
  if($orderDetailStatement->rowCount()>0){
    $orderDetailData = $orderDetailStatement->fetchAll(PDO::FETCH_ASSOC);

    // 記錄成功的 log
    error_log('Orderdetail selected successfully');
    $result = $orderDetailData;
  }else{
    $result = ['error' => true, 'errMsg'=>'沒有找到訂單明細資料'];
  }

}catch(PDOException $e){
  // 捕捉一個特定型別的例外狀況(即 PDOException)
  // 在這個情境中，PDOException 是與 PDO 有關的例外狀況，通常發生在與資料庫的連線、查詢等操作中。
  $result = ['errMsg' => '執行失敗，無法檢索訂單明細資料，請聯繫系統管理員。原因: ' . $e->getMessage()];

  // 記錄錯誤的 log
  error_log($result['errMsg']);
}
echo json_encode($result);
exit; // 新增這一行，確保在返回 JSON 資料後停止腳本的執行
?>