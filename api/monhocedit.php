<?php
// Thiết lập tiêu đề CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Kiểm tra nếu là yêu cầu OPTIONS và trả về nhanh chóng
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Thực hiện các công việc cần thiết cho yêu cầu OPTIONS
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
if (!isset($data['mh_ma']) || !isset($data['mh_ten'])) {
    echo json_encode(["message" => "Thiếu dữ liệu cần thiết"]);
    $conn->close();
    exit();
}

// Lấy dữ liệu từ yêu cầu
$mh_ma = $conn->real_escape_string($data['mh_ma']);
$mh_ten = $conn->real_escape_string($data['mh_ten']);

// Câu lệnh SQL để cập nhật dữ liệu
$sql = "UPDATE mon_hoc SET mh_ten = ? WHERE mh_ma = ?";

// Chuẩn bị và thực thi câu lệnh SQL
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["message" => "Lỗi chuẩn bị câu lệnh: " . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("ss", $mh_ten, $mh_ma);

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
