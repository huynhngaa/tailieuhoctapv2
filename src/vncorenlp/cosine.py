from pymongo import MongoClient
import math

# Kết nối đến MongoDB
client = MongoClient('mongodb://localhost:27017/')
db = client['Test']
chimucquery = db['chimucquery']
chimuctieude = db['chimuctieude']
chimucnoidung = db['chimucnoidung']
baiviet = db['baiviet']
idtimkiem = db['idtimkiem']

def get_total_document_count():
    return baiviet.count_documents({"trangthai": "Đã duyệt"})

def calculate_query_tf_idf(query_words):
    tf_idf_scores = {}
    for word in query_words:
        query_data = chimucquery.find_one({"word": word})
        if query_data is None:
            print(f"Word '{word}' not found in chimucquery.")
            continue
        
        tf = query_data['count']
        doc_data_tieude = chimuctieude.find_one({"word": word})
        doc_data_noidung = chimucnoidung.find_one({"word": word})
        
        if doc_data_tieude is None and doc_data_noidung is None:
            print(f"Word '{word}' not found in chimuctieude or chimucnoidung.")
            continue
        
        df = 0
        if doc_data_tieude:
            df += len(doc_data_tieude['doc'])
        if doc_data_noidung:
            df += len(doc_data_noidung['doc'])
        
        N = get_total_document_count()
        if N == 0:
            print("Total document count is 0. Cannot calculate IDF.")
            return None
        idf = math.log(N / df)
        tf_idf = tf * idf
        tf_idf_scores[word] = tf_idf

    return tf_idf_scores

def calculate_document_scores(tf_idf_scores):
    document_scores = {}
    
    for word, tf_idf in tf_idf_scores.items():
        doc_data_tieude = chimuctieude.find_one({"word": word})
        doc_data_noidung = chimucnoidung.find_one({"word": word})
        
        if doc_data_tieude:
            for doc_info in doc_data_tieude['doc']:
                doc_id = doc_info['doc_id']
                if doc_id not in document_scores:
                    document_scores[doc_id] = {}
                document_scores[doc_id][word] = tf_idf * doc_info['count']
        
        if doc_data_noidung:
            for doc_info in doc_data_noidung['doc']:
                doc_id = doc_info['doc_id']
                if doc_id not in document_scores:
                    document_scores[doc_id] = {}
                document_scores[doc_id][word] = tf_idf * doc_info['count']

    return document_scores

def calculate_cosine_similarity(query_vector, doc_vector):
    dot_product = sum(query_vector.get(word, 0) * doc_vector.get(word, 0) for word in query_vector)
    norm_query = math.sqrt(sum(v ** 2 for v in query_vector.values()))
    norm_doc = math.sqrt(sum(v ** 2 for v in doc_vector.values()))
    if norm_query == 0 or norm_doc == 0:
        return 0
    return dot_product / (norm_query * norm_doc)

def get_top_documents(query_words, num_top=10):
    tf_idf_scores = calculate_query_tf_idf(query_words)
    
    if tf_idf_scores is None:
        return None

    document_scores = calculate_document_scores(tf_idf_scores)
    
    cosine_scores = {}
    query_vector = tf_idf_scores

    for doc_id, doc_vector in document_scores.items():
        cosine_scores[doc_id] = calculate_cosine_similarity(query_vector, doc_vector)

    sorted_documents = sorted(cosine_scores.items(), key=lambda x: x[1], reverse=True)
    top_documents = sorted_documents[:num_top]
    
    return top_documents

def update_idtimkiem(top_documents):
    # Xóa tất cả các tài liệu hiện có trong bảng idtimkiem
    idtimkiem.delete_many({})

    for doc_id, score in top_documents:
        idtimkiem.insert_one({"id": doc_id, "score": score})

def get_sorted_idtimkiem():
    sorted_docs = idtimkiem.find().sort("score", -1)
    return sorted_docs

# Lấy danh sách các từ khóa từ chimucquery
query_words = [word_data['word'] for word_data in chimucquery.find()]

# Tìm các tài liệu tốt nhất dựa trên từ khóa
top_documents = get_top_documents(query_words, num_top=10)

if top_documents is not None:
    for doc_id, score in top_documents:
        print(f"Document ID: {doc_id}, Cosine Similarity Score: {score}")

    # Cập nhật bảng idtimkiem với kết quả tìm kiếm mới
    update_idtimkiem(top_documents)

    # Lấy và in danh sách các tài liệu từ bảng idtimkiem đã được sắp xếp
    sorted_idtimkiem = get_sorted_idtimkiem()
    for doc in sorted_idtimkiem:
        print(f"Document ID: {doc['id']}, Score: {doc['score']}")
