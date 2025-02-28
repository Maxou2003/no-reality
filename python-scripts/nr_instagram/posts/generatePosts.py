import json
import mysql.connector
from datetime import timedelta, datetime
from random import randint, shuffle, choice

from getPostPictures import download_images_with_api


def random_timestamp(start, end):
    """
    Génère un timestamp aléatoire entre deux dates.

    :param start: datetime.datetime - Date de début
    :param end: datetime.datetime - Date de fin
    :return: datetime.datetime - Timestamp aléatoire
    """
    delta = end - start
    random_seconds = randint(0, int(delta.total_seconds()))
    return start + timedelta(seconds=random_seconds)


def get_user_id(database, instance_id):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database=database
        )
        cursor = conn.cursor()

        # Execute a query
        cursor.execute("SELECT user_id FROM userlinkinstance WHERE instance_id = %s", [instance_id])
        rows = cursor.fetchall()

        user_ids = [row[0] for row in rows]

        # Close the connection
        cursor.close()
        conn.close()
        return user_ids
    except mysql.connector.Error as err:
        print(f"Error: {err}")


def add_post(database, post):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database=database
        )
        cursor = conn.cursor()

        # Parameterized query
        sql = '''
            INSERT INTO posts (
                user_id,
                instance_id,
                nb_likes,
                nb_views,
                time_stamp,
                post_picture_path, 
                post_description, 
                post_location,
                nb_comments
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
        '''

        # Execute with values
        cursor.execute(sql, (post['user_id'], post['instance_id'], post['nb_likes'],
                             post['nb_views'], post['timestamp'], post['post_picture_path'],
                             post['post_description'], post['post_location'], post['nb_comments']))

        # Commit and close
        conn.commit()

        # Close the connection
        cursor.close()
        conn.close()
    except mysql.connector.Error as err:
        print(f"Error: {err}")


def get_last_post_info(database, instance_id):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database=database
        )
        cursor = conn.cursor()

        # Execute a query
        cursor.execute("SELECT MAX(post_id), time_stamp FROM posts WHERE instance_id = %s", [instance_id])
        rows = cursor.fetchall()

        post_info = rows[0]

        # Close the connection
        cursor.close()
        conn.close()
        return post_info
    except mysql.connector.Error as err:
        print(f"Error: {err}")


def add_com(database, com):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database=database
        )
        cursor = conn.cursor()

        # Parameterized query
        sql = '''
            INSERT INTO comments (
                user_id,
                post_id,
                comment_text,
                time_stamp,
                nb_responses
            ) VALUES (%s, %s, %s, %s, %s)
        '''

        # Execute with values
        cursor.execute(sql, (com['user_id'], com['post_id'], com['comment_text'],
                             com['time_stamp'], com['nb_responses']))
        # Commit and close
        conn.commit()

        # Close the connection
        cursor.close()
        conn.close()
    except mysql.connector.Error as err:
        print(f"Error: {err}")


def random_exclude(lst, exclude):
    """
    Sélectionne un élément aléatoire d'une liste en excluant une valeur spécifique.

    :param lst: list - Liste des valeurs
    :param exclude: valeur à exclure
    :return: valeur aléatoire de la liste sans la valeur exclue
    """
    filtered_list = [x for x in lst if x != exclude]
    return choice(filtered_list) if filtered_list else None


# Read data from json file
with open("mon_fichier.json", "r") as file:
    data = json.load(file)

# Get posts in data
posts = data.get("posts")
nb_posts = len(posts)
print(f'{nb_posts} found in json file')

# # Define parameters for image API
theme = 'sources thermales'
pexel_api_key = "0NMkYhKereL0Ne2PfmTECpAF7SFgy9vGzlWMY2ieB1ByvDGUpKzS3mJn"

# Get as many image as nb_posts
print(f"{download_images_with_api(theme, pexel_api_key, nb_posts)} images téléchargées")

database: str = "nr_instagram"
instance_id: int = 1
user_ids = get_user_id(database, instance_id)

post_per_person: int = 30
cnt: int = 0
post_id: int = 0

start_date = datetime(2025, 1, 1)
end_date = datetime(2025, 12, 1)

location = 'Angers'

while cnt < post_per_person:
    for user_id in user_ids:
        post_picture_path = f"{theme}_{post_id // 10 + 1}_{post_id % 10}.jpg"
        post = {
            'user_id': user_id,
            'instance_id': instance_id,
            'nb_likes': randint(0, 100),
            'nb_views': randint(0, 100),
            'timestamp': random_timestamp(start_date, end_date),
            'post_picture_path': post_picture_path,
            'post_description': posts[post_id].get('description'),
            'post_location': location,
            'nb_comments': len(posts[post_id].get('comments'))
        }
        # print(f"(user_id: {user_id}, instance_id: {instance_id}, nb_likes: {randint(0, 100)}, nb_views: {randint(0, 100)}, "
        #       f"timestamp: {random_timestamp(start_date, end_date)}, post_picture_path: {post_picture_path}, post_description: {posts[post_id].get('description')}, "
        #       f"post_location:  {location}, nb_comments: {len(posts[post_id].get('comments'))})")
        add_post(database, post)
        for comment in posts[post_id].get('comments'):
            post_info = get_last_post_info(database, instance_id)
            com = {
                'user_id': random_exclude(user_ids, user_id),
                'post_id': post_info[0],
                'comment_text': comment.get('content'),
                'time_stamp': random_timestamp(post_info[1], end_date),
                'nb_responses': 0
            }
            add_com(database, com)
        post_id += 1
        if post_id >= nb_posts:
            cnt = post_per_person
            break
    cnt += 1
    shuffle(user_ids)







