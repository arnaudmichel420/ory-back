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

# Fill database

php bin/console doctrine:fixtures:load
php bin/console app:import-pole-emploi
php bin/console app:scrap-territoire
php bin/console app:seed-reco-onboarding

# Fill job attractiveness data

php bin/console --no-debug app:import-metier-attractivite
php bin/console messenger:consume async -vv
php bin/console app:import-metier-attractivite-status <runId>

# Run the local server

symfony serve

# Run the messenger

php bin/console messenger:consume async -vv

# Linter

```
composer cs-check   # vérifie le style sans modifier
composer cs-fix     # corrige automatiquement
composer phpstan    # analyse statique
```

