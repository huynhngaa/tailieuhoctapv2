<?php
header("Access-Control-Allow-Origin: *");
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

// Bắt đầu session
session_start();

// Câu lệnh SQL để lấy dữ liệu
$sql = "SELECT a.*, e.*,c.ghi_chu, d.* FROM bai_viet a
             LEFT JOIN nguoi_dung e ON a.nd_username=e.nd_username
             LEFT JOIN kiem_duyet c ON c.bv_ma = a.bv_ma
             LEFT JOIN trang_thai d ON c.tt_ma = d.tt_ma ";

// Chuẩn bị và thực thi câu lệnh SQL
$stmt = $conn->prepare($sql);
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
header('Content-Type: application/json');
echo json_encode($data);
