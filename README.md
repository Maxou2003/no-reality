# no-reality


No reality est un outil d'OSINT pour générer des instances de réseau sociaux figées.

## Setup pour les scripts pythons

```python
pip install mistralai
pip install requests
pip install deepface
pip install mysql
```

## Utilisation

### Config

Dans config.py, remplir les informations pour accéder à la base de données. (penser à créer un .env à la place si vous comptez push en public)

### Instagram

Pour remplir Instagram, utiliser les script nr_instagram/nr_instagram_filling.py.

On remplit:
- **INSTANCE_NAME** le nom de l'instance à créer ou à modifier.

- **PEOPLE** la liste des utilisateurs, sous la forme:   
**[(nb_personnes, genre, ethnie),(nb_personnes, genre, ethnie)...]**  
_genre_ = 0 pour homme, 1 pour femme,  
_ethnie_ = 0 pour blanc, 1 pour noir, 2 pour asiatique, 3 pour latino.  

- **POSTS** la liste des posts, sous la forme:  
**[(nb_posts, theme, theme_en_anglais),(nb_posts, theme, theme_en_anglais)...]**

Nous avons aussi des scripts pour supprimer facilement des posts (deletePost.py), des utilisateurs (deleteUser.py) et des instances (deleteInstance.py)

### Facebook

Les scripts Facebook ne sont pas entièrement finis, il n'y a donc pas un seul script nr_facebook_filling.py

Il faut donc lancer les scripts 1 par 1:

- **profiles/generateProfiles.py**   
génère et insère des profils facebook dans la base

- **profiles/generateFriends.py**  
créé les interactions _amitiés_ automatiquement
- **posts/generatePosts.py**  
génère et insère des posts dans la base, télécharge les images associées
- **groups/generateGroups.py**  
génère et insère des groupes dans la base, télécharge les images associées, créé les relations _membres du groupe_.

