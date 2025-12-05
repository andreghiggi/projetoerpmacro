@extends('layouts.app', ['title' => 'Consultar Produto por Código Único'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::select('produto_id', 'Pesquisar por produto')
                            ->options($produto != null ? [$produto->id => $produto->nome] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('codigo', 'Pesquisar por código')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('produto-consulta-codigo.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>Produto</th>
                                    <th>Código</th>
                                    <th>Observação</th>
                                    
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    <td>{{ $item->produto->nome }}</td>
                                    <td>{{ $item->codigo }}</td>
                                    <td>{{ $item->observacao }}</td>
                                    <td>
                                        <button type="button" class="btn btn-dark btn-sm" title="Ver" onclick="modalData('{{$item->id}}')">
                                            <i class="ri-file-list-3-line"></i>
                                        </button>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nada encontrado</td>
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
@include('modals._dados_produto_unico', ['not_submit' => true])

@endsection
@section('js')
<script src="/js/produto_unico.js"></script>

@endsection