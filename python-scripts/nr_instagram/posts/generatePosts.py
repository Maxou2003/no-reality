import json
from postCreation import PostGenerator
from getPostPictures import download_images_with_api
from generateJson import create_json
import os

# Définition des paramètres
DATABASE_NAME = "nr_instagram"
INSTANCE_ID = 1
INSTANCE_NAME = "hates"
THEME = "art"
ENGLISH_THEME = "art"
PEXEL_API_KEY = "0NMkYhKereL0Ne2PfmTECpAF7SFgy9vGzlWMY2ieB1ByvDGUpKzS3mJn"
POST_FILE = "art_posts.json"

NUMBER_POSTS = 50

PRECISIONS = ""

post_pictures_directory = f"web/instagram/public/{INSTANCE_NAME}/img/post_img"

create_json(number_posts=NUMBER_POSTS, theme=THEME, file=POST_FILE, min_comments=1, max_comments=5, precisions=PRECISIONS)
def load_posts(post_file):
    with open(post_file, "r") as file:
        data = json.load(file)
        posts = data.get("posts", [])
        nb_posts = len(posts)
        print(f"{nb_posts} posts trouvés dans le fichier JSON.")
    return posts, nb_posts


posts, nb_posts = load_posts(POST_FILE)

# Télécharger les images associées aux posts
download_images_with_api(ENGLISH_THEME, PEXEL_API_KEY, nb_posts, post_pictures_directory)


generator = PostGenerator(DATABASE_NAME, INSTANCE_ID, ENGLISH_THEME, PEXEL_API_KEY, posts)
generator.generate_posts(post_per_person=30, location="Angers")