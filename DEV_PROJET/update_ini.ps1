$ini = 'C:\php\php.ini'
$content = Get-Content $ini
$content = $content -replace ';extension=mysqli', 'extension=mysqli'
$content = $content -replace '; extension_dir = "\./"', 'extension_dir = "C:\xampp\php\ext"'
$content | Set-Content $ini
Write-Host "Patched $ini"
