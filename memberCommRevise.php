<?php
// ini_set("display_errors", "On"); // 开启 PHP 错误显示

try {
    // 根据当前环境选择数据库连接
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        header("Access-Control-Allow-Origin: *"); // 允许跨域访问
        header("Access-Control-Allow-Headers: Content-Type"); // 设置响应头
        header("Content-Type: application/json; charset=UTF-8"); // 设置响应数据类型和编码

        // 如果是在开发环境
        require_once("connectPxzoo.php");
    } else {
        // 如果是在生产环境
        require_once("connect_chd104g4.php");
    }

    // 从 HTTP 请求中获取 JSON 格式的输入数据
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE); // 将 JSON 字符串转换为 PHP 关联数组

    // 从解析后的数据中提取特定属性值
    $com_id = $input['com_id'];
    $com_text = $input['com_text'];

    // 准备 SQL 更新语句，根据实际情况调整表名和列名
    $sql = "UPDATE comment SET com_text = ? WHERE com_id = ?";

    // 预处理 SQL 语句
    $stmt = $pdo->prepare($sql);

    // 绑定参数到预处理语句
    $stmt->bindParam(1, $com_text);
    $stmt->bindParam(2, $com_id);

    // 执行 SQL 语句
    $stmt->execute();

    // 检查更新操作是否成功
    if ($stmt->rowCount() > 0) {
        echo json_encode(["successMsg" => "更新成功"]);
    } else {
         echo json_encode(["errMsg" => "更新失敗"]);
    }
} catch (PDOException $e) {
    echo json_encode(["errMsg" => "error: " . $e->getMessage()]);
}
?>
