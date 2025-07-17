 // Liste des ID soumis pour l'établissement des bonus
        $tab = $request->bonuslist;
        $date = $request->created_at;

        // Initialisation du compteur
        $i=0;

        // On parcours la liste des ID soumis
        foreach($tab as $key => $value)
        {
            $direct[] = $this->bonusDirect($value, $date);
            $indirect[] = $this->bonusIndirect($value, $key);
            return $direct;
            //return $indirect;
            $children = $this->getSubdistribids($value);
            foreach($children as $lign)
            {
                $level = Level::where('distributeur_id', $lign)->get();
                if($level[0]->new_cumul != 0){
                    switch($level[0]->etoiles_id)
                    {
                        case 2:
                            $bonus = $level[0]->new_cumul * (6/100);
                            $finalbonus[] = $level[0]->new_cumul;
                            $distributeur[] = $level[0]->distributeur_id;
                        break;
                        default;
                    }
                }
                else {
                    $distributeur[] = $level[0]->distributeur_id;
                    $finalbonus[] = 0;
                    $i++;
                };
                $returned[] = $finalbonus;
                unset($finalbonus);
            }
            $list[] = array('count' => $distributeur,'bonus' => $returned);
            unset($returned);
            unset($distributeur);
            //$child[$key] = array('Child' => );
        }
        return $list;
        //return $child[0];







                                                <a href="{{ route('distrib.show', [$items->id, $items->distributeur_id]) }}" class="waves-effect waves-light"><i class="tiny material-icons">visibility</i></a>
