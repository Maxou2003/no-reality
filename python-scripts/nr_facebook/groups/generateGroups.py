from generateJson import generate_groups_names
import mysql.connector
import json
import unicodedata
import sys
import os
root_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
if root_path not in sys.path:
    sys.path.insert(0, root_path)
from config import HOST, USER, PASSWORD, DATABASE_FACEBOOK
from nr_facebook.posts.generatePosts import generate_posts

def fill_group_names_table(instance_id, group_name, group_description, theme):
    """
    Create entries in the group_names table
    """
    conn = mysql.connector.connect(
        host=HOST,
        user=USER,
        password=PASSWORD,
        database=DATABASE_FACEBOOK
    )
    cursor = conn.cursor()
    sql_insert = '''
        INSERT INTO groups (
            instance_id,
            group_name,
            group_description,
            group_slug
        ) VALUES (
            %s, %s, %s, %s
        )
    '''
    slug = create_group_slug(group_name)  # Create slug for the group name
    cursor.execute(sql_insert, (instance_id, group_name, group_description, slug))
    conn.commit()
    cursor.close()
    conn.close()

def create_group_slug(group_name):
    """
    Create a slug for the group name
    :param group_name: name of the group
    :returns: slug: unique slug for the group
    """
    conn = mysql.connector.connect(
        host=HOST,
        user=USER,
        password=PASSWORD,
        database=DATABASE_FACEBOOK
    )
    cursor = conn.cursor()

    # Normalize and remove accents
    slug = ''.join(
        c for c in unicodedata.normalize('NFD', group_name) if unicodedata.category(c) != 'Mn'
    )
    # Remove whitespace and capitalize words
    slug = ''.join(word.capitalize() for word in slug.split())

    # Check if the slug already exists in the database
    sql_check = '''
        SELECT COUNT(*) FROM groups WHERE group_slug = %s
    '''
    original_slug = slug
    counter = 1
    while True:
        cursor.execute(sql_check, (slug,))
        if cursor.fetchone()[0] == 0:  # Slug is unique
            break
        slug = f"{original_slug}{counter}"
        counter += 1

    # Close the connection
    cursor.close()
    conn.close()

    return slug



def generate_groups(instance_id, nb_group, theme, file_path):
    """
    Create a JSON file with group names and descriptions
    and insert them into the database.
    """
    # Generate group names using the generate_groups_names function
    generate_groups_names('train', nb_group, file_path)

    with open(file_path, 'r', encoding='utf-8') as file:
        data = json.load(file)
        group_names = data['names']
        for group in group_names:
            # Extract group name and description
            group_name = list(group.keys())[0]
            group_description = group[group_name]

            # Insert into the database
            fill_group_names_table(instance_id, group_name, group_description, theme)


if __name__ == '__main__':
    theme = 'chevaux'
    english_theme = 'horses'
    average_posts_per_group = 5
    nb_group = 10
    instance_id = 1
    file_path = 'python-scripts/nr_facebook/groups/groups_names.json'
    generate_groups(instance_id, nb_group, theme, file_path)

