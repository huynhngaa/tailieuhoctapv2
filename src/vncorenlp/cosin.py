import py_vncorenlp
rdrsegmenter = py_vncorenlp.VnCoreNLP(annotators=["wseg"], save_dir='C:/xampp/htdocs/VnCoreNLP-master/models/')
text = "Hôm nay tôi buồn. Ngày mai vui vẻ trở lại."
output = rdrsegmenter.annotate_text(text)
rdrsegmenter.print_out(output)