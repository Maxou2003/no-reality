import sys
sys.path.insert(0, 'python-scripts')

from nr_instagram.profiles.generateFollowings import generate_followings
from nr_instagram.profiles.generateProfiles import generate_profiles
from nr_instagram.profiles.create_descriptions import create_json
from nr_instagram.posts.generatePosts import generate_posts
from nr_instagram.posts.generateIdentifications import generate_identifications
from nr_instagram.posts.database import Database
import os
import shutil
import mysql.connector


def create_direcory(instance_name):
    # Create a copy of the folder if it doesn't already exist
    source_folder = "web/public/reference_instagram"
    destination_folder = f"web/public/{instance_name}"
    if not os.path.exists(destination_folder):
        print(f"Creating folder for instance: {destination_folder}")
        shutil.copytree(source_folder, destination_folder)
    else:
        print(f"Folder for instance {instance_name} already exists.")


def create_instance(instance_name, average_age, gender_prop, population):
    print(f"population: {population}")
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="nr_instagram"
    )
    cursor = conn.cursor()
    instance_exists = '''
        SELECT instance_id FROM instance WHERE instance_name = %s
    '''
    cursor.execute(instance_exists, (instance_name,))
    result = cursor.fetchone()
    if result is not None:
        print(f"Instance {instance_name} already exists. Instance ID: {result[0]}")
        insert_new_total_person = '''
            UPDATE instance 
            SET population = population + %s 
            WHERE instance_id = %s
        '''
        cursor.execute(insert_new_total_person, (population, result[0]))
        conn.commit()
        return result[0]
    
    # Create the instance if it doesn't exist
    sql = '''
        INSERT INTO instance (instance_name, average_age, gender_prop, population)
        VALUES (%s, %s, %s, %s)
    '''
    print(f"Creating instance {instance_name}...")
    cursor.execute(sql, (instance_name, average_age, gender_prop, population))
    conn.commit()
    instance_id = cursor.lastrowid
    print(f"Instance {instance_name} created with ID {instance_id}.")

    create_direcory(instance_name)

    return instance_id

    

def fill_instance(instance_name, people_list, post_list, post_file, pexel_api_key, follow_chance, follow_back_chance, database_name, precisions):
    nb_people = 0
    for i in range(len(people_list)):
        nb_people += people_list[i][0]
    print(nb_people)
    instance_id = create_instance(instance_name, 30, 0.5, nb_people)
    for people in people_list:
        generate_profiles(people[0], instance_id, people[1], people[2], 'python-scripts/nr_source/descriptions.json')
    generate_followings(instance_id, follow_chance, follow_back_chance)
    for post in post_list:
        generate_posts(DATABASE_NAME=database_name, INSTANCE_ID=instance_id, INSTANCE_NAME=instance_name,THEME=post[1], ENGLISH_THEME=post[2], PEXEL_API_KEY=pexel_api_key, POST_FILE=post_file, NUMBER_POSTS=post[0], PRECISIONS=precisions)
    generate_identifications(instance_id)


if __name__ == "__main__":
    """
    This script is used to fill the instance with people and posts.\n
    It creates the repository in public for the instance.\n
    It creates the instance in the database and generates the people and posts.\n
    It also generates the followings and identifications for the posts.\n
    """
    DATABASE_NAME = "nr_instagram"
    INSTANCE_NAME = "test"
    PEXEL_API_KEY = "0NMkYhKereL0Ne2PfmTECpAF7SFgy9vGzlWMY2ieB1ByvDGUpKzS3mJn"
    POST_FILE = "posts.json"

    #PEOPLE = [(5,0,0),(5,1,0),(5,0,1),(5,1,1),(5, 0, 2), (5, 1, 2), (5, 0, 3), (5, 1, 3)]
    PEOPLE = [(6,0,0), (10,1,2)]
    POSTS = [(10, 'voitures', 'cars'), (10, 'santé', 'health')]
    fill_instance(instance_name=INSTANCE_NAME, people_list=PEOPLE, post_list=POSTS, post_file=POST_FILE, pexel_api_key=PEXEL_API_KEY, follow_chance=0.3, follow_back_chance=0.9, database_name=DATABASE_NAME, precisions="")
    print("Instance filled")
