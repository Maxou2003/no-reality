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
        print(f"Nombre d'utilisateurs insuffisant pour le nombre d'utilisateurs demandé. {len(users_all_infos)} utilisateurs trouvés. Création de {nb_users - len(users_all_infos)} nouveaux utilisateurs...")
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
    print("final_list: ", final_list)
    users_ids = fill_table(final_list)
    print("users_ids: ", users_ids)
    fill_instance(instance, users_ids)

def get_users_in_instance(instance_id):
    """
    Get all user IDs already linked to a specific instance
    returns: a list of user IDs
    """
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_instagram"
    )
    cursor = conn.cursor()
    sql = '''
        SELECT user_id FROM userlinkinstance WHERE instance_id = %s
    '''
    cursor.execute(sql, (instance_id,))
    result = cursor.fetchall()
    conn.close()
    return [user[0] for user in result]

def check_instance_links(instance):
    """
    Check if there are already links for this instance in the userlinkinstance table
    returns: True if links exist, False otherwise
    """
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_instagram"
    )
    cursor = conn.cursor()
    sql = '''
        SELECT COUNT(*) FROM userlinkinstance WHERE instance_id = %s
    '''
    cursor.execute(sql, (instance,))
    result = cursor.fetchone()
    conn.close()
    return result[0] > 0

def generate_profiles(nb_users, instance, gender, ethnicity, json_file_path):
    # Check if links already exist for this instance
    instance_has_links = check_instance_links(instance)
    
    # Get list of users already in this instance
    existing_instance_users = get_users_in_instance(instance)
    
    # Configure gender/ethnicity path format
    if gender == 0:
        name_gender = 'M'
    else:
        name_gender = 'F'
    pp_path = f'{name_gender}/{ethnicity}'
    print(pp_path)
    
    # Connect to database
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_instagram"
    )
    cursor = conn.cursor()
    
    # Get all users matching gender/ethnicity
    sql = '''
        SELECT user_id FROM users WHERE user_pp_path LIKE %s
    '''
    cursor.execute(sql, (f"{pp_path}/%",))
    all_matching_users = cursor.fetchall()
    
    # Filter out users already in the instance
    available_users = []
    for user in all_matching_users:
        if user[0] not in existing_instance_users:
            available_users.append(user[0])
    
    print(f"Found {len(available_users)} available users not yet in instance {instance}")
    
    # If we don't have enough available users, generate new ones
    if len(available_users) < nb_users:
        print(f"Not enough available in nr_instagram, {len(available_users)} found")
        needed_users = nb_users - len(available_users)
        print(f"Generating {needed_users} new profiles...")
        
        # Get the total count of existing users for this gender/ethnicity
        sql = '''
            SELECT COUNT(*) FROM users WHERE user_pp_path LIKE %s
        '''
        cursor.execute(sql, (f"{pp_path}/%",))
        last_user_index = cursor.fetchone()[0]
        
        # Generate completely new profiles
        generate_new_profiles(needed_users, last_user_index, instance, gender, ethnicity, json_file_path)
        
        # Get updated list of available users
        cursor.execute(sql, (f"{pp_path}/%",))
        all_updated_users = cursor.fetchall()
        
        # Add the newly generated users to our available list
        for user in all_updated_users:
            if user[0] not in existing_instance_users and user[0] not in available_users:
                available_users.append(user[0])
    
    # Take the first nb_users available users
    users_list = available_users[:nb_users]
    
    # Link these users to the instance
    print(f"Linking {len(users_list)} users to instance {instance}")
    fill_instance(instance, users_list)
    
    conn.close()
    return users_list


if __name__ == '__main__':
    
    generate_profiles(nb_users=5, instance=2, gender=0, ethnicity=0, json_file_path='python-scripts/nr_source/descriptions.json')
    #generate_profiles(nb_users=40, instance=1, gender=1, ethnicity=2, json_file_path='python-scripts/nr_source/descriptions.json')
    # generate_profiles(nb_users=40, instance=1, gender=0, ethnicity=1, json_file_path='python-scripts/nr_source/descriptions.json')
    # generate_profiles(nb_users=40, instance=1, gender=1, ethnicity=1, json_file_path='python-scripts/nr_source/descriptions.json')
    # generate_profiles(nb_users=40, instance=1, gender=0, ethnicity=2, json_file_path='python-scripts/nr_source/descriptions.json')
    print(descriptions_from_json('python-scripts/nr_instagram/profiles/descriptions.json'))