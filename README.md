# Catalogue de véhicules – Exercice technique Développeur Web Full Stack

Petite application qui affiche la liste des véhicules d'une API partenaire (Glinche Automobiles) et permet de les filtrer par marque.

- **Back-end** : Laravel (sert d'intermédiaire avec l'API Glinche)
- **Front-end** : Vue.js 3 (Vite)
- **Base de données** : aucune (voir [Choix techniques](#choix-techniques))

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Variables d'environnement](#variables-denvironnement)
- [Lancer le projet](#lancer-le-projet)
- [Choix techniques](#choix-techniques)
- [Difficultés rencontrées](#difficultés-rencontrées)
- [Limites connues et pistes d'amélioration](#limites-connues-et-pistes-damélioration)

## Fonctionnalités

- Affichage des véhicules retournés par l'API Glinche sous forme de cartes.
- Informations affichées pour chaque véhicule : marque, modèle, version, kilométrage, énergie, prix et photo principale.
- Filtre par marque : la liste des marques est construite automatiquement à partir des véhicules reçus (aucune marque écrite en dur) et le filtrage est immédiat.
- Interface responsive (3 colonnes, 2 colonnes, puis 1 colonne selon la largeur d'écran).

## Prérequis

| Outil | Version |
|---|---|
| PHP | 8.3 ou plus (voir `backend/composer.json`) |
| Composer | 2.x |
| Node.js | `^22.18` ou `>=24.12` (voir `frontend/package.json`) |
| npm | fourni avec Node.js |

Extensions PHP nécessaires : `curl`, `mbstring`, `openssl`, `fileinfo` et `zip` (cette dernière est utilisée par Composer).

## Installation

```bash
git clone https://github.com/TheoLebreton72/exo-technique-Glinche.git
cd exo-technique-Glinche
```

### 1. Back-end (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Ouvrir ensuite `backend/.env` et renseigner les identifiants de l'API (voir la section suivante).

### 2. Front-end (Vue.js)

```bash
cd frontend
npm install
```

## Variables d'environnement

Toute la configuration se fait dans `backend/.env` (copié depuis `backend/.env.example`). Le fichier `.env` n'est jamais versionné.

| Variable | Description | Valeur |
|---|---|---|
| `GLINCHE_API_URL` | URL de base de l'API Glinche | `https://marketplace-dev.glinche-automobiles.com` (déjà renseignée) |
| `GLINCHE_API_EMAIL` | Identifiant fourni pour l'exercice | À renseigner |
| `GLINCHE_API_PASSWORD` | Mot de passe fourni pour l'exercice | À renseigner (le mettre entre guillemets, il contient des caractères spéciaux) |

Exemple :

```env
GLINCHE_API_URL=https://marketplace-dev.glinche-automobiles.com
GLINCHE_API_EMAIL=identifiant-fourni
GLINCHE_API_PASSWORD="mot-de-passe-fourni"
```

Le projet n'utilisant pas de base de données, `.env.example` configure déjà `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` et `DB_CONNECTION=null`. Aucune migration n'est à lancer.

Ces variables sont lues via `backend/config/services.php` (clé `glinche`), jamais directement par le code.

## Lancer le projet

Deux terminaux sont nécessaires.

**Terminal 1 – Laravel** (http://localhost:8000) :

```bash
cd backend
php artisan serve
```

**Terminal 2 – Vue.js** (http://localhost:5173) :

```bash
cd frontend
npm run dev
```

Ouvrir ensuite **http://localhost:5173** : la liste des véhicules s'affiche et la liste déroulante « Marque » permet de filtrer.

> Le serveur de développement Vite relaie les appels `/api/...` vers Laravel (`http://localhost:8000`). Laravel doit donc être lancé avant d'ouvrir la page.


## Choix techniques

### Pas de base de données

Le sujet liste une base relationnelle parmi les technologies, en la laissant « libre ». J'ai choisi de ne pas en utiliser, pour ces raisons :

- l'API Glinche reste la source de vérité et l'application se limite à afficher et filtrer une liste ;
- le périmètre n'a besoin d'aucune persistance (pas de compte utilisateur, de favoris ni d'historique) ;
- copier les véhicules en base aurait ajouté de la duplication et un risque de désynchronisation (prix modifiés, véhicules retirés) sans bénéfice fonctionnel ;
- le volume est faible (9 véhicules, environ 71 Ko de JSON).

Si le besoin évoluait (volume important, favoris, recherche avancée, historique), j'importerais les véhicules en base relationnelle (MySQL) via une commande Artisan planifiée, avec une table `brands` et une table `vehicles`, ce qui permettrait de filtrer et paginer en SQL.

### Laravel comme intermédiaire entre le front et l'API Glinche

Même sans base de données, le back-end Laravel a un rôle :

- **sécurité** : l'identifiant et le mot de passe de l'API restent côté serveur (`.env`) et ne sont jamais exposés au navigateur ;
- **authentification** : Laravel se connecte à l'API (`POST /api/partners/login`), récupère le token Bearer et l'utilise pour récupérer les véhicules ;
- **gestion des erreurs** : les erreurs de l'API (`->throw()`) sont remontées au front au lieu d'être renvoyées avec un statut 200.

### Filtre par marque côté front

L'API renvoie toute la liste en une seule réponse et le volume est faible : le filtre est donc fait côté Vue avec des propriétés `computed`, sans rappeler le serveur :

- la liste des marques est dérivée des véhicules (sans doublons, triée) ;
- les véhicules affichés sont recalculés à chaque changement de la liste déroulante, ce qui rend le filtrage immédiat.

### Proxy Vite en développement

Le front (`localhost:5173`) et Laravel (`localhost:8000`) sont deux origines différentes. Le proxy Vite (`frontend/vite.config.js`) relaie les appels `/api` vers Laravel, ce qui évite de configurer CORS en développement. Pour un déploiement, front et back seraient servis derrière le même domaine, ou CORS serait configuré côté Laravel.

### Interface

Les véhicules sont présentés sous forme de cartes dans une grille CSS (3, 2 puis 1 colonne selon la largeur d'écran). L'énergie, renvoyée sous forme de code par l'API (`ES`, `GO`, `EH`, `EL`), est traduite en libellé lisible. Aucune bibliothèque d'interface n'a été ajoutée, pour garder le projet léger.

## Difficultés rencontrées

**Mise en cache du token** : j'avais mis en cache le token d'authentification pour éviter de se reconnecter à chaque requête, mais cela posait problème, au bout d'un certain temps le token devenait inutilisable. Je l'ai retiré pour avancer. Conséquence : chaque chargement fait deux appels successifs à l'API (login, puis liste).

**Temps d'affichage** : l'affichage prend environ une seconde. Mesures sur la requête `/api/vehicles` : 863 ms au total (861 ms d'attente côté serveur) pour 71 Ko, ce qui correspond aux deux appels séquentiels vers l'API de développement. Les images (environ 75 Ko chacune, deux à 266 Ko) ne se chargent qu'après l'affichage des cartes.

**Forme des données** : l'API renvoie plus de 100 champs par véhicule. Certains sont `null` selon les véhicules, le prix est une chaîne (`"26700.00"`) et l'énergie est un code (`ES`, `GO`, `EH`, `EL`) à traduire.

## Limites connues et pistes d'amélioration

Le sujet conseille 3 à 4 heures : voici ce que je n'ai pas fait, et comment je l'aurais fait avec plus de temps.

- **Réponse de Laravel non filtrée** : le back-end relaie le JSON de l'API tel quel (plus de 100 champs par véhicule, dont des données inutiles comme la plaque d'immatriculation). Avec plus de temps, j'ajouterais une `API Resource` Laravel qui ne renverrait que les champs utiles (marque, modèle, version, kilométrage, énergie, prix, photo), ce qui allégerait la réponse et simplifierait le front.
- **Cache** : mettre en cache le token et la liste des véhicules (quelques minutes) pour éviter les deux appels à l'API à chaque visite.
- **Gestion d'erreurs** : renvoyer une réponse 502 avec un message propre quand l'API Glinche est indisponible, et afficher dans l'interface des états de chargement, d'erreur et de liste vide.
- **Tests** : ajouter un test Laravel avec `Http::fake()` pour simuler l'API Glinche. Seuls les tests d'exemple fournis par Laravel sont présents.
- **Données volumineuses** : si le nombre de véhicules augmentait, ajouter une pagination et filtrer côté serveur, éventuellement avec un import en base (voir [Choix techniques](#choix-techniques)).
- **TypeScript** : typer les données renvoyées par l'API (véhicule, prix, énergie).
- **Nettoyage** : supprimer les fichiers par défaut de Laravel devenus inutiles (`User.php`, `welcome.blade.php`, `backend/package.json`).