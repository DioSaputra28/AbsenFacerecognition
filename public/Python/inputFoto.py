import os
import io
from os import listdir
from PIL import Image
from numpy import asarray, expand_dims
from keras_facenet import FaceNet
import pickle
import cv2
import sys

# Atur encoding untuk output
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

# Inisialisasi model FaceNet dan HaarCascade
HaarCascade = cv2.CascadeClassifier(cv2.samples.findFile(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'))
MyFaceNet = FaceNet()

folder = '/xamppver8/htdocs/e-learning/public/Python/fotoPeserta/'
database = {}

for filename in listdir(folder):
    try:
        # Mengatasi masalah encoding jika ada karakter non-ASCII
        filename = filename.encode('utf-8').decode('utf-8')

        path = os.path.join(folder, filename)
        gbr1 = cv2.imread(path)

        wajah = HaarCascade.detectMultiScale(gbr1, 1.1, 4)

        if len(wajah) > 0:
            x1, y1, width, height = wajah[0]
        else:
            x1, y1, width, height = 1, 1, 10, 10

        x1, y1 = abs(x1), abs(y1)
        x2, y2 = x1 + width, y1 + height

        gbr = cv2.cvtColor(gbr1, cv2.COLOR_BGR2RGB)
        gbr = Image.fromarray(gbr)  # konversi dari OpenCV ke PIL
        gbr_array = asarray(gbr)

        face = gbr_array[y1:y2, x1:x2]

        face = Image.fromarray(face)
        face = face.resize((160, 160))
        face = asarray(face)
        face = expand_dims(face, axis=0)
        signature = MyFaceNet.embeddings(face)

        database[os.path.splitext(filename)[0]] = signature

    except UnicodeEncodeError as e:
        print(f"Encoding error processing {filename}: {e}")
    except Exception as e:
        print(f"Error processing {filename}: {e}")

# Simpan database ke file data.pkl
try:
    with io.open("/xamppver8/htdocs/e-learning/public/Python/data.pkl", "wb") as myfile:
        pickle.dump(database, myfile)
except Exception as e:
    print(f"Error saving database: {e}")

# Output pesan bahwa proses training sudah selesai
print("Proses training telah selesai")
