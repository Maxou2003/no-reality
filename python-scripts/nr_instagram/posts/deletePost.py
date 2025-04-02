import mysql.connector

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
            host="localhost",
            user="root",
            password="",
            database="nr_instagram"
        )
        cursor = conn.cursor()
        sql = '''
            SELECT comment_id FROM comments
            WHERE post_id = %s
        '''
        cursor.execute(sql, (post_id,))  # Note the comma to make it a tuple
        comments_ids = cursor.fetchall()  # Fetch all results
        
        delete_responses = '''
            DELETE FROM response
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
            DELETE FROM identification
            WHERE post_id = %s
        '''
        cursor.execute(delete_identifications, (post_id,))

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