from mistralai import Mistral
import json


def create_json(json_file, person_list):
    api_key = "0c39aFiRhrVY69uI1sTbF3f3HSQAyXmN"
    model = "mistral-large-latest"

    client = Mistral(api_key=api_key)


    goal = f'Écris un fichier json avec les pseudos et descriptions instagram de chacune des personnes suivantes : {person_list}. Écris les descriptions à la première personne du singuler'
    
    format_instructions =  'Suis exactement ce format: {"descriptions": {"pseudo":"La description de la personne"}, {"pseudo": "La description de la personne"}, ...}'

    warnings = 'Ne répond que par le json sous forme de string, pas de markdown pour indiqué que c est un json ni de phrases. Sois original dans chaque description. Ne copie pas les descriptions des autres personnes. Pas d emoji. N oublie aucune personne. Format URL, pas d accent ou d underscore.'


    final_prompt = f'{goal}\n\n{format_instructions}\n\n{warnings}'
    chat_response = client.chat.complete(
        model= model,
        messages = [
            {
                "role": "user",
                "content": final_prompt,
            },
        ]
    )
    response = chat_response.choices[0].message.content

    # Check if the format is correct
    if response.startswith('```json'):
        response = response[7:-3]


    json_string = response
    try:
        json_object = json.loads(json_string)
        #print("JSON valide, converti en:", json_object)
    except json.JSONDecodeError as e:
        print("Erreur: JSON invalide -", e)

    with open(json_file, 'w', encoding='utf-8') as fichier:
        json.dump(json_object, fichier, indent=4)

if __name__ == '__main__':
    create_json('python-scripts/nr_source/descriptions.json', [['John', 'Doe', 53], ['Jane', 'Doe', 21], ['Alice', 'Smith', 30], ['Bob', 'Smith', 40]])