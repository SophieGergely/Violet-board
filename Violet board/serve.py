#!/usr/bin/env python3
import subprocess
import webbrowser
import time
import threading

def open_browser():
    time.sleep(1)
    webbrowser.open("http://localhost:8000")

threading.Thread(target=open_browser).start()
subprocess.run("php artisan serve --host=localhost", shell=True)
