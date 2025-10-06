import os
import shutil

# Project root path
PROJECT_PATH = r"C:\Users\mikem\Documents\code\voice"
UNUSED_LIST_FILE = "unused_assets.txt"
UNUSED_DIR = os.path.join(PROJECT_PATH, "unused")

# Ensure "unused" folder exists
os.makedirs(UNUSED_DIR, exist_ok=True)

# Read unused assets list
with open(UNUSED_LIST_FILE, "r", encoding="utf-8") as f:
    unused_assets = [line.strip() for line in f if line.strip()]

moved = 0
skipped = 0

for rel_path in unused_assets:
    src = os.path.join(PROJECT_PATH, rel_path)
    if os.path.exists(src):
        # Create subfolder structure inside "unused"
        dest = os.path.join(UNUSED_DIR, rel_path)
        os.makedirs(os.path.dirname(dest), exist_ok=True)
        shutil.move(src, dest)
        moved += 1
    else:
        skipped += 1

print(f"✅ Moved {moved} unused files into '{UNUSED_DIR}'")
if skipped:
    print(f"⚠️ Skipped {skipped} files (not found)")
