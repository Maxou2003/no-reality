import pandas as pd
import random

# Read the CSV file
forenames_df = pd.read_csv('python-scripts/names/names_filtered/filtered_forenames.csv')
surnames_df = pd.read_csv('python-scripts/names/names_filtered/filtered_surnames.csv')



choice_race = int(input("1 pour blanc\n2 pour noir\n3 pour moyen-orient\n4  pour asiatique\n5 pour indien\n"))
choice_sexe = int(input("1 pour homme\n2 pour femme\n"))
if choice_race==1:
    country_code_forename = "FR"
    country_code_surname = "FR"
elif choice_race==2:
    country_code_forename = "FR"
    country_code_surname = "CM"
elif choice_race==3:
    country_code_forename = "JO"
    country_code_surname = "DZ"
elif choice_race==4:
    country_code_forename = "FR"
    country_code_surname = "JP"
elif choice_race==5:
    country_code_forename = "IN"
    country_code_surname = "IN"

if choice_sexe==1:
    gender = 'M'
elif choice_sexe==2:
    gender = 'F'
# Filter the dataframe where the country is 'FR'
forenames_country_df = forenames_df[forenames_df['country'] == country_code_forename]
forenames_gender_df = forenames_country_df[forenames_country_df['gender'] == gender]

surnames_country_df = surnames_df[surnames_df['country'] == country_code_surname]
# Get a random forename from the filtered dataframe
for i in range(20):

    random_forename = random.choice(forenames_gender_df['forename'].tolist())
    random_lastname = random.choice(surnames_country_df['surname'].tolist())
    print(random_forename, random_lastname)