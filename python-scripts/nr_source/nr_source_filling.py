from get_names import get_names
from image_scraping import download_images
from deepface import DeepFace
import mysql.connector


def create_table():
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
    cursor.execute(sql, (forename, surname, image_path, age, gender, ethnicity))
    
    # Commit and close
    conn.commit()
    conn.close()

def create_persons(nb, binary_gender, ethnicity, path):
    if ethnicity == 0:
        country_code_forename = "FR"
        country_code_surname = "FR"
    if binary_gender == 0:
        gender = 'M'
    else:
        gender = 'F'
    names = get_names(nb+1, country_code_forename, country_code_surname, gender)
    images_paths = download_images(nb, gender, path)
    
    create_table()
    for i in range(len(images_paths)):
        objs = DeepFace.analyze(
        img_path = images_paths[i],
        actions = ['age'],
        )
        age = objs[0]["age"]
        forename = names[i]['forename']
        surname = names[i]['lastname']
        print(images_paths[i][len(path)::])
        execute_sql_insert(forename, surname, age, images_paths[i][len(path)+1::], binary_gender, ethnicity)
        print(f"{forename} {surname} is {age} years old and he/she looks like {images_paths[i]}")
    


if __name__ == '__main__':
    create_persons(50, 0, 0, "web/profile_pictures")