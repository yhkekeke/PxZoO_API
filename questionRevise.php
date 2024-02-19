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
    require_once("connectPxzoo.php");

    // 从 HTTP 请求中获取 JSON 格式的输入数据
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 将 JSON 字符串转换为 PHP 关联数组

    // 从解析后的数据中提取特定属性值
    $question_id = $input['question_id'];
    $question_text = $input['question_text'];
    $question_option_a = $input['question_option_a'];
    $question_img_a = $input['question_img_a'];
    $question_option_b = $input['question_option_b'];
    $question_img_b = $input['question_img_b'];
    $question_option_c = $input['question_option_c'];
    $question_img_c = $input['question_img_c'];
    $question_option_d = $input['question_option_d'];
    $question_img_d = $input['question_img_d'];
    $question_correctanswer = $input['question_correctanswer'];
    $question_answer_illustrate = $input['question_answer_illustrate'];

    // 檢查新的圖片 URL，如果有的話
    $new_question_img_a = $_FILES['new_question_img_a']["name"] ?? '';
    $new_question_img_b = $_FILES['new_question_img_b']["name"] ?? '';
    $new_question_img_c = $_FILES['new_question_img_c']["name"] ?? '';
    $new_question_img_d = $_FILES['new_question_img_d']["name"] ?? '';

    // 目標路徑
    $target_directory = '/images/school/animal/';

    // 檢查目標路徑是否存在，不存在則創建
    if (!file_exists($target_directory)) {
        mkdir($target_directory, 0777, true);
    }

    // 更新圖片 URL 並复制文件
    if (!empty($new_question_img_a)) {
        $question_img_a = $target_directory . '/' . basename($_FILES["new_question_img_a"]["name"]);
        if (!copy($_FILES["new_question_img_a"]["tmp_name"], $question_img_a)) {
            echo json_encode(["errMsg" => "無法將 new_question_img_a 檔案複製到目標資料夾"]);
            exit;
        }
    }

    try {
        // 准备 SQL 更新语句，请根据您的数据库实际情况进行调整
        $sql = "UPDATE questions SET question_text = ?, 
          question_option_a = ?, 
          question_img_a = ? ,
          question_option_b = ? ,
          question_img_b = ? ,
          question_option_c = ? ,
          question_img_c = ? ,
          question_option_d = ? ,
          question_img_d = ? ,
          question_correctanswer = ? ,  
          question_answer_illustrate = ? 
          WHERE question_id = ?";

        // 预处理 SQL 语句
        $stmt = $pdo->prepare($sql);

        // 绑定参数到预处理语句
        $stmt->bindParam(1, $question_text);
        $stmt->bindParam(2, $question_option_a);
        $stmt->bindParam(3, $question_img_a);
        $stmt->bindParam(4, $question_option_b);
        $stmt->bindParam(5, $question_img_b);
        $stmt->bindParam(6, $question_option_c);
        $stmt->bindParam(7, $question_img_c);
        $stmt->bindParam(8, $question_option_d);
        $stmt->bindParam(9, $question_img_d);
        $stmt->bindParam(10, $question_correctanswer);
        $stmt->bindParam(11, $question_answer_illustrate);
        $stmt->bindParam(12, $question_id); // 为 WHERE 条件也绑定参数

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
