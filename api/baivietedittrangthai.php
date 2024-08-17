<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Không kết nối: " . $conn->connect_error]));
}

mysqli_set_charset($conn, "utf8");

$bv_ma = $_GET['bv_ma'] ?? null;
$tt_ma = $_GET['tt_ma'] ?? null;

if (!$bv_ma || !$tt_ma) {
    echo json_encode(["message" => "Thiếu dữ liệu cần thiết"]);
    $conn->close();
    exit();
}

$sql = "UPDATE kiem_duyet SET tt_ma = ? WHERE bv_ma = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["message" => "Lỗi chuẩn bị câu lệnh: " . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("ii", $tt_ma, $bv_ma);

if ($stmt->execute()) {
    echo json_encode(["message" => "Cập nhật thành công", "success" => true]);
} else {
    echo json_encode(["message" => "Cập nhật thất bại", "success" => false]);
}

$stmt->close();
$conn->close();
?>
