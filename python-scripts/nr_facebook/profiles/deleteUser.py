import mysql.connector
import os
import sys
root_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
if root_path not in sys.path:
    sys.path.insert(0, root_path)

from nr_facebook.posts.deletePost import delete_post
from config import HOST, USER, PASSWORD, DATABASE_FACEBOOK 

def delete_user_from_nr_facebook(user_id):
    conn = None
    cursor = None

    try:
        conn = mysql.connector.connect(
            host=HOST,
            user=USER,
            password=PASSWORD,
            database=DATABASE_FACEBOOK
        )

        cursor = conn.cursor()
        # Delete the user from the database
        get_posts = '''
            SELECT post_id FROM posts
            WHERE user_id = %s
        '''
        cursor.execute(get_posts, (user_id,))
        posts_ids = cursor.fetchall()

        for post in posts_ids:
            delete_post(post[0])
        delete_likes = '''
            DELETE FROM likes
            WHERE user_id = %s
        '''
        delete_user = '''
            DELETE FROM users
            WHERE user_id = %s
        '''
        delete_comments = '''
            DELETE FROM comments
            WHERE user_id = %s
        '''
        delete_identifications = '''
            DELETE FROM identifications
            WHERE user_id = %s
        '''
        delete_user_link_instance = '''
            DELETE FROM userlinkinstance
            WHERE user_id = %s
        '''
        cursor.execute(delete_likes, (user_id,))
        cursor.execute(delete_identifications, (user_id,))
        conn.commit()
        cursor.execute(delete_comments, (user_id,))
        cursor.execute(delete_user_link_instance, (user_id,))

        # # Delete the followings
        delete_friends = '''
            DELETE FROM friends
            WHERE user_id_1 = %s OR user_id_2 = %s
        '''
        cursor.execute(delete_friends, (user_id, user_id))

        cursor.execute(delete_user, (user_id,))
        conn.commit()

        return True
    except mysql.connector.Error as e:
        print(f"Error connecting to the database: {e}")
        return False
    finally:
        if cursor:
            cursor.close()
        if conn and conn.is_connected():
            conn.close()

if __name__ == "__main__":
    for n in range(0,500):
        delete_user_from_nr_facebook(n)
    delete_user_from_nr_facebook(8)