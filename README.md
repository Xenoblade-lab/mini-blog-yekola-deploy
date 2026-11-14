# Évaluation — Mini Blog Laravel / Blade

**Module :** Développement Web avec Laravel
**Niveau :** L3 — Informatique et Logiciels
**Dépôt GitHub :** [Dr-Lab1/mini-blog-l3-il](https://github.com/Dr-Lab1/mini-blog-l3-il)

---

## Mise en place du projet

Avant de commencer l'évaluation, effectuez les étapes suivantes dans l'ordre :

```bash
# 1. Cloner le dépôt GitHub
git clone https://github.com/Dr-Lab1/mini-blog-l3-il.git

# 2. Se déplacer dans le répertoire du projet
cd mini-blog-l3-il

# 3. Installer les dépendances PHP
composer install

# 4. Copier le fichier d'environnement
cp .env.example .env

# 5. Générer la clé de l'application
php artisan key:generate

# 6. Créer la base SQLite et lancer les migrations (sessions, cache, etc.)
# Sous Windows PowerShell :
New-Item -ItemType File -Path database\database.sqlite -Force
# Sous Linux/macOS : touch database/database.sqlite
php artisan migrate
```

> Assurez-vous d'avoir **PHP 8.1+**, **Composer** et **Laravel 10+** installés sur votre machine avant de commencer.
>
> **Erreur « database.sqlite introuvable » ?** Le fichier `database/database.sqlite` n'est pas versionné (`.gitignore`). Créez-le puis exécutez `php artisan migrate` comme à l'étape 6.

---

## Travail à réaliser

### Question 1 — Layouts Blade (racines des deux parties)

Créez deux fichiers root Blade distincts :

- `resources/views/App.blade.php` → pour la **partie publique** du blog
- `resources/views/Dashboard.blade.php` → pour la **partie dashboard** (administration)

Chaque root doit utiliser les directives `@yield` pour définir les zones dynamiques (au minimum : `title`, `content`). Chaque vue enfant devra utiliser `@extends` pour hériter du bon layout et `@section` / `@endsection` pour injecter son contenu dans les zones correspondantes.

**Questions :**

1. Quelle est la différence entre `@yield('title')` et `@yield('title', 'Valeur par défaut')` ?

   **Réponse :** `@yield('title')` affiche une zone vide si la vue enfant ne définit pas de section `title`. Avec `@yield('title', 'Valeur par défaut')`, Laravel affiche la valeur par défaut lorsque aucune section n'est fournie — utile pour un titre de page de secours.

2. Pourquoi utilise-t-on `@extends` plutôt que d'inclure le header et le footer manuellement dans chaque fichier de vue ?

   **Réponse :** `@extends` centralise la structure HTML (head, layout, zones `@yield`) dans un seul fichier. On évite la duplication : modifier le header ou le CSS se fait une fois dans le layout, et toutes les pages enfants en héritent automatiquement via `@section('content')`.

3. Comment s'assure-t-on qu'une vue du dashboard n'étende jamais accidentellement le layout public ?

   **Réponse :** En séparant clairement les layouts (`app` vs `dashboard`), les dossiers de vues (`public/` vs `dashboard/`) et les conventions de nommage (`@extends('dashboard')` pour l'admin). Une revue de code et des routes préfixées `dashboard.*` réduisent aussi le risque d'erreur.

---

### Question 2 — Assets & Composants de la partie publique

1. Déplacez le fichier `index.css` dans le dossier `public/css/`.
2. Référencez-le dans vos layouts en utilisant la fonction **`asset()`** de Laravel.
3. Créez deux **composants Blade anonymes** :
   - `resources/views/components/header.blade.php`
   - `resources/views/components/footer.blade.php`
4. Incluez ces composants dans le layout public en utilisant la syntaxe `@include()`.

> **Réalisé :** `public/css/public.css`, layout `resources/views/app.blade.php`, composants `components/header.blade.php` et `components/footer.blade.php`.

---

### Question 3 — Assets & Composants du dashboard

1. Déplacez le fichier `Dashboard.css` dans le dossier `public/css/`.
2. Référencez-le dans vos layouts en utilisant la fonction **`asset()`**.
3. Créez deux composants Blade pour le dashboard :
   - `resources/views/components/dashboard/topbar.blade.php`
   - `resources/views/components/dashboard/sidebar.blade.php`
4. Incluez ces composants dans `Dashboard.blade.php`.

**Questions :**

1. Comment rendre la classe `active` d'un lien de la sidebar **dynamique** selon la route courante, en utilisant `request()->routeIs()` ou `Route::currentRouteName()` ?

   **Réponse :** Exemple avec `request()->routeIs()` :
   ```blade
   <a href="{{ route('dashboard.articles') }}"
      class="nav-item {{ request()->routeIs('dashboard.articles') ? 'active' : '' }}">
   ```
   Avec `Route::currentRouteName()` : comparer le nom retourné (ex. `dashboard.articles`) à la route du lien. `routeIs()` accepte aussi des jokers : `request()->routeIs('dashboard.*')`.

2. Pourquoi est-il préférable de placer les composants du dashboard dans un sous-dossier `components/dashboard/` plutôt que directement dans `components/` ?

   **Réponse :** Cela organise les composants par contexte (public vs admin), évite les conflits de noms (ex. un `sidebar` public et un `sidebar` dashboard), et rend le projet plus lisible quand il grossit.

---

### Question 4 — Création des routes

Dans le fichier `routes/web.php`, déclarez une route nommée pour chacune des vues suivantes :

**Partie publique :**

| URL | Nom de la route | Description |
|---|---|---|
| `/` | `home` | Page d'accueil |
| `/articles` | `articles.index` | Liste des articles |
| `/articles/{slug}` | `articles.show` | Détail d'un article |
| `/categories` | `categories.index` | Liste des catégories |
| `/about` | `about` | Page à propos |

**Questions :**

1. Quelle est la différence entre `Route::get()` et `Route::post()` ? Dans quel cas utilise-t-on l'un plutôt que l'autre ?

   **Réponse :** `GET` sert à **lire** une ressource ou afficher une page (pas de modification côté serveur attendue). `POST` sert à **envoyer** des données (formulaire de création, connexion, etc.). On utilise `GET` pour la navigation et `POST` pour les actions qui créent ou modifient des données.

2. Comment déclarer et nommer une route avec la méthode `->name()` ? Pourquoi les noms de routes sont-ils indispensables pour utiliser `route()` dans les vues Blade ?

   **Réponse :** Exemple : `Route::get('/articles', [MainController::class, 'articles'])->name('articles.index');`. Le nom permet à `route('articles.index')` de générer l'URL correcte même si le chemin change plus tard — indispensable pour des liens maintenables dans Blade.

3. Qu'est-ce qu'un paramètre de route dynamique comme `{id}` ? Comment le récupérer dans le contrôleur ?

   **Réponse :** `{slug}` est un segment d'URL variable. Laravel l'injecte dans la méthode du contrôleur : `public function article(string $slug)`. On peut aussi le typer et le lier à un modèle avec le route model binding.

4. Que se passe-t-il si deux routes ont la même URL mais des méthodes HTTP différentes (`GET` et `POST`) ?

   **Réponse :** Laravel les distingue : la même URL peut avoir une route `GET` (afficher un formulaire) et une route `POST` (traiter l'envoi). Le routeur choisit selon la méthode HTTP de la requête.

---

### Question 5 — Groupement des routes du dashboard

Créez un **groupe de routes** pour toutes les pages du dashboard en utilisant `Route::prefix()` et `->group()`.

Toutes les routes du dashboard doivent :
- Avoir le **préfixe d'URL** `/dashboard`
- Avoir le **préfixe de nom** `dashboard.`
- Pointer vers les méthodes de `DashboardController`

Exemple de routes attendues :

| URL | Nom de la route | Méthode du contrôleur |
|---|---|---|
| `/dashboard` | `dashboard.index` | `index` |
| `/dashboard/articles` | `dashboard.articles` | `articles` |
| `/dashboard/categories` | `dashboard.categories` | `categories` |
| `/dashboard/utilisateurs` | `dashboard.users` | `users` |
| `/dashboard/commentaires` | `dashboard.comments` | `comments` |
| `/dashboard/reglages` | `dashboard.settings` | `settings` |

**Questions :**

1. Quelle est la syntaxe complète pour créer un groupe de routes avec un préfixe d'URL et un préfixe de nom en même temps ?

   **Réponse :**
   ```php
   Route::prefix('dashboard')->name('dashboard.')->group(function () {
       Route::get('/', [DashboardController::class, 'index'])->name('index');
   });
   ```
   L'URL finale est `/dashboard` et le nom complet de la route est `dashboard.index`.

2. Quelle est la différence entre `Route::prefix()` et `Route::middleware()` dans un groupe de routes ?

   **Réponse :** `prefix()` ajoute un segment au **chemin URL** de toutes les routes du groupe (ex. `/dashboard/articles`). `middleware()` applique des **filtres** (auth, CSRF, etc.) avant d'exécuter le contrôleur — ce n'est pas lié à l'URL.

3. Qu'est-ce que `Route::resource()` ? Pour quelles ressources (articles, catégories, utilisateurs) serait-il pertinent de l'utiliser et quelles routes génère-t-il automatiquement ?

   **Réponse :** `Route::resource('articles', ArticleController::class)` génère les 7 routes REST : `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` (GET/POST/PUT/PATCH/DELETE). Pertinent pour articles, catégories et utilisateurs en CRUD complet dans le dashboard.

---

### Question 6 — Création des contrôleurs

Générez les deux contrôleurs suivants via la commande `php artisan make:controller` :

**`MainController`** — gérera toutes les vues publiques :
- `index()` → vue de la page d'accueil
- `articles()` → vue de la liste des articles
- `article($slug)` → vue du détail d'un article
- `categories()` → vue de la liste des catégories
- `about()` → vue de la page à propos

**`DashboardController`** — gérera toutes les vues du dashboard :
- `index()` → vue principale du dashboard
- `articles()` → vue des articles (admin)
- `categories()` → vue des catégories (admin)
- `users()` → vue des utilisateurs
- `comments()` → vue des commentaires
- `settings()` → vue des réglages

Chaque méthode doit retourner sa vue correspondante avec `return view('...')`.

**Questions :**

1. Quelle est la commande artisan pour générer un contrôleur ? Quelle option ajouter pour générer directement un **contrôleur de ressource** avec toutes les méthodes CRUD ?

   **Réponse :** `php artisan make:controller NomController`. Pour un contrôleur de ressource : `php artisan make:controller ArticleController --resource` (ou `--api` pour une API sans `create`/`edit`).

2. Quelle est la convention de nommage des méthodes d'un contrôleur de ressource Laravel (`index`, `show`, `create`, `store`, `edit`, `update`, `destroy`) ? À quelle action correspond chacune ?

   **Réponse :** `index` = liste ; `show` = détail ; `create` = formulaire de création ; `store` = enregistrer (POST) ; `edit` = formulaire d'édition ; `update` = mettre à jour (PUT/PATCH) ; `destroy` = supprimer (DELETE).

3. Quelle est la différence entre ces trois façons de passer des données à une vue depuis un contrôleur ?
   ```php
   return view('articles', ['posts' => $posts]);
   return view('articles', compact('posts'));
   return view('articles')->with('posts', $posts);
   ```

   **Réponse :** Les trois passent la variable `$posts` à la vue. La première utilise un tableau associatif explicite. `compact('posts')` crée `['posts' => $posts]` à partir du nom de variable. `->with()` permet d'enchaîner plusieurs variables ; le résultat est équivalent.

---

### Question 7 — Liens et navigation

Sont concernés (liste non exhaustive) :
- Les liens de la navbar publique (Accueil, Articles, Catégories, À propos)
- Les liens de la sidebar du dashboard (Dashboard, Articles, Catégories, Utilisateurs, Commentaires, Réglages)
- Le lien « Voir le blog » dans la topbar du dashboard
- Le lien « Dashboard / Admin » dans le footer public
- Les liens « Voir tout → » sur la page d'accueil
- Les liens sur les cartes d'articles (qui mènent vers le détail d'un article)
- Le breadcrumb sur la page article
- Le bouton « ↗ Voir le blog » dans le dashboard

---

## 📁 Structure de fichiers attendue

À la fin de l'évaluation, votre projet doit respecter l'arborescence suivante :

```
resources/
└── views/
    ├── app.blade.php       ← Layout partie publique
    ├── dashboard.blade.php ← Layout dashboard
    ├── components/
    │   ├── header.blade.php           ← Header public
    │   ├── footer.blade.php           ← Footer public
    │   └── dashboard/
    │       ├── topbar.blade.php       ← Topbar dashboard
    │       └── sidebar.blade.php      ← Sidebar dashboard
    ├── public/                        ← Vues publiques
    │   ├── index.blade.php
    │   ├── articles.blade.php
    │   ├── article.blade.php
    │   ├── categories.blade.php
    │   └── about.blade.php
    └── dashboard/                     ← Vues dashboard
        ├── index.blade.php
        ├── articles.blade.php
        ├── categories.blade.php
        ├── users.blade.php
        ├── comments.blade.php
        └── settings.blade.php

app/
└── Http/
    └── Controllers/
        ├── MainController.php
        └── DashboardController.php

public/
└── css/
    ├── public.css
    └── dashboard.css

routes/
└── web.php
```

---

## Critères d'évaluation

| Critère | Points |
|---|---|
| Layouts Blade corrects avec `@extends`, `@yield`, `@section` | 3 pts |
| Composants publics (header, footer) fonctionnels avec `asset()` | 3 pts |
| Composants dashboard (topbar, sidebar) fonctionnels avec `asset()` | 3 pts |
| Routes publiques nommées et correctement déclarées | 3 pts |
| Routes dashboard groupées avec préfixe et nommage cohérent | 3 pts |
| Contrôleurs créés avec les bonnes méthodes et retours de vues | 3 pts |
| Liens Blade partout | 4 pts |
| Réponses aux questions théoriques | 8 pts |
| **Total** | **30 pts** |

---

## Consignes générales

- Le travail est **individuel**.
- Soumettez votre travail en poussant votre code sur un dépôt GitHub **personnel** et en partageant le lien.
- Le dépôt doit contenir un fichier `.env.example` à jour mais **jamais** le fichier `.env` lui-même.
- Toute ressemblance de code entre deux rendus entraînera un **zéro** pour les deux parties concernées.
- Les réponses aux questions théoriques sont à rédiger directement dans ce fichier `README.md`, sous chaque question.

**Bonne évaluation !**