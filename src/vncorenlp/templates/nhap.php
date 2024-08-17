<?php
// Kết nối đến MongoDB
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Tên cơ sở dữ liệu và tên bảng
$database = "ten-csdl";
$collection = "baiviet";

// Dữ liệu cần chèn
$dataToInsert = [
    ['id' => 1, 'text' => 'Nội dung bài viết 1'],
    ['id' => 2, 'text' => 'Nội dung bài viết 2'],
    ['id' => 3, 'text' => 'Nội dung bài viết 3'],
];

// Tạo một danh sách các bản ghi để chèn
$bulk = new MongoDB\Driver\BulkWrite;

foreach ($dataToInsert as $doc) {
    $bulk->insert($doc);
}

// Thực hiện chèn dữ liệu vào bảng "baiviet"
$manager->executeBulkWrite("$database.$collection", $bulk);

echo "Dữ liệu đã được chèn thành công.";
?>
