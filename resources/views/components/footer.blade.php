{{-- Composant Blade — Pied de page public --}}
<footer>
    <span>© 2026 Le Blog. Tous droits réservés.</span>
    <div>
        <a href="#">Mentions légales</a>
        <a href="#">Confidentialité</a>
        {{-- Lien vers l'administration via la route nommée dashboard.index --}}
        <a href="{{ route('dashboard.index') }}">Dashboard / Admin</a>
    </div>
</footer>
