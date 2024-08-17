<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}
require 'D:/vuejs/nongtraivuive/vendor/autoload.php';
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->Test->query;
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['noidung'])) {
    echo json_encode(["message" => "Missing required data"]);
    exit();
}
$deleteResult = $collection->deleteMany([]);
$result = $collection->insertOne([
    'noidung' => $data['noidung']
]);
if ($result->getInsertedCount() > 0) {
    $pythonScriptPath = 'D:/vuejs/nongtraivuive/src/vncorenlp/tachtuquery.py';
    $pythonScriptPath2 = 'D:/vuejs/nongtraivuive/src/vncorenlp/cosine.py';
    
    // Chạy lệnh đầu tiên và đợi cho đến khi lệnh hoàn tất
    $output1 = shell_exec("python $pythonScriptPath");
    if ($output1 !== null) {
        // Lệnh đầu tiên thành công, chạy lệnh thứ hai
        $output2 = shell_exec("python $pythonScriptPath2");
        
        if ($output2 !== null) {
            echo "Lệnh thứ hai thành công.";
        } else {
            echo "Lệnh thứ hai thất bại.";
        }
    } else {
        echo "Lệnh đầu tiên thất bại.";
    }

    exec($command, $output, $return_var);
    if ($return_var === 0) {
        echo json_encode(["message" => "successful", "success" => true]);
    } else {
        echo json_encode(["message" => "failed", "success" => true]);
    }
} else {
    echo json_encode(["message" => "failed", "success" => false]);
}

