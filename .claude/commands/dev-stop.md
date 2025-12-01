---
description: "Stop all development services"
tools: [Bash]
---

# Stopping Development Services

```bash
powershell.exe -ExecutionPolicy Bypass -Command "
Write-Host '🛑 Deteniendo servicios...' -ForegroundColor Yellow
Get-Process -Name 'php' -ErrorAction SilentlyContinue | ForEach-Object { \$_.Kill() }
Get-Process -Name 'node' -ErrorAction SilentlyContinue | ForEach-Object { \$_.Kill() }
try { Stop-Service 'Apache2.4' -ErrorAction Stop } catch { Get-Process -Name 'httpd' -ErrorAction SilentlyContinue | ForEach-Object { \$_.Kill() } }
try { Stop-Service 'MySQL' -ErrorAction Stop } catch { Get-Process -Name 'mysqld' -ErrorAction SilentlyContinue | ForEach-Object { \$_.Kill() } }
Write-Host '🎉 ¡Servicios detenidos!' -ForegroundColor Green
"
```

🛑 **All services stopped!**