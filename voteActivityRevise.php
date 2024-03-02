<?php
//下面這個if則是我設定好讓它在開發時，會自動判斷我們是在開發環境還是在網站上線
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // 開發環境
    //這是本地端的mySQL資料庫帳號密碼檔案
    require_once("connectPxzoo.php");
        //允許跨域存取
    header("Access-Control-Allow-Origin: *"); // 允許所有來源
    header("Content-Type: application/json; charset=UTF-8");
} else {
    // 生產環境  
    //這裡則是我們網站上線後要偵測緯育資料庫的帳號密碼檔案
    require_once("connect_chd104g4.php");
}
// 确保任何来自 OPTIONS 方法的请求都会被接受
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 从 HTTP 请求中获取 JSON 格式的输入数据
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 将 JSON 字符串转换为 PHP 关联数组
    // 从解析后的数据中提取特定属性值
    $vote_activity_id = $input['vote_activity_id'];
    $vote_activity_name = $input['vote_activity_name'];
    $vote_activity_content = $input['vote_activity_content'];
    $vote_activity_date = $input['vote_activity_date'];
    $animal_id_1 = $input['animal_id_1'];
    $animal_id_2 = $input["animal_id_2"];
    $animal_id_3 = $input["animal_id_3"];


    try {
        // 准备 SQL 更新语句，请根据您的数据库实际情况进行调整
        $sql = "UPDATE vote_activity SET vote_activity_name = ?, 
          vote_activity_content = ?, 
          vote_activity_date = ? ,
          animal_id_1 = ? ,
          animal_id_2 = ? ,
          animal_id_3 = ? ,
          WHERE vote_activity_id = ?";

        // 预处理 SQL 语句
        $stmt = $pdo->prepare($sql);

        // 绑定参数到预处理语句
        $stmt->bindParam(1, $vote_activity_name);
        $stmt->bindParam(2, $vote_activity_content);
        $stmt->bindParam(3, $vote_activity_date);
        $stmt->bindParam(4, $animal_id_1);
        $stmt->bindParam(5, $animal_id_2);
        $stmt->bindParam(6, $animal_id_3);
        $stmt->bindParam(7, $vote_activity_id);

        // 执行 SQL 语句
        $stmt->execute();

        // 检查更新操作是否成功
        if ($stmt->rowCount() > 0) {
            echo json_encode(["successMsg" => "更新成功"]);
        } else {
            echo json_encode(["errMsg" => "更新失败"]);
        }
    } catch (PDOException $e) {
        // 输出错误信息
        echo json_encode(["errMsg" => "更新失败：" . $e->getMessage()]);
    }
}
