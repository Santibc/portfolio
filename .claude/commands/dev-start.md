---
description: "Start XAMPP, Laravel, Vite for montano project and open Chrome"
tools: [Bash]
---

# Starting Montano Development Environment

Starting development environment for **montano** project...

```bash
echo "🚀 Starting development environment for: montano"

powershell.exe -ExecutionPolicy Bypass -Command "
\$PROJECT = 'montano'
\$projectPath = 'C:\\xampp\\htdocs\\montano'

Write-Host '🚀 Iniciando entorno de desarrollo para: montano' -ForegroundColor Green

if (-not (Get-Process -Name 'httpd' -ErrorAction SilentlyContinue)) {
    try {
        Start-Service 'Apache2.4' -ErrorAction Stop
        Write-Host '✅ Apache iniciado' -ForegroundColor Green
    } catch {
        Start-Process 'C:\\xampp\\apache\\bin\\httpd.exe' -WindowStyle Hidden
        Write-Host '✅ Apache iniciado' -ForegroundColor Green
    }
} else {
    Write-Host '✅ Apache ya ejecutándose' -ForegroundColor Green
}

if (-not (Get-Process -Name 'mysqld' -ErrorAction SilentlyContinue)) {
    try {
        Start-Service 'MySQL' -ErrorAction Stop
        Write-Host '✅ MySQL iniciado' -ForegroundColor Green
    } catch {
        Start-Process 'C:\\xampp\\mysql\\bin\\mysqld.exe' -ArgumentList '--defaults-file=C:\\xampp\\mysql\\bin\\my.ini' -WindowStyle Hidden
        Write-Host '✅ MySQL iniciado' -ForegroundColor Green
    }
} else {
    Write-Host '✅ MySQL ya ejecutándose' -ForegroundColor Green
}

Start-Sleep 2

Set-Location \$projectPath

try {
    \$response = Invoke-WebRequest -Uri 'http://127.0.0.1:8000' -TimeoutSec 2 -UseBasicParsing -ErrorAction Stop
    Write-Host '✅ Laravel ya en puerto 8000' -ForegroundColor Green
} catch {
    Start-Job -ScriptBlock {
        Set-Location 'C:\\xampp\\htdocs\\montano'
        php artisan serve --host=127.0.0.1 --port=8000
    } | Out-Null
    Write-Host '✅ Laravel iniciando...' -ForegroundColor Green
    Start-Sleep 3
}

if (Test-Path 'package.json') {
    try {
        \$response = Invoke-WebRequest -Uri 'http://localhost:5173' -TimeoutSec 2 -UseBasicParsing -ErrorAction Stop
        Write-Host '✅ Vite ya en puerto 5173' -ForegroundColor Green
    } catch {
        Start-Job -ScriptBlock {
            Set-Location 'C:\\xampp\\htdocs\\montano'
            npm run dev
        } | Out-Null
        Write-Host '✅ Vite iniciando...' -ForegroundColor Green
        Start-Sleep 4
    }
}

Start-Process 'chrome.exe' 'http://127.0.0.1:8000' -ErrorAction SilentlyContinue
Write-Host '✅ Chrome abierto' -ForegroundColor Green

Write-Host '🎉 ¡Montano listo!' -ForegroundColor Green
Write-Host '🌐 Laravel: http://127.0.0.1:8000' -ForegroundColor Cyan
"
```

✅ **Montano development environment started!** 🎉