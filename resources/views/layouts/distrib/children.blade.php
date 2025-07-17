@if ($distributeur->childrenRecursive->isNotEmpty())
    <ul>
        @foreach ($distributeur->childrenRecursive as $child)
            <li>
                Distributeur ID: {{ $child->distributeur_id }}
                @include('distributeurs.partials.children', ['distributeur' => $child])
            </li>
        @endforeach
    </ul>
@endif
