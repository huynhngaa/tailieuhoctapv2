<?php
header("Access-Control-Allow-Origin: *");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";

// Kết nối với MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Không kết nối: " . $conn->connect_error);
}

// Thiết lập mã hóa ký tự UTF-8
mysqli_set_charset($conn, "utf8");

// Kết nối với MongoDB
require 'D:/vuejs/nongtraivuive/vendor/autoload.php';
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDb = $mongoClient->selectDatabase("Test");
$idtimkiemCollection = $mongoDb->idtimkiem;

// Lấy danh sách các id và điểm từ bảng idtimkiem trong MongoDB
$idtimkiemData = $idtimkiemCollection->find([], ['projection' => ['id' => 1, 'score' => 1]]);
$idtimkiemList = iterator_to_array($idtimkiemData);
$idtimkiemIds = array_column($idtimkiemList, 'id');
$idtimkiemScores = array_column($idtimkiemList, 'score', 'id');

// Nếu không có id nào, trả về mảng rỗng
if (empty($idtimkiemIds)) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

// Tạo các dấu hỏi cho câu lệnh SQL
$placeholders = implode(',', array_fill(0, count($idtimkiemIds), '?'));

// Câu lệnh SQL
$sql = "
SELECT 
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
WHERE 
  kd.tt_ma = 1 
  AND (
    kd.ghi_chu != 5 
    OR kd.ghi_chu IS NULL
  ) 
  AND bv.bv_ma IN ($placeholders)
GROUP BY 
  bv.bv_ma
";

// Chuẩn bị và thực thi câu lệnh SQL
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Lỗi chuẩn bị câu lệnh SQL: " . $conn->error);
}

// Liên kết các tham số với câu lệnh SQL
$types = str_repeat('i', count($idtimkiemIds)); // 'i' cho số nguyên
$stmt->bind_param($types, ...$idtimkiemIds);

// Thực thi câu lệnh SQL
$stmt->execute();

// Lấy kết quả
$result = $stmt->get_result();

// Chuyển kết quả thành một mảng
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}


$stmt->close();
$conn->close();

usort($data, function($a, $b) use ($idtimkiemScores) {
    $scoreA = $idtimkiemScores[$a['bv_ma']] ?? 0;
    $scoreB = $idtimkiemScores[$b['bv_ma']] ?? 0;
    return $scoreB <=> $scoreA; 
});

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
