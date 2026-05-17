# HEIG-VD WebMobUI - TP Système de sondages

Système de sondages créé pour le TP de développement fullstack du cours WebMobUI enseigné à la
[Haute Ecole d'Ingénierie et de Gestion du Canton de Vaud (HEIG-VD)](https://heig-vd.ch),
Suisse.

## Pré-requis

Afin de lancer ce projet, une stack compatible avec Laravel, est requise.

Voici les pré-requis nécessaires :

- PHP >= 8.0.
- Composer.
- Node.js et npm.
- Une base de données (MySQL, PostgreSQL, SQLite, etc.).
- Un serveur web (Apache, Nginx, etc.).

[Laravel Herd](https://helm.sh/docs/charts/laravel/) est recommandé pour une installation facile de Laravel et de ses dépendances.

## Développement local

Pour développer et tester le TP en local, voici les étapes à suivre :

1. Forker ce dépôt

2. Installer les dépendances avec npm et Composer :

    ```bash
    npm install && npm run build

    composer install
    ```

3. Copier le fichier `.env.example` en `.env`.
4. Modifier les variables d'environnement si nécessaire (optionnel).
5. Générer la clé d'application Laravel :

    ```bash
    php artisan key:generate
    ```

6. Créer le lien symbolique pour les fichiers téléversés :

    ```bash
    php artisan storage:link
    ```

7. Créer la base de données et exécuter les migrations :

    ```bash
    php artisan migrate
    ```

    S'il est nécessaire de réinitialiser la base de données, utiliser la commande `php artisan migrate:reset` puis `php artisan migrate` à nouveau.

8. Optionnel : en mode développement, il est possible de peupler la base de données avec des données fictives :

    ```bash
    php artisan db:seed
    ```

9. Démarrer le serveur de développement Laravel :

    ```bash
    composer run dev
    ```

L'application sera accessible à l'adresse <http://127.0.0.1:8000>.

## Fonctionnalités

### Dashboard, création et modification

Une personne connectée peut accéder à un dashboard pour voir la liste des sondages qu'elle a créé. Elle peut créer de nouveaux sondages ou modifier ceux qui sont enregistrés comme brouillon. Les paramètres qu'elle peut régler sont :
- Titre (optionnel), question et options du sondage
- Possibilité de voter pour plusieurs options
- Possibilité de modifier son vote
- Accès public aux résultats
- Date de fin du sondage
La personne peut ensuite enregistrer le sondage comme brouillon ou le publier.

### Sondage publié et affichage des résultats

Une fois un sondage publié, l'auteur.e peut facilement copier le lien vers le sondage pour le partager. N'importe qui peut accéder à la page du détail d'un sondage, mais seules les personnes connectées peuvent voter.

Le système gère l'unicité et la modification du vote selon les paramètres définis.

Les résultats d'un sondage sont toujours visibles par l'auteur.e et sont aussi accessibles à tout le monde si le sondage est reglé ainsi. 

## Choix techniques

### Navigation

Il y a deux vues blade afin de gérer différement le dashboard de la page d'un vote et que cette dernière soit aussi accessible aux personnes non-authentifiées. Ceci fait qu'il y a deux apps Vue.js.

La navigation dans le dashboard pour accéder aux formulaires se fait directement dans Vue.js grâce au hash. La navigation entre le dashboard et le détail d'un vote est gérée par Laravel.

### Librairie vue-chartjs

L'affichage du graphique de visualisation des résultats est faite avec la librairie vue-chartjs car Chart.js est passablement répandue et vue-chartjs permet l'intégration avec Vue.js.
