@extends('layouts.index')

@section('content')

<main class="mn-inner">
    <div class="row">
        <div class="col s12">
            <div class="page-title"><h5>Categories des Produits</h5></div>
        </div>
        <div class="col s12 m12 l6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Liste des catégories</span>
                    <a href="{{ route('categories.create')}}" class="btn btn-sm btn-primary"><i class="material-icons left">add</i>Ajouter une Categorie</a><p></p>
                    <table class="bordered">
                        <thead>
                            <tr>
                                <th data-field="id">#</th>
                                <th data-field="name">Nom</th>
                                <th data-field="price">actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($categories)
                            @foreach($categories as $key => $category)
                                <tr>
                                    <td>{{ ++$key}}</td>
                                    <td>{{ $category->name ?? ''}}</td>
                                    <td>
                                        <a href="{{ route('categories.edit', $category->id) }}" class="waves-effect waves-light"><i class="tiny material-icons">edit</i></a>
                                         | 
                                        <a href="javascript:;" class="waves-effect waves-light" data-form-id="category-delete-{{ $category->id }}">
                                            <i class="tiny material-icons">delete</i></a>

                                        <form id="category-delete-{{ $category->id }}" action="{{ route('categories.destroy', $category->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')

                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


@endsection

