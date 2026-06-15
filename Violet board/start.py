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

# Copy nginx config
os.makedirs("docker", exist_ok=True)
if os.path.exists("nginx.conf") and not os.path.exists("docker/nginx.conf"):
    shutil.copy("nginx.conf", "docker/nginx.conf")
    print("[OK] nginx.conf copied")

# Remove public/storage symlink (causes Docker build error on Windows)
storage_link = os.path.join("public", "storage")
if os.path.islink(storage_link):
    os.unlink(storage_link)
    print("[OK] public/storage symlink removed")
elif os.path.isdir(storage_link):
    shutil.rmtree(storage_link)
    print("[OK] public/storage removed")

# Fix storage permissions before build
run("chmod -R 775 storage bootstrap/cache 2>/dev/null || true", check=False)

# Build and start
print("\n[..] Building and starting Docker containers...")
run("docker compose up -d --build")

# Wait for DB
print("[..] Waiting for the database...")
time.sleep(10)

# Fix storage permissions inside container
print("[..] Fixing storage permissions...")
run("docker compose exec app chown -R www-data:www-data storage bootstrap/cache", check=False)
run("docker compose exec app chmod -R 775 storage bootstrap/cache", check=False)

# Generate APP_KEY if missing
env_content = open(".env").read() if os.path.exists(".env") else ""
if "APP_KEY=" in env_content and "APP_KEY=base64" not in env_content:
    print("[..] Generating APP_KEY...")
    run("docker compose exec app php artisan key:generate --force", check=False)
    print("[OK] APP_KEY generated")

# Migrate and seed
print("[..] Running migrations and seeders...")
run("docker compose exec app php artisan migrate --force", check=False)
run("docker compose exec app php artisan db:seed --force", check=False)
print("[OK] Database ready")

# Storage link
run("docker compose exec app php artisan storage:link", check=False)

print()
print("=" * 50)
print("  Violet Board is running!")
print("  Open: http://localhost:8000")
print("=" * 50)

time.sleep(2)
webbrowser.open("http://localhost:8000")

input("\nPress Enter to exit (the server keeps running in the background)...")
