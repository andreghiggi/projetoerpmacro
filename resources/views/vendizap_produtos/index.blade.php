@extends('layouts.app', ['title' => 'Produtos VendiZap'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <a href="{{ route('produtos.create', ['vendizap=1']) }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Produto
                    </a>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('vendizap-produtos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                
                <div class="col-md-12 mt-3">
                    <p>Total de registros na pagina: <strong>{{ sizeof($data) }}</strong></p>
                    <p>Pagina: <strong>{{ $skip/$limite > 0 ? (int)($skip/$limite)+1 : 1 }}</strong></p>
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Código</th>
                                    <th>Data de cadastro</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    <td>{{ $item->id }}</td>
                                    <td style="width: 50%">{{ $item->descricao }}</td>
                                    <td>{{ __moeda($item->preco) }}</td>
                                    <td>
                                        @if($item->exibir)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td style="width: 10%">{{ $item->codigo }}</td>
                                    <td style="width: 10%">{{ $item->data_cadastro }}</td>
                                    <td>
                                        <div style="width: 150px">
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('vendizap-produtos.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <br>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-12">

                            <div class="d-flex justify-content-between">
                                <input type="hidden" name="skip" value="{{ $skip }}">

                                @if($skip >= $limite)
                                {!!Form::open()->fill(request()->all())
                                ->get()
                                !!}
                                <input type="hidden" name="skip" value="{{ $skip }}">
                                <input type="hidden" name="pagina_anterior" value="1">
                                <button class="btn btn-sm">Pagina anterior</button>
                                {!!Form::close()!!}
                                @else
                                <button class="btn btn-sm disabled">Pagina anterior</button>
                                @endif

                                @if(sizeof($data) == $limite)
                                {!!Form::open()->fill(request()->all())
                                ->get()
                                !!}
                                <input type="hidden" name="skip" value="{{ $skip }}">
                                <input type="hidden" name="proxima_pagina" value="1">
                                <button class="btn btn-sm">Próxima pagina</button>
                                {!!Form::close()!!}
                                @endif
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

