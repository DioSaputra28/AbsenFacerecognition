import os
import cv2
from PIL import Image
from numpy import asarray, expand_dims
import numpy as np
import pickle
from keras_facenet import FaceNet
import tensorflow as tf
import io
import sys
import mysql.connector
# Disable TQDM Progress Bar
from tqdm import tqdm
tqdm.__init__ = lambda *args, **kwargs: None

# Disable TensorFlow log messages
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
tf.get_logger().setLevel('ERROR')
tf.autograph.set_verbosity(0)

# Inisialisasi model FaceNet dan HaarCascade
HaarCascade = cv2.CascadeClassifier(cv2.samples.findFile(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'))
MyFaceNet = FaceNet()

def get_latest_image_path(folder_path):
    import glob
    import os

    # Cari semua file dengan ekstensi .jpg di folder_path
    list_of_files = glob.glob(os.path.join(folder_path, '*.jpeg'))
    if list_of_files:
        # Ambil file yang paling baru berdasarkan waktu modifikasi
        return max(list_of_files, key=os.path.getctime)
    return None

def process_and_identify_image(image_path):
    gbr1 = cv2.imread(image_path)
    wajah = HaarCascade.detectMultiScale(gbr1, 1.1, 4)

    if len(wajah) > 0:
        # Ambil koordinat dari wajah pertama yang terdeteksi
        x1, y1, width, height = wajah[0]
        x1, y1 = abs(x1), abs(y1)
        x2, y2 = x1 + width, y1 + height

        gbr = cv2.cvtColor(gbr1, cv2.COLOR_BGR2RGB)
        gbr = Image.fromarray(gbr)
        gbr_array = asarray(gbr)

        # Potong gambar untuk mengambil wajah
        face = gbr_array[y1:y2, x1:x2]
        face = Image.fromarray(face)
        face = face.resize((160, 160))
        face = asarray(face)
        face = expand_dims(face, axis=0)

        # Dapatkan embedding dari wajah
        signature = MyFaceNet.embeddings(face)

        min_dist = 100
        identity = 'Unknown'
        for key, value in database.items():
            # Hitung jarak antara signature yang terdeteksi dengan signature dalam database
            dist = np.linalg.norm(value - signature)
            if dist < min_dist:
                min_dist = dist
                identity = key

        return identity
    else:
        return "Tidak ada wajah terdeteksi."

# Set sys.stdout encoding
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

# Path absolut ke file data.pkl
script_dir = os.path.dirname(os.path.abspath(__file__))  # Direktori dari skrip Python
data_file_path = os.path.join(script_dir, 'data.pkl')

db = mysql.connector.connect(
    host='127.0.0.1',
    port=3306,
    database='e-learning-main',
    user='root',
    password=''
)
# Load database dari file data.pkl
try:
    with open(data_file_path, "rb") as myfile:
        database = pickle.load(myfile)
except FileNotFoundError as e:
    print(f"File not found: {data_file_path}")
    sys.exit(1)

if __name__ == "__main__":
    # Path ke folder yang berisi gambar
    folder_path = "/xamppver8/htdocs/e-learning/public/Python/image"
    latest_image_path = get_latest_image_path(folder_path)
    if latest_image_path:
        # Identifikasi wajah pada gambar terbaru
        identity = process_and_identify_image(latest_image_path)
        print(identity)  # Output hanya identitas
    else:
        print("Tidak ada gambar jpeg terbaru ditemukan di folder.")