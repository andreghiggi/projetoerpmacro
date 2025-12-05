@extends('layouts.app', ['title' => 'Categorias Nuvem Shop'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <a href="{{ route('nuvem-shop-categorias.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Categoria
                    </a>
                </div>
                <hr class="mt-3">
                @php
                if(sizeof($data) > 0)
                $categoria = $data[0]->name->pt;
                @endphp
                
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Sub categoria</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name->pt }}</td>
                                    <td>
                                        @if($item->parent > 0)
                                        {{ $categoria }}
                                        @else
                                        --
                                        @endif
                                    </td>

                                    <td>
                                        <form action="{{ route('nuvem-shop-categorias.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('nuvem-shop-categorias.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                @php
                                if(!$item->parent)
                                $categoria = $item->name->pt;
                                @endphp
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <br>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

