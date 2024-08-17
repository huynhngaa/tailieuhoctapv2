<?php
// Thiết lập tiêu đề CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Kiểm tra nếu là yêu cầu OPTIONS và trả về nhanh chóng
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Tạo kết nối và xử lý dữ liệu API ở đây
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die(json_encode(["message" => "Không kết nối: " . $conn->connect_error]));
}

mysqli_set_charset($conn, "utf8");

// Nhận dữ liệu JSON từ yêu cầu
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu đầu vào
if (!isset($data['kl_ma'])) {
    echo json_encode(["message" => "Thiếu dữ liệu cần thiết"]);
    $conn->close();
    exit();
}

// Lấy dữ liệu từ yêu cầu
$kl_ma = $conn->real_escape_string($data['kl_ma']);

// Câu lệnh SQL để xóa dữ liệu
$sql = "DELETE FROM khoi_lop WHERE kl_ma = ?";

// Chuẩn bị và thực thi câu lệnh SQL
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["message" => "Lỗi chuẩn bị câu lệnh: " . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("s", $kl_ma);

// Ví dụ mã PHP
if ($stmt->execute()) {
    echo json_encode(["message" => "Cập nhật thành công", "success" => true]);
} else {
    echo json_encode(["message" => "Cập nhật thất bại", "success" => false]);
}


// Đóng statement và kết nối
$stmt->close();
$conn->close();
?>
