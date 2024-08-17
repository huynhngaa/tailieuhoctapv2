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

// Lấy tham số nd_username từ URL (hoặc từ bất kỳ nguồn nào bạn sử dụng)
$nd_username = isset($_GET['nd_username']) ? $_GET['nd_username'] : '';

if ($nd_username === '') {
    die(json_encode(['error' => 'Missing nd_username parameter']));
}

// Câu lệnh SQL để lấy dữ liệu
$sql = "SELECT 
  bv.*, 
  nd.*, 
  dm.*, 
  mh.mh_ten, 
  dg.dg_diem, 
  kl.*, 
  CURRENT_TIMESTAMP(), 
  COUNT(
    DISTINCT CASE WHEN bl.trangthai = 1 THEN bl.bl_ma END
  ) AS slbl, 
  SUM(DISTINCT bv.bv_luotxem) AS luotxem 
FROM 
  bai_viet bv 
  JOIN nguoi_dung nd ON bv.nd_username = nd.nd_username 
  JOIN danh_muc dm ON bv.dm_ma = dm.dm_ma 
  JOIN mon_hoc mh ON dm.mh_ma = mh.mh_ma 
  JOIN khoi_lop kl ON kl.kl_ma = mh.kl_ma 
  LEFT JOIN kiem_duyet kd ON kd.bv_ma = bv.bv_ma 
  LEFT JOIN binh_luan bl ON bv.bv_ma = bl.bv_ma 
  LEFT JOIN danh_gia dg ON bv.bv_ma = dg.bv_ma 
where 
  kd.tt_ma = 1 
  AND (
    kd.ghi_chu != 5 
    OR kd.ghi_chu IS NULL
  ) 
  and bv.nd_username = ?
GROUP BY 
  bv.bv_ma
";

// Chuẩn bị câu lệnh SQL
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Hiển thị lỗi SQL nếu câu lệnh không được chuẩn bị
    die(json_encode(['error' => 'SQL prepare error: ' . $conn->error]));
}

// Liên kết tham số với câu lệnh SQL
$stmt->bind_param("s", $nd_username); // "s" cho biết tham số là kiểu chuỗi

// Thực thi câu lệnh SQL
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
?>
