"""
Download furniture images from Unsplash and resize to 800x600 JPEG.
Run: python download_images.py
"""
import urllib.request
import os
from PIL import Image
import io

OUTPUT_DIR = os.path.join(os.path.dirname(__file__), "public", "images")
os.makedirs(OUTPUT_DIR, exist_ok=True)

# Direct Unsplash CDN URLs — format: photo-{id}?w=800&h=600&fit=crop
BASE = "https://images.unsplash.com/photo-"
IMAGES = {
    "canape":        BASE + "1555041469-a586c61ea9bc?w=800&h=600&fit=crop&auto=format",
    "table":         BASE + "1549187774-b4e9b0445b41?w=800&h=600&fit=crop&auto=format",
    "meuble-tv":     BASE + "1593784991095-a205069470b6?w=800&h=600&fit=crop&auto=format",
    "fauteuil":      BASE + "1506439773649-6e0eb8cfb237?w=800&h=600&fit=crop&auto=format",
    "lit":           BASE + "1588046130717-0eb0c9a3ba15?w=800&h=600&fit=crop&auto=format",
    "armoire":       BASE + "1556909114-f6e7ad7d3136?w=800&h=600&fit=crop&auto=format",
    "commode":       BASE + "1555041469-a586c61ea9bc?w=800&h=600&fit=crop&auto=format&q=60",
    "chevet":        BASE + "1588046130717-0eb0c9a3ba15?w=800&h=600&fit=crop&auto=format&q=60",
    "bureau":        BASE + "1497366216548-37526070297c?w=800&h=600&fit=crop&auto=format",
    "chaise-bureau": BASE + "1506439773649-6e0eb8cfb237?w=800&h=600&fit=crop&auto=format&sat=-30",
    "bibliotheque":  BASE + "1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop&auto=format",
    "caisson":       BASE + "1550258987-190a2d41a8ba?w=800&h=600&fit=crop&auto=format",
    "table-cuisine": BASE + "1414235077428-338989a2e8c0?w=800&h=600&fit=crop&auto=format",
    "chaise-bar":    BASE + "1414235077428-338989a2e8c0?w=800&h=600&fit=crop&auto=format&q=75",
    "ilot":          BASE + "1556909114-f6e7ad7d3136?w=800&h=600&fit=crop&auto=format&hue=200",
    "buffet":        BASE + "1555041469-a586c61ea9bc?w=800&h=600&fit=crop&auto=format&bri=-10",
}

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
}

def download_and_save(name, url):
    out_path = os.path.join(OUTPUT_DIR, f"{name}.jpg")
    req = urllib.request.Request(url, headers=HEADERS)
    try:
        with urllib.request.urlopen(req, timeout=15) as resp:
            data = resp.read()
        img = Image.open(io.BytesIO(data)).convert("RGB")
        img = img.resize((800, 600), Image.LANCZOS)
        img.save(out_path, "JPEG", quality=85, optimize=True)
        print(f"  OK  {name}.jpg ({img.width}x{img.height})")
    except Exception as e:
        print(f"  ERR {name}: {e}")

print(f"Saving images to: {OUTPUT_DIR}\n")
for name, url in IMAGES.items():
    download_and_save(name, url)
print("\nDone.")
