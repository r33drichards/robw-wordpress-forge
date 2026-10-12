#!/usr/bin/env python3
from pathlib import Path
import zipfile,hashlib
root=Path('robw-forge');out=Path('dist/robw-forge.zip');out.parent.mkdir(exist_ok=True)
with zipfile.ZipFile(out,'w',zipfile.ZIP_DEFLATED)as z:
 for p in sorted(root.rglob('*')):
  if not p.is_file():continue
  info=zipfile.ZipInfo(p.as_posix(),(2026,1,1,0,0,0));info.external_attr=0o644<<16;info.compress_type=zipfile.ZIP_DEFLATED;z.writestr(info,p.read_bytes())
print(out,out.stat().st_size,hashlib.sha256(out.read_bytes()).hexdigest())
