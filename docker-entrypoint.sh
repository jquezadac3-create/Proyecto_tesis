#!/bin/sh
set -e

# Verifica si ya se hizo la inicialización
INIT_FLAG="/var/www/.init_done"
VENDOR_DIR="/var/www/vendor"

if [ ! -f "$INIT_FLAG" ]; then
    echo "🏗️ Ejecutando inicialización por primera vez..."

    if [ ! -d "$VENDOR_DIR" ]; then

        echo "📦 Ejecutando 'composer install' porque no existe /vendor"
        composer install --optimize-autoloader --no-dev
    fi

    # Genera clave solo si falta
    if [ -z "$(grep '^APP_KEY=' .env | grep -v '=$')" ]; then
        php artisan key:generate
    fi

    touch "$INIT_FLAG"
else
    echo "✅ Inicialización ya realizada previamente. Saltando pasos..."
fi

echo "🚀 Iniciando servidor PHP-FPM..."
exec "$@"
