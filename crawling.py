# buat crawling data
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from urllib.parse import quote_plus # biar bisa encode URL dari spasi ke +

# buat ambil argumen
import sys

# buat preprocessing
import nltk 

# lainnya
import pandas as pd

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
            print("(SYSTEM) FOUND AUTHOR PROFILE LINK:", link.get_attribute("href"))
            driver.get(link.get_attribute("href"))
            break
        
    return author_links

# fungsi yang dijalankan saat load awal
def onLoad():
    print("(SYSTEM) INITIAL URL:",initial_url)
    driver.get(initial_url)
    print("(SYSTEM) MINIMIZING WINDOW...")
    driver.minimize_window() # biar UI nya clean aja
    import time
    print("(SYSTEM) WAITING 10 SECONDS BEFORE SCRAPING...")
    time.sleep(10)
     
params_keyword = sys.argv[1] if len(sys.argv) > 1 else None
# params_author  = sys.argv[2] if len(sys.argv) > 2 else None
params_author  = "Joko siswantoro" #pakai dummy dulu biar gampang testing
params_limit   = int(sys.argv[3]) if len(sys.argv) > 3 else 5

chrm_ops = webdriver.ChromeOptions()
chrm_ops.add_argument('--no-sandbox')
chrm_ops.add_argument('--disable-dev-shm-usage')

driver = webdriver.Chrome(options=chrm_ops)

# URL utama sebelum masuk ke url author
initial_url = build_google_scholar_url(author=params_author)

if params_author:
    onLoad()
    # cari link profile author
    author_links = get_author_profile_url(params_author)

# cari artikel dari author yg udh dipilih & batasi sesuai params_limit

selector_articles_links = []
i = 0
for link_article in driver.find_elements(By.CSS_SELECTOR, 'tr.gsc_a_tr'):
    if i >= params_limit:
        break
    title = link_article.find_element(By.CSS_SELECTOR, 'a.gsc_a_at').text
    detail_link = link_article.find_element(By.CSS_SELECTOR, 'a.gsc_a_at').get_attribute('href')

    selector_articles_links.append({
        'title': title,
        'detail_link': detail_link
    })
    i += 1

# KOLOM YG DIBUTUHKAN JUDUL ARTIKEL, PENULIS, TANGGAL RILIS, NAMA JURNAL, SITASI, LINK JURNAL, SIMILARITY
articles_detail = []
for article in selector_articles_links:
    driver.get(article['detail_link'])

    fields = driver.find_elements(By.CSS_SELECTOR, "div.gs_scl")    

    judul_artikel = driver.find_element(By.CSS_SELECTOR, "a.gsc_oci_title_link").text

    link_jurnal = driver.find_element(By.CSS_SELECTOR, "#gsc_oci_title a.gsc_oci_title_link").get_attribute("href")


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
        label = field.find_element(By.CSS_SELECTOR, ".gsc_oci_field").text.lower()
        value = field.find_element(By.CSS_SELECTOR, ".gsc_oci_value").text
        
        if "authors" in label:
            data["penulis"] = value
        elif "publication date" in label:
            data["tanggal_rilis"] = value
        elif "journal" in label:
            data["nama_jurnal"] = value
        elif "total citations" in label:
            data["sitasi"] = value
    articles_detail.append(data)
    print("DATA ARTICLE:", data)
    
# simpan ke dataframe pandas
df = pd.DataFrame(articles_detail)
print(df.head())
    