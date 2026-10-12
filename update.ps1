# ============================================================
#  DIKSERA - Update Tool
#  Dobel-klik update.bat, atau dari terminal:  .\update
#  Mode cepat (tanpa menu):  .\update "pesan perubahan" [-Migrate]
# ============================================================
param(
    [string]$Pesan = "",
    [switch]$Migrate,
    [switch]$Status
)

$Server = "rsudslg@192.168.244.104"
$Key    = Join-Path $HOME ".ssh\diksera_server"
$AppDir = "/www/wwwroot/103.139.47.173/diksera"
$Web    = "http://103.139.47.173/diksera"

Set-Location $PSScriptRoot
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

# ---------- tampilan ----------
function Line { Write-Host ("-" * 60) -ForegroundColor DarkGray }
function Title($t) { Write-Host ""; Write-Host "  $t" -ForegroundColor Cyan; Line }
function Ok($t)    { Write-Host "  [OK] $t" -ForegroundColor Green }
function Warn($t)  { Write-Host "  [!]  $t" -ForegroundColor Yellow }
function Err($t)   { Write-Host "  [X]  $t" -ForegroundColor Red }
function Info($t)  { Write-Host "       $t" -ForegroundColor Gray }
function Ask($q)   { Write-Host ""; return (Read-Host "  $q") }
function YesNo($q) { $a = Ask "$q (y/n)"; return ($a -match '^(y|ya|yes)$') }

# ---------- helper ----------
function Remote([string]$cmd) { ssh -o ConnectTimeout=8 -o BatchMode=yes -i $Key $Server $cmd 2>$null }

function Get-Versions {
    git fetch -q origin 2>$null
    $v = [ordered]@{
        Lokal  = (git rev-parse --short HEAD).Trim()
        GitHub = (git rev-parse --short origin/main 2>$null)
        Server = (Remote "git -c safe.directory=$AppDir -C $AppDir rev-parse --short HEAD")
    }
    if ($v.GitHub) { $v.GitHub = $v.GitHub.Trim() }
    if ($v.Server) { $v.Server = "$($v.Server)".Trim() }
    return $v
}

function Show-Status {
    Title "STATUS SINKRON"
    $v = Get-Versions
    foreach ($k in $v.Keys) {
        $val = if ($v[$k]) { $v[$k] } else { "(tidak terhubung)" }
        $msg = if ($v[$k]) { (git log -1 --format=%s $v[$k] 2>$null) } else { "" }
        Write-Host ("  {0,-7}: " -f $k) -NoNewline
        Write-Host ("{0,-9}" -f $val) -ForegroundColor White -NoNewline
        Write-Host " $msg" -ForegroundColor DarkGray
    }
    Line
    $ahead = [int](git rev-list --count origin/main..HEAD 2>$null)
    if ($v.Lokal -eq $v.GitHub -and $v.GitHub -eq $v.Server) { Ok "Lokal, GitHub, dan Server SAMA." }
    else {
        if ($ahead -gt 0) { Warn "$ahead commit lokal belum di-push ke GitHub." }
        if ($v.Server -and $v.Server -ne $v.GitHub) { Warn "Server belum versi terbaru GitHub." }
        if (-not $v.Server) { Err "Server tidak bisa dihubungi (cek jaringan/VPN)." }
    }

    $changes = git status --porcelain
    if ($changes) {
        Write-Host ""
        Warn "Ada $(@($changes).Count) file berubah yang belum di-commit:"
        $changes | Select-Object -First 15 | ForEach-Object {
            $code = $_.Substring(0, 2).Trim(); $file = $_.Substring(3)
            $label = switch -Regex ($code) { '^\?\?$' {'baru  '} '^D' {'hapus '} default {'ubah  '} }
            Info "$label $file"
        }
        if (@($changes).Count -gt 15) { Info "... dan $(@($changes).Count - 15) file lain" }
    }
}

function Do-Commit {
    if (-not (git status --porcelain)) { Ok "Tidak ada perubahan baru untuk di-commit."; return $true }
    if (-not $script:Pesan) {
        $script:Pesan = Ask "Tulis pesan perubahan (contoh: perbaiki tampilan dashboard)"
    }
    if (-not $script:Pesan) { Err "Pesan wajib diisi."; return $false }
    git add -A | Out-Null
    git commit -q -m $script:Pesan
    if ($LASTEXITCODE -ne 0) { Err "Commit gagal."; return $false }
    Ok "Commit: $($script:Pesan)"
    return $true
}

function Do-Push {
    Write-Host "  Mengirim ke GitHub..." -ForegroundColor Gray
    git push -q origin main 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0) {
        Err "Push ke GitHub GAGAL."
        Info "Kemungkinan login GitHub salah akun (error 403)."
        if (YesNo "Hapus login GitHub tersimpan lalu coba lagi (akan muncul jendela login)?") {
            cmdkey /delete:git:https://github.com | Out-Null
            git push origin main
            if ($LASTEXITCODE -ne 0) { Err "Masih gagal. Pastikan akun punya akses ke itdevrsudslg/diksera."; return $false }
        } else { return $false }
    }
    Ok "GitHub sudah terbaru."
    return $true
}

