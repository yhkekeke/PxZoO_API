<?php
ini_set("display_errors", "On"); // PHP偵錯

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

    // SQL 查詢指令
    $couSQL ='SELECT DISTINCT cd.cou_id, c.cou_name, c.cou_discount, cd.mem_id, cd.ord_id FROM coupon c JOIN coupon_detail cd ON c.cou_id=cd.cou_id WHERE cd.mem_id=:mem_id AND cd.ord_id IS NULL AND (cd.cou_exp>0 OR cd.cou_exp IS NULL) ORDER BY c.cou_discount, cd.cou_detail_time;';

    // 準備 SQL 查詢
    $couSQLStatement = $pdo->prepare($couSQL);

    // 解析 JSON 請求數據
    // PHP 的 file_get_contents 函數用於讀取文件的內容
    // 參數 "php://input" 是用於讀取請求主體的URL，特別是對於 POST 請求
    // json_decode 函數用於將 JSON 字符串轉換為 PHP 關聯數組（如果第二個參數為 true）或對象（如果第二個參數為 false或省略）
    $data = json_decode(file_get_contents('php://input'), true);

    $couSQLStatement->bindValue(':mem_id', $data['mem_id']);

    // 執行 SQL: 使用 PDO（PHP Data Objects）中的 execute 方法，執行之前準備好的 SQL 查詢
    $couSQLStatement->execute();

    // 檢查是否有資料
    if($couSQLStatement->rowCount()>0){
        $couData = $couSQLStatement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($couData);
        // fetchAll 方法將所有的查詢結果取出。PDO::FETCH_ASSOC 參數表示將結果以關聯陣列的形式返回，這樣每一行的資料就是一個關聯陣列。
        //  最後，將取得的資料轉換成 JSON 格式並輸出到客戶端。json_encode 函式將 PHP 陣列轉換為 JSON 字串
    }else{
        echo json_encode(['errMsg'=>'沒有找到優惠券資料']);
    }

}catch(PDOException $e){
    // 捕捉一個特定型別的例外狀況(即 PDOException)
    // 在這個情境中，PDOException 是與 PDO 有關的例外狀況，通常發生在與資料庫的連線、查詢等操作中。

    echo json_encode(['errMsg'=>'執行失敗:'.$e->getMessage()]);

}
?>