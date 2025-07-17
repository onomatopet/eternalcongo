<!-- ARBORESCENCE -->

                <div class="col s12 m12 l12">
                    <div class="card">

                        <section class="management-hierarchy">
                            <div class="hv-container">
                                <div class="hv-wrapper">

                                    <!-- Key component -->
                                    <div class="hv-item">

                                        <div class="hv-item-parent">
                                            <a href="">
                                                <div class="person">
                                                    <img src="https://pbs.twimg.com/profile_images/762654833455366144/QqQhkuK5.jpg"
                                                        alt="">
                                                    <p class="name">
                                                        {{ $distributeurs->nom_distributeur.' '.$distributeurs->pnom_distributeur }}
                                                        <br />
                                                        <b class="name">Points Valeur :</b>
                                                    </p>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="hv-item-children">

                                            @include('layouts.distrib.recursive',['distribparents' => $distribparents])

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>





<div id="test2" class="s12">
                    <main class="mn-inner">
                        <div class="row">
                            <div class="col s12">
                                <div class="row">
                                    <div class="col s4"><h5>Arborescence des Distribiteurs</h5></div>
                                    <div class="col s8" style="text-align:right"><h5>Arborescence des Distribiteurs</h5></div>
                                </div>
                            </div>
                            <div class="col s12 m12 l12">
                                <div class="card">
                    
                                    <section class="management-hierarchy">
                                        <div class="hv-container">
                                            <div class="hv-wrapper">
                    
                                                <!-- Key component -->
                                                <div class="hv-item">
                    
                                                    <div class="hv-item-parent">
                                                        <a href="">
                                                        <div class="person">
                                                            <img src="https://pbs.twimg.com/profile_images/762654833455366144/QqQhkuK5.jpg" alt="">
                                                            <p class="name">
                                                                {{ $distributeurs->nom_distributeur.' '.$distributeurs->pnom_distributeur }} <b></b>
                                                            </p>
                                                        </div>
                                                        </a>
                                                    </div>
                    
                                                    <div class="hv-item-children">
                    
                                                        @include('layouts.distrib.recursive',['distribparents' => $distribparents])  
                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                    
                                    </section>   
                                </div>
                            </div>
                        </div>
                    </main>     
                </div>
                

                
@foreach($distribparents as $key=>$distribparent)

<div class="hv-item-child">
    <!-- Key component -->
    <div class="hv-item">
        <div class="hv-item-parent">
            <a href="">
            <div class="person">
                <img src="https://pbs.twimg.com/profile_images/762654833455366144/QqQhkuK5.jpg" alt="">
                <p class="name">
                    {{ $distributeurs->etoiles_id??null.' | '.$distribparent->distributeur_id??null }} <br/>
                    {{ $distribparent->nom_distributeur??null.' '.$distribparent->pnom_distributeur??null }}
                </p>
            </div>
            </a>
        </div>   
        
        @if(!empty($distribparent->children) && $distribparent->children->count())      
            <div class="hv-item-children">             
                @include('layouts.distrib.recursive',['distribparents' => $distribparent->children])    
            </div>  
        @endif 
    </div>
</div>
    
@endforeach

