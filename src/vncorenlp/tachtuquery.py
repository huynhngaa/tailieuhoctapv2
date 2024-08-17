import py_vncorenlp
from pymongo import MongoClient
import os

# Kết nối đến MongoDB
client = MongoClient("mongodb://localhost:27017/")
db = client["Test"]  # Thay thế bằng tên thực tế của cơ sở dữ liệu
input_collection = db["query"]  # Thay thế bằng tên bộ sưu tập chứa dữ liệu đầu vào
output_collection = db["tachtuquery"]  # Thay thế bằng tên bộ sưu tập mong muốn cho dữ liệu đã đánh dấu
index_collection = db["chimucquery"]  # Thay thế bằng tên bộ sưu tập cho chỉ mục nghịch đảo

# Xóa dữ liệu hiện có trong bộ sưu tập đã đánh dấu và chỉ mục nghịch đảo
output_collection.delete_many({})
index_collection.delete_many({})

# Tải mô hình VnCoreNLP và khởi tạo
py_vncorenlp.download_model(save_dir='C:/xampp/htdocs/VnCoreNLP-master/models/')
model = py_vncorenlp.VnCoreNLP(annotators=["wseg"], save_dir='C:/xampp/htdocs/VnCoreNLP-master/models/')

# Tạo từ điển để lưu trữ chỉ mục nghịch đảo
inverted_index = {}

# Xử lý từng tài liệu
all_documents = input_collection.find()
for input_document in all_documents:
    input_text = input_document["noidung"]  # Lấy dữ liệu từ trường "text"
    
    # Annotate dữ liệu đầu vào
    annotated_text = model.annotate_text(input_text)

    # Chuyển đổi annotated_text từ điển thành chuỗi JSON
    annotated_text_str = str(annotated_text)
    
    # Lưu dữ liệu đã đánh dấu vào tệp văn bản
    annotated_file_path = "C:/xampp/htdocs/VnCoreNLP-master/ketqua.txt"
    with open(annotated_file_path, "w", encoding="utf-8") as file:
        file.write(annotated_text_str)
    
    # Đọc dữ liệu từ tệp văn bản đã chú thích
    with open(annotated_file_path, "r", encoding="utf-8") as file:
        annotated_content = file.read()

    # Chuyển đổi chuỗi JSON thành từ điển
    annotated_data = eval(annotated_content)
    words_array = []
    for doc_index, words_info in annotated_text.items():
        for word_info in words_info:
            word = word_info['wordForm'].lower()
            words_array.append(word)
            # Cập nhật chỉ mục nghịch đảo
            if word in inverted_index:
                inverted_index[word] += 1
            else:
                inverted_index[word] = 1

    # Thêm mảng các từ vào tài liệu hiện tại
    input_document["tachtu"] = words_array
    
    # Chèn tài liệu vào cơ sở dữ liệu đã đánh dấu
    output_collection.insert_one({
        'wordForms': words_array,  # Tên trường có thể thay đổi tùy thuộc vào yêu cầu của bạn
    })

# Chèn chỉ mục nghịch đảo vào bộ sưu tập chimucquery
for word, count in inverted_index.items():
    index_collection.insert_one({
        "word": word,
        "count": count
    })

print("Đã chú thích, lưu dữ liệu vào các tài liệu và tạo chỉ mục nghịch đảo thành công!")
