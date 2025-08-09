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

        @if(isset($item) && $item->produto->variacoes && $item->produto->variacoes->count() > 0)
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
            <div class="col-md-2">
                {!!Form::select('variavel', 'Com variações', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])
                ->value((isset($item) && $item->variacao_modelo_id != null) ? 1 : 0)
                !!}
            </div>
            <div class="row g-2 m-2">
                <div class="col-12 div-variavel d-none">
                    <div class="table-responsive">
                        <table class="table table-dynamic">
                            <thead class="table-dark">
                            <tr>
                                <th>Variação</th>
                                <th>Valores da variação</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td width="250px">
                                    <div class="mt-1">
                                        {!!Form::select('variacao_modelo_id', 'Variação principal', ['' => 'Selecione'] + $variacoes->pluck('descricao', 'id')->all())
                                        ->attrs(['class' => 'form-select'])
                                        ->value(isset($item) ? $item->variacao_modelo_id : null)
                                        !!}
                                    </div>

                                    <div class="mt-2">
                                        {!!Form::select('sub_variacao_modelo_id', 'Sub variação', ['' => 'Selecione'] + $variacoes->pluck('descricao', 'id')->all())
                                        ->attrs(['class' => 'form-select'])
                                        ->value(isset($item) ? $item->variacao_modelo_id : null)
                                        !!}
                                    </div>
                                </td>
                                <td>
                                    <div class="row">
                                        <table class="table table-dynamic table-variacao">
                                            <thead class="table-success">
                                            <tr>
                                                <th>Descrição</th>
                                                <th>Valor</th>
                                                <th>Código de barras</th>
                                                <th>Referência</th>
                                                <th>Estoque</th>
                                                <th>Imagem</th>
                                                <th>

                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                    <tr class="dynamic-form">
                                                        <input type="hidden" name="variacao_id[]]]">
                                                        <td>
                                                            <input type="text" class="form-control" name="descricao_variacao[]" required readonly>
                                                        </td>
                                                        <td>
                                                            <input type="tel" class="form-control moeda" name="valor_venda_variacao[]" required>
                                                        </td>

                                                        <td>
                                                            <input type="tel" class="form-control ignore" name="codigo_barras_variacao[]">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control ignore" name="referencia_variacao[]" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control ignore" name="estoque_variacao[]">
                                                        </td>
                                                        <td>
                                                            <input class="ignore" accept="image/*" type="file" class="form-control" name="imagem_variacao[]" value="">
                                                            <img class="image-variation"><br>
                                                            <span>imagem atual</span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger btn-remove-tr-variacao">
                                                                <i class="ri-subtract-line"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row col-12 col-lg-3 mt-3">
                                        <button type="button" class="btn btn-dark btn-add-tr-variacao">
                                            <i class="ri-add-fill"></i>
                                            Adicionar linha
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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

    function changeVariavel() {
        let variavel = $('#inp-variavel').val()
        if (variavel == 1) {
            $('.div-variavel').removeClass('d-none')
            $('#inp-valor_unitario').val('0')
            //$('#inp-valor_compra').val('0')
        } else {
            $('.div-variavel').addClass('d-none')
        }
    }

    $('#inp-variavel').change(() => {
        changeVariavel()
    })

    $(document).on("change", "#inp-sub_variacao_modelo_id", function () {

        let sub_variacao_modelo_id = $(this).val()
        let variacao_modelo_id = $('#inp-variacao_modelo_id').val()
        if(!sub_variacao_modelo_id) return;
        if(variacao_modelo_id){
            $.get(path_url + "api/variacoes/modelo-subvariacoes", {
                variacao_modelo_id: variacao_modelo_id,
                sub_variacao_modelo_id: sub_variacao_modelo_id
            })
                .done((res) => {
                    $('.table-variacao tbody').html(res)
                })
                .fail((err) => {
                    console.log(err)
                    swal("Erro", "Algo deu errado", "error")
                })
        }else{
            swal("Erro", "Selecione a variação principal primeiro!", "error")
        }
    })

    $(document).on("change", "#inp-variacao_modelo_id", function () {

        let variacao_modelo_id = $(this).val()
        if(variacao_modelo_id){
            $.get(path_url + "api/variacoes/modelo", {
                variacao_modelo_id: variacao_modelo_id
            })
                .done((res) => {
                    $('.table-variacao tbody').html(res)
                    $('#inp-sub_variacao_modelo_id').val('').change()
                })
                .fail((err) => {
                    console.log(err)
                    swal("Erro", "Algo deu errado", "error")
                })
        }
    })

    if($('.table-variacao tbody tr').length == 0){
        $('#inp-variacao_modelo_id').val('').change()
        $('#inp-sub_variacao_modelo_id').val('').change()
    }

    $(document).delegate(".btn-remove-tr-variacao", "click", function (e) {
        e.preventDefault();
        swal({
            title: "Você esta certo?",
            text: "Deseja remover esse item mesmo?",
            icon: "warning",
            buttons: true
        }).then(willDelete => {
            if (willDelete) {
                var trLength = $(this)
                    .closest("tr")
                    .closest("tbody")
                    .find("tr")
                    .not(".dynamic-form-document").length;
                if (!trLength || trLength > 1) {
                    $(this)
                        .closest("tr")
                        .remove();
                } else {
                    swal("Atenção", "Você deve ter ao menos um item na lista", "warning");
                }
            }
        });
    });

    $('.btn-add-tr-variacao').on("click", function () {
        console.clear()
        var $table = $(this)
            .closest(".row")
            .prev()
            .find(".table-variacao");

        console.log($table)

        var hasEmpty = false;

        $table.find("input, select").each(function () {
            if (($(this).val() == "" || $(this).val() == null) && $(this).attr("type") != "hidden" && $(this).attr("type") != "file" && !$(this).hasClass("ignore")) {
                hasEmpty = true;
            }
        });

        if (hasEmpty) {
            swal(
                "Atenção",
                "Preencha todos os campos antes de adicionar novos.",
                "warning"
            );
            return;
        }
        // $table.find("select.select2").select2("destroy");
        var $tr = $table.find(".dynamic-form").first();
        $tr.find("select.select2").select2("destroy");
        var $clone = $tr.clone();
        $clone.show();

        $clone.find("input,select").val("");
        $clone.find("input,select").removeAttr('readonly');
        $table.append($clone);
        setTimeout(function () {
            $("tbody select.select2").select2({
                language: "pt-BR",
                width: "100%",
                theme: "bootstrap4"
            });
        }, 100);
    })
    document.addEventListener('DOMContentLoaded', function () {
        const pesoInputs = document.querySelectorAll('.peso');

        pesoInputs.forEach(input => {
            input.addEventListener('input', function () {
                let raw = this.value.replace(/\D/g, ''); // remove tudo que não for dígito

                // limita a 6 dígitos (ex: 999999 => 999.999 kg)
                if (raw.length > 6) raw = raw.slice(0, 6);

                // se estiver vazio, assume 0
                let valor = (parseInt(raw || 0) / 1000).toFixed(3);

                this.value = valor;
            });

            input.addEventListener('focus', function () {
                // remove qualquer texto não numérico ao focar
                this.value = this.value.replace(/[^\d]/g, '');
            });

            input.addEventListener('blur', function () {
                let raw = this.value.replace(/\D/g, '');
                let valor = (parseInt(raw || 0) / 1000).toFixed(3);
                this.value = valor;
            });
        });
    });
</script>
@endsection
