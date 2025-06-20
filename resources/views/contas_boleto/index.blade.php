@extends('layouts.app', ['title' => 'Contas para Boleto'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('contas_boleto_create')
                    <a href="{{ route('contas-boleto.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Conta
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::select('banco', 'Pesquisar por banco', \App\Models\ContaBoleto::bancos())
                            ->value($banco ? $banco : null)
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('contas-boleto.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>Banco</th>
                                    <th>Agência</th>
                                    <th>Conta</th>
                                    <th>Tipo</th>
                                    <th>Documento</th>
                                    <th>Padrão</th>
                                    
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->banco }}</td>
                                    <td>{{ $item->agencia }}</td>
                                    <td>{{ $item->conta }}</td>
                                    <td>{{ $item->tipo }}</td>
                                    <td>{{ $item->documento }}</td>
                                    <td>
                                        @if($item->padrao)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('contas-boleto.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('contas_boleto_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('contas-boleto.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf

                                            @can('contas_boleto_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
