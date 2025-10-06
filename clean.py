import os
import re

# Project root path
PROJECT_PATH = r"C:\Users\mikem\Documents\code\voice"

# Directories to ignore
IGNORE_DIRS = {".venv", "venv"}

# File extensions to scan for code references
CODE_EXTENSIONS = [".php", ".html"]

# Asset extensions we care about (ignore fonts, css, js, svg, etc.)
ASSET_EXTENSIONS = [".png", ".jpg", ".jpeg", ".gif", ".webp"]

# Regex patterns to catch asset references
patterns = [
    r'src=["\']([^"\']+)["\']',
    r'href=["\']([^"\']+)["\']',
    r'url\(([^)]+)\)',
    r'import\s+["\']([^"\']+)["\']'
]

def is_ignored(path):
    """Check if path contains ignored directories"""
    parts = path.split(os.sep)
    return any(d in IGNORE_DIRS for d in parts)

# Collect all assets
all_assets = []
asset_sizes = {}
for root, _, files in os.walk(PROJECT_PATH):
    if is_ignored(root):
        continue
    for f in files:
        if any(f.lower().endswith(ext) for ext in ASSET_EXTENSIONS):
            full_path = os.path.join(root, f)
            size = os.path.getsize(full_path)
            # Skip small files < 1KB
            if size < 1024:
                continue
            rel_path = os.path.relpath(full_path, PROJECT_PATH).replace("\\", "/")
            all_assets.append(rel_path)
            asset_sizes[rel_path] = size

# Collect code files
code_files = []
for root, _, files in os.walk(PROJECT_PATH):
    if is_ignored(root):
        continue
    for f in files:
        if any(f.lower().endswith(ext) for ext in CODE_EXTENSIONS):
            code_files.append(os.path.join(root, f))

used_assets = set()

# Scan code for asset usage
for code_file in code_files:
    with open(code_file, "r", encoding="utf-8", errors="ignore") as f:
        content = f.read()
        for pattern in patterns:
            matches = re.findall(pattern, content)
            for match in matches:
                match = match.strip("'\"").split("?")[0]  # remove query strings
                # Ignore external URLs
                if match.startswith("http://") or match.startswith("https://") or match.startswith("//"):
                    continue
                if any(match.endswith(ext) for ext in ASSET_EXTENSIONS):
                    used_assets.add(match.lstrip("/"))

# Compare
used_assets_normalized = {ua for ua in used_assets}
unused_assets = [a for a in all_assets if a not in used_assets_normalized]

# Calculate MB saved
total_bytes_saved = sum(asset_sizes[a] for a in unused_assets)
mb_saved = total_bytes_saved / (1024 * 1024)

print("✅ Used Assets:", len(used_assets_normalized))
print("❌ Unused Assets:", len(unused_assets))
print(f"💾 Potential Space Saved: {mb_saved:.2f} MB")

# Save reports
with open("used_assets.txt", "w") as f:
    f.write("\n".join(sorted(used_assets_normalized)))
with open("unused_assets.txt", "w") as f:
    f.write("\n".join(sorted(unused_assets)))
