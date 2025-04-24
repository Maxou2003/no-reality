import mysql.connector
import sys
import os
root_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
if root_path not in sys.path:
    sys.path.insert(0, root_path)
from nr_instagram.posts.deletePost import delete_post
from nr_instagram.profiles.deleteUser import delete_user_from_nr_instagram
from config import HOST, USER, PASSWORD, DATABASE_INSTAGRAM
import os
import shutil

def delete_instance_repository(instance_name):
    # Delete the directory for the instance
    folder_path = f"web/public/{instance_name}"
    if os.path.exists(folder_path):
        print(f"Deleting folder for instance: {folder_path}")
        try:
            import shutil
            shutil.rmtree(folder_path)  # Remove the directory even if it's not empty
        except OSError as e:
            print(f"Error deleting folder: {e}")
    else:
        print(f"Folder for instance {instance_name} does not exist.")

def delete_instance(instance_id):
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
        get_all_posts = '''
            SELECT post_id FROM posts
            WHERE instance_id = %s
        '''
        get_all_users = '''
            SELECT user_id FROM userlinkinstance
            WHERE instance_id = %s
        '''
        get_instance_name = '''
            SELECT instance_name FROM instance
            WHERE instance_id = %s
        '''
        cursor.execute(get_instance_name, (instance_id,))
        instance_name = cursor.fetchone()[0]
        delete_instance_repository(instance_name)  # Delete the instance repository

        cursor.execute(get_all_posts, (instance_id,))
        posts_ids = cursor.fetchall()

        for post in posts_ids:
            delete_post(post[0])
        cursor.execute(get_all_users, (instance_id,))
        users_ids = cursor.fetchall()
        for user in users_ids:
            delete_user_from_nr_instagram(user[0])
        delete_instance = '''
            DELETE FROM instance
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

if __name__ == "__main__":
    instance_id = 18  # Replace with the actual instance ID you want to delete
    success = delete_instance(instance_id)
    if success:
        print(f"Instance {instance_id} deleted successfully.")
    else:
        print(f"Failed to delete instance {instance_id}.")