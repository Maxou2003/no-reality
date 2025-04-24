from mistralai import Mistral
import json

def generate_json(theme, number_posts, min_comments, max_comments,file, precisions=""):
    api_key = "0c39aFiRhrVY69uI1sTbF3f3HSQAyXmN"
    model = "mistral-large-latest"

    client = Mistral(api_key=api_key)


    prompt = f'Écris {str(number_posts)} descriptions pour {str(number_posts)} posts instagram sur le thème: "{theme}" sous forme de json.'\
        f'Tu peux différer légérement du thème en parlant de quelque chose en rapport. Chaque posts a un nombre de commentaires aléatoire entre {str(min_comments)} et {str(max_comments)}.'\
        f'Change de nombre de commentaires pour chaques post. Certains commentaires ont des réponses, d autres non. Crée une ou deux réponses pour certains commentaires.'
    
    description_instructions = ''\
    'Cette description ne doit pas se référer spécifiquement à l image à laquelle elle est associée mais être très général sur le thème du post.'\
    'Par exemple, "J ai passé des vacances incroyables." si le thème évoque un pays ou bien une description rigolote en rapport avec le thème.'
    
    comments_instructions = '' \
    'De même pour les commentaires. Ceux-ci doivent être courts mais doivent se référer à la description associée.'\
    'Par exemple, "J y suis allé cet été !" si la description est "J ai passé des vacances incroyables." ou "Trop mignon !" si la description est "J ai adopté un chaton."'\
    'ou bien "LoL" si la description est une blague.'
    
    format_instructions =  ''\
    'Suis ce format de json: {"posts":[{"description":"...","comments":[{"content":"...", "responses":[]}, {"content":"...", "responses":[{"response":"..."}]}]},{"description":"...","comments":[{"content":"...", "responses":[{"response":"..."}]}, {"content": "...", "responses":[]}]}'\
    'mais fais absolument en sorte qu il y ait un nombre de commentaires et de réponses qui change en fonction des posts'

    warnings = '' \
    'Ne réponds que par le json sous forme de string, pas de markdown pour indiqué que c est un json ni de phrases.'\
    'Ne te répète pas et sois original dans les descriptions, les commentaires et les réponses. Tu peux être négatif, comme sur un vrai réseau social.'\
    'Ne mentionne pas forcément le thème in texto. Le plus important est d être réaliste.'

    final_prompt = f'{prompt} \n{description_instructions} \n{comments_instructions} \n{format_instructions} \n{warnings} \n{precisions}'
    print(final_prompt)
    print('Generating... (this may take a while)')
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
        generate_json(theme, number_posts, min_comments, max_comments)

    with open(file, 'w', encoding='utf-8') as fichier:
        json.dump(json_object, fichier, indent=4)


theme = 'littérature'
number_posts = 50
min_comments = 1
max_comments = 5
precisions = ""
file = 'art_posts.json'

if __name__ == '__main__':
    generate_json(theme, number_posts, min_comments, max_comments, file, precisions)