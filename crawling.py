# buat crawling data
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from urllib.parse import quote_plus # biar bisa encode URL dari spasi ke +

# buat ambil argumen
import sys

# buat preprocessing
import re #untuk menghapus tanda baca
import nltk 
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize
nltk.download('punkt')
nltk.download('punkt_tab')
nltk.download('stopwords')

# buat itung TF-IDF
from sklearn.feature_extraction.text import TfidfVectorizer
import numpy as np

# buat implementasi cosine similarity
from sklearn.metrics.pairwise import cosine_similarity

# lainnya
import pandas as pd
import json

# buat bikin URL Google Scholar untuk pencarian author menggunakan advaced search
def build_google_scholar_url(author):
    if not author:
        raise ValueError("Minimal author harus diisi")

    return (
        "https://scholar.google.com/scholar?"
        "as_q=&as_epq=&as_oq=&as_eq=&as_occt=any"
        f"&as_sauthors={quote_plus(author)}"
        "&as_publication=&as_ylo=&as_yhi=&hl=en&as_sdt=0%2C5"
    )

# buat dapetin urlnya profile author
def get_author_profile_url(author_name):
    author_links = driver.find_elements(By.CSS_SELECTOR, 'h4.gs_rt2 a[href^="/citations?user="]')

    for link in author_links:
        if author_name.lower() in link.text.lower():
            driver.get(link.get_attribute("href"))
            return True            
        
    return False
        

# fungsi yang dijalankan saat load awal
def onLoad():
    driver.get(initial_url)
    driver.minimize_window() # biar UI nya clean aja
    import time
     
def preprocessing_text(title):
    # ubah ke lowercase
    title = title.lower()
    
    # hapus tanda baca
    title = re.sub(r'[^\w\s]', ' ', title)

    tokens = word_tokenize(title)
    
    # hapus stop words yang inggris
    stop_words = set(stopwords.words('english'))

    clean_tokens = []
    
    # hapus stop words dan kata yang panjangnya <= 2
    for word in tokens:
        if word not in stop_words and len(word) > 2:
            clean_tokens.append(word)

    return ' '.join(clean_tokens)


# parameter dari index.php
params_keyword = sys.argv[1]
params_author  = sys.argv[2]
params_limit   = int(sys.argv[3])

chrm_ops = webdriver.ChromeOptions()
chrm_ops.add_argument('--no-sandbox')
chrm_ops.add_argument('--disable-dev-shm-usage')

driver = webdriver.Chrome(options=chrm_ops)

# URL utama sebelum masuk ke url author
initial_url = build_google_scholar_url(author=params_author)

if params_author:
    onLoad()
    # cari link profile author
    # NOTE DI SINI BISA AJA GADA AUTHOR SAMA SEKALI YG COCOK, JADI HARUSNYA DIBUAT HANDLE ERROR NYA
    author_found = get_author_profile_url(params_author)
    if not author_found:
        error_response = {
            "status": "error",
            "message": "Author tidak ditemukan"
        }
        print(json.dumps(error_response))
        driver.quit()
        sys.exit(0)

# cari artikel dari author yg udh dipilih & batasi sesuai params_limit
selector_articles_links = []
i = 0
for link_article in driver.find_elements(By.CSS_SELECTOR, 'tr.gsc_a_tr'):
    if i >= params_limit:
        break
    title = link_article.find_element(By.CSS_SELECTOR, 'a.gsc_a_at').text
    detail_link = link_article.find_element(By.CSS_SELECTOR, 'a.gsc_a_at').get_attribute('href')

    # simpan ke list 
    selector_articles_links.append({
        'title': title,
        'detail_link': detail_link
    })
    i += 1

# KOLOM YG DIBUTUHKAN JUDUL ARTIKEL, PENULIS, TANGGAL RILIS, NAMA JURNAL, SITASI, LINK JURNAL, SIMILARITY
articles_detail = []
for article in selector_articles_links:
    driver.get(article['detail_link'])

    # dari struktur div yg ada, google scholar membuat div.gs_scl untuk setiap informasi detail
    # jadi kita cari semua div.gs_scl, kemudian kita menemukan bahwa di dalamnya ada label dan value
    fields = driver.find_elements(By.CSS_SELECTOR, "div.gs_scl")    

    # untuk judul artikel dan link jurnal, diambil terpisah karena strukturnya beda
    judul_artikel = driver.find_element(By.CSS_SELECTOR, "a.gsc_oci_title_link").text
    link_jurnal = driver.find_element(By.CSS_SELECTOR, "#gsc_oci_title a.gsc_oci_title_link").get_attribute("href")

    # siapkan dict untuk nyimpen data
    data = {
        "judul_artikel": judul_artikel,
        "penulis": None,
        "tanggal_rilis": None,
        "nama_jurnal": None,
        "sitasi": None,
        "link_jurnal": link_jurnal,
        "similarity": None
    }

    for field in fields:
        # cari label dan value
        label = field.find_element(By.CSS_SELECTOR, ".gsc_oci_field").text.lower()
        value = field.find_element(By.CSS_SELECTOR, ".gsc_oci_value").text
        
        # cek perlabelan untuk dimasukkan ke data
        if "authors" in label:
            data["penulis"] = value
        elif "publication date" in label:
            data["tanggal_rilis"] = value
        elif "journal" in label:
            data["nama_jurnal"] = value
        elif "total citations" in label:
            # ini biasanya isinya "Cited by 123" nah yang dimau cuma angkanya aja
            citation_text = field.find_element(By.CSS_SELECTOR, ".gsc_oci_value a").text 
            # ngeganti tulisan "Cited by" jadi kosong terus di strip biar bersih
            # strip itu ngapus spasi di awal dan akhir
            data["sitasi"] = citation_text.replace("Cited by", "").strip()
            articles_detail.append(data)
    
# simpan ke dataframe pandas
df = pd.DataFrame(articles_detail)

# preprocessing judul artikel
df['preprocessing_judul_artikel'] = df['judul_artikel'].apply(preprocessing_text)

# hitung TF-IDF
# gunakan norm='l2' untuk menormalkan vektor TF-IDF agar perhitungan cosine similarity tidak dipengaruhi panjang teks
# Parameter norm='l2' digunakan untuk melakukan normalisasi vektor TF-IDF 
# agar setiap dokumen memiliki panjang vektor yang sama. 
# Hal ini bertujuan untuk menghindari bias akibat perbedaan panjang judul artikel dan 
# memastikan bahwa perhitungan cosine similarity merepresentasikan kemiripan makna, bukan jumlah kata.
tfidf = TfidfVectorizer(norm='l2', sublinear_tf=True)

# fit transform
vector_tfidf = tfidf.fit_transform(df['preprocessing_judul_artikel'])

# preprocessing keyword
preprocessed_keyword = preprocessing_text(params_keyword)
vector_keyword = tfidf.transform([preprocessed_keyword])

# implementasi cosine similarity
similarity_scores = []

for i in range(vector_tfidf.shape[0]):
    similarity = cosine_similarity(vector_tfidf[i], vector_keyword)[0][0]
    similarity_scores.append(similarity)
    
df['similarity'] = similarity_scores

# sorting result berdasarkan similarity
df_sorted = df.sort_values(by='similarity', ascending=False)

# konversi dataframe ke list of dict
result = df_sorted.to_dict(orient='records')
result = {
    "status": "success",
    "data": result
}
# kirim sebagai JSON
print(json.dumps(result))
