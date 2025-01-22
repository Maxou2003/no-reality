from thispersondoesnotexist import get_online_person
from thispersondoesnotexist import save_picture

for i in range(10):
    picture = get_online_person()  

    # Save to a file
    save_picture(picture, f"images/person_{i}.jpeg")