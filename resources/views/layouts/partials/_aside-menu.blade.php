<aside id="slide-out" class="side-nav white fixed">
    <div class="side-nav-wrapper">
        <div class="sidebar-profile">
            <div class="sidebar-profile-image">
                <img src="{{ asset('assets/images/profile-image.png') }}" class="circle" alt="">
            </div>
            <div class="sidebar-profile-info">
                <a href="javascript:void(0);" class="account-settings-link">
                    <p>{{ Auth::user()->name ?? ''}}</p>
                    <span>{{ Auth::user()->email ?? ''}}<i class="material-icons right">arrow_drop_down</i></span>
                </a>
            </div>
        </div>
        <div class="sidebar-account-settings">
            <!-- Account Management -->
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Manage Account') }}
            </div>

            <x-dropdown-link href="{{ route('profile.show') }}">
                {{ __('Profile') }}
            </x-dropdown-link>

            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                    {{ __('API Tokens') }}
                </x-dropdown-link>
            @endif

            <div class="border-t border-gray-200"></div>

            <ul>
                <li class="no-padding">
                    <!-- Authentication -->
                    <a href="{{ route('logout') }}" class="waves-effect waves-grey"><i class="material-icons">exit_to_app</i>Se déconnecter</a>
                </li>
            </ul>
        </div>
        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
            <li class="no-padding {{ request()->routeIs('dashboard.*') ? 'active': ''}}"><a class="waves-effect waves-grey active" href="{{ route('dashboard.index') }}"><i class="material-icons">home</i>Accueil</a></li>
            <li class="no-padding {{ request()->routeIs('achats.*') ? 'active': '' }}"><a class="collapsible-header waves-effect waves-grey"><i class="material-icons">tag_faces</i>Achats<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="{{ route('achats.index') }}">Consulter les achats</a></li>
                        <li><a href="{{ route('achats.create') }}">Nouvel achat (avec bonus)</a></li>
                        <li><a href="{{ route('achats.created') }}">Achats différés (sans bonus)</a></li>
                    </ul>
                </div>
            </li>
            <li class="no-padding {{ request()->routeIs('distrib.*') ? 'active': '' }}">
                <a href="{{ route('distrib.index') }}" class="waves-effect waves-grey"><i class="material-icons">apps</i>Distributeurs<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>

            <li class="no-padding {{ request()->routeIs('network.*') ? 'active': '' }}">
                <a href="{{ route('network.index') }}" class="waves-effect waves-grey"><i class="material-icons">star_border</i>Details Network<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
            </li>
            <li class="no-padding {{ request()->routeIs('products.*') ? 'active': '' }}">
                <a href="{{ route('products.index') }}" class="waves-effect waves-grey"><i class="material-icons">code</i>Produits<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
            </li>
            <li class="no-padding {{ request()->routeIs('bonus.*') ? 'active': '' }}">
                <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">tag_faces</i>Bonus<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="{{ route('bonus.index') }}">Consulter les bonus</a></li>
                        <li><a href="{{ route('bonus.create') }}">Editer le Bon</a></li>
                    </ul>
                </div>
            </li>
            <li class="no-padding"">
                <a href="{{ route('configs.index') }}"><i class="material-icons">settings</i>Configuration<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
            </li>
        </ul>
        <div class="footer">
            <p class="copyright">2024 © Inform@tika</p>
            <a href="#!">Privacy</a> &amp; <a href="#!">Terms</a>
        </div>
    </div>
</aside>
