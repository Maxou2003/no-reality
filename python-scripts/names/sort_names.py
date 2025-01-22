import pandas as pd
# Open the CSV file and read the values of the column 'count'
df = pd.read_csv('names_dataset/forenames.csv')

# Filter the dataframe where the count is greater than 10000
filtered_df = df[df['country'] == 'IN']
filtered_df = filtered_df[filtered_df['count'] > 5000]


# Verify if the string in filtered_df['surname'] is not empty and only contains A-Z a-z
filtered_df = filtered_df[filtered_df['forename'].str.match('^[A-Za-z]+$') & filtered_df['forename'].str.strip().astype(bool)]


# Select the columns 'forename', 'count', and 'country'
filtered_df = filtered_df[['forename', 'gender', 'count', 'country']]

# Write the filtered dataframe to a new CSV file


with open ('names_filtered/filtered_forenames.csv', 'a') as f:
    f.write(filtered_df.to_csv(index=False))