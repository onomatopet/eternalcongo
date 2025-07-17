<?php $i=1; $j=1; $k=1; ?>
@foreach($distribparents as $key=>$distribparent)
    <tr class="treegrid-{{ $i }} treegrid-parent-{{ $i }}" id="node-{{ $i }}-{{ $i}}-{{ $i++ }}">
        <td>{{ $distribparent->nom_distributeur.' '.$distribparent->pnom_distributeur }}</td>
        <td>ICI</td>
        <td></td>
    </tr>
    @if(!empty($distribparent->children) && $distribparent->children->count())      
        @include('layouts.distrib.recursive2',['distribparents' => $distribparent->children])
    @endif
@endforeach
