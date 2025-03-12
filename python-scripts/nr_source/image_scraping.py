from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.firefox.service import Service
from selenium.webdriver.firefox.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import requests
import os

def url_maker(gender,ethnicity):
    if gender == 'M':
        url_gender = 'male'
    else:
        url_gender = 'female'
    if ethnicity == 0:
        url_ethnicity = 'white'
    elif ethnicity == 1:
        url_ethnicity = 'black'
    elif ethnicity == 2:
        url_ethnicity = 'asian'
    elif ethnicity == 3:
        url_ethnicity= 'latino'
    return f'https://generated.photos/faces/young-adult/{url_ethnicity}-race/{url_gender}'

def get_last_index(path):
    existing_files = [f for f in os.listdir(path) if f.startswith("image_") and f.endswith(".jpg")]
    if existing_files:
        max_index = max([int(f.split("_")[1].split(".")[0]) for f in existing_files])
    else:
        max_index = -1  # Start from 0 if no files exist
    return max_index

def download_images(num_img=100, gender='M', ethnicity=0, path='web/profile_pictures'):
    """
    returns: a list of the paths of the downloaded images
    """
    # Configure Firefox options
    firefox_options = Options()
    # firefox_options.add_argument("--headless")  # Uncomment for headless mode
    firefox_options.set_preference("detach", True)

    # Setup driver
    driver = webdriver.Firefox(options=firefox_options)
    
    try:
        print("Loading website...")
        url = url_maker(gender, ethnicity)
        driver.get(url)
        
        # Check for cookies popup
        print("Checking for cookies popup...")
        try:
            cookie_button = WebDriverWait(driver, 20).until(
                EC.element_to_be_clickable((By.CLASS_NAME, "cookies-agree"))
            )
            print("Cookies popup found, accepting...")
            cookie_button.click()
        except Exception as e:
            print(f"No cookies popup found or timeout: {e}")
            with open("page_source.html", "w", encoding="utf-8") as f:
                f.write(driver.page_source)
            print("Page source saved to page_source.html for debugging")
        
        # Click "Load More" button x times
        print("Loading more images...")
        for i in range(num_img // 30):  # 30 images per load
            try:
                # Ensure it's clickable
                load_more_button = WebDriverWait(driver, 30).until(
                    EC.element_to_be_clickable((By.CLASS_NAME, "loadmore-btn"))
                )
                driver.execute_script("arguments[0].scrollIntoView(true);", load_more_button)
                load_more_button.click()
                print(f"Clicked 'Load More' {i+1}/{num_img//30}")
                time.sleep(5)  # Increased wait time for content to load
            except Exception as e:
                print(f"Could not click load more button: {e}")
                with open(f"load_more_error_{i}.html", "w", encoding="utf-8") as f:
                    f.write(driver.page_source)
                print(f"Page source saved to load_more_error_{i}.html")
                break
        
        # Create directory for images
        if not os.path.exists(path):
            os.makedirs(path)
        
        # Find all images
        print("Finding images...")
        image_elements = driver.find_elements(By.CSS_SELECTOR, "div.grid-photos img")
        print(f"Found {len(image_elements)} images")
        

        max_index = get_last_index(path)

        # Download each image
        images_paths = []
        i = 0;
        for idx, img in enumerate(image_elements):
            img_url = img.get_attribute("src")
            if img_url:
                try:
                    response = requests.get(img_url, stream=True, timeout=10)
                    if response.status_code == 200:
                        file_path = f"{path}/image_{max_index + idx + 1}.jpg"
                        with open(file_path, 'wb') as f:
                            f.write(response.content)
                            images_paths.append(file_path)
                        print(f"Downloaded image {idx + 1}/{num_img}")
                except Exception as e:
                    print(f"Error downloading image {idx}: {e}")
            if i==num_img:
                break
            i+=1
        
        print(f"Total images downloaded: {i}")
        
    except Exception as e:
        print(f"Unexpected error: {e}")
        with open("error_page_source.html", "w", encoding="utf-8") as f:
            f.write(driver.page_source)
        print("Error page source saved to error_page_source.html")
    finally:
        print("Script completed (browser remains open)")
        driver.quit()
        return images_paths
    
if __name__ == "__main__":
    download_images(num_img=29, gender='M', ethnicity=2, path='web/instagram/public/img/profile_picture')