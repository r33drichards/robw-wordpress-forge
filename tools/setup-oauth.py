#!/usr/bin/env python3
"""Fetch a separate, pinned OAuth dependency for disposable tests. Never package it."""
from pathlib import Path
import subprocess
COMMIT='01e4e5856bc06a84daafc4ce8d7e8c79fc2c7c5c'
p=Path('.tools/oauth')
if p.exists():
    current=subprocess.check_output(['git','-C',str(p),'rev-parse','HEAD'],text=True).strip()
    if current!=COMMIT: raise SystemExit('Existing OAuth dependency differs; inspect before replacing')
else:
    p.mkdir(parents=True)
    subprocess.run(['git','init',str(p)],check=True)
    subprocess.run(['git','-C',str(p),'remote','add','origin','https://github.com/r33drichards/robw-wordpress-oauth.git'],check=True)
    subprocess.run(['git','-C',str(p),'fetch','--depth','1','origin',COMMIT],check=True)
    subprocess.run(['git','-C',str(p),'checkout','--detach',COMMIT],check=True)
print('Pinned separate OAuth test dependency ready')
