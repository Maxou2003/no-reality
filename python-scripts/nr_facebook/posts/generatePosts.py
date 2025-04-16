import json
import mysql.connector
import sys
import os
sys.path.insert(0, 'python-scripts')
from postCreation import PostGenerator
from getPostPictures import download_images_with_api
from generateJson import generate_json
from config import HOST, USER, PASSWORD, DATABASE_FACEBOOK

def load_posts(post_file):
    if not os.path.exists(post_file):
        print(f"Error: The file '{post_file}' does not exist.")
        sys.exit(1)  # Exit the program with an error code
    with open(post_file, "r") as file:
        data = json.load(file)
        posts = data.get("posts", [])
        nb_posts = len(posts)
        print(f"{nb_posts} posts trouvés dans le fichier JSON.")
    return posts, nb_posts

def get_last_page_of_post_associated_with_theme(theme, instance_id):
    """
    Get the last page of posts associated with a theme from the database.
    :param theme: str - The theme to search for.
    :return: int - The last page number.
    """
    # Placeholder for actual database query to get the last page number
    conn = mysql.connector.connect(
        host=HOST,
        user=USER,
        password=PASSWORD,
        database=DATABASE_FACEBOOK
    )
    cursor = conn.cursor()
    query = """
        SELECT post_picture_path 
        FROM posts 
        WHERE instance_id = %s AND post_picture_path LIKE %s
        ORDER BY post_picture_path DESC
        LIMIT 1
    """
    theme_pattern = f"{theme}_%_%"  # Matches the format theme_page_index
    cursor.execute(query, (instance_id, theme_pattern))
    result = cursor.fetchone()
    
    if result:
        last_picture_path = result[0]
        # Extract the page number from the path (assuming format theme_page_index)
        try:
            page = int(last_picture_path.split('_')[1])
            return page
        except (ValueError, IndexError) as e:
            print(f"Error parsing page number from post_picture_path: {e}")
            return 0
    else:
        return 0

def generate_posts(DATABASE_NAME, INSTANCE_ID, INSTANCE_NAME, THEME, ENGLISH_THEME, PEXEL_API_KEY, POST_FILE, NUMBER_POSTS, PRECISIONS):
    post_pictures_directory = f"web/public/{INSTANCE_NAME}/img/post_img"

    generate_json(number_posts=NUMBER_POSTS, theme=THEME, file=POST_FILE, min_comments=1, max_comments=5, precisions=PRECISIONS)

    posts, nb_posts = load_posts(POST_FILE)
    
    starting_page = get_last_page_of_post_associated_with_theme(ENGLISH_THEME, INSTANCE_ID) + 1
    print(f"Last page of posts associated with theme '{ENGLISH_THEME}': {starting_page}")
    # Télécharger les images associées aux posts
    all_posts_pathes = download_images_with_api(ENGLISH_THEME, PEXEL_API_KEY, nb_posts, post_pictures_directory, starting_page)

    generator = PostGenerator(DATABASE_NAME, INSTANCE_ID, ENGLISH_THEME, PEXEL_API_KEY, posts)
    generator.generate_posts(post_per_person=30, location="Angers", posts_pathes=all_posts_pathes)

if __name__ == "__main__":
    DATABASE_NAME = "nr_facebook"
    INSTANCE_ID = 1
    INSTANCE_NAME = "love"
    THEME = "amour"
    ENGLISH_THEME = "love"
    PEXEL_API_KEY = "0NMkYhKereL0Ne2PfmTECpAF7SFgy9vGzlWMY2ieB1ByvDGUpKzS3mJn"
    POST_FILE = "python-scripts/nr_facebook/posts/posts.json"

    NUMBER_POSTS = 10

    PRECISIONS = ""
    generate_posts(DATABASE_NAME, INSTANCE_ID, INSTANCE_NAME, THEME, ENGLISH_THEME, PEXEL_API_KEY, POST_FILE, NUMBER_POSTS, PRECISIONS)