from pymongo import MongoClient
from bson import ObjectId
from vncorenlp import VnCoreNLP
import py_vncorenlp

# Connect to MongoDB
client = MongoClient('mongodb://localhost:27017/')
db = client['Test']
baiviet_collection = db['baiviet']
timkiem_collection = db['timkiem']

# Connect to VnCoreNLP
py_vncorenlp.download_model(save_dir='C:/xampp/htdocs/VnCoreNLP-master/models/')
vncorenlp = py_vncorenlp.VnCoreNLP(save_dir='C:/xampp/htdocs/VnCoreNLP-master/models/')

# Function to build inverted index
def build_inverted_index():
    # Ensure that timkiem collection is initially empty
    timkiem_collection.delete_many({})
    
    # Iterate through documents in baiviet collection
    for doc in baiviet_collection.find({}, {"_id": 1, "text": 1}):
        doc_id = doc["_id"]
        text = doc["text"]
        tokens = vncorenlp.word_segment(text)
        
        for token in tokens:
            term = token[0]
            # Insert term and doc_id into timkiem collection
            timkiem_collection.update_one({'text': term}, {'$addToSet': {'doc_id': str(doc_id)}}, upsert=True)

# Search function
def search(query):
    results = timkiem_collection.find({"text": query}, {"_id": 0, "doc_id": 1})
    matching_doc_ids = [result["doc_id"] for result in results]
    return matching_doc_ids

# Sample query
# Sample query
query = "K"  # Ensure case matches what's in the database
results = search(query)

def print_text_by_doc_id(doc_id_list):
    flattened_doc_ids = [doc_id for sublist in doc_id_list for doc_id in sublist]
    for doc_id in flattened_doc_ids:
        document = baiviet_collection.find_one({"_id": ObjectId(doc_id)})
        if document:
            text = document.get("text")
            print(f"Document ID {doc_id}: {text}")
        else:
            print(f"Document ID {doc_id} not found.")

if results:
    print("Found in documents:", results)
    print_text_by_doc_id(results)
else:
    print("Not found in any documents.")
