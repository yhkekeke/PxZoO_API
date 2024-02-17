<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 确保任何来自 OPTIONS 方法的请求都会被接受
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once("../pxzoo/connectPxzoo.php");

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
