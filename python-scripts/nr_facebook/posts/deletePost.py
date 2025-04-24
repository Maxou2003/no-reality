import mysql.connector
import os
import sys
root_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
if root_path not in sys.path:
    sys.path.insert(0, root_path)

from config import HOST, USER, PASSWORD, DATABASE_FACEBOOK

def delete_post_picture(post_picture_path, instance_name):
    post_picture_path = os.path.join("web", "public", instance_name, "img", "post_img", post_picture_path)
    if os.path.exists(post_picture_path):
        try:
            os.remove(post_picture_path)
            print(f"Deleted image at path: {post_picture_path}")
        except OSError as e:
            print(f"Error deleting image at path {post_picture_path}: {e}")
    else:
        print(f"Image path does not exist: {post_picture_path}")

def delete_post(post_id):
    """
    Delete a post from the database.
    
    Args:
        post_id: The ID of the post to delete.
    Returns:
        True if the post was deleted, False otherwise.
    """
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
        sql = '''
            SELECT comment_id FROM comments
            WHERE post_id = %s
        '''
        cursor.execute(sql, (post_id,))  # Note the comma to make it a tuple
        comments_ids = cursor.fetchall()  # Fetch all results
        
        delete_responses = '''
            DELETE FROM responses
            WHERE comment_id = %s
        '''
        for comment in comments_ids:
            cursor.execute(delete_responses, (comment[0],))
        
        delete_comments = '''
            DELETE FROM comments
            WHERE post_id = %s
        '''
        cursor.execute(delete_comments, (post_id,))

        delete_likes = '''
            DELETE FROM likes
            WHERE post_id = %s
        '''
        cursor.execute(delete_likes, (post_id,))

        delete_identifications = '''
            DELETE FROM identifications
            WHERE post_id = %s
        '''
        cursor.execute(delete_identifications, (post_id,))

        select_post_picture_path = '''
            SELECT post_picture_path FROM posts
            WHERE post_id = %s
        '''
        cursor.execute(select_post_picture_path, (post_id,))  # Fetch the post picture path
        post_picture_path = cursor.fetchone() # Get the first result
        if post_picture_path is not None:
            post_picture_path = post_picture_path[0]
            select_instance_name = '''
                SELECT instance_name FROM instances
                WHERE instance_id = %s
            '''
            cursor.execute('SELECT instance_id FROM posts WHERE post_id = %s', (post_id,))  # Get the instance ID
            instance_id = cursor.fetchone()[0]  # Get the first result
            cursor.execute(select_instance_name, (instance_id,))  # Fetch the instance name
            instance_name = cursor.fetchone()[0]  # Get the first result

            delete_post_picture(post_picture_path, instance_name)  # Replace with actual instance name

        delete_post = '''
            DELETE FROM posts
            WHERE post_id = %s
        '''
        cursor.execute(delete_post, (post_id,))  # Execute the deletion
        
        conn.commit()  # Commit the deletion
        return True  # Return True on success
    except mysql.connector.Error as e:
        print(f"Erreur lors de l'accès à la base de données : {e}")
        return False  # Return False on error
    finally:
        if cursor:
            cursor.close()
        if conn and conn.is_connected():
            conn.close()

if __name__ == "__main__":

    for i in range(0, 600):
        delete_post(i)