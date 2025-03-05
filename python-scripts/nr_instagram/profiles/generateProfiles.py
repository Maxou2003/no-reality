import mysql.connector
import json
from create_descriptions import create_json


def create_table():
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_source"
    )
    cursor = conn.cursor()
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS users (
        user_id int(11) NOT NULL,
        user_username varchar(50) NOT NULL,
        user_firstname varchar(50) NOT NULL,
        user_lastname varchar(50) NOT NULL,
        user_pp_path text DEFAULT NULL,
        user_description text NOT NULL
    ''')

def get_all_info():
    """
    Get all the informations from nr_source.users
    """
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="nr_source"
        )
        cursor = conn.cursor()
        cursor.execute('''
            SELECT user_firstname, user_lastname, user_age, user_pp_path FROM users
        ''')
    except mysql.connector.Error as e:
        print(f"Erreur lors de l'accès à la base de données : {e}")
    finally:
        return cursor.fetchall()


def descriptions_from_json(json_file):
    """
    Load the json file and return the values
    returns: a list of the values
    format: [[username, description], [username, description], ...]
    """
    with open(json_file, 'r') as f:
        data = json.load(f)
    values = []
    for key, v in data.items():
        for key, value in v.items():
            values.append([key,value])
    return values


def fill_table(users):
    """
    Fill the table nr_instagram.users with the users informations
    returns: a list of the user ids generated
    """
    conn = mysql.connector.connect(
        host="localhost",
        user = "root",
        password = "",
        database = "nr_instagram"
    )
    cursor = conn.cursor()
    sql = '''
        INSERT INTO users (
            user_firstname,
            user_lastname,
            user_pp_path,
            user_username,
            user_description
        ) VALUES (%s, %s, %s, %s, %s)
    '''
    user_ids = []
    for i in range(len(users)):
        forename = users[i][0]
        surname = users[i][1]
        age = users[i][2]   #We don't use it on Instagram
        image = users[i][3]
        username = users[i][4]
        description = users[i][5]
        cursor.execute(sql, (forename, surname, image, username, description))
        user_id = cursor.lastrowid
        user_ids.append(user_id)
    
    conn.commit()
    conn.close()
    return user_ids

def fill_instance(instance, users):
    conn = mysql.connector.connect(
        host="localhost",
        user = "root",
        password = "",
        database = "nr_instagram"
    )
    cursor = conn.cursor()
    insert = '''
        INSERT INTO userlinkinstance (
            user_id,
            instance_id
        ) VALUES (%s, %s)
    '''

    for user in users:
        user_id = user  
        cursor.execute(insert, (user_id, instance))
    
    conn.commit()
    conn.close()

def main(nb_users, instance, json_file_path):
    """
    Get all infos from nr_source, create a json file with the usernames and descriptions, fill the table nr_instagram.users with the users informations\n
    returns: a list of the user name, surmane, pp_path, username, description\n
    format: [[forename, surname, age, image_path, username, description], [forename, surname, image_path, username, description], ...]
    """
    users_all_infos = get_all_info()[0:nb_users]
    users = []
    for i in range(len(users_all_infos)):
        users.append(users_all_infos[i][0:3])
    create_json(json_file_path, users)
    user_names_descriptions = descriptions_from_json(json_file_path)
    final_list = []
    for i in range(len(user_names_descriptions)):
        all_infos = list(users_all_infos[i])
        all_infos.append(user_names_descriptions[i][0])
        all_infos.append(user_names_descriptions[i][1])
        final_list.append(all_infos)
    print(final_list)
    users_ids = fill_table(final_list)
    print(users_ids)
    fill_instance(instance, users_ids)


if __name__ == '__main__':
    main(10, 1, 'python-scripts/nr_source/descriptions.json')
    #descriptions_from_json('python-scripts/nr_source/descriptions.json')