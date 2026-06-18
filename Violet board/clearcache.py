#!/usr/bin/env python3
import subprocess

def run(cmd, check=False):
    print(f"[..] {cmd}")
    result = subprocess.run(cmd, shell=True, check=check)
    return result.returncode == 0

print("=" * 50)
print("  Violet Board – Clearing all caches")
print("=" * 50)

steps = [
    "docker compose exec app php artisan view:clear",
    "docker compose exec app php artisan config:clear",
    "docker compose exec app php artisan route:clear",
    "docker compose exec app php artisan cache:clear",
    "docker compose exec app php artisan optimize:clear",
]

all_ok = True
for step in steps:
    ok = run(step)
    print("[OK]" if ok else "[FAIL]", step)
    all_ok = all_ok and ok

print()
print("[..] Restarting containers (clears PHP OPcache in memory)...")
run("docker compose restart app")
run("docker compose restart nginx")

print()
print("=" * 50)
if all_ok:
    print("  Done. Now hard-refresh the browser (Ctrl+Shift+R / Cmd+Shift+R).")
else:
    print("  Done, but some steps failed above — check the container is running")
    print("  (docker compose ps) and that 'app' is the correct service name.")
print("=" * 50)

input("\nPress Enter to exit...")
