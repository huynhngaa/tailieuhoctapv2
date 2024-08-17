<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Thay đổi các thông tin kết nối cơ sở dữ liệu theo cấu hình của bạn
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die(json_encode(["error" => "Không kết nối: " . $conn->connect_error]));
}

// Thiết lập mã hóa ký tự UTF-8
$conn->set_charset("utf8");

// Bắt đầu session
session_start();

// Lấy `nd_username` từ query string
$nd_username = isset($_GET['nd_username']) ? $_GET['nd_username'] : null;

if ($nd_username === null) {
    echo json_encode(["error" => "Thiếu tham số `nd_username`"]);
    $conn->close();
    exit;
}

// Câu lệnh SQL để lấy dữ liệu
$sql = "SELECT c.*, d.*
        FROM nguoi_dung c
        JOIN vai_tro d ON c.vt_ma = d.vt_ma
        WHERE c.nd_username = ?";

// Chuẩn bị câu lệnh SQL
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Nếu chuẩn bị câu lệnh SQL không thành công, trả về thông tin lỗi
    echo json_encode(["error" => "Failed to prepare SQL statement: " . $conn->error]);
    $conn->close();
    exit;
}

// Liên kết tham số
$stmt->bind_param('s', $nd_username); // Sử dụng 's' cho chuỗi

// Thực thi câu lệnh SQL
$stmt->execute();

// Lấy kết quả
$result = $stmt->get_result();

// Lấy một hàng kết quả (vì bạn chỉ cần một người dùng cụ thể)
$data = $result->fetch_assoc();

// Đóng statement và kết nối
$stmt->close();
$conn->close();

// Trả về dữ liệu dưới dạng JSON
echo json_encode($data);
?>
