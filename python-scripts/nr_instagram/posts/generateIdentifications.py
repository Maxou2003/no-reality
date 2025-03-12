import mysql.connector
import random

def get_users(instance_id):
    """
    Get all the users from the database for a given instance_id.
    
    Args:
        instance_id: The ID of the instance to filter users by.
    Returns:
        List of user_ids or empty list if no results or on error.
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
            SELECT user_id FROM userlinkinstance
            WHERE instance_id = %s
        '''
        cursor.execute(sql, (instance_id,))  # Note the comma to make it a tuple
        results = cursor.fetchall()  # Fetch all results
        return [row[0] for row in results]  # Extract user_id from each row

    except mysql.connector.Error as e:
        print(f"Erreur lors de l'accès à la base de données : {e}")
        return []  # Return empty list on error
    finally:
        if cursor:
            cursor.close()
        if conn and conn.is_connected():
            conn.close()



def get_posts(instance_id):
    """
    Get all the users from the database for a given instance_id.
    
    Args:
        instance_id: The ID of the instance to filter users by.
    Returns:
        List of user_ids or empty list if no results or on error.
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
            SELECT post_id FROM posts
            WHERE instance_id = %s
        '''
        cursor.execute(sql, (instance_id,))  # Note the comma to make it a tuple
        results = cursor.fetchall()  # Fetch all results
        return [row[0] for row in results]  # Extract user_id from each row

    except mysql.connector.Error as e:
        print(f"Erreur lors de l'accès à la base de données : {e}")
        return []  # Return empty list on error
    finally:
        if cursor:
            cursor.close()
        if conn and conn.is_connected():
            conn.close()

def fillTable(follow_list):
    """
    Create the followings table
    """
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_instagram"
    )
    cursor = conn.cursor()
    sql = '''
        INSERT INTO identification (
            post_id,
            user_id,
            instance_id
        ) VALUES (
            %s, %s, %s
        )
    '''
    for i in range(len(follow_list)):
        cursor.execute(sql, (follow_list[i][0], follow_list[i][1], follow_list[i][2]))
    conn.commit()
    cursor.close()
    conn.close()

def main(instance_id=1):
    posts = get_posts(instance_id)
    print(posts)
    users = get_users(instance_id)
    print(users)
    for post_id in posts:
        if random.random() < 0.1:
            user_id = random.choice(users)
            fillTable([(post_id, user_id, instance_id)])

if __name__ == "__main__": 
    main(1)