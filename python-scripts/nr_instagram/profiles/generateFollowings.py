import mysql.connector
import random

import mysql.connector
from mysql.connector import Error
from config import HOST, USER, PASSWORD, DATABASE_INSTAGRAM

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
            database=DATABASE_INSTAGRAM
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

    

def fillTable(follow_list):
    """
    Create the followings table
    """
    conn = mysql.connector.connect(
        host=HOST,
        user=USER,
        password=PASSWORD,
        database=DATABASE_INSTAGRAM
    )
    cursor = conn.cursor()
    sql_check = '''
        SELECT COUNT(*) FROM subscriptions
        WHERE follower_id = %s AND followed_id = %s AND instance_id = %s
    '''
    sql_insert = '''
        INSERT INTO subscriptions (
            follower_id,
            followed_id,
            instance_id
        ) VALUES (
            %s, %s, %s
        )
    '''
    for i in range(len(follow_list)):
        cursor.execute(sql_check, (follow_list[i][0], follow_list[i][1], follow_list[i][2]))
        if cursor.fetchone()[0] == 0:  # Only insert if no existing subscription
            cursor.execute(sql_insert, (follow_list[i][0], follow_list[i][1], follow_list[i][2]))
    conn.commit()
    cursor.close()
    conn.close()

def generate_followings(instance_id, follow_chance, follow_back_chance):
    users = get_users(instance_id)
    follow_list = []
    for i in range(len(users)):
        for j in range(i + 1, len(users)):  # Start j after i to avoid self-follows
            if random.random() < follow_chance:  # 20% chance i follows j
                follow_list.append([users[i], users[j], instance_id])
                if random.random() < follow_back_chance:  # 90% chance j follows i back
                    follow_list.append([users[j], users[i], instance_id])

    fillTable(follow_list)

if __name__ == "__main__":
    generate_followings(1, follow_chance=0.3, follow_back_chance=0.9)