import mysql.connector
import sys
sys.path.insert(0, 'python-scripts')
from nr_instagram.posts.deletePost import delete_post
from config import HOST, USER, PASSWORD, DATABASE_INSTAGRAM

def delete_user_from_nr_instagram(user_id):
    conn = None
    cursor = None

    try:
        conn = mysql.connector.connect(
            host=HOST,
            user=USER,
            password=PASSWORD,
            database=DATABASE_INSTAGRAM
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
            DELETE FROM identification
            WHERE user_id = %s
        '''
        delete_responses = '''
            DELETE FROM response
            WHERE user_id = %s
        '''
        select_comments = '''
            SELECT comment_id FROM comments
            WHERE user_id = %s
        '''
        delete_responses_of_comments = '''
            DELETE FROM response
            WHERE comment_id = %s
        '''
        delete_user_link_instance = '''
            DELETE FROM userlinkinstance
            WHERE user_id = %s
        '''
        cursor.execute(select_comments, (user_id,))
        comments_ids = cursor.fetchall()
        for comment in comments_ids:
            cursor.execute(delete_responses_of_comments, (comment[0],))
        cursor.execute(delete_likes, (user_id,))
        cursor.execute(delete_responses, (user_id,))
        cursor.execute(delete_identifications, (user_id,))
        conn.commit()
        cursor.execute(delete_comments, (user_id,))
        cursor.execute(delete_user_link_instance, (user_id,))

        # Delete the followings
        delete_followings = '''
            DELETE FROM subscriptions
            WHERE follower_id = %s OR followed_id = %s
        '''
        cursor.execute(delete_followings, (user_id, user_id))

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
    for i in range(331,500):
        delete_user_from_nr_instagram(i)