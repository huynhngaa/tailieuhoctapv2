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
if (!isset($data['mh_ten']) || !isset($data['kl_ma'])) {
    echo json_encode(["message" => "Thiếu dữ liệu cần thiết"]);
    $conn->close();
    exit();
}

// Lấy dữ liệu từ yêu cầu
$mh_ten = $conn->real_escape_string($data['mh_ten']);
$kl_ma = $conn->real_escape_string($data['kl_ma']);

// Câu lệnh SQL để thêm dữ liệu vào bảng mon_hoc
$sql = "INSERT INTO mon_hoc (mh_ten, kl_ma) VALUES (?, ?)";

// Chuẩn bị và thực thi câu lệnh SQL
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["message" => "Lỗi chuẩn bị câu lệnh: " . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("si", $mh_ten, $kl_ma);

if ($stmt->execute()) {
    // Lấy ID của bản ghi mới
    $mh_ma = $stmt->insert_id;
    echo json_encode(["message" => "Thêm thành công", "success" => true, "data" => ["mh_ma" => $mh_ma, "mh_ten" => $mh_ten, "kl_ma" => $kl_ma]]);
} else {
    echo json_encode(["message" => "Thêm thất bại", "success" => false]);
}

// Đóng statement và kết nối
$stmt->close();
$conn->close();
?>
