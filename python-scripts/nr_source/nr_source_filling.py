from deepface import DeepFace
import mysql.connector
import sys
sys.path.insert(0, 'python-scripts')
from nr_source.get_names import get_names
from nr_source.image_scraping import download_images
import math

def clean_value(x):
    if isinstance(x, (float, int)) and math.isnan(x):
        return None
    return x if x == x else None  # Handles NaN for strings

def check_last_index(gender, ethnicity):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_source"
    )

    cursor = conn.cursor()
    
    # Convert gender/ethnicity to match the format used in paths
    gender_str = 'M' if gender == 0 else 'F'
    ethnicity_str = str(ethnicity)
    
    # Look for pattern that matches the specific gender/ethnicity folder structure
    pattern = f"{gender_str}/{ethnicity_str}/image_%.jpg"
    
    cursor.execute('''
         SELECT user_pp_path FROM users WHERE user_pp_path LIKE %s
    ''', (pattern,))
    
    rows = cursor.fetchall()
    
    max_index = -1
    for row in rows:
        # Extract index from path like "web/profile_pictures/M/0/image_42.jpg"
        try:
            img_path = row[0]
            index = int(img_path.split('image_')[1].split('.jpg')[0])
            max_index = max(max_index, index)
        except (IndexError, ValueError):
            pass
    
    cursor.close()
    conn.close()
    
    return max_index

def create_user_table():
    """
    Create the users table in the nr_source database if it doesn't exist.
    """
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_source"
    )
    cursor = conn.cursor()
    # Create table if it doesn't exist
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            user_firstname VARCHAR(50),
            user_lastname VARCHAR(50),
            user_pp_path VARCHAR(255),
            user_age INT,
            user_gender VARCHAR(20),
            user_ethnicity VARCHAR(50)
        )
    ''')

def execute_sql_insert(forename, surname, age, image_path, gender, ethnicity):
    # Connect to MySQL database
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_source"
    )
    cursor = conn.cursor()

    cleaned_values = (
        clean_value(forename),
        clean_value(surname),
        image_path,
        clean_value(age),
        clean_value(gender),
        clean_value(ethnicity)
    )

    # Parameterized query
    sql = '''
        INSERT INTO users (
            user_firstname, 
            user_lastname, 
            user_pp_path, 
            user_age, 
            user_gender, 
            user_ethnicity
        ) VALUES (%s, %s, %s, %s, %s, %s)
    '''
    
    # Execute with values
    cursor.execute(sql, cleaned_values)
    
    # Commit and close
    conn.commit()
    conn.close()

def create_persons(nb, binary_gender, ethnicity, path):
    """
    Create persons with the given parameters in nr_source database.\n
    :param nb: Number of persons to create\n
    :param binary_gender: gender of the persons (0 for male, 1 for female)\n
    :param ethnicity: ethnicity of the persons (0 for white, 1 for black, 2 for asian, 3 for latino)\n
    :param path: path to save the images\n
    :return: None
    """
    country_code_forename = "FR"
    country_code_surname = "FR"
    if binary_gender == 0:
        gender = 'M'
    elif binary_gender == 1:
        gender = 'F'
    names = get_names(nb+1, country_code_forename, country_code_surname, gender)
    temp_path = f"{path}/{gender}/{ethnicity}"
    
    # Get the last index used for this gender/ethnicity combination
    last_index = check_last_index(binary_gender, ethnicity)
    images_paths = download_images(nb, gender, ethnicity, temp_path, start_index=last_index+1)
    create_user_table()
    for i in range(len(images_paths)):
        objs = DeepFace.analyze(
        img_path = images_paths[i],
        actions = ['age'],
        )
        age = objs[0]["age"]
        forename = names[i]['forename']
        surname = names[i]['lastname']
        execute_sql_insert(forename, surname, age, images_paths[i][len(path)+1::], binary_gender, ethnicity)
        print(f"{forename} {surname} is {age} years old and he/she looks like {images_paths[i]}")
    


if __name__ == '__main__':
    from get_names import get_names
    from image_scraping import download_images
    """
    create_persons(50, 0, 0, "web/profile_pictures")
    create_persons(50, 1, 0, "web/profile_pictures")
    create_persons(50, 0, 1, "web/profile_pictures")
    create_persons(50, 1, 1, "web/profile_pictures")"""
    create_persons(20, 0, 2, "web/profile_pictures")
    #create_persons(50, 1, 2, "web/profile_pictures")