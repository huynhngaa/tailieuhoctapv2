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
    die("Không kết nối: " . $conn->connect_error);
}

// Thiết lập mã hóa ký tự UTF-8
mysqli_set_charset($conn, "utf8");

// Bắt đầu session
session_start();

// Lấy `bv_ma` và `dm_ma` từ query string
$bv_ma = isset($_GET['bv_ma']) ? intval($_GET['bv_ma']) : 0;
$dm_ma = isset($_GET['dm_ma']) ? intval($_GET['dm_ma']) : 0;

// Câu lệnh SQL để lấy dữ liệu bài viết liên quan
$sql = "SELECT * FROM bai_viet bv
        JOIN nguoi_dung nd ON bv.nd_username = nd.nd_username
        JOIN danh_muc dm ON dm.dm_ma = bv.dm_ma
        JOIN mon_hoc mh ON dm.mh_ma = mh.mh_ma
        JOIN khoi_lop kl ON kl.kl_ma = mh.kl_ma
        LEFT JOIN kiem_duyet kd on kd.bv_ma = bv.bv_ma
        WHERE bv.dm_ma = ? AND bv.bv_ma != ? AND kd.tt_ma = 1
        LIMIT 3";

// Chuẩn bị câu lệnh SQL
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["error" => "Failed to prepare SQL statement: " . $conn->error]);
    $conn->close();
    exit;
}

// Liên kết tham số
$stmt->bind_param('ii', $dm_ma, $bv_ma);

// Thực thi câu lệnh SQL
$stmt->execute();

// Lấy kết quả
$result = $stmt->get_result();

// Lấy tất cả các hàng kết quả
$data = $result->fetch_all(MYSQLI_ASSOC);

// Đóng statement và kết nối
$stmt->close();
$conn->close();

// Trả về dữ liệu dưới dạng JSON
echo json_encode($data);
?>
