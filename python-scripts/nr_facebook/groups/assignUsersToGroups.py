import mysql.connector
import random
from datetime import datetime
import sys
import os
root_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
if root_path not in sys.path:
    sys.path.insert(0, root_path)
from config import HOST, USER, PASSWORD, DATABASE_FACEBOOK

def assign_users_to_groups(instance_id):
    conn = mysql.connector.connect(
        host=HOST,
        user=USER,
        password=PASSWORD,
        database=DATABASE_FACEBOOK
    )
    cursor = conn.cursor()

    # Récupérer tous les user_id
    cursor.execute("SELECT user_id FROM userlinkinstance WHERE instance_id = %s;", (instance_id,))
    user_ids = [row[0] for row in cursor.fetchall()]

    # Récupérer tous les group_id
    cursor.execute("SELECT group_id FROM groups WHERE instance_id = %s;", (instance_id,))
    group_ids = [row[0] for row in cursor.fetchall()]

    # Préparer les insertions
    insert_sql = '''
        INSERT INTO group_members (group_id, user_id, time_stamp)
        VALUES (%s, %s, %s)
    '''

    inserts = []
    for user_id in user_ids:
        n = random.randint(1, 3)
        chosen_groups = random.sample(group_ids, min(n, len(group_ids)))
        for group_id in chosen_groups:
            timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            inserts.append((group_id, user_id, timestamp))

    # Exécuter les insertions par batch
    cursor.executemany(insert_sql, inserts)
    conn.commit()

    print(f"{len(inserts)} utilisateurs ajoutés à des groupes.")
    cursor.close()
    conn.close()

if __name__ == '__main__':
    assign_users_to_groups(3)