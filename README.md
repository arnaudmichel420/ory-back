# Generate keys
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Install dependencies (only the first time)
composer install

# Create database
php bin/console doctrine:database:create --if-not-exists

# Load migrations
php bin/console doctrine:migrations:migrate

# Load fixtures
php bin/console doctrine:fixtures:load

# Run the local server
symfony serve

# Run the messenger
php bin/console messenger:consume async -vv