from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from urllib.parse import quote_plus
import sys

def build_google_scholar_url(keyword=None):
    if not keyword:
        raise ValueError("Minimal keyword harus diisi")

    return (
        # default url nya google scholar
        "https://scholar.google.com/scholar?"f"hl=en&as_sdt=0%2C5&q={quote_plus(keyword)}"
    )

def onLoad():
    print("(SYSTEM) URL USE:",url_used)
    driver.get(url_used)

    print("(SYSTEM) MINIMIZING WINDOW...")
    driver.minimize_window() # biar UI nya clean aja
    import time
    print("(SYSTEM) WAITING 10 SECONDS BEFORE SCRAPING...")
    time.sleep(10)

chrm_ops = webdriver.ChromeOptions()
chrm_ops.add_argument('--no-sandbox')
chrm_ops.add_argument('--disable-dev-shm-usage')

driver = webdriver.Chrome(options=chrm_ops)

params_keyword = sys.argv[1] if len(sys.argv) > 1 else None
params_author = sys.argv[2] if len(sys.argv) > 2 else None

# masih dummy, belum dipake untuk author search
url_used = build_google_scholar_url(keyword="Joko Siswantoro")

onLoad()
i = 0
for article in driver.find_elements(By.CSS_SELECTOR, "div.gs_r"):
    if(i==5):break
    else:
        title_element = article.find_element(By.TAG_NAME, "h3")
        title = title_element.text
        link = title_element.find_element(By.TAG_NAME, "a").get_attribute("href") if title_element.find_elements(By.TAG_NAME, "a") else "No link available"
        snippet = article.find_element(By.CLASS_NAME, "gs_rs").text if article.find_elements(By.CLASS_NAME, "gs_rs") else "No snippet available"
    
        print(f"Article {i+1}:")
        print(f"Title: {title}")
        print(f"Link: {link}")
        print(f"Snippet: {snippet}")
        print("-" * 80)        
        
    i += 1