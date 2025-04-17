from datetime import datetime, timedelta
from random import randint, shuffle, choice
from database import Database


class PostGenerator:
    def __init__(self, database_name, instance_id, theme, pexel_api_key, posts):
        self.db = Database(database_name)
        self.instance_id = instance_id
        self.theme = theme
        self.pexel_api_key = pexel_api_key
        self.user_ids = self.get_user_ids()
        self.posts = posts
        self.nb_posts = len(self.posts)

    def get_user_ids(self):
        query = "SELECT user_id FROM userlinkinstance WHERE instance_id = %s"
        rows = self.db.fetch_all(query, [self.instance_id])
        return [row[0] for row in rows]

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
            INSERT INTO posts (user_id, instance_id, time_stamp, 
                              post_picture_path, post_content, nb_comments)
            VALUES (%s, %s, %s, %s, %s, %s)'''
        return self.db.execute(query, (
            post['user_id'], post['instance_id'],
            post['timestamp'], post['post_picture_path'], post['post_description'],
            post['nb_comments']
        ))

    def add_comment(self, comment):
        query = '''
            INSERT INTO comments (user_id, post_id, comment_text, time_stamp, nb_responses)
            VALUES (%s, %s, %s, %s, %s)'''
        return self.db.execute(query, (
            comment['user_id'], comment['post_id'], comment['comment_text'],
            comment['time_stamp'], comment['nb_responses']
        ))

    def add_reponse(self, reponse):
        query = '''
            INSERT INTO responses (comment_id, user_id, response_content, time_stamp)
            VALUES (%s, %s, %s, %s)'''
        return self.db.execute(query, (
            reponse['comment_id'], reponse['user_id'],
            reponse['content'], reponse['time_stamp']
        ))

    def add_likes(self, user_id, post_id):
        query = '''
            INSERT INTO likes (user_id, post_id)
            VALUES (%s, %s)'''
        return self.db.execute(query, (user_id, post_id))

    def add_shares(self, user_id, post_id):
        query = '''
            INSERT INTO shares (user_id, post_id, instance_id)
            VALUES (%s, %s, %s)'''
        return self.db.execute(query, (user_id, post_id, self.instance_id))

    def generate_posts(self, post_per_person=30, location='Angers', posts_pathes=None):
        start_date = datetime(2025, 1, 1)
        end_date = datetime(2025, 12, 1)
        print(self.posts)
        post_idx = 0
        cnt = 0

        while cnt < post_per_person:
            for user_id in self.user_ids:
                if post_idx >= self.nb_posts:
                    return
                post_picture_path = f"{posts_pathes[post_idx]}"
                post = {
                    'user_id': user_id,
                    'instance_id': self.instance_id,
                    'nb_views': randint(0, 100),
                    'timestamp': self.random_timestamp(start_date, end_date),
                    'post_picture_path': post_picture_path,
                    'post_description': self.posts[post_idx].get('description'),
                    'post_location': location,
                    'nb_comments': len(self.posts[post_idx].get('comments', []))
                }
                post_id_added = self.add_post(post)
                
                for _id in range(len(self.user_ids)):
                    like_chance = randint(0, 100)
                    share_chance = randint(0, 100)
                    if like_chance < 10:
                        self.add_likes(self.user_ids[_id], post_id_added)
                    if share_chance < 8:
                        self.add_shares(self.user_ids[_id], post_id_added)

                for comment in self.posts[post_idx].get('comments', []):
                    comment_ts = self.random_timestamp(post['timestamp'], end_date)
                    comment_uid = self.random_exclude(self.user_ids, user_id)
                    comment_data = {
                        'user_id': comment_uid,
                        'post_id': post_id_added,
                        'comment_text': comment.get('content'),
                        'time_stamp': comment_ts,
                        'nb_responses': len(comment.get('responses'))
                    }
                    comment_id = self.add_comment(comment_data)

                    for reponse in comment.get('responses'):
                        reponse_data = {
                            'user_id': self.random_exclude(self.user_ids, comment_uid),
                            'comment_id': comment_id,
                            'content': reponse.get('response'),
                            'time_stamp': self.random_timestamp(comment_ts, end_date)
                        }
                        reponse_id = self.add_reponse(reponse_data)

                post_idx += 1
            cnt += 1
            shuffle(self.user_ids)
