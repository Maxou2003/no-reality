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
    if not os.path.exists(path):
        return -1  # If directory doesn't exist, start from 0
        
    existing_files = [f for f in os.listdir(path) if f.startswith("image_") and f.endswith(".jpg")]
    if existing_files:
        max_index = max([int(f.split("_")[1].split(".")[0]) for f in existing_files])
    else:
        max_index = -1  # Start from 0 if no files exist
    return max_index

def download_images(num_img=100, gender='M', ethnicity=0, path='web/profile_pictures', start_index=None):
    """
    returns: a list of the paths of the downloaded images
    
    Parameters:
    num_img: number of images to download
    gender: 'M' or 'F'
    ethnicity: ethnicity code (0, 1, 2, 3)
    path: base directory for saving images
    start_index: starting index for image naming (overrides get_last_index if provided)
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
        
        # Get starting index - use provided start_index if available, otherwise get from filesystem
        max_index = start_index - 1 if start_index is not None else get_last_index(path)
        print(f"Starting image numbering from index {max_index + 1}")
        
        # Create directory for images
        if not os.path.exists(path):
            os.makedirs(path)
        
        # We need to load more images to get past the already downloaded ones
        # We need to load enough images to cover: existing images (max_index) + new images (num_img)
        total_images_needed = max_index + 1 + num_img
        
        # Click "Load More" button until we have enough images to skip past what we've already downloaded
        print(f"Need to load at least {total_images_needed} images to skip past existing ones")
        load_more_count = 0
        while True:
            # Check how many images are currently loaded
            current_images = driver.find_elements(By.CSS_SELECTOR, "div.grid-photos img")
            print(f"Currently loaded {len(current_images)} images")
            
            # If we have enough images to skip past the existing ones and still have num_img left, break
            if len(current_images) >= total_images_needed:
                print(f"Loaded enough images: {len(current_images)}")
                break
                
            try:
                # Ensure it's clickable
                load_more_button = WebDriverWait(driver, 30).until(
                    EC.element_to_be_clickable((By.CLASS_NAME, "loadmore-btn"))
                )
                driver.execute_script("arguments[0].scrollIntoView(true);", load_more_button)
                load_more_button.click()
                load_more_count += 1
                print(f"Clicked 'Load More' {load_more_count} times")
                time.sleep(5)  # Increased wait time for content to load
            except Exception as e:
                print(f"Could not click load more button: {e}")
                with open(f"load_more_error_{load_more_count}.html", "w", encoding="utf-8") as f:
                    f.write(driver.page_source)
                print(f"Page source saved to load_more_error_{load_more_count}.html")
                # If we can't load more, just use what we have
                break
        
        # Find all images
        print("Finding images...")
        image_elements = driver.find_elements(By.CSS_SELECTOR, "div.grid-photos img")
        print(f"Found {len(image_elements)} images")
        
        # Skip the first max_index images (those we've already downloaded)
        # Then download the next num_img images
        skip_count = max_index + 1  # +1 because indices start at 0
        print(f"Skipping first {skip_count} images as they were already downloaded")
        
        # Download each image (starting after the ones we've already downloaded)
        images_paths = []
        downloaded_count = 0
        
        # Use a slice to skip the first max_index+1 images
        for idx, img in enumerate(image_elements[skip_count:]):
            if downloaded_count >= num_img:
                break
                
            img_url = img.get_attribute("src")
            if img_url:
                try:
                    response = requests.get(img_url, stream=True, timeout=10)
                    if response.status_code == 200:
                        file_path = f"{path}/image_{max_index + downloaded_count + 1}.jpg"
                        with open(file_path, 'wb') as f:
                            f.write(response.content)
                            images_paths.append(file_path)
                        downloaded_count += 1
                        print(f"Downloaded image {downloaded_count}/{num_img} as {file_path}")
                except Exception as e:
                    print(f"Error downloading image {idx + skip_count}: {e}")
        
        print(f"Total new images downloaded: {downloaded_count}")
        
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