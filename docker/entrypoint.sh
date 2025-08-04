if [ ! -f vendor/autoload.php ]; then
    echo "⚙️ Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

echo "🚀 Starting Symfony HTTP server..."
php -S 0.0.0.0:8000 -t public &

echo "🎯 Starting gRPC server via RoadRunner..."
exec rr serve -c roadrunner.yaml