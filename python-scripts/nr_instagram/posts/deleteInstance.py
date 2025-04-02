import mysql.connector
import sys
sys.path.insert(0, 'python-scripts')
from nr_instagram.posts.deletePost import delete_post
from nr_instagram.profiles.deleteUser import delete_user_from_nr_instagram

def delete_instance(instance_id):
    conn = None
    cursor = None
    try:
        conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_instagram"
        )
        cursor = conn.cursor()
        get_all_posts = '''
            SELECT post_id FROM posts
            WHERE instance_id = %s
        '''
        get_all_users = '''
            SELECT user_id FROM users
            WHERE instance_id = %s
        '''
        cursor.execute(get_all_posts, (instance_id,))
        posts_ids = cursor.fetchall()
        for post in posts_ids:
            delete_post(post[0])
        cursor.execute(get_all_users, (instance_id,))
        users_ids = cursor.fetchall()
        for user in users_ids:
            delete_user_from_nr_instagram(user[0])
        delete_instance = '''
            DELETE FROM instances
            WHERE instance_id = %s
        '''
        cursor.execute(delete_instance, (instance_id,))  # Execute the deletion
        conn.commit()  # Commit the deletion

    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return False
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()
    return True  # Return True on success