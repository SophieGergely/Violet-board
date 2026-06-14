#!/usr/bin/env python3
import subprocess
import os
import shutil
import time
import webbrowser

def run(cmd, check=True):
    return subprocess.run(cmd, shell=True, check=check)

print("=" * 50)
print("  Violet Board – Starting up")
print("=" * 50)

# Copy nginx config into docker/ folder
os.makedirs("docker", exist_ok=True)
if os.path.exists("nginx.conf") and not os.path.exists("docker/nginx.conf"):
    shutil.copy("nginx.conf", "docker/nginx.conf")
    print("[OK] nginx.conf copied")

# Remove public/storage symlink – causes Docker build error on Windows
storage_link = os.path.join("public", "storage")
if os.path.islink(storage_link):
    os.unlink(storage_link)
    print("[OK] public/storage symlink removed (will be recreated)")
elif os.path.isdir(storage_link):
    shutil.rmtree(storage_link)
    print("[OK] public/storage removed (will be recreated)")

# Build and start containers
print("\n[..] Building and starting Docker containers...")
run("docker compose up -d --build")

# Wait for DB to be ready
print("[..] Waiting for the database...")
time.sleep(8)

# Generate APP_KEY if missing
env_content = open(".env").read() if os.path.exists(".env") else ""
if "APP_KEY=" in env_content and "APP_KEY=base64" not in env_content:
    print("[..] Generating APP_KEY...")
    run("docker compose exec app php artisan key:generate --force")
    print("[OK] APP_KEY generated")

# Run migrations and seeders
print("[..] Running migrations and seeders...")
run("docker compose exec app php artisan migrate --force", check=False)
run("docker compose exec app php artisan db:seed --force", check=False)
print("[OK] Database ready")

# Create storage symlink inside container
run("docker compose exec app php artisan storage:link", check=False)

print()
print("=" * 50)
print("  Violet Board is running!")
print("  Open: http://localhost:8000")
print("=" * 50)

time.sleep(2)
webbrowser.open("http://localhost:8000")

input("\nPress Enter to exit (the server keeps running in the background)...")
