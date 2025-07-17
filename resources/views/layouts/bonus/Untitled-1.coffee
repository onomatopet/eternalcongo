
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS new_date")
            ->selectRaw("sum(new_cumul) as new_cumul")
            ->selectRaw("sum(cumul_individuel) as cumul_individuel")
            ->selectRaw("sum(cumul_total) as cumul_total, etoiles")
            ->selectRaw("sum(cumul_collectif) as cumul_collectif")
            ->groupBy('distributeur_id')
            ->orderBy('new_date', 'ASC')
            //->limit(100)

            2272829


                                            @foreach ($products as $produit)
                                                @if ($produit->id == $items->products_id)
                                                    <td>{{ $produit->nom_produit }}</td>
                                                    <td>{{ number_format($produit->prix_product, 2,",",".") }}</td>
                                                    <td>
                                                    @foreach ($pvaleurs as $pv)
                                                        @if ($produit->pointvaleur_id == $pv->id)
                                                            {{ $pv->numbers }}
                                                        @endif
                                                    @endforeach
                                                    </td>
                                                @endif
                                            @endforeach




                                    <select class="js-states browser-default" name="id_parent" tabindex="-1" style="width: 100%" id="basic" class="validate">
                                        <optgroup label="Selectionnez l'ID de référence">

                                        @foreach($distributeurs as $id => $distributeur_id)
                                            <option value={{ $distributeur_id }}>{{ $distributeur_id }}</option>
                                        @endforeach

                                        </optgroup>
                                    </select>

                                    @if($errors->has('id_parent'))
                                        <span class="red-text accent-4">{{ $errors->first('id_parent') }}</span>
                                    @endif







                                @if($indirectInfos)

                                    <tr>
                                        <td>{{ $date }}</td>
                                        <td>{{ $indirectInfos['distributeur_id'] ?? ''}}</td>
                                        <td>{{ $indirectInfos['nom_distributeur'] ?? ''}} {{ $indirectInfos['pnom_distributeur'] ?? ''}}</td>
                                        <td>{{ $indirectInfos['etoiles'] ?? ''}}</td>
                                        <td>$ {{ $indirectInfos['new_cumul'] ?? '' }}</td>
                                        <td>$ {{ $indirectInfos['cumul_total'] ?? ''}}</td>
                                        <td>$ {{ $total_direct = $indirectInfos['bonusdirect'] ?? ''}}</td>
                                        <td>
                                            @php
                                                $total_indirect = $indirectInfos['bonusindirect'] ?? ''
                                            @endphp
                                            $ {{ $total_indirect }}
                                        </td>
                                        <td>$ {{ $bonus = $total_direct + $total_indirect}}</td>
                                        <td>
                                            @php
                                                $bonus = number_format(($bonus * 500), 2, ',', ' ');
                                            @endphp
                                                {{ $bonus }} xaf</td>
                                    </tr>

                                @endif
