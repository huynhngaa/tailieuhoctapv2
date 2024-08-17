<?php
// Thiết lập tiêu đề CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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
if (!isset($data['kl_ten'])) {
    echo json_encode(["message" => "Thiếu dữ liệu cần thiết"]);
    $conn->close();
    exit();
}

// Lấy dữ liệu từ yêu cầu
$kl_ten = $conn->real_escape_string($data['kl_ten']);

// Câu lệnh SQL để thêm dữ liệu
$sql = "INSERT INTO khoi_lop (kl_ten) VALUES (?)";

// Chuẩn bị và thực thi câu lệnh SQL
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["message" => "Lỗi chuẩn bị câu lệnh: " . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("s", $kl_ten);

if ($stmt->execute()) {
    // Lấy ID của bản ghi mới
    $kl_ma = $stmt->insert_id;
    echo json_encode(["message" => "Thêm thành công", "success" => true, "data" => ["kl_ma" => $kl_ma, "kl_ten" => $kl_ten]]);
} else {
    echo json_encode(["message" => "Thêm thất bại", "success" => false]);
}

// Đóng statement và kết nối
$stmt->close();
$conn->close();
?>
