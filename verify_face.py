import sys
import os
import urllib.request
import json
import cv2
import numpy as np

# Print json helper
def exit_with_json(matched, message="", score=0.0):
    print(json.dumps({
        "matched": matched,
        "message": message,
        "score": float(score)
    }))
    sys.exit(0)

if len(sys.argv) < 3:
    exit_with_json(False, "Missing arguments. Usage: python verify_face.py <selfie_path> <registered_dir_path>")

selfie_path = sys.argv[1]
registered_dir = sys.argv[2]

if not os.path.exists(selfie_path):
    exit_with_json(False, f"Selfie file not found: {selfie_path}")

if not os.path.exists(registered_dir) or not os.path.isdir(registered_dir):
    exit_with_json(False, f"Registered directory not found or invalid: {registered_dir}")

# Models directory setup
MODELS_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'storage', 'models')
os.makedirs(MODELS_DIR, exist_ok=True)

# Correct GitHub raw URLs using the main branch
YUNET_URL = "https://github.com/opencv/opencv_zoo/raw/main/models/face_detection_yunet/face_detection_yunet_2023mar.onnx"
SFACE_URL = "https://github.com/opencv/opencv_zoo/raw/main/models/face_recognition_sface/face_recognition_sface_2021dec.onnx"

yunet_path = os.path.join(MODELS_DIR, 'yunet.onnx')
sface_path = os.path.join(MODELS_DIR, 'sface.onnx')

def ensure_model(url, path):
    if not os.path.exists(path) or os.path.getsize(path) < 10000:
        try:
            if os.path.exists(path):
                os.remove(path)
            urllib.request.urlretrieve(url, path)
        except Exception as e:
            exit_with_json(False, f"Failed to download face model: {str(e)}")

# Ensure models exist (only downloads if missing or invalid)
ensure_model(YUNET_URL, yunet_path)
ensure_model(SFACE_URL, sface_path)

# Initialize OpenCV face detector and recognizer
try:
    detector = cv2.FaceDetectorYN.create(
        yunet_path,      # model
        "",              # config
        (320, 320),      # inputSize
        0.6,             # scoreThreshold
        0.3,             # nmsThreshold
        5000             # topK
    )
    recognizer = cv2.FaceRecognizerSF.create(
        sface_path,      # model
        ""               # config
    )
except Exception as e:
    exit_with_json(False, f"Failed to initialize face recognition: {str(e)}")

def extract_feature_from_image(img):
    if img is None:
        return None
    h, w, _ = img.shape
    
    # Optimize detection speed: resize if image is too large while preserving aspect ratio
    max_dim = 640
    scale = 1.0
    if max(h, w) > max_dim:
        scale = max_dim / float(max(h, w))
        target_w = int(w * scale)
        target_h = int(h * scale)
        proc_img = cv2.resize(img, (target_w, target_h), interpolation=cv2.INTER_AREA)
    else:
        proc_img = img
        target_w, target_h = w, h

    detector.setInputSize((target_w, target_h))
    _, faces = detector.detect(proc_img)
    
    if faces is None or len(faces) == 0:
        return None
    
    first_face = faces[0].copy()
    if scale != 1.0:
        # Scale bounding box and landmarks back to original image coordinate space
        first_face[0:14] = first_face[0:14] / scale
    
    # Align and crop the first detected face from original image
    try:
        aligned_face = recognizer.alignCrop(img, first_face)
        feature = recognizer.feature(aligned_face)
        return feature
    except Exception:
        return None

def extract_feature(img_path):
    img = cv2.imread(img_path)
    return extract_feature_from_image(img)

# Extract feature from uploaded selfie
selfie_feature = extract_feature(selfie_path)
if selfie_feature is None:
    exit_with_json(False, "Wajah tidak terdeteksi pada foto selfie Anda.")

# Scan registered folder and compare
valid_extensions = ('.png', '.jpg', '.jpeg', '.webp')
registered_files = [os.path.join(registered_dir, f) for f in os.listdir(registered_dir) 
                    if f.lower().endswith(valid_extensions)]

if not registered_files:
    exit_with_json(False, "Tidak ada data foto wajah terdaftar di sistem.")

# Feature vector cache path inside registered directory
CACHE_FILE = os.path.join(registered_dir, '.embeddings_cache.json')

def get_registered_features():
    # Get latest modification time of registered images
    latest_mtime = max(os.path.getmtime(f) for f in registered_files)
    
    # Try reading from cache
    if os.path.exists(CACHE_FILE):
        try:
            with open(CACHE_FILE, 'r') as f:
                cache_data = json.load(f)
            if cache_data.get('mtime') == latest_mtime and 'features' in cache_data:
                features = [np.array(vec, dtype=np.float32) for vec in cache_data['features']]
                if features:
                    return features
        except Exception:
            pass

    # Recompute and cache features
    features = []
    serializable_features = []
    for reg_file in registered_files:
        reg_feat = extract_feature(reg_file)
        if reg_feat is not None:
            features.append(reg_feat)
            serializable_features.append(reg_feat.tolist())
    
    if serializable_features:
        try:
            with open(CACHE_FILE, 'w') as f:
                json.dump({
                    'mtime': latest_mtime,
                    'features': serializable_features
                }, f)
        except Exception:
            pass
            
    return features

registered_features = get_registered_features()
if not registered_features:
    exit_with_json(False, "Wajah tidak terdeteksi pada data foto wajah terdaftar.")

best_score = -1.0
COSINE_THRESHOLD = 0.363

for reg_feat in registered_features:
    try:
        score = recognizer.match(selfie_feature, reg_feat, cv2.FaceRecognizerSF_FR_COSINE)
        if score > best_score:
            best_score = score
    except Exception:
        continue

if best_score >= COSINE_THRESHOLD:
    exit_with_json(True, "Verifikasi wajah berhasil.", best_score)
else:
    exit_with_json(False, "Wajah tidak cocok dengan data wajah yang terdaftar.", best_score)
