import mysql.connector
import json
import sys
sys.path.insert(0, 'python-scripts')
from nr_source.nr_source_filling import create_persons
from nr_instagram.profiles.create_descriptions import create_json



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

def get_all_info(gender, ethnicity):
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
        sql = '''
                SELECT user_firstname, user_lastname, user_age, user_pp_path FROM users
                WHERE user_gender = %s AND user_ethnicity = %s
            '''

        cursor = conn.cursor()
        cursor.execute(sql, (gender, ethnicity))
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
        try: 
            user_id = user  
            cursor.execute(insert, (user_id, instance))
        except mysql.connector.Error as e:
            print(f"Erreur lors de l'insertion dans la table userlinkinstance : {e}")
    
    conn.commit()
    conn.close()
    

def generate_new_profiles(nb_users, last_user, instance, gender, ethnicity, json_file_path):
    """
    Get all infos from nr_source, create a json file with the usernames and descriptions, fill the table nr_instagram.users with the users informations\n
    returns: a list of the user name, surmane, pp_path, username, description\n
    format: [[forename, surname, age, image_path, username, description], [forename, surname, image_path, username, description], ...]
    """
    users_all_infos = get_all_info(gender, ethnicity)[last_user:last_user + nb_users]
    print(f"All users infos retrieved: {len(users_all_infos)}")
    if len(users_all_infos) < nb_users:
        print(f" !!!! Nombre d'utilisateurs insuffisant pour le nombre d'utilisateurs demandé. {len(users_all_infos)} utilisateurs trouvés. Création de nouveaux {nb_users - len(users_all_infos)}utilisateurs...")
        create_persons(nb_users - len(users_all_infos), gender, ethnicity, "web/profile_pictures")
        users_all_infos = get_all_info(gender, ethnicity)[last_user:last_user + nb_users]
    users = []
    for i in range(len(users_all_infos)):
        users.append(users_all_infos[i][0:3])
    create_json(json_file_path, users)
    print(f"Json file created with {len(users)} users")
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

def generate_profiles(nb_users, instance, gender, ethnicity, json_file_path):
    # Check if the users already exist in nr_instagram.users
    conn = mysql.connector.connect(
        host="localhost",
        user = "root",
        password = "",
        database = "nr_instagram"
    )
    cursor = conn.cursor()
    if gender == 0:
        name_gender = 'M'
    else:
        name_gender = 'F'
    pp_path = f'{name_gender}/{ethnicity}'
    print(pp_path)
    sql = '''
        SELECT user_id FROM users WHERE user_pp_path LIKE %s;
    '''
    cursor.execute(sql, (f"{pp_path}/%",))
    result = cursor.fetchall()
    if len(result) >= nb_users:
        print(f"Enough users in the database")
    else:
        print(f"Not enough users in the database, {len(result)} found")
        print(f"Generating {nb_users - len(result)} new profiles...")
        generate_new_profiles(nb_users - len(result), len(result), instance,gender,ethnicity,json_file_path)
        cursor.execute(sql, (f"{pp_path}/%",))
        result = cursor.fetchall()
    users_list = []
    users = result[0:nb_users]
    for user in users:
        users_list.append(user[0])
    fill_instance(instance, users_list)
        

if __name__ == '__main__':
    
    #generate_profiles(nb_users=20, instance=2, gender=0, ethnicity=0, json_file_path='python-scripts/nr_source/descriptions.json')
    generate_profiles(nb_users=40, instance=1, gender=1, ethnicity=2, json_file_path='python-scripts/nr_source/descriptions.json')
    # generate_profiles(nb_users=40, instance=1, gender=0, ethnicity=1, json_file_path='python-scripts/nr_source/descriptions.json')
    # generate_profiles(nb_users=40, instance=1, gender=1, ethnicity=1, json_file_path='python-scripts/nr_source/descriptions.json')
    # generate_profiles(nb_users=40, instance=1, gender=0, ethnicity=2, json_file_path='python-scripts/nr_source/descriptions.json')
    #descriptions_from_json('python-scripts/nr_source/descriptions.json')