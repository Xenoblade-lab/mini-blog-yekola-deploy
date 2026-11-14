{{--
    Composant Blade — Barre de navigation publique
    La classe "active" est appliquée dynamiquement selon la route courante (request()->routeIs)
--}}
<nav>
    <a href="{{ route('home') }}" class="nav-logo">Le Blog</a>
    <ul class="nav-links">
        <li>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a>
        </li>
        <li>
            <a href="{{ route('articles.index') }}"
                class="{{ request()->routeIs('articles.*') ? 'active' : '' }}">Articles</a>
        </li>
        <li>
            <a href="{{ route('categories.index') }}"
                class="{{ request()->routeIs('categories.index') ? 'active' : '' }}">Catégories</a>
        </li>
        <li>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">À propos</a>
        </li>
        <li>
            <a href="{{ route('dashboard.index') }}">Dashboard</a>
        </li>
    </ul>
</nav>
