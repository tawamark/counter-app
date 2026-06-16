$ErrorActionPreference = 'Stop'

function Write-Step {
    param ([string] $Message)
    Write-Host "[Counter] $Message"
}

function Test-Port {
    param ([int] $Port)
    return [bool](Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue)
}

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$backend = Join-Path $root 'backend'
$mysqlBase = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64'
$mysqlServer = Join-Path $mysqlBase 'bin\mysqld.exe'
$mysqlClient = Join-Path $mysqlBase 'bin\mysql.exe'
$mysqlConfig = Join-Path $mysqlBase 'my.ini'
$php = 'php'
$npm = 'npm.cmd'
$appUrl = 'http://127.0.0.1:8000'
$viteUrl = 'http://127.0.0.1:5173'

Write-Step 'Preparando ambiente da apresentação.'

if (!(Test-Path $backend)) {
    throw 'Pasta backend não encontrada.'
}

if (!(Test-Path (Join-Path $backend '.env'))) {
    Copy-Item (Join-Path $backend '.env.example') (Join-Path $backend '.env')
    Write-Step 'Arquivo .env criado a partir do .env.example.'
}

if (!(Test-Port 3306)) {
    if (!(Test-Path $mysqlServer)) {
        throw "MySQL do Laragon não encontrado em $mysqlServer."
    }

    Start-Process -FilePath $mysqlServer -ArgumentList "--defaults-file=$mysqlConfig" -WindowStyle Hidden
    Write-Step 'MySQL iniciado.'
    Start-Sleep -Seconds 5
}
else {
    Write-Step 'MySQL já estava rodando.'
}

if (!(Test-Port 3306)) {
    throw 'MySQL não ficou disponível na porta 3306.'
}

if (Test-Path $mysqlClient) {
    & $mysqlClient -uroot -e "CREATE DATABASE IF NOT EXISTS counter CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    Write-Step 'Banco counter conferido.'
}

Push-Location $backend

try {
    & $php artisan config:clear
    & $php artisan migrate --force

    $usersCount = 0
    if (Test-Path $mysqlClient) {
        $usersCount = (& $mysqlClient -uroot counter -N -B -e "SELECT COUNT(*) FROM users;" 2>$null)
    }

    if ([int]$usersCount -eq 0) {
        & $php artisan db:seed --force
        Write-Step 'Dados de demonstração criados.'
    }

    if (!(Test-Port 8000)) {
        $laravelOutLog = Join-Path $backend 'storage\logs\counter-laravel-serve.out.log'
        $laravelErrLog = Join-Path $backend 'storage\logs\counter-laravel-serve.err.log'
        Start-Process -FilePath $php -ArgumentList 'artisan','serve','--host=0.0.0.0','--port=8000' -WorkingDirectory $backend -WindowStyle Hidden -RedirectStandardOutput $laravelOutLog -RedirectStandardError $laravelErrLog
        Write-Step 'Servidor Laravel iniciado.'
        Start-Sleep -Seconds 3
    }
    else {
        Write-Step 'Servidor Laravel já estava rodando.'
    }

    if (!(Test-Port 5173)) {
        $viteOutLog = Join-Path $backend 'storage\logs\counter-vite.out.log'
        $viteErrLog = Join-Path $backend 'storage\logs\counter-vite.err.log'
        Start-Process -FilePath $npm -ArgumentList 'run','dev','--','--host=127.0.0.1','--port=5173' -WorkingDirectory $backend -WindowStyle Hidden -RedirectStandardOutput $viteOutLog -RedirectStandardError $viteErrLog
        Write-Step 'Vite iniciado.'
        Start-Sleep -Seconds 5
    }
    else {
        Write-Step 'Vite já estava rodando.'
    }
}
finally {
    Pop-Location
}

if (!(Test-Port 8000)) {
    throw 'Laravel não ficou disponível na porta 8000.'
}

if (!(Test-Port 5173)) {
    throw 'Vite não ficou disponível na porta 5173.'
}

Write-Step "Web: $appUrl"
Write-Step "Assets Vite: $viteUrl"
Write-Step 'Mobile no emulador: http://10.0.2.2:8000'
Write-Step 'Usuários: admin@counter.test, estoquista@counter.test, contador@counter.test'
Write-Step 'Senha de demonstração: password'

Start-Process $appUrl

Write-Step 'Ambiente pronto.'
