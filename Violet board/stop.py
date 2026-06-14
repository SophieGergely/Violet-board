#!/usr/bin/env python3
import subprocess

print("Stopping Violet Board...")
subprocess.run("docker compose down", shell=True)
print("Stopped. You can now use 'php artisan serve' normally.")
input("\nPress Enter to exit...")
