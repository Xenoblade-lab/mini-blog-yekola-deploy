{{--
    Composant Blade — Barre supérieure du dashboard
    @yield('page-title') : titre affiché (défini par chaque vue dashboard)
    @yield('topbar-actions') : boutons optionnels à droite (ex. "Nouvel article")
--}}
<div class="topbar">
    <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
    <div class="topbar-actions">
        <a href="{{ route('home') }}" class="view-site">↗ Voir le blog</a>
        @yield('topbar-actions')
    </div>
</div>