function Do-Deploy([bool]$runMigrate) {
    $flag = ""
    if ($runMigrate) { $flag = "--migrate" }
    else {
        $srv = Remote "git -c safe.directory=$AppDir -C $AppDir rev-parse HEAD"
        if ($srv) {
            $newMig = git diff --name-only --diff-filter=A "$("$srv".Trim())" HEAD -- database/migrations 2>$null
            if ($newMig) {
                Warn "Ada migration (perubahan database) baru:"
                $newMig | ForEach-Object { Info $_ }
                Info "Sebelum lanjut, BACKUP database dulu di aaPanel > Database > Backup."
                if (YesNo "Sudah backup dan jalankan migration sekarang?") { $flag = "--migrate" }
                else { Warn "Migration dilewati. Kode tetap di-update." }
            }
        }
    }
    Write-Host "  Deploy ke server... (jika diminta, ketik password sudo user rsudslg)" -ForegroundColor Gray
    Line
    ssh -t -o ConnectTimeout=8 -i $Key $Server "bash $AppDir/deploy.sh $flag"
    $code = $LASTEXITCODE
    Line
    if ($code -ne 0) { Err "Deploy di server GAGAL. Baca pesan di atas."; return $false }
    Ok "Server sudah di-update."
    return $true
}

function Run-Update([bool]$runMigrate) {
    Title "UPDATE: commit -> GitHub -> server"
    if (-not (Do-Commit)) { return }
    if (-not (Do-Push))   { return }
    if (-not (Do-Deploy $runMigrate)) { return }
    $v = Get-Versions
    if ($v.Lokal -eq $v.Server) {
        Write-Host ""
        Write-Host "  ======================================================" -ForegroundColor Green
        Write-Host "   SELESAI - Lokal, GitHub & Server sama ($($v.Lokal))" -ForegroundColor Green
        Write-Host "   Cek di: $Web" -ForegroundColor Green
        Write-Host "  ======================================================" -ForegroundColor Green
    } else { Err "Versi server ($($v.Server)) berbeda dengan lokal ($($v.Lokal))." }
}

function Show-Log {
    Title "ERROR TERBARU DI SERVER (laravel.log)"
    $out = Remote "grep -hoE '^\[[0-9-]+ [0-9:]+\] [a-z]+\.ERROR: .{0,150}' $AppDir/storage/logs/laravel.log | tail -15"
    if ($out) { $out | ForEach-Object { Info $_ } } else { Ok "Tidak ada error tercatat (atau log tidak terbaca)." }
}

function Show-History {
    Title "10 PERUBAHAN TERAKHIR"
    git log -10 --format="  %C(yellow)%h%Creset %C(dim)%ad%Creset  %s" --date=format:"%d-%m-%Y %H:%M"
}

# ---------- mode cepat ----------
if ($Status) { Show-Status; exit }
if ($Pesan) {
    Show-Status
    Run-Update $Migrate.IsPresent
    exit
}

# ---------- menu ----------
while ($true) {
    Clear-Host
    Write-Host ""
    Write-Host "   ____  _ _                       " -ForegroundColor Cyan
    Write-Host "  |  _ \(_) | _____  ___ _ __ __ _ " -ForegroundColor Cyan
    Write-Host "  | | | | | |/ / __|/ _ \ '__/ _`` |" -ForegroundColor Cyan
    Write-Host "  | |_| | |   <\__ \  __/ | | (_| |" -ForegroundColor Cyan
    Write-Host "  |____/|_|_|\_\___/\___|_|  \__,_|   Update Tool" -ForegroundColor Cyan
    Show-Status
    Title "MENU"
    Write-Host "  [1] Update sekarang  (commit + push GitHub + deploy server)"
    Write-Host "  [2] Deploy ulang server saja"
    Write-Host "  [3] Lihat error terbaru di server"
    Write-Host "  [4] Riwayat perubahan"
    Write-Host "  [5] Refresh status"
    Write-Host "  [0] Keluar"
    $skipPause = $false
    $pilih = Ask "Pilih"
    switch ($pilih) {
        '1' { $script:Pesan = ""; Run-Update $false }
        '2' { Title "DEPLOY ULANG SERVER"; Do-Deploy $false | Out-Null }
        '3' { Show-Log }
        '4' { Show-History }
        '5' { $skipPause = $true }
        '0' { exit }
        default { $skipPause = $true }
    }
    if ($skipPause) { continue }
    Write-Host ""
    Read-Host "  Tekan Enter untuk kembali ke menu" | Out-Null
}
