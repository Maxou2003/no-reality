import requests

pexel_api_key = "0NMkYhKereL0Ne2PfmTECpAF7SFgy9vGzlWMY2ieB1ByvDGUpKzS3mJn"


def download_images_with_api(searchContent, api_key, max, pictures_dir, starting_page=1):
    """
    Download images from Pexels API based on search content.\n
    :param searchContent: str - Content to search for images\n
    :param api_key: str - API key for Pexels\n
    :param max: int - Maximum number of images to download\n
    :param pictures_dir: str - Directory to save downloaded images\n
    :param starting_index: int - Starting index of images\n
    :return: int - Total number of images downloaded\n
    """
    total_downloaded = 0
    per_page = 10
    page = starting_page
    post_pathes = []
    
    while total_downloaded<max:
        url = f"https://api.pexels.com/v1/search?query={searchContent}&per_page={per_page}&page={page}"
        print(url)
        headers = {"Authorization": api_key}
        response = requests.get(url, headers=headers)
        if response.status_code != 200:
            print(f"Failed to fetch images: {response.status_code}")
            break
        
        data = response.json()
 
        print("Total results:", data.get("total_results"))
        photos = data.get('photos', [])
        print(f"{len(photos)} photos availabe")
    
        print(f"total downloaded: {total_downloaded}, page: {page}")
        if not photos:
            print("no photo left")
            break  # No more photos available
        for i, photo in enumerate(photos):
            image_url = photo['src']['large2x']
            print(photo['src']['original'])
            try:
                img_headers = {"User-Agent": "Mozilla/5.0"}
                img_response = requests.get(image_url, headers=img_headers)
                if img_response.status_code == 200:
                    with open(f"{pictures_dir}/{searchContent}_{page}_{i}.jpg", 'wb') as f:
                        f.write(img_response.content)
                    total_downloaded += 1
                    post_pathes.append(f"{searchContent}_{page}_{i}.jpg")
                else:
                    print(f"Failed to download {image_url}: {img_response.status_code}")
            except Exception as e:
                print(f"Failed to download {image_url}: {e}")

            if total_downloaded == max:
                break

        # Break out if we've reached the last page
        if len(photos) < per_page:
            print("we've reached the last page")
            break
        page += 1
    
    return post_pathes

if __name__ == "__main__":
    print(f"{download_images_with_api('japan', pexel_api_key,10, 'web/instagram/public/test/img/post_img', starting_index=10)} images téléchargées")



