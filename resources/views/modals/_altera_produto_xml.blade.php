<div class="modal fade" id="modal_altera_produto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-md-2 mt-2">
                        <label>Código</label>
                        <input readonly class="form-control" type="text" id="modal_codigo">
                    </div>

                    <div class="col-md-4 mt-2">
                        <label>Nome</label>
                        <input class="form-control" type="text" id="modal_nome">
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Categoria</label>
                        <div class="input-group flexw-nowrap">
                            <select class="form-select" id="modal_categoria_id">
                                <option value="">Selecione</option>
                                @foreach($categorias as $c)
                                <option value="{{ $c->id }}">{{ $c->nome }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-dark btn-modal-categoria" type="button">
                                <i class="ri-add-circle-fill"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Subcategoria</label>
                        <select class="form-select" id="modal_sub_categoria_id">
                            <option value="">Selecione</option>
                        </select>
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Marca</label>
                        <div class="input-group flexw-nowrap">
                            <select class="form-select" id="modal_marca_id">
                                <option value="">Selecione</option>
                                @foreach($marcas as $c)
                                <option value="{{ $c->id }}">{{ $c->nome }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-dark btn-modal-marca" type="button">
                                <i class="ri-add-circle-fill"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Gerenciar estoque</label>
                        <select class="form-select" id="modal_gerenciar_estoque">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Código de barras</label>
                        <input class="form-control" type="text" id="modal_codigo_barras">
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Código de barras2</label>
                        <input class="form-control" type="text" id="modal_codigo_barras2">
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>NCM</label>
                        <input class="form-control ncm" type="text" id="modal_ncm">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Quantidade</label>
                        <input class="form-control moeda" type="tel" id="modal_quantidade">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Valor de compra</label>
                        <input class="form-control moeda" type="tel" id="modal_valor_compra">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Margem</label>
                        <input class="form-control percentual" type="tel" id="modal_margem">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Valor de venda</label>
                        <input class="form-control moeda" type="tel" id="modal_valor_venda">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Valor de atacado</label>
                        <input class="form-control moeda" type="tel" id="modal_valor_atacado">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Quantidade para atacado</label>
                        <input class="form-control" data-mask="000" type="tel" id="modal_quantidade_atacado">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Valor mínimo de venda</label>
                        <input class="form-control moeda" type="tel" id="modal_valor_minimo_venda">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Estoque mínimo</label>
                        <input class="form-control" type="tel" id="modal_estoque_minimo" data-mask="00000.00" data-mask-reverse="true">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Referência</label>
                        <input class="form-control" type="text" id="modal_referencia">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Referência balança</label>
                        <input class="form-control" type="text" id="modal_referencia_balanca">
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Unidade</label>
                        <select class="form-select" id="modal_unidade">
                            @foreach($unidades as $u)
                            <option value="{{ $u->nome }}">{{ $u->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mt-2">
                        <label>Exportar para Balança</label>
                        <select class="form-select" id="modal_exportar_balanca">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Obsevação</label>
                        <input class="form-control" type="text" id="modal_observacao">
                    </div>
                    <div class="col-md-3 mt-2">
                        <label>Obsevação 2</label>
                        <input class="form-control" type="text" id="modal_observacao2">
                    </div>
                    <div class="col-md-3 mt-2">
                        <label>Obsevação 3</label>
                        <input class="form-control" type="text" id="modal_observacao3">
                    </div>
                    <div class="col-md-3 mt-2">
                        <label>Obsevação 4</label>
                        <input class="form-control" type="text" id="modal_observacao4">
                    </div>

                    @if(__countLocalAtivo() > 1)
                    <div class="col-md-4 mt-2">
                        <label>Disponibilidade</label>

                        <select class="select2 form-control select2-multiple" data-toggle="select2" id="modal_disponibilidade" multiple="multiple">
                            @foreach(__getLocaisAtivoUsuario() as $local)
                            <option @if(in_array($local->id, (isset($item) ? $item->locais->pluck('localizacao_id')->toArray() : []))) selected @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else

                    <input type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
                    @endif

                    <div class="col-md-2 mt-2 d-none select-desvincular">
                        <label>Desvincular Produto</label>
                        <select class="form-select" id="modal_desvincular">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>

                    <div class="col-md-2 mt-2 form-check" style="margin-left: 10px">
                        <br>
                        <input type="checkbox" class="form-check-input ml-3" id="check">
                        <label>Revisado</label>
                    </div>
                    
                    <div class="mt-3 ms-auto">
                        <button type="submit" class="btn btn-success px-3 float-end btn-modal-alterar">Alterar</button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
