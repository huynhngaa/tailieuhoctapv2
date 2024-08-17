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

// Lấy `bv_ma` từ query string
$bv_ma = isset($_GET['bv_ma']) ? intval($_GET['bv_ma']) : 0;

// Câu lệnh SQL để lấy dữ liệu
$sql = "SELECT bv.*, nd.nd_hoten, dm_ten, mh_ten, kl_ten, kd.tt_ma, tt_ten, NOW() AS currentTimestamp, COALESCE(slbl, 0) AS slbl
        FROM bai_viet bv
        LEFT JOIN (
            SELECT bv_ma, COUNT(*) AS slbl
            FROM binh_luan
            WHERE trangthai = 1
            GROUP BY bv_ma
        ) bl ON bv.bv_ma = bl.bv_ma
        JOIN nguoi_dung nd ON bv.nd_username = nd.nd_username
        JOIN danh_muc dm ON bv.dm_ma = dm.dm_ma
        JOIN mon_hoc mh ON dm.mh_ma = mh.mh_ma
        JOIN khoi_lop kl ON kl.kl_ma = mh.kl_ma
        LEFT JOIN kiem_duyet kd ON kd.bv_ma = bv.bv_ma
          LEFT JOIN trang_thai tt on kd.tt_ma = tt.tt_ma
        WHERE  bv.bv_ma = ?";

// Chuẩn bị câu lệnh SQL
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Nếu chuẩn bị câu lệnh SQL không thành công, trả về thông tin lỗi
    echo json_encode(["error" => "Failed to prepare SQL statement: " . $conn->error]);
    $conn->close();
    exit;
}

// Liên kết tham số
$stmt->bind_param('i', $bv_ma);

// Thực thi câu lệnh SQL
$stmt->execute();

// Lấy kết quả
$result = $stmt->get_result();

// Lấy một hàng kết quả (vì bạn chỉ cần một bài viết cụ thể)
$data = $result->fetch_assoc();

// Đóng statement và kết nối
$stmt->close();
$conn->close();

// Trả về dữ liệu dưới dạng JSON
echo json_encode($data);
?>
