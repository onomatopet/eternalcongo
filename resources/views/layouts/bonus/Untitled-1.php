
<!--
<span class="card-title">Bonu - Etablissement du bonus DIRECT</span>
                    <div class="row">
                        <div class="col s8">
                            <p>Vous pouvez faire une selection de distributeur à établir le bonus. <br/>Pour cela selectionnez à la suite tous les distributeurs dont vous souhaiter établir une facture commune.</p><br>
                        </div>
                        <div class="cols s4">
                            <p>Veuillez introduire la periode sur laquelle le calcul du bonus va se faire. <br/>
                            insérer la date de la periode conserné au format ci-dessus.</p><br>
                        </div>
                    </div>
                    <div class="row">
                        <form class="col s12" action="{{ route('bonus.store') }}" method="post">
                            @csrf
                        <div class="input-field col s8">
                            <select name="bonuslist[]" class="js-example-tokenizer js-states browser-default" id="bonuslist" multiple="multiple" tabindex="-1" style="width: 100%" id="tokenizer">

                                @foreach( $distributeurs as $lines )
                                <option value={{ $lines->distributeur_id }} title="{{ $lines->id }}">{{ $lines->distributeur_id.'-'.$lines->nom_distributeur.' '.$lines->pnom_distributeur }}</option>
                                @endforeach

                            </select>
                            @if($errors->has('distributeur_id'))
                                <span class="red-text accent-4">{{ $errors->first('distributeur_id') }}</span>
                            @endif
                        </div>
                        <div class="input-field col s4">
                            <input placeholder="" type="text" name="created_at" value="">
                            <label for="mask2" class="active">Date AU FORMAT : MM/YYYY</label>
                        </div>
                    </div>

                    <div class="row center" id="main_content_div"> Veuillez selectionner les distributeur un à la suite de l'autre et valider !</div>
                -->



                
                "new_date": "2023-12",
          "cumul_total": 55,
          "etoiles": 3,
          "new_cumul": 55,
          "cumul_individuel": 55,
          "cumul_collectif": 55