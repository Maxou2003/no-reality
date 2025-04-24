from mistralai import Mistral
import json

def generate_groups_names(theme,nb_groups,file_path,precisions=""):
    """
    Creates a json containing the names of groups on a certain theme
    """
    api_key = "0c39aFiRhrVY69uI1sTbF3f3HSQAyXmN"
    model = "mistral-large-latest"

    client = Mistral(api_key=api_key)

    goal = f"Écris {nb_groups} titres et descriptions pour {nb_groups} groupes facebook sur le theme: {theme}. " \
        f"Le titre doit être court et accrocheur. Chaque description est courte et unique" 
    examples = 'Exemple de noms attendus: Si le thème est chien: "Nos amis les chiens", "Les amis canins", "Fans de chiens"'
    format_instructions = "Ne réponds que par json contenant les informations respecte le format suivant:" \
        '{"names":[{"nom groupe 1": "description du groupe"}, {"nom groupe 2": "description du groupe"}, {"nom groupe 3": "description du groupe"}, ...]}'
    warnings = "Ne répond que par le json sous forme de string, pas de markdown pour indiqué que c'est un json ni de phrases. Chaque description doit être unique"
    
    final_prompt = f'{goal}\n{examples}\n{format_instructions}\n{warnings}\n{precisions}'
    print(final_prompt)
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
    print(response)
    if response.startswith('```json'):
        response = response[7:]
        if response.endswith('```'):
            response = response[:-3]
    # Try to parse JSON
    json_string = response.strip()
    try:
        json_object = json.loads(json_string)
        print("JSON valide, converti en:", json_object)
        
        # Write valid JSON to file
        with open(file_path, 'w', encoding='utf-8') as fichier:
            json.dump(json_object, fichier, indent=4)
    except json.JSONDecodeError as e:
        print(f"Erreur: JSON invalide - {e}")
        

if __name__ == '__main__':
    file_path = 'python-scripts/nr_facebook/groups/groups_names.json'
    generate_groups_names('train',10,file_path)