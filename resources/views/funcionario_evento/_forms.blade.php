<div class="row">
    @if(!isset($item))
    <div class="col-md-4 col-12">
        {!! Form::select('funcionario_id', 'Funcionario', ['' => 'Selecione'] + $funcionarios->pluck('nome', 'id')->all())->attrs(['class' => 'form-select']) !!}
    </div>
    @endif
    <div class="card border mt-3">
        <div class="row">
            <div class="table-responsive mt-2">
                <table class="table table-dynamic">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Evento</th>
                            <th>Condição</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Ativo</th>
                        </tr>
                    </thead>
                    <tbody id="body" class="datatable-body">
                        @isset($item)
                        @foreach($item->eventos as $ev)
                        <tr class="dynamic-form">
                            <td>
                                <span class="codigo" id="id">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </span>
                            </td>
                            <td>
                                <select required name="evento[]" class="form-select evento">
                                    <option value="">Selecione</option>
                                    @foreach($eventos as $e)
                                    <option @if($e->id == $ev->evento_id) selected @endif value="{{$e->id}}" data-condicao="{{ $e->condicao }}"
                                        data-metodo="{{ $e->metodo }}">{{$e->nome}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select required name="condicao[]" class="form-select condicao_chave" readonly>
                                    <option value="">Selecione</option>
                                    <option @if($ev->condicao == "soma") selected @endif value="soma">Soma</option>
                                    <option @if($ev->condicao == "diminui") selected @endif value="diminui">Diminui</option>
                                </select>
                            </td>
                            <td>
                                <input value="{{ __moeda($ev->valor) }}" required type="tel" name="valor[]" class="form-control moeda">
                            </td>
                            <td>
                                <select required name="metodo[]" class="form-select metodo">
                                    <option value="">Selecione</option>
                                    <option @if($ev->metodo == "informado") selected @endif value="informado">Informado</option>
                                    <option @if($ev->metodo == "fixo") selected @endif value="fixo">Fixo</option>
                                </select>
                            </td>
                            <td>
                                <span class="codigo">
                                    <select required name="ativo[]" class="form-select ativo">
                                        <option @if($ev->ativo == 1) selected @endif value="1">Sim</option>
                                        <option @if($ev->ativo == 0) selected @endif value="0">Não</option>
                                    </select>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr class="datatable-row dynamic-form">
                            <td>
                                <span class="codigo" id="id">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </span>
                            </td>
                            <td>
                                <span class="codigo">
                                    <select required name="evento[]" class="form-select evento">
                                        <option value="">Selecione</option>
                                        @foreach($eventos as $e)
                                        <option value="{{$e->id}}" data-condicao="{{ $e->condicao }}" data-metodo="{{ $e->metodo }}">{{$e->nome}}
                                        </option>
                                        @endforeach
                                    </select>
                                </span>
                            </td>
                            <td>
                                <span class="codigo" id="id">
                                    <select required name="condicao[]" class="form-select condicao_chave" readonly>
                                        <option value="">Selecione</option>
                                        <option value="soma">Soma</option>
                                        <option value="diminui">Diminui</option>
                                    </select>
                                </span>
                            </td>
                            <td>
                                <span class="codigo">
                                    <input required type="tel" name="valor[]" class="form-control moeda">
                                </span>
                            </td>
                            <td>
                                <span class="codigo" id="id">
                                    <select required name="metodo[]" class="form-select metodo">
                                        <option value="">Selecione</option>
                                        <option value="informado">Informado</option>
                                        <option value="fixo">Fixo</option>
                                    </select>
                                </span>
                            </td>
                            <td>
                                <span class="codigo">
                                    <select required name="ativo[]" class="form-select ativo">
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </span>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-success btn-add m-2">
                    <i class="ri-play-list-add-line"></i> Adicionar linha
                </button>
            </div>
        </div>
    </div>
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5">Salvar</button>
    </div>
</div>


@section('js')
<script type="text/javascript">
    $('body').on('change', '.evento', function() {
        let value = $(this).val()
        if (value) {
            const condicao = ($('option:selected', this).attr('data-condicao'));
            const metodo = ($('option:selected', this).attr('data-metodo'));
            $(this).closest('tr').find('.condicao_chave').val(condicao)
            $(this).closest('tr').find('.condicao_chave').addClass('select-disabled')
            $(this).closest('tr').find('.metodo').val(metodo)
            $(this).closest('tr').find('.metodo').addClass('select-disabled')
        }
    })
    $(".btn-add").on("click", function() {
        var $table = $(this)
            .closest(".row")
            .find(".table-dynamic");
        console.clear()
        var hasEmpty = false;
        $table.find("input, select").each(function() {
            console.log("val", $(this).val())
            if (($(this).val() == "" || $(this).val() == null)) {
                hasEmpty = true;
            }
        });
        if (hasEmpty) {
            swal(
                "Atenção"
                , "Preencha todos os campos antes de adicionar novos."
                , "warning"
            );
            return;
        }
        console.log($table)
        var $tr = $table.find(".dynamic-form").first();
        console.log($tr)
        var $clone = $tr.clone();
        $clone.show();
        $clone.find("input,select").val("");
        $clone.find(".ativo").val("1");
        $clone.find(".moeda").mask('000000000000000,00', {
            reverse: true
        });
        $table.append($clone);
    });
    $(document).delegate(".btn-remove", "click", function(e) {
        e.preventDefault();
        swal({
            title: "Você esta certo?"
            , text: "Deseja remover esse item mesmo?"
            , icon: "warning"
            , buttons: true
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
                    swal(
                        "Atenção"
                        , "Você deve ter ao menos um item na lista"
                        , "warning"
                    );
                }
            }
        })
    })
</script>
@endsection