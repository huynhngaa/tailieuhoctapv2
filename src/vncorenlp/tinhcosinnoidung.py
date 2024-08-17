from pymongo import MongoClient
import math
client = MongoClient('mongodb://localhost:27017/') 
db = client['Test']  
chimucquery = db['chimucquery']
chimuctieude = db['chimucnoidung']
baiviet = db['baiviet'] 

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
        doc_data = chimuctieude.find_one({"word": word})
        if doc_data is None:
            print(f"Word '{word}' not found in chimuctieude.")
            continue
        df = len(doc_data['doc'])
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
        doc_data = chimuctieude.find_one({"word": word})
        if doc_data is None:
            continue
        for doc_info in doc_data['doc']:
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
query_words = [word_data['word'] for word_data in chimucquery.find()]
top_documents = get_top_documents(query_words, num_top=10)

if top_documents is not None:
    for doc_id, score in top_documents:
        print(f"Document ID: {doc_id}, Cosine Similarity Score: {score}")
