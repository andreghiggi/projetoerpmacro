<div class="row">
    <div class="col-md-4">
        {!!Form::select('produto_id', 'Produto')
        ->attrs(['class' => 'form-select'])->required()
        ->options(isset($item) ? [$item->produto->id => $item->produto->nome] : [])
        ->disabled(isset($item) ? true : false)
        !!}
    </div>

    @if(isset($item) && __countLocalAtivo() > 1)
    <div class="row">
        <div class="col-md-8">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Local</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locais as $l)
                        <tr>
                            <td style="width: 60%;">
                                @if($l->local)

                                <select class="form-select" name="local_id[]">
                                    <option value="">Selecione</option>
                                    @foreach(__getLocaisAtivoUsuario() as $localAtivo)
                                    <option @if($l->local_id == $localAtivo->id) selected @endif value="{{ $localAtivo->id }}">{{ $localAtivo->descricao }}</option>
                                    @endforeach
                                </select>

                                <input type="hidden" readonly class="form-control" required name="local_anteior_id[]" value="{{ $l->local_id }}">
                                <!-- <input readonly class="form-control" required value="{{ $l->local->descricao }}"> -->

                                @else
                                <input type="hidden" readonly class="form-control" required name="local_id[]" value="{{ $firstLocation->id }}">
                                <input readonly class="form-control" required value="{{ $firstLocation->nome }}">
                                <input type="hidden" name="novo_estoque" value="1">
                                @endif
                            </td>
                            <td>
                                <input class="form-control @if($item->produto->unidadeDecimal()) quantidade @endif" @if(!$item->produto->unidadeDecimal()) value="{{ number_format($l->quantidade, 0) }}" @else value="{{ number_format($l->quantidade, 3) }}" @endif required name="quantidade[]" @if(!$item->produto->unidadeDecimal()) data-mask="000000" @endif>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
{{--    <div class="col-md-2">--}}
{{--        {!!Form::text('quantidade', 'Quantidade')--}}
{{--        ->attrs(['class' => 'quantidade'])--}}
{{--        ->attrs((isset($item) && (!$item->produto->unidadeDecimal())) ? ['data-mask' => '000000'] : ['class' => 'quantidade'])--}}
{{--        ->required()--}}
{{--        ->value(isset($item) ? ((!$item->produto->unidadeDecimal()) ? number_format($item->quantidade, 0) : number_format($item->quantidade, 3, '.', '')) : '')--}}
{{--        !!}--}}
{{--    </div>--}}

        @if($item->produto->variacoes && $item->produto->variacoes->count() > 0)
            <div class="col-md-12">
                <label for="">Variações</label>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Quantidade</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($item->produto->variacoes as $variacao)
{{--                            {{dd($item)}}--}}
                            <tr>
                                <td>{{ $variacao->descricao }}</td>
                                <td>
                                    <input type="hidden" name="variacao_id[]" value="{{ $variacao->id }}">
                                    <input class="form-control quantidade" name="quantidade_variacao[]" value="{{ number_format($variacao->estoque()->sum('quantidade'), 0, '', '') }}">

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="col-md-2">
                {!!Form::text('quantidade', 'Quantidade')
                ->attrs(['class' => 'quantidade'])
                ->required()
                ->value(isset($item) ? ((!$item->produto->unidadeDecimal()) ? number_format($item->quantidade, 0) : number_format($item->quantidade, 3, '.', '')) : '')
                !!}
            </div>
        @endif

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
    @endif

    <input name="produto_variacao_id" id="produto_variacao_id" type="hidden">
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
@include('modals._variacao')

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
