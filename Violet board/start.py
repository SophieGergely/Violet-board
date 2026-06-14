#!/usr/bin/env python3
import subprocess
import sys
import os
import shutil
import time
import webbrowser

def run(cmd, check=True):
    return subprocess.run(cmd, shell=True, check=check)

print("=" * 50)
print("  Violet Board – Starting up")
print("=" * 50)

# 1. Copy nginx.conf
os.makedirs("docker", exist_ok=True)
if os.path.exists("nginx.conf") and not os.path.exists("docker/nginx.conf"):
    shutil.copy("nginx.conf", "docker/nginx.conf")
    print("[OK] nginx.conf copied")

# 2. Create .env if missing
if not os.path.exists(".env"):
    shutil.copy(".env.docker", ".env")
    print("[OK] .env file created")
else:
    print("[--] .env already exists, skipping")

# 3. Fix DB_HOST – must be 'db' (Docker container name), not 127.0.0.1
env_content = open(".env").read()
if "DB_HOST=127.0.0.1" in env_content:
    env_content = env_content.replace("DB_HOST=127.0.0.1", "DB_HOST=db")
    open(".env", "w").write(env_content)
    print("[OK] DB_HOST corrected to 'db'")

# 4. Remove public/storage symlink if exists (causes Docker build error on Windows)
storage_link = os.path.join("public", "storage")
if os.path.islink(storage_link) or os.path.exists(storage_link):
    if os.path.islink(storage_link):
        os.unlink(storage_link)
    else:
        shutil.rmtree(storage_link)
    print("[OK] public/storage symlink removed (will be recreated)")

# 5. Build and start
print("\n[..] Building and starting Docker containers (this may take a few minutes on first run)...")
run("docker compose up -d --build")

# 6. Wait for database
print("[..] Waiting for the database...")
time.sleep(8)

# 7. Generate APP_KEY if empty
env_content = open(".env").read()
if "APP_KEY=" in env_content and "APP_KEY=base64" not in env_content:
    print("[..] Generating APP_KEY...")
    run("docker compose exec app php artisan key:generate --force")
    print("[OK] APP_KEY generated")

# 8. Migrate and seed
print("[..] Running database migrations and seeders...")
run("docker compose exec app php artisan migrate --force", check=False)
run("docker compose exec app php artisan db:seed --force", check=False)
print("[OK] Database ready")

# 9. Storage link
run("docker compose exec app php artisan storage:link", check=False)

print()
print("=" * 50)
print("  Violet Board is running!")
print("  Open: http://localhost:8000")
print("=" * 50)

time.sleep(2)
webbrowser.open("http://localhost:8000")

input("\nPress Enter to exit (the server keeps running in the background)...")
