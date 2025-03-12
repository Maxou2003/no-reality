from mistralai import Mistral
import json

def create_json(theme, number_posts, min_comments, max_comments,file, precisions=""):
    api_key = "0c39aFiRhrVY69uI1sTbF3f3HSQAyXmN"
    model = "mistral-large-latest"

    client = Mistral(api_key=api_key)


    prompt = f'Écris {str(number_posts)} descriptions pour {str(number_posts)} posts instagram sur le thème: {theme} sous forme de json. Tu peux différer légérement du thème en parlant de quelque chose en rapport. Chaque posts a un nombre de commentaires aléatoire entre {str(min_comments)} et {str(max_comments)}. Certains commentaires ont des réponses, d autres non. Crée une ou deux réponses pour certains commentaires.'
    description_instructions = 'Cette description ne doit pas se référer spécifiquement à l image à laquelle elle est associée mais être très général sur le thème du post. Par exemple, "J ai passé des vacances incroyables." si le thème évoque un pays ou bien une description rigolote en rapport avec le thème.'
    comments_instructions = 'De même pour les commentaires. Ceux-ci doivent être courts mais doivent se référer à la description associée. Par exemple, "J y suis allé cet été !" si la description est "J ai passé des vacances incroyables." ou "Trop mignon !" si la description est "J ai adopté un chaton." ou bien "LoL" si la description est une blague.'
    format_instructions =  'Suis exactement ce format de json: {"posts":[{"description":"Les cookies de ma maman","comments":[{"content":"Super !", "responses":[]}, {"content":"Ils ont l air bons...", "responses":[{"response":"Ils le sont !"}]}]},{"description":"La cuisine japonaise est délicieuse et variée.","comments":[{"content":"Je prérère la cuisine italienne", "responses":[{"response":"La japonaise est meilleure !"}]}, {"content": "J adore les sushis !", "responses":[]}]}'
    warnings = 'Ne répond que par le json sous forme de string, pas de markdown pour indiqué que c est un json ni de phrases. Ne te répète pas et sois original dans les descriptions, les commentaires et les réponses. Tu peux être négatif, comme sur un vrai réseau social. Le plus important est d être réaliste.'

    final_prompt = f'{prompt} \n{description_instructions} \n{comments_instructions} \n{format_instructions} \n{warnings} \n{precisions}'
    print(final_prompt)
    print('Generating...')
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
        create_json(theme, number_posts, min_comments, max_comments)

    with open(file, 'w', encoding='utf-8') as fichier:
        json.dump(json_object, fichier, indent=4)


theme = 'art. Cela peut être des posts sur une oeuvre que la personne a fait ou a vu par exemple'
number_posts = 100
min_comments = 1
max_comments = 5
precisions = "crée des descriptions d'artiste, c'est un artiste qui les poste sur son compte instagram. NE TE REPETE JAMAIS"
file = 'art_posts.json'

if __name__ == '__main__':
    create_json(theme, number_posts, min_comments, max_comments, file, precisions)