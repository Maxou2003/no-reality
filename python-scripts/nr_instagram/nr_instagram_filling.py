import sys
sys.path.insert(0, 'python-scripts/nr_instagram')

from profiles.generateFollowings import generate_followings
from profiles.generateProfiles import generate_profiles
from posts.generatePosts import generate_posts
from posts.generateIdentifications import generate_identifications
from posts.database import Database

def create_instance(instance_name, average_age, gender_prop, population, database_name):
    database = Database(database_name)
    database.connect()
    sql = '''
        INSERT INTO instance (instance_name, average_age, gender_prop, population)
        VALUES (%s, %s, %s, %s)
        '''
    return database.execute(sql, (instance_name, average_age, gender_prop, population))
    

def fill_instance(instance_name, people_list, post_list, post_file, pexel_api_key, follow_chance, follow_back_chance, database_name, precisions):
    instance_id = create_instance(instance_name, 30, 0.5, 100, database_name)
    for people in people_list:
        generate_profiles(people[0], instance_id, people[1], people[2], 'python-scripts/nr_source/descriptions.json')
    generate_followings(instance_id, follow_chance, follow_back_chance)
    for post in post_list:
        generate_posts(DATABASE_NAME=database_name, INSTANCE_ID=instance_id, INSTANCE_NAME=instance_name,THEME=post[1], ENGLISH_THEME=post[2], PEXEL_API_KEY=pexel_api_key, POST_FILE=post_file, NUMBER_POSTS=post[0], PRECISIONS=precisions)
    generate_identifications(instance_id)
if __name__ == "__main__":
    DATABASE_NAME = "nr_instagram"
    INSTANCE_ID = 2
    INSTANCE_NAME = "love"
    THEME = "art"
    ENGLISH_THEME = "art"
    PEXEL_API_KEY = "0NMkYhKereL0Ne2PfmTECpAF7SFgy9vGzlWMY2ieB1ByvDGUpKzS3mJn"
    POST_FILE = "art_posts.json"

    PEOPLE = [(40, 0, 0), (40, 1, 0), (40, 0, 1), (40, 1, 1), (40, 0, 2), (40, 1, 2), (40, 0, 3), (40, 1, 3)]
    POSTS = [(40, 'art', 'art'), (40, 'nourriture', 'food'), (40, 'nature', 'nature'), (40, 'amour', 'love')]
    fill_instance(instance_name=INSTANCE_NAME, people_list=PEOPLE, post_list=POSTS, post_file=POST_FILE, pexel_api_key=PEXEL_API_KEY, follow_chance=0.3, follow_back_chance=0.9, database_name=DATABASE_NAME, precisions="")
    print("Done")
