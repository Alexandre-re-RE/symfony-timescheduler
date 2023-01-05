Documentation de lancement

Prérequis:

php >= 8.0.2
node >= v16
npm >= v8 

Créer un fichoer .env à la racine est implémenter les valeur suivantes:

APP_ENV
APP_SECRET
MESSENGER_TRANSPORT_DSN
DATABASE_URL

Listes des commandes à lancer :

composer install
npm install
npm run dev
php bin/console make:migration
php bin/console d:m:m