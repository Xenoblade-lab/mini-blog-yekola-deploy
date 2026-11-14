# Explications du TP — Mini Blog Laravel / Blade

**Auteur :** Bradley Ngonde  
**Dépôt personnel :** [Xenoblade-lab/Ngonde-Shadai-Bradley](https://github.com/Xenoblade-lab/Ngonde-Shadai-Bradley)

Ce document reprend **chaque question** du `README.md` d'évaluation : ce qui a été fait dans le projet, où se trouve le code, et les réponses théoriques.

---

## Mise en place du projet

### Étapes réalisées

1. Cloner le dépôt, `composer install`, copier `.env.example` vers `.env`, `php artisan key:generate`.
2. Créer `database/database.sqlite` (fichier non versionné) puis `php artisan migrate` pour les tables `sessions`, `cache`, `users`, etc.

Sans la base SQLite, Laravel renvoie une erreur 500 car `.env` utilise `SESSION_DRIVER=database` et `CACHE_STORE=database`.

---

## Question 1 — Layouts Blade

### Travail réalisé

| Fichier | Rôle |
|---------|------|
| `resources/views/app.blade.php` | Layout **partie publique** |
| `resources/views/dashboard.blade.php` | Layout **administration** |

Chaque layout définit au minimum :

- `@yield('title', '...')` — titre de l’onglet
- `@yield('content')` — contenu principal

Les pages enfants utilisent :

```blade
@extends('app')

@section('title', 'Mon titre')

@section('content')
    ...
@endsection
```

**Exemple :** `resources/views/public/index.blade.php` étend `app`, `resources/views/dashboard/index.blade.php` étend `dashboard`.

### Réponses théoriques

**1. Différence entre `@yield('title')` et `@yield('title', 'Valeur par défaut')`**

- `@yield('title')` : si la vue enfant ne définit pas `@section('title')`, la zone reste **vide**.
- `@yield('title', 'Le Blog')` : si aucune section n’est fournie, Laravel affiche **« Le Blog »** par défaut.

**2. Pourquoi `@extends` plutôt que `@include` du header/footer dans chaque page ?**

`@extends` centralise le squelette HTML (DOCTYPE, `<head>`, CSS, structure). On modifie le layout **une seule fois** ; toutes les pages héritent du changement. Avec `@include` répété partout, chaque fichier dupliquerait la même structure et les erreurs se multiplieraient.

**3. Comment éviter qu’une vue dashboard étende le layout public ?**

- Deux layouts distincts : `app` et `dashboard`.
- Deux dossiers de vues : `public/` et `dashboard/`.
- Convention stricte : les vues admin font toujours `@extends('dashboard')`.
- Routes nommées avec préfixe `dashboard.*` pour repérer facilement le contexte.

---

## Question 2 — Assets & composants publics

### Travail réalisé

| Consigne | Réalisation |
|----------|-------------|
| Déplacer `index.css` | Fichier `public/css/public.css` (copie de `index.css`) |
| Référencer avec `asset()` | Dans `app.blade.php` : `{{ asset('css/public.css') }}` |
| Composant header | `resources/views/components/header.blade.php` |
| Composant footer | `resources/views/components/footer.blade.php` |
| `@include()` dans le layout | `@include('components.header')` et `@include('components.footer')` |

**Fonction `asset()` :** génère l’URL complète vers un fichier dans `public/` (ex. `http://localhost:8000/css/public.css`). Si le projet change de domaine, les liens restent corrects.

---

## Question 3 — Assets & composants dashboard

### Travail réalisé

| Consigne | Réalisation |
|----------|-------------|
| CSS dashboard | `public/css/dashboard.css` |
| `asset()` | Dans `dashboard.blade.php` : `{{ asset('css/dashboard.css') }}` |
| Topbar | `resources/views/components/dashboard/topbar.blade.php` |
| Sidebar | `resources/views/components/dashboard/sidebar.blade.php` |
| Inclusion | `@include('components.dashboard.topbar')` et `sidebar` dans le layout |

La topbar utilise `@yield('page-title')` pour afficher un titre différent par page (Articles, Utilisateurs, etc.).

### Réponses théoriques

**1. Classe `active` dynamique dans la sidebar**

Avec `request()->routeIs()` :

```blade
<a href="{{ route('dashboard.articles') }}"
   class="nav-item {{ request()->routeIs('dashboard.articles') ? 'active' : '' }}">
```

- Si l’URL courante correspond à la route `dashboard.articles`, la classe `active` est ajoutée.
- Variante : `request()->routeIs('dashboard.*')` pour toutes les pages du dashboard.
- Avec `Route::currentRouteName()` : comparer à `'dashboard.articles'` manuellement.

**Implémentation :** voir `resources/views/components/dashboard/sidebar.blade.php`.

**2. Pourquoi `components/dashboard/` ?**

Séparer les composants **public** et **admin** évite les conflits de noms (deux `sidebar` possibles), clarifie l’arborescence et facilite la maintenance quand le projet grandit.

---

## Question 4 — Routes publiques

### Travail réalisé

Fichier : `routes/web.php` — contrôleur `MainController`.

| URL | Nom de route | Méthode |
|-----|--------------|---------|
| `/` | `home` | `index()` |
| `/articles` | `articles.index` | `articles()` |
| `/articles/{slug}` | `articles.show` | `article($slug)` |
| `/categories` | `categories.index` | `categories()` |
| `/about` | `about` | `about()` |

Exemple :

```php
Route::get('/articles/{slug}', [MainController::class, 'article'])->name('articles.show');
```

### Réponses théoriques

**1. `Route::get()` vs `Route::post()`**

- **GET** : lecture, affichage d’une page (navigation, liste, détail).
- **POST** : envoi de données (formulaire, création, connexion).

**2. `->name()` et `route()` dans Blade**

```php
Route::get('/about', [MainController::class, 'about'])->name('about');
```

Dans une vue : `href="{{ route('about') }}"` → génère `/about`. Si l’URL change dans `web.php`, les vues restent correctes sans modifier chaque lien.

**3. Paramètre dynamique `{slug}`**

Segment variable dans l’URL. Laravel l’injecte dans le contrôleur :

```php
public function article(string $slug)
{
    return view('public.article', compact('slug'));
}
```

**4. Même URL, méthodes GET et POST différentes**

Laravel distingue les routes par **méthode HTTP**. Ex. `GET /articles` affiche la liste, `POST /articles` peut créer un article — pas de conflit.

---

## Question 5 — Groupement des routes dashboard

### Travail réalisé

```php
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/articles', [DashboardController::class, 'articles'])->name('articles');
    // ...
});
```

| URL | Nom complet | Contrôleur |
|-----|-------------|------------|
| `/dashboard` | `dashboard.index` | `index()` |
| `/dashboard/articles` | `dashboard.articles` | `articles()` |
| `/dashboard/categories` | `dashboard.categories` | `categories()` |
| `/dashboard/utilisateurs` | `dashboard.users` | `users()` |
| `/dashboard/commentaires` | `dashboard.comments` | `comments()` |
| `/dashboard/reglages` | `dashboard.settings` | `settings()` |

Le préfixe `name('dashboard.')` + `->name('index')` donne le nom final **`dashboard.index`**.

### Réponses théoriques

**1. Syntaxe complète du groupe**

```php
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/articles', [DashboardController::class, 'articles'])->name('articles');
});
```

URL : `/dashboard/articles` — Nom : `dashboard.articles`.

**2. `prefix()` vs `middleware()`**

- **`prefix()`** : ajoute un segment à l’**URL** (`/dashboard/...`).
- **`middleware()`** : applique des **filtres** (authentification, etc.) avant le contrôleur — sans changer l’URL.

**3. `Route::resource()`**

Une ligne génère 7 routes CRUD :

| Méthode | Route | Action |
|---------|-------|--------|
| GET | `/articles` | `index` |
| GET | `/articles/create` | `create` |
| POST | `/articles` | `store` |
| GET | `/articles/{id}` | `show` |
| GET | `/articles/{id}/edit` | `edit` |
| PUT/PATCH | `/articles/{id}` | `update` |
| DELETE | `/articles/{id}` | `destroy` |

Pertinent pour **articles**, **catégories** et **utilisateurs** en administration complète.

---

## Question 6 — Contrôleurs

### Travail réalisé

**Commandes utilisées :**

```bash
php artisan make:controller MainController
php artisan make:controller DashboardController
```

**`MainController`** (`app/Http/Controllers/MainController.php`) :

| Méthode | Vue retournée |
|---------|----------------|
| `index()` | `public.index` |
| `articles()` | `public.articles` |
| `article($slug)` | `public.article` (+ variable `$slug`) |
| `categories()` | `public.categories` |
| `about()` | `public.about` |

**`DashboardController`** : retourne `dashboard.index`, `dashboard.articles`, etc.

Chaque méthode : `return view('nom.dossier.fichier');`

### Réponses théoriques

**1. Commande artisan**

- Contrôleur simple : `php artisan make:controller NomController`
- Contrôleur ressource (CRUD) : `php artisan make:controller ArticleController --resource`
- API (sans vues create/edit) : `--api`

**2. Convention des méthodes ressource**

| Méthode | Action |
|---------|--------|
| `index` | Liste |
| `show` | Détail |
| `create` | Formulaire de création |
| `store` | Enregistrement (POST) |
| `edit` | Formulaire d’édition |
| `update` | Mise à jour |
| `destroy` | Suppression |

**3. Trois façons de passer des données à une vue**

```php
return view('articles', ['posts' => $posts]);
return view('articles', compact('posts'));
return view('articles')->with('posts', $posts);
```

Les trois exposent `$posts` dans la vue Blade. `compact('posts')` est un raccourci pour `['posts' => $posts]`. `->with()` permet d’enchaîner : `->with('a', $a)->with('b', $b)`.

---

## Question 7 — Liens et navigation

### Travail réalisé

Tous les liens statiques (`.html`, `#`) ont été remplacés par **`route('nom.de.route')`** :

| Emplacement | Exemple |
|-------------|---------|
| Navbar publique (`header.blade.php`) | `route('home')`, `route('articles.index')`, … |
| Footer public | `route('dashboard.index')` pour Admin |
| Sidebar dashboard | `route('dashboard.articles')`, etc. |
| Topbar dashboard | `route('home')` pour « Voir le blog » |
| Page d’accueil | `route('articles.index')` pour « Voir tout → » |
| Cartes articles | `route('articles.show', ['slug' => '...'])` |
| Breadcrumb article | `route('home')`, `route('articles.index')` |

**Classe active (navbar publique) :** même principe que la sidebar :

```blade
class="{{ request()->routeIs('home') ? 'active' : '' }}"
```

---

## Structure finale du projet

```
resources/views/
├── app.blade.php
├── dashboard.blade.php
├── components/
│   ├── header.blade.php
│   ├── footer.blade.php
│   └── dashboard/
│       ├── topbar.blade.php
│       └── sidebar.blade.php
├── public/          → pages du blog
└── dashboard/       → pages admin

app/Http/Controllers/
├── MainController.php
└── DashboardController.php

public/css/
├── public.css
└── dashboard.css

routes/web.php
```

---

## Vérifications utiles

```bash
php artisan route:list          # Liste des routes nommées
php artisan serve             # http://localhost:8000
php artisan view:clear        # Si une vue ne se met pas à jour
```

---

## Dépôt GitHub

- Branche principale : **`main`**
- Ne pas versionner : `.env`, `database/database.sqlite`, `vendor/`
- À fournir au correcteur : lien vers le dépôt personnel + ce fichier d’explications
