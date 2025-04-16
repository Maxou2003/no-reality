import csv

# Input and output file paths
input_file = 'python-scripts/nr_facebook/communes-france-2025.csv'
total_population = 0

# Read the CSV file and calculate the total population
with open(input_file, mode='r', encoding='utf-8') as file:
    reader = csv.DictReader(file)
    for row in reader:
        population = int(row['population'])  # Assuming the column is named 'population'
        total_population += population

print(f"Total population: {total_population}")