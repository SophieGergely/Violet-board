#!/usr/bin/env python3
import subprocess
import shutil
import sys

if not shutil.which("docker"):
    print("[ERROR] Docker is not installed.")
    input("\nPress Enter to exit...")
    sys.exit(1)

print("Stopping Violet Board...")
subprocess.run("docker compose down", shell=True)
print("Stopped.")
input("\nPress Enter to exit...")
