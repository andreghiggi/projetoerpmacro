@extends('layouts.app', ['title' => 'Retirada de Estoque'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Retirada de Estoque</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('estoque.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
        <hr>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('estoque-retirada.store')
        !!}
        <div class="pl-lg-4">
            <h4>Adicionar</h4>
            <div class="row g-2">
                <div class="col-md-3">
                    {!!Form::select('produto_id', 'Produto')
                    ->attrs(['class' => 'form-select'])->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('quantidade', 'Quantidade')
                    ->attrs(['class' => 'quantidade'])->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::select('motivo', 'Motivo', App\Models\RetiradaEstoque::motivos())
                    ->attrs(['class' => 'form-select'])->required()
                    !!}
                </div>

                <div class="col-md-3">
                    {!!Form::text('observacao', 'Observação')
                    !!}
                </div>

                @if(__countLocalAtivo() > 1)
                <div class="col-md-3">
                    <label for="">Local</label>
                    <select required class="select2" data-toggle="select2" name="local_id">
                        <option value="">Selecione</option>
                        @foreach(__getLocaisAtivoUsuario() as $local)
                        <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <input name="produto_variacao_id" id="produto_variacao_id" type="hidden">

                <div class="col-2 mt-4">
                    <button type="submit" class="btn btn-success px-5">Salvar</button>
                </div>
            </div>

            <hr>

        </div>
        {!!Form::close()!!}

        <div class="col-md-12 mt-3">

            <h4>Lista de Retiradas</h4>
            <div class="col-lg-12">

                {!!Form::open()->fill(request()->all())
                ->get()
                !!}
                <div class="row mt-3">
                    <div class="col-md-3">
                        {!!Form::text('produto', 'Pesquisar por produto')
                        !!}
                    </div>


                    @if(__countLocalAtivo() > 1)
                    <div class="col-md-2">
                        {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                        ->attrs(['class' => 'select2'])
                        !!}
                    </div>
                    @endif
                    <div class="col-md-3 text-left ">
                        <br>
                        <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                        <a id="clear-filter" class="btn btn-danger" href="{{ route('estoque.retirada') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                    </div>
                </div>
                {!!Form::close()!!}
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-centered">
                    <thead class="table-dark">
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Motivo</th>
                            <th>Observação</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ $item->produto->nome }}</td>
                            <td>{{ number_format($item->quantidade, 3, '.', '') }}</td>
                            <td>{{ $item->motivo }}</td>
                            <td>{{ $item->observacao }}</td>
                            <td>{{ __data_pt($item->created_at) }}</td>
                            <td>
                                <form action="{{ route('estoque-retirada.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                    @method('delete')
                                    @csrf

                                    @can('estoque_delete')
                                    <button type="button" class="btn btn-delete btn-sm btn-danger">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                    @endcan
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Nada encontrado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {!! $data->appends(request()->all())->links() !!}
        </div>
    </div>
</div>

@include('modals._variacao')

@endsection

@section('js')
<script type="text/javascript">
    $(function(){

        $('#produto_variacao_id').val('')
    })

    $(document).on("change", "#inp-produto_id", function () {
        $('#produto_variacao_id').val('')

        let product_id = $(this).val()
        $.get(path_url + "api/produtos/find", 
        { 
            produto_id: product_id,
            usuario_id: $('#usuario_id').val()
        })
        .done((e) => {

            let codigo_variacao = $(this).select2('data')[0].codigo_variacao
            if(e.variacao_modelo_id > 0 && !codigo_variacao){
                buscarVariacoes(product_id)
            }

            if(codigo_variacao > 0){
                $('#produto_variacao_id').val(codigo_variacao)
            }
        })
        .fail((err) => {
            console.log(err)
        })
    })

    function buscarVariacoes(produto_id){
        $.get(path_url + "api/variacoes/find", { produto_id: produto_id })
        .done((res) => {
            $('#modal_variacao .modal-body').html(res)
            $('#modal_variacao').modal('show')
        })
        .fail((err) => {
            console.log(err)
            swal("Algo deu errado", "Erro ao buscar variações", "error")
        })
    }

    function selecionarVariacao(id, descricao, valor){
        $('#produto_variacao_id').val(id)
        $('#modal_variacao').modal('hide')
    }
</script>
@endsection
