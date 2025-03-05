import json
from datetime import datetime, timedelta
from random import randint, shuffle, choice

from database import Database


class PostGenerator:
    def __init__(self, database_name, instance_id, theme, pexel_api_key, post_file):
        self.db = Database(database_name)
        self.instance_id = instance_id
        self.theme = theme
        self.pexel_api_key = pexel_api_key
        self.user_ids = self.get_user_ids()
        self.posts = self.load_posts(post_file)
        self.nb_posts = len(self.posts)

    def get_user_ids(self):
        query = "SELECT user_id FROM userlinkinstance WHERE instance_id = %s"
        rows = self.db.fetch_all(query, [self.instance_id])
        return [row[0] for row in rows]

    @staticmethod
    def load_posts(post_file):
        with open(post_file, "r") as file:
            data = json.load(file)
            posts = data.get("posts", [])
            print(f"{len(posts)} posts trouvés dans le fichier JSON.")
        return posts

    @staticmethod
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

    @staticmethod
    def random_exclude(lst, exclude):
        """
        Sélectionne un élément aléatoire d'une liste en excluant une valeur spécifique.

        :param lst: list - Liste des valeurs
        :param exclude: valeur à exclure
        :return: valeur aléatoire de la liste sans la valeur exclue
        """
        filtered_list = [x for x in lst if x != exclude]
        return choice(filtered_list) if filtered_list else None

    def add_post(self, post):
        query = '''
            INSERT INTO posts (user_id, instance_id, nb_likes, nb_views, time_stamp, 
                              post_picture_path, post_description, post_location, nb_comments)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)'''
        return self.db.execute(query, (
            post['user_id'], post['instance_id'], post['nb_likes'], post['nb_views'],
            post['timestamp'], post['post_picture_path'], post['post_description'],
            post['post_location'], post['nb_comments']
        ))

    def add_comment(self, comment):
        query = '''
            INSERT INTO comments (user_id, post_id, comment_text, time_stamp, nb_responses)
            VALUES (%s, %s, %s, %s, %s)'''
        self.db.execute(query, (
            comment['user_id'], comment['post_id'], comment['comment_text'],
            comment['time_stamp'], comment['nb_responses']
        ))

    def generate_posts(self, post_per_person=30, location='Angers'):
        start_date = datetime(2025, 1, 1)
        end_date = datetime(2025, 12, 1)

        post_id = 0
        cnt = 0
        while cnt < post_per_person:
            for user_id in self.user_ids:
                if post_id >= self.nb_posts:
                    return
                post_picture_path = f"{self.theme}_{post_id // 10 + 1}_{post_id % 10}.jpg"
                post = {
                    'user_id': user_id,
                    'instance_id': self.instance_id,
                    'nb_likes': randint(0, 100),
                    'nb_views': randint(0, 100),
                    'timestamp': self.random_timestamp(start_date, end_date),
                    'post_picture_path': post_picture_path,
                    'post_description': self.posts[post_id].get('description'),
                    'post_location': location,
                    'nb_comments': len(self.posts[post_id].get('comments', []))
                }
                post_id = self.add_post(post)

                for comment in self.posts[post_id].get('comments', []):
                    comment_data = {
                        'user_id': self.random_exclude(self.user_ids, user_id),
                        'post_id': post_id,
                        'comment_text': comment.get('content'),
                        'time_stamp': self.random_timestamp(post['timestamp'], end_date),
                        'nb_responses': 0
                    }
                    self.add_comment(comment_data)

                post_id += 1
            cnt += 1
            shuffle(self.user_ids)
