
from post_generator import PostGenerator

# Définition des paramètres
DATABASE_NAME = "nr_instagram"
INSTANCE_ID = 1
THEME = "sources thermales"
PEXEL_API_KEY = "0NMkYhKereL0Ne2PfmTECpAF7SFgy9vGzlWMY2ieB1ByvDGUpKzS3mJn"
POST_FILE = "mon_fichier.json"

post_pictures_directory = "img"

# Télécharger les images associées aux posts
# download_images_with_api(THEME, PEXEL_API_KEY, nb_posts, post_pictures_directory)

generator = PostGenerator(DATABASE_NAME, INSTANCE_ID, THEME, PEXEL_API_KEY, POST_FILE)
generator.generate_posts(post_per_person=30, location="Angers")


