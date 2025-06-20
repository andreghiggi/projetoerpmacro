@extends('layouts.app', ['title' => 'Editar Lista'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Produtos Lista - <strong class="text-success">{{ $item->nome }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('lista-preco.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="col-lg-12">
            {!!Form::open()->fill(request()->all())
            ->get()
            !!}
            <div class="row mt-3">
                <div class="col-md-4">
                    {!!Form::text('nome', 'Pesquisar por nome')
                    !!}
                </div>
                <div class="col-md-3 text-left">
                    <br>
                    <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                    <a id="clear-filter" class="btn btn-danger" href="{{ route('lista-preco.show', [$item->id]) }}"><i class="ri-eraser-fill"></i>Limpar</a>
                </div>
            </div>
            {!!Form::close()!!}
        </div>

        <div class="col-md-12 mt-3">
            <div class="table-responsive-sm">
                <table class="table table-striped table-centered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50%">Produto</th>
                            <th>Valor normal do produto</th>
                            <th>Valor da lista</th>
                            <th width="10%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produtos as $p)
                        <tr>
                            <td>{{ $p->produto->nome }}</td>
                            <td>{{ __moeda($p->produto->valor_unitario) }}</td>
                            <td>{{ __moeda($p->valor) }}</td>
                            <td>
                                <button onclick="editValor('{{ $p->id }}', '{{ $p->produto->nome }}', '{{ $p->valor }}')" class="btn btn-warning btn-sm text-white">
                                    <i class="ri-pencil-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Nada encontrado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        {!! $produtos->appends(request()->all())->links() !!}

    </div>
</div>

@include('modals._editar_valor_lista')

@endsection

@section('js')
<script type="text/javascript">
    function editValor(id, nome, valor){
        $('#editar_valor_lista').modal('show')
        $('#produto-nome').text(nome)
        $('#item_id').val(id)
        $('#inp-valor').val(convertFloatToMoeda(valor))
    }
</script>
@endsection
