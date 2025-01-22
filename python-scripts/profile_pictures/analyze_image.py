from thispersondoesnotexist import get_online_person
from thispersondoesnotexist import save_picture
from thispersondoesnotexist import Person
from deepface import DeepFace

"""for i in range(50):
    objs = DeepFace.analyze(
    img_path = f"generation\images\person_{i}.jpeg", 
    actions = ['age', 'gender', 'race'],
    )
    print(f"{objs}")"""
    
objs = DeepFace.analyze(
img_path = f"images\maxime_lambert.jpg", 
actions = ['age', 'gender', 'race'],
)
print(f"{objs}")


"""for i in range(100):
    picture = get_online_person()  # bytes representation of the image

    # Save to a file
    save_picture(picture, f"generation/images/person_{i}.jpeg")
    # If no filename is provided, one will be generated using the checksum of the picture"""
