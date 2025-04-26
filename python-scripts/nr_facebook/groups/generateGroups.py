import mysql.connector
import json
import unicodedata
import sys
import os
root_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
if root_path not in sys.path:
    sys.path.insert(0, root_path)
from generateJson import generate_groups_names
from config import HOST, USER, PASSWORD, DATABASE_FACEBOOK
from nr_facebook.posts.getPostPictures import download_images_with_api
from nr_facebook.groups.assignUsersToGroups import assign_users_to_groups

def fill_group_names_table(instance_id, group_name, group_description, banner_picture_path):
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
            group_slug,
            group_banner_picture_path
        ) VALUES (
            %s, %s, %s, %s, %s
        )
    '''
    slug = create_group_slug(group_name)  # Create slug for the group name
    cursor.execute(sql_insert, (instance_id, group_name, group_description, slug, banner_picture_path))
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

def get_last_page_of_post_associated_with_theme(theme, instance_id):
    """
    Get the last page of posts associated with a theme from the database.
    :param theme: str - The theme to search for.
    :return: int - The last page number.
    """
    # Placeholder for actual database query to get the last page number
    conn = mysql.connector.connect(
        host=HOST,
        user=USER,
        password=PASSWORD,
        database=DATABASE_FACEBOOK
    )
    cursor = conn.cursor()
    query = """
        SELECT group_banner_picture_path 
        FROM groups 
        WHERE instance_id = %s AND group_banner_picture_path LIKE %s
        ORDER BY group_banner_picture_path DESC
        LIMIT 1
    """
    theme_pattern = f"{theme}_%_%"  # Matches the format theme_page_index
    cursor.execute(query, (instance_id, theme_pattern))
    result = cursor.fetchone()
    
    if result:
        last_picture_path = result[0]
        # Extract the page number from the path (assuming format theme_page_index)
        try:
            page = int(last_picture_path.split('_')[1])
            return page
        except (ValueError, IndexError) as e:
            print(f"Error parsing page number from post_picture_path: {e}")
            return 0
    else:
        return 0

def get_instance_id(instance_name):
    """
    Get the instance_id from the database.
    :param instance_name: str - The name of the instance.
    :return: int - The instance_id.
    """
    conn = mysql.connector.connect(
        host=HOST,
        user=USER,
        password=PASSWORD,
        database=DATABASE_FACEBOOK
    )
    cursor = conn.cursor()
    sql_query = '''
        SELECT instance_id FROM instances WHERE instance_name = %s
    '''
    cursor.execute(sql_query, (instance_name,))
    result = cursor.fetchone()
    
    if result:
        return result[0]
    else:
        print(f"Instance '{instance_name}' not found.")
        return None

def generate_groups(instance_name, nb_group, theme, english_theme, pexel_api_key, file_path):
    """
    Create a JSON file with group names and descriptions
    and insert them into the database.
    """
    # Generate group names using the generate_groups_names function
    generate_groups_names(theme, nb_group, file_path)
    instance_id = get_instance_id(instance_name)
    if instance_id is None:
        print(f"Instance '{instance_name}' not found.")
        return
    with open(file_path, 'r', encoding='utf-8') as file:
        data = json.load(file)
        group_names = data['names']
        banner_pictures_directory = f"web/public/{instance_name}/img/banner_pictures"
        starting_index = get_last_page_of_post_associated_with_theme(theme, instance_id) + 1
        banner_pathes = download_images_with_api(english_theme, pexel_api_key, len(group_names), banner_pictures_directory, starting_index)
        for i in range(len(group_names)):
            # Extract group name and description
            group_name = list(group_names[i].keys())[0]
            group_description = group_names[i][group_name]
            # Insert into the database
            fill_group_names_table(instance_id, group_name, group_description, banner_pathes[i])
            # Generate posts for the groups
            #generate_group_posts(instance_id, group_name, group_description, theme, english_theme, banner_pathes[i], pexel_api_key, starting_index)
        assign_users_to_groups(instance_id)


    

if __name__ == '__main__':
    theme = 'chevaux'
    english_theme = 'horses'
    average_posts_per_group = 5
    nb_group = 10
    instance_name = 'horses'
    file_path = 'python-scripts/nr_facebook/groups/groups_names.json'
    pexel_api_key = "0NMkYhKereL0Ne2PfmTECpAF7SFgy9vGzlWMY2ieB1ByvDGUpKzS3mJn"
    
    generate_groups(instance_name, nb_group, theme, english_theme, pexel_api_key, file_path)

