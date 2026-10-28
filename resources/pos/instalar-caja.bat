<# :
@echo off
title Instalador de la caja
powershell -NoProfile -ExecutionPolicy Bypass -Command "iex ([IO.File]::ReadAllText('%~f0', [Text.Encoding]::UTF8))"
exit /b
#>

# =============================================================================
#  Deja este equipo Windows listo como caja (POS). Se puede ejecutar las veces que
#  haga falta y no pide permisos de administrador:
#   1. Busca Google Chrome.
#   2. Deja la impresora de recibos como predeterminada de Windows (la caja imprime ahi).
#   3. Crea el icono de la caja en el escritorio y en el Inicio de Windows (se abre sola
#      al prender el equipo). El icono abre Chrome en modo kiosco (--kiosk: pantalla
#      completa, sin barra ni pestanas) con --kiosk-printing (imprime directo, sin la
#      ventana de impresion), en un perfil de Chrome propio de la caja.
#   4. Activa el teclado en pantalla de Windows al tocar un campo (el equipo no tiene
#      teclado y en pantalla completa no se ve la barra de tareas) y pide reiniciar.
#  Lo genera InstaladorCajaService (los __VALORES__ se reemplazan al descargarlo).
# =============================================================================
$ErrorActionPreference = 'Stop'

$UrlCaja       = '__URL_CAJA__'
$UrlIcono      = '__URL_ICONO__'
$NombreAcceso  = '__NOMBRE_ACCESO__'
$PaginaDrivers = 'https://www.xprintertech.com/download.html'
$Carpeta       = Join-Path $env:LOCALAPPDATA 'CajaPOS'
$Perfil        = Join-Path $Carpeta 'chrome'

function Paso([string] $texto)  { Write-Host ''; Write-Host $texto -ForegroundColor Cyan }
function Bien([string] $texto)  { Write-Host "  [OK] $texto" -ForegroundColor Green }
function Aviso([string] $texto) { Write-Host "  [!]  $texto" -ForegroundColor Yellow }
function Texto([string] $texto) { Write-Host "       $texto" }

function Terminar([int] $codigo) {
    Write-Host ''
    Read-Host 'Presiona Enter para cerrar esta ventana' | Out-Null
    exit $codigo
}

function ElegirImpresora($lista) {
    for ($i = 0; $i -lt $lista.Count; $i++) {
        Texto ('{0}. {1}' -f ($i + 1), $lista[$i].Name)
    }
    Texto '0. Ninguna de estas es la impresora de recibos'
    while ($true) {
        $respuesta = Read-Host '       Escribe el número de la impresora de recibos y presiona Enter'
        $numero = 0
        if ([int]::TryParse($respuesta, [ref] $numero) -and $numero -ge 0 -and $numero -le $lista.Count) {
            if ($numero -eq 0) { return $null }
            return $lista[$numero - 1]
        }
    }
}

