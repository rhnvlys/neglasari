$envVars = @{
    "APP_KEY" = "base64:F1FdnJ57539kVNNfQR7L9+g4eVQMqUMzyjfi4LIbEs8="
    "APP_ENV" = "production"
    "APP_DEBUG" = "true"
    "APP_URL" = "https://neglasari-pi.vercel.app"
    "LOG_CHANNEL" = "stderr"
    "SESSION_DRIVER" = "database"
    "CACHE_STORE" = "database"
    "QUEUE_CONNECTION" = "sync"
    "FILESYSTEM_DISK" = "public"
    "DB_CONNECTION" = "sqlite"
    "DB_DATABASE" = "/tmp/database.sqlite"
}

foreach ($key in $envVars.Keys) {
    $value = $envVars[$key]
    Write-Host "Setting $key = $value"
    npx vercel env rm $key production -y 2>$null
    $value | npx vercel env add $key production
}
Write-Host "Done!"
