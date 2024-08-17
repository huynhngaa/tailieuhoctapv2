<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Không kết nối: " . $conn->connect_error);
}

// Thiết lập mã hóa ký tự UTF-8
mysqli_set_charset($conn, "utf8");

// Nhận dữ liệu từ POST
$tentaikhoan = $_POST['username'];
$matkhau = $_POST['password']; // Bạn có thể cần mã hóa mật khẩu trước khi so sánh

// Câu lệnh SQL để lấy dữ liệu
$sql = "SELECT * FROM nguoi_dung nd, vai_tro t WHERE nd.vt_ma = t.vt_ma AND nd_username = ? AND nd_matkhau = ?";

// Chuẩn bị và thực thi câu lệnh SQL
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $tentaikhoan, $matkhau);
$stmt->execute();

// Lấy kết quả
$result = $stmt->get_result();

// Chuyển kết quả thành một mảng
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Đóng statement và kết nối
$stmt->close();
$conn->close();

// Trả về dữ liệu dưới dạng JSON
echo json_encode($data);
?>
