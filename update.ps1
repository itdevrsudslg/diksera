# Update Diksera dalam satu perintah: commit -> push GitHub -> deploy ke server aaPanel.
#
# Pemakaian (dari folder D:\RSUD\diksera):
#   .\update "pesan perubahan"            -> commit semua perubahan, push, deploy
#   .\update "pesan perubahan" -Migrate   -> sama, plus jalankan migration (backup DB dulu!)
#   .\update                              -> tanpa perubahan baru: push commit yang ada + deploy
param(
    [string]$Pesan = "",
    [switch]$Migrate
)

$ErrorActionPreference = "Stop"
$Server  = "rsudslg@192.168.244.104"
$Key     = Join-Path $HOME ".ssh\diksera_server"
$AppDir  = "/www/wwwroot/103.139.47.173/diksera"

Set-Location $PSScriptRoot

function Step($text) { Write-Host "`n>> $text" -ForegroundColor Cyan }
function Fail($text) { Write-Host "`n!! $text" -ForegroundColor Red; exit 1 }

# 1. Commit
Step "Cek perubahan lokal"
git status --short
if (git status --porcelain) {
    if (-not $Pesan) { $Pesan = Read-Host "Pesan commit (jelaskan perubahan)" }
    if (-not $Pesan) { Fail "Pesan commit wajib diisi." }
    git add -A
    git commit -m $Pesan
    if ($LASTEXITCODE -ne 0) { Fail "Commit gagal." }
} else {
    Write-Host "Tidak ada perubahan baru untuk di-commit."
}

# 2. Push ke GitHub
Step "Push ke GitHub"
git push origin main
if ($LASTEXITCODE -ne 0) {
    Fail "Push gagal. Kalau error 403, jalankan: cmdkey /delete:git:https://github.com lalu ulangi dan login dengan akun yang punya akses ke itdevrsudslg/diksera."
}
$LocalHead = (git rev-parse --short HEAD).Trim()

# 3. Deploy ke server
Step "Deploy ke server (mungkin diminta password sudo user rsudslg)"
$Flag = if ($Migrate) { "--migrate" } else { "" }
ssh -t -i $Key $Server "bash $AppDir/deploy.sh $Flag"
if ($LASTEXITCODE -ne 0) { Fail "Deploy di server gagal. Baca pesan di atas." }

# 4. Verifikasi
Step "Verifikasi versi"
$ServerHead = (ssh -i $Key $Server "git -c safe.directory=$AppDir -C $AppDir rev-parse --short HEAD").Trim()
Write-Host "Lokal/GitHub : $LocalHead"
Write-Host "Server       : $ServerHead"
if ($LocalHead -eq $ServerHead) {
    Write-Host "`nSELESAI - lokal, GitHub, dan server sudah sama." -ForegroundColor Green
} else {
    Fail "Versi server berbeda dengan lokal."
}
