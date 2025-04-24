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
            host=HOST,
            user=USER,
            password=PASSWORD,
            database=DATABASE_FACEBOOK
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

def fill_friends_table(friendship_list):
    """
    Create entries in the friends table
    """
    conn = mysql.connector.connect(
        host=HOST,
        user=USER,
        password=PASSWORD,
        database=DATABASE_FACEBOOK
    )
    cursor = conn.cursor()
    sql_check = '''
        SELECT COUNT(*) FROM friends
        WHERE user_id_1 = %s AND user_id_2 = %s AND instance_id = %s
    '''
    sql_insert = '''
        INSERT INTO friends (
            user_id_1,
            user_id_2,
            instance_id
        ) VALUES (
            %s, %s, %s
        )
    '''
    for friendship in friendship_list:
        cursor.execute(sql_check, (friendship[0], friendship[1], friendship[2]))
        if cursor.fetchone()[0] == 0:  # Only insert if no existing friendship
            cursor.execute(sql_insert, (friendship[0], friendship[1], friendship[2]))
    conn.commit()
    cursor.close()
    conn.close()

def generate_friendships(instance_id, friendship_chance):
    users = get_users(instance_id)
    friendship_list = []
    for i in range(len(users)):
        for j in range(i + 1, len(users)):  # Ensure we only create one friendship between each pair
            if random.random() < friendship_chance:  
                # Always store with smaller ID first to prevent duplicates
                smaller_id = min(users[i], users[j])
                larger_id = max(users[i], users[j])
                friendship_list.append([smaller_id, larger_id, instance_id])

    fill_friends_table(friendship_list)

if __name__ == "__main__":
    import sys
    sys.path.append(sys.path[0] + "/../..")  # Adjust the path to include the parent directory
    from config import HOST, USER, PASSWORD, DATABASE_FACEBOOK
    generate_friendships(3, friendship_chance=0.3)