try {
    Write-Host "Instalador de $NombreAcceso" -ForegroundColor White
    Write-Host 'Deja este equipo listo para cobrar e imprimir los recibos.'

    # 1. Google Chrome -------------------------------------------------------------
    Paso '1 de 4 · Google Chrome'
    $chrome = @(
        (Get-ItemProperty 'HKCU:\Software\Microsoft\Windows\CurrentVersion\App Paths\chrome.exe' -ErrorAction SilentlyContinue).'(default)'
        (Get-ItemProperty 'HKLM:\Software\Microsoft\Windows\CurrentVersion\App Paths\chrome.exe' -ErrorAction SilentlyContinue).'(default)'
        "$env:ProgramFiles\Google\Chrome\Application\chrome.exe"
        "${env:ProgramFiles(x86)}\Google\Chrome\Application\chrome.exe"
        "$env:LOCALAPPDATA\Google\Chrome\Application\chrome.exe"
    ) | Where-Object { $_ -and (Test-Path $_) } | Select-Object -First 1

    if (-not $chrome) {
        Aviso 'No encontré Google Chrome en este equipo.'
        Texto 'Instálalo (se abre la página de descarga) y vuelve a abrir este instalador.'
        Start-Process 'https://www.google.com/chrome/'
        Terminar 1
    }
    Bien 'Google Chrome instalado.'

    # 2. Impresora de recibos --------------------------------------------------------
    Paso '2 de 4 · Impresora de recibos'
    $virtuales  = 'PDF|XPS|OneNote|Fax|AnyDesk|Send To|Enviar a'
    $impresoras = @(Get-Printer | Where-Object { $_.Name -notmatch $virtuales } | Sort-Object Name)
    $termicas   = @($impresoras | Where-Object { "$($_.Name) $($_.DriverName)" -match 'XP-?\d|POS|Xprinter|58 ?mm|80 ?mm|Thermal|T[eé]rmica|Receipt|Recibo' })

    $impresora = $null
    if ($termicas.Count -eq 1) {
        $impresora = $termicas[0]
    } elseif ($termicas.Count -gt 1) {
        Texto 'Encontré varias impresoras de recibos:'
        $impresora = ElegirImpresora $termicas
    } elseif ($impresoras.Count -gt 0) {
        Texto 'No reconocí la impresora de recibos por su nombre. Impresoras instaladas:'
        $impresora = ElegirImpresora $impresoras
    }

    if (-not $impresora) {
        Aviso 'Windows todavía no tiene instalada la impresora de recibos.'
        Texto '1. Conecta la impresora por USB y préndela.'
        Texto '2. Instala el driver de Xprinter para Windows (se abre la página oficial de descargas).'
        Texto '3. Vuelve a abrir este instalador.'
        Start-Process $PaginaDrivers
        Terminar 1
    }

    # Que Windows no cambie la predeterminada por la última impresora que se usó.
    Set-ItemProperty -Path 'HKCU:\Software\Microsoft\Windows NT\CurrentVersion\Windows' -Name 'LegacyDefaultPrinterMode' -Value 1 -Type DWord
    (New-Object -ComObject WScript.Network).SetDefaultPrinter($impresora.Name)
    Bien "Impresora predeterminada: $($impresora.Name)"
    if ("$($impresora.PrinterStatus)" -notin @('Normal', '0')) {
        Aviso "Windows la reporta como `"$($impresora.PrinterStatus)`": revisa que esté prendida, con papel y conectada."
    }

    # 3. Icono de la caja -------------------------------------------------------------
    Paso '3 de 4 · Ícono de la caja'
    New-Item -ItemType Directory -Force -Path $Perfil | Out-Null
    $icono = "$chrome,0"
    try {
        [Net.ServicePointManager]::SecurityProtocol = [Net.ServicePointManager]::SecurityProtocol -bor [Net.SecurityProtocolType]::Tls12
        $archivoIcono = Join-Path $Carpeta 'caja.ico'
        Invoke-WebRequest -Uri $UrlIcono -OutFile $archivoIcono -UseBasicParsing -TimeoutSec 20
        $icono = "$archivoIcono,0"
    } catch {
        # Sin internet o sin ícono: el acceso queda con el ícono de Chrome.
    }

    # Perfil de Chrome propio de la caja: los modos kiosco aplican aunque haya otro Chrome
    # abierto, y la configuración de la impresora (guardada en el navegador) queda en él.
    # --overscroll-history-navigation=0: deslizar el dedo no devuelve de página (y no se pierde el carrito).
    # --disable-usb-keyboard-detect: Chrome pide el teclado en pantalla aunque crea que hay uno
    #   conectado (algunas tablets reportan sus botones como teclado).
    $argumentos = '--kiosk --kiosk-printing --overscroll-history-navigation=0 --disable-usb-keyboard-detect --user-data-dir="{0}" --no-first-run --no-default-browser-check --hide-crash-restore-bubble "{1}"' -f $Perfil, $UrlCaja

    $shell = New-Object -ComObject WScript.Shell
    foreach ($destino in @([Environment]::GetFolderPath('Desktop'), [Environment]::GetFolderPath('Startup'))) {
        $acceso = $shell.CreateShortcut((Join-Path $destino "$NombreAcceso.lnk"))
        $acceso.TargetPath       = $chrome
        $acceso.Arguments        = $argumentos
        $acceso.WorkingDirectory = Split-Path $chrome
        $acceso.IconLocation     = $icono
        $acceso.Description      = 'Abre la caja e imprime los recibos sin preguntar'
        $acceso.Save()
    }
    Bien "Ícono `"$NombreAcceso`" en el escritorio."
    Bien 'La caja se abrirá sola, en pantalla completa, cada vez que se prenda el equipo.'

    # 4. Teclado en pantalla -----------------------------------------------------------
    # Windows 11: TouchKeyboardTapInvoke (2 = siempre). Windows 10: EnableDesktopModeAutoInvoke.
    # Toman efecto al volver a entrar a Windows: por eso se reinicia al final.
    Paso '4 de 4 · Teclado en pantalla'
    $teclado = 'HKCU:\Software\Microsoft\TabletTip\1.7'
    if (-not (Test-Path $teclado)) { New-Item -Path $teclado -Force | Out-Null }
    New-ItemProperty -Path $teclado -Name 'TouchKeyboardTapInvoke' -Value 2 -PropertyType DWord -Force | Out-Null
    New-ItemProperty -Path $teclado -Name 'EnableDesktopModeAutoInvoke' -Value 1 -PropertyType DWord -Force | Out-Null
    Bien 'El teclado de Windows se abrirá solo al tocar un campo.'

    Paso 'Listo: falta reiniciar el equipo'
    Texto 'Al reiniciar, la caja se abre sola en pantalla completa y el teclado sale al tocar un campo.'
    Texto '1. Inicia sesión.'
    Texto '2. En la Caja toca el botón "Impresora".'
    Texto '3. Toca "Imprimir prueba" y confirma que el recibo salió.'
    Texto 'Para cerrar la caja en pantalla completa: Alt+F4 con un teclado conectado.'
    Write-Host ''
    Aviso "Abre siempre la caja con el ícono `"$NombreAcceso`": desde el Chrome normal sale la ventana de impresión."
    Write-Host ''
    Read-Host 'Presiona Enter para reiniciar ahora (o cierra esta ventana y reinicia después)' | Out-Null
    shutdown.exe /r /t 5 /c 'Reiniciando para terminar de instalar la caja'
    exit 0
} catch {
    Write-Host ''
    Write-Host "No se pudo terminar la instalación: $($_.Exception.Message)" -ForegroundColor Red
    Terminar 1
}
