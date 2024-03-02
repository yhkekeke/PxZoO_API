<?php
// ini_set("display_errors", "On"); //PHP偵錯

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
  $updateOrderSQL = 'UPDATE orders SET sta_id=:sta_id, ord_status=:ord_status, ord_altertime=now() WHERE ord_id=:ord_id;' ;

  // 準備 SQL 查詢
  $updateOrderStatement = $pdo->prepare($updateOrderSQL);

  // 解析 JSON 請求數據
  // PHP 的 file_get_contents 函數用於讀取文件的內容
  // 參數 "php://input" 是用於讀取請求主體的URL，特別是對於 POST 請求
  // json_decode 函數用於將 JSON 字符串轉換為 PHP 關聯數組（如果第二個參數為 true）或對象（如果第二個參數為 false或省略）
  $data = json_decode(file_get_contents("php://input"), true);

  $updateOrderStatement->bindValue(":sta_id", $data['sta_id']);
  $updateOrderStatement->bindValue(":ord_status", $data['ord_status']);
  $updateOrderStatement->bindValue(':ord_id', $data['ord_id']);
  $updateOrderStatement->execute();

  // 記錄成功的 log
  error_log('Order updated successfully');

  $result = ['error'=>false, 'msg'=>"異動成功"];

}catch(PDOException $e){
  // 捕捉一個特定型別的例外狀況(即 PDOException)
  // 在這個情境中，PDOException 是與 PDO 有關的例外狀況，通常發生在與資料庫的連線、查詢等操作中。
  $result = ['error' => true, 'msg' => "訂單異動失敗，請聯繫系統管理員。原因: " . $e->getMessage()];

  // 記錄錯誤的 log
  error_log($result['errMsg']);
}
echo json_encode($result);
exit; // 新增這一行，確保在返回 JSON 資料後停止腳本的執行
?>