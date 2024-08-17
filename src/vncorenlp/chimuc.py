import py_vncorenlp
rdrsegmenter = py_vncorenlp.VnCoreNLP(annotators=["wseg"], save_dir='C:/xampp/htdocs/VnCoreNLP-master/models/')
document1 = "The quick brown fox jumped over the lazy dog."
document2 = "Nguyễn Thị Huỳnh Nga"
output1 = rdrsegmenter.word_segment(document1)
output2 = rdrsegmenter.word_segment(document2)
# Step 1: Tokenize the documents
print(output1)
terms = list(set(output1 + output2))
inverted_index = {}
for term in terms:
	documents = []
	if term in output1:
		documents.append("Document 1")
	if term in output2:
		documents.append("Document 2")
	inverted_index[term] = documents

for term, documents in inverted_index.items():
    print(term, "->", ", ".join(documents))