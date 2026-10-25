param(
    [string]$PackagePath = "deploy/savebite-arenhost.zip"
)

$ErrorActionPreference = 'Stop'

# This script is intentionally run from the project root. It builds the exact
# assets that production serves and produces an uploadable ZIP for cPanel.
if (-not (Test-Path 'artisan')) {
    throw 'Run this script from the Laravel project root.'
}

composer install --no-dev --prefer-dist --optimize-autoloader
npm.cmd ci
npm.cmd run build

$packageDirectory = Split-Path -Parent $PackagePath
if ($packageDirectory) {
    New-Item -ItemType Directory -Force -Path $packageDirectory | Out-Null
}

if (Test-Path $PackagePath) {
    Remove-Item -LiteralPath $PackagePath -Force
}

tar -a -c -f $PackagePath `
    --exclude=.env `
    --exclude=.env.backup `
    --exclude=.env.production `
    --exclude=.git `
    --exclude=node_modules `
    --exclude=public/hot `
    --exclude=tests `
    --exclude=storage/framework/views `
    --exclude=storage/framework/cache `
    --exclude=storage/framework/sessions `
    --exclude=storage/logs `
    --exclude=deploy `
    .

Write-Host "Deployment package created: $PackagePath"
