
<!DOCTYPE html>
<html lang="en">
    <head>

        @include('layouts.partials._header')

    </head>
    <body>
        <div class="loader-bg"></div>
        
        @include('layouts.partials._loader')

        <div class="mn-content fixed-sidebar">
            
            @include('layouts.partials._head')

            <aside id="slide-out" class="side-nav white fixed">
                <div class="side-nav-wrapper">
                    <div class="sidebar-profile">
                        <div class="sidebar-profile-image">
                            <img src="{{ asset('assets/images/profile-image.png') }}" class="circle" alt="">
                        </div>
                        <div class="sidebar-profile-info">
                            <a href="javascript:void(0);" class="account-settings-link">
                                <p>David Doe</p>
                                <span>david@gmail.com<i class="material-icons right">arrow_drop_down</i></span>
                            </a>
                        </div>
                    </div>
                    <div class="sidebar-account-settings">
                        <ul>
                            <li class="no-padding">
                                <a class="waves-effect waves-grey"><i class="material-icons">mail_outline</i>Inbox</a>
                            </li>
                            <li class="no-padding">
                                <a class="waves-effect waves-grey"><i class="material-icons">star_border</i>Starred<span class="new badge">18</span></a>
                            </li>
                            <li class="no-padding">
                                <a class="waves-effect waves-grey"><i class="material-icons">done</i>Sent Mail</a>
                            </li>
                            <li class="no-padding">
                                <a class="waves-effect waves-grey"><i class="material-icons">history</i>History<span class="new grey lighten-1 badge">3 new</span></a>
                            </li>
                            <li class="divider"></li>
                            <li class="no-padding">
                                <a class="waves-effect waves-grey"><i class="material-icons">exit_to_app</i>Sign Out</a>
                            </li>
                        </ul>
                    </div>
                <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
                    <li class="no-padding"><a class="waves-effect waves-grey" href="/template"><i class="material-icons">home</i>Accueil</a></li>
                    <li class="no-padding"><a class="collapsible-header waves-effect waves-grey"><i class="material-icons">apps</i>Distributeurs<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/distrib/liste">Liste</a></li>
                                <li><a href="/distrib/pointe-valeur">Points Valeurs</a></li>
                                <li><a href="/distrib/arbo">Arborescence</a></li>
                                <li><a href="/distrib/detail">Details Network</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="no-padding active"><a class="collapsible-header waves-effect waves-grey active"><i class="material-icons">code</i>Produits<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li class="no-padding"><a class="active" href="/produit/stock">Stock des produits</a></li>
                                <li><a href="/produit/category">Catégories des produits</a></li>
                                <li><a href="/produit/point-valeur">Points Valeurs</a></li>
                                <li><a href="/produit/tarification">Tarification</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="no-padding">
                        <a href="/achats" class="waves-effect waves-grey"><i class="material-icons">star_border</i>Achats<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                    </li>
                    <li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">tag_faces</i>Bonus<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/bonus/direct">Bonus Directs</a></li>
                                <li><a href="/bonus/indirect">Bonus Inditects</a></li>
                                <li><a href="/bonus/leadership">Bonus Leadership</a></li>
                                <li><a href="/bonus/compte-epargne">Compte Epargne</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">grid_on</i>Bulletin<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/bulletin/individuel">Individuel</a></li>
                                <li><a href="/bulletin/collectif">Collectif</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="no-padding">
                        <a class="waves-effect waves-grey"><i class="material-icons">my_location</i>Cartographie<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>                        
                    </li>
                </ul>
                <div class="footer">
                    <p class="copyright">2024 © Inform@tika</p>
                    <a href="#!">Privacy</a> &amp; <a href="#!">Terms</a>
                </div>
                </div>
            </aside>
            
            <!-- @include('layouts.main')  -->
        </div>
        <div class="left-sidebar-hover"></div>
        @include('layouts.partials._script-footer')
        
    </body>
</html>
