import math

def calculate_cosine_score(query, inverted_index, documents):
    N = len(documents)
    Scores = [0.0] * N
    Length = [0.0] * N

    # Calculate the scores
    for term in query.split():
        if term in inverted_index:
            postings_list = inverted_index[term]
            for posting in postings_list:
                doc_id = posting["doc_id"]
                tf_td = posting["count"]
                tf_idf_query = 1  # For simplicity, assuming equal weights for all query terms
                tf_idf_doc = (1 + math.log(tf_td, 10)) * math.log(N / len(postings_list), 10)
                Scores[doc_id] += tf_idf_query * tf_idf_doc

    # Normalize the scores
    for doc_id in range(N):
        Length[doc_id] = math.sqrt(sum(x**2 for x in Scores))
        if Length[doc_id] != 0:
            Scores[doc_id] /= Length[doc_id]

    # Get the top K components
    K = min(3, N)  # Change 3 to the desired number of top documents
    top_documents = sorted(enumerate(Scores), key=lambda x: x[1], reverse=True)[:K]

    return top_documents

# Example usage
inverted_index = {
    "em": [
        {"doc_id": 98, "count": 1},
        {"doc_id": 99, "count": 1},
        {"doc_id": 97, "count": 2}
    ]
}

documents = [
    "Nội em là người con gái rất tốt.",
    "Em thích học tập và luôn cố gắng.",
    "Em luôn giữ tinh thần lạc quan."
]

query = "nội em"
result = calculate_cosine_score(query, inverted_index, documents)
print("Top documents:", result)
