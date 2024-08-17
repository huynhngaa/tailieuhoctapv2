<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=UTF-8');


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die(json_encode(["error" => "Không kết nối: " . $conn->connect_error]));
}

$conn->set_charset("utf8");
$id = intval($_GET['bv_ma']); 


$sql = "SELECT DISTINCT b.*, c.nd_hoten, c.nd_username, c.nd_hinh, d.*, CURRENT_TIMESTAMP
        FROM bai_viet a
        LEFT JOIN binh_luan b ON a.bv_ma = b.bv_ma
        JOIN nguoi_dung c ON b.nd_username = c.nd_username
        JOIN vai_tro d ON c.vt_ma = d.vt_ma
        LEFT JOIN rep_bl r ON b.bl_ma = r.bl_cha
        WHERE a.bv_ma = '$id' AND b.trangthai = 1
        AND b.bl_ma NOT IN (SELECT bl_con FROM rep_bl WHERE bl_con IS NOT NULL)";

$result = $conn->query($sql);

if ($result === false) {
    error_log('Query Error: ' . $conn->error);
    echo json_encode(["error" =>  $conn->error]);
    exit;
}

$comments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $parentCommentId = $row['bl_ma'];
        $row['replies'] = getReplies($parentCommentId, $conn);
        $comments[] = $row;
    }
}


echo json_encode($comments);

function getReplies($parentCommentId, $conn) {
    $replySql = "WITH RECURSIVE binh_luan_paths AS (
                    SELECT b1.bl_ma AS bl_cha, b2.bl_ma AS bl_con, 1 AS lvl
                    FROM binh_luan b1
                    LEFT JOIN rep_bl r1 ON b1.bl_ma = r1.bl_cha
                    LEFT JOIN binh_luan b2 ON r1.bl_con = b2.bl_ma
                    WHERE b1.bl_ma = $parentCommentId
                    
                    UNION ALL
                    
                    SELECT b1.bl_ma AS bl_cha, b2.bl_ma AS bl_con, bp.lvl + 1 AS lvl
                    FROM binh_luan b1
                    LEFT JOIN rep_bl r1 ON b1.bl_ma = r1.bl_cha
                    LEFT JOIN binh_luan b2 ON r1.bl_con = b2.bl_ma
                    INNER JOIN binh_luan_paths bp ON b1.bl_ma = bp.bl_con
                )
                SELECT bl.*, ngd.*, vt.*, CURRENT_TIMESTAMP 
                FROM binh_luan_paths bp
                INNER JOIN binh_luan bl ON bp.bl_con = bl.bl_ma
                INNER JOIN nguoi_dung ngd ON bl.nd_username = ngd.nd_username
                INNER JOIN vai_tro vt ON ngd.vt_ma = vt.vt_ma
                WHERE bl_con IS NOT NULL AND bl.trangthai = 1
                ORDER BY bp.lvl, bl.bl_ma";

    $result = $conn->query($replySql);
    if ($result === false) {
        error_log('Query Error: ' . $conn->error);
        return [];
    }
    
    $replies = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['replies'] = getReplies($row['bl_ma'], $conn);
            $replies[] = $row;
        }
    }
    return $replies;
}
?>
