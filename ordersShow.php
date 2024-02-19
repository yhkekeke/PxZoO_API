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

    // 判斷是否有搜尋條件
    $searchTerm = isset($_GET['searchTerm']) ? "%".$_GET['searchTerm']."%" : '%';

    // SQL 查詢
    $ordersSQL = 'SELECT o.* , m.mem_name, m.mem_title, SUM(od.ord_detail_qty) AS allqty, 
    od.ord_detail_qty , c.cou_name , s.sta_pos FROM orders o JOIN member m ON o.mem_id = m.mem_id JOIN orders_detail od ON o.ord_id = od.ord_id LEFT JOIN coupon_detail cd ON od.ord_id = cd.ord_id LEFT JOIN coupon c ON cd.cou_id = c.cou_id LEFT JOIN staff s ON o.sta_id = s.sta_id WHERE o.ord_id LIKE :searchTerm OR m.mem_name LIKE :searchTerm 
    GROUP BY o.ord_id;';

    // 準備 SQL 查詢
    $ordersStatement = $pdo->prepare($ordersSQL);
        // 1. $pdo: PDO 實例，它代表了與資料庫的連線
        // 2. 會返回一個 PDOStatement 物件，其中包含了已經準備好的 SQL 查詢。
    $ordersStatement->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);

    // 執行 SQL: 使用 PDO（PHP Data Objects）中的 execute 方法，執行之前準備好的 SQL 查詢
    $ordersStatement->execute();

    // 檢查是否有資料
    if($ordersStatement->rowCount()>0){
        $ordersData = $ordersStatement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ordersData);
        // fetchAll 方法將所有的查詢結果取出。PDO::FETCH_ASSOC 參數表示將結果以關聯陣列的形式返回，這樣每一行的資料就是一個關聯陣列。
        //  最後，將取得的資料轉換成 JSON 格式並輸出到客戶端。json_encode 函式將 PHP 陣列轉換為 JSON 字串
    }else{
        echo json_encode(['errMsg'=>'沒有找到訂單資料']);
    }
} catch (PDOException $e){
    // 捕捉一個特定型別的例外狀況(即 PDOException)
    // 在這個情境中，PDOException 是與 PDO 有關的例外狀況，通常發生在與資料庫的連線、查詢等操作中。

    echo json_encode(['errMsg'=>'執行失敗:'.$e->getMessage()]);
}
?>