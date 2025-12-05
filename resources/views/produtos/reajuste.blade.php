@extends('layouts.app', ['title' => 'Reajuste de Produtos'])
@section('css')
<style type="text/css">
    .div-overflow {
        width: 180px;
        overflow-x: auto;
        white-space: nowrap;
    }
</style>
@endsection
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-1">
                        <div class="col-md-3">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        
                        <div class="col-md-2">
                            {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('marca_id', 'Marca', ['' => 'Selecione'] + $marcas->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-5">
                            {!!Form::select('cst_csosn', 'CST/CSOSN', ['' => 'Selecione'] + App\Models\Produto::listaCSTCSOSN())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('pendentes', 'Dados pendentes', ['' => 'Selecione', 1 => 'Sim', 0 => 'Não'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        @if(__countLocalAtivo() > 1)
                        <div class="col-md-2">
                            {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        @endif
                        
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('produtos.reajuste') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                            
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>

                @if(sizeof($data) > 0)
                <br>
                <form method="post" action="{{ route('produtos-reajuste.update') }}">
                    @csrf

                    <div class="row mt-2">
                        <div class="col-md-2">
                            {!!Form::tel('percentual_valor_venda', '% reajustar valor de venda')
                            ->attrs(['class' => ''])
                            !!}
                        </div>


                        <div class="col-md-2">
                            {!!Form::select('padrao_id', 'Padrão de tributação', ['' => 'Selecione'] + $padroes->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                    </div>
                    @endif

                    <div class="col-md-12 mt-3 table-responsive">
                        <h6>Total de registros: <strong>{{ sizeof($data) }}</strong></h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-centered mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Produto</th>
                                        @if(__countLocalAtivo() > 1)
                                        <th>Disponibilidade</th>
                                        @endif
                                        <th>Categoria</th> 
                                        <th>Valor de venda</th> 
                                        <th>Valor de compra</th> 
                                        <th>CST/CSOSN</th> 
                                        <th>CST PIS</th> 
                                        <th>CST COFINS</th> 
                                        <th>CST IPI</th> 
                                        <th>% ICMS</th> 
                                        <th>% PIS</th> 
                                        <th>% COFINS</th> 
                                        <th>% IPI</th> 
                                        <th>% RED. BC</th> 
                                        <th>CFOP Saída estadual</th> 
                                        <th>CFOP Saída outro estado</th>
                                        <th>CFOP Entrada estadual</th> 
                                        <th>CFOP Entrada outro estado</th>

                                        <th>CST IBS/CBS</th>
                                        <th>Classificação Tributária</th>
                                        <th>% IBS UF</th>
                                        <th>% IBS Municipal</th>
                                        <th>% CBS</th>
                                        <th>% Diferido</th>

                                        <th>Código benefício</th>
                                        <th>Modalidade BC-ST</th>
                                        <th>% ICMS ST</th>
                                        <th>% MVA ST</th>
                                        <th>% Red BC ST</th>
                                        <th>% Efetivo do ICMS</th>
                                        <th>% Redução Efetivo do ICMS</th>

                                        <th>Ativo</th>  
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                    <tr>
                                        <td>
                                            <label style="width: 300px;">{{ $item->nome }}</label>
                                        </td>
                                        @if(__countLocalAtivo() > 1)
                                        <td>

                                            <select required class="select2 form-control select2-multiple local" data-toggle="select2" name="locais[]" multiple="multiple">
                                                @foreach(__getLocaisAtivoUsuario() as $local)
                                                <option @if(in_array($local->id, (isset($item) ? $item->locais->pluck('localizacao_id')->toArray() : []))) selected @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 300px"></div>
                                            @if($loop->first)
                                            <a onclick="setLocal()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            <select name="categoria[]" class="select2 categoria">
                                                <option value="">Selecione</option>
                                                @foreach($categorias as $c)
                                                <option @isset($item) @if($item->categoria_id == $c->id) selected @endif @endif value="{{ $c->id}}">{{ $c->nome }}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 200px"></div>

                                            @if($loop->first)
                                            <a onclick="setCategoria()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="hidden" name="produto_id[]" value="{{ $item->id }}">
                                            <input type="hidden" class="valor_venda" value="{{ $item->valor_unitario }}">
                                            <input required style="width: 150px" type="tel" class="form-control moeda valor_venda" name="valor_unitario[]" value="{{ __moeda($item->valor_unitario) }}">
                                            @if($loop->first)
                                            <a onclick="setValorVenda()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control moeda valor_compra" name="valor_compra[]" value="{{ __moeda($item->valor_compra) }}">
                                            @if($loop->first)
                                            <a onclick="setValorCompra()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <select required class="select2 cst_csosn" name="cst_csosn[]" style="width: 450px">
                                                @foreach(App\Models\Produto::listaCSTCSOSN() as $key => $v)
                                                <option @if($key == $item->cst_csosn) selected @endif value="{{ $key }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            @if($loop->first)
                                            <a onclick="setCstCsosn()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif

                                            <div style="width: 400px;"></div>
                                        </td>

                                        <td>
                                            <select required class="select2 cst_pis" name="cst_pis[]">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $v)
                                                <option @if($key == $item->cst_pis) selected @endif value="{{ $key }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            @if($loop->first)
                                            <a onclick="setCstPis()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                            <div style="width: 400px;"></div>
                                        </td>

                                        <td>
                                            <select required class="select2 cst_cofins" name="cst_cofins[]">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $v)
                                                <option @if($key == $item->cst_cofins) selected @endif value="{{ $key }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            @if($loop->first)
                                            <a onclick="setCstCofins()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                            <div style="width: 400px;"></div>
                                        </td>

                                        <td>
                                            <select required class="select2 cst_ipi" name="cst_ipi[]">
                                                @foreach(App\Models\Produto::listaCST_IPI() as $key => $v)
                                                <option @if($key == $item->cst_ipi) selected @endif value="{{ $key }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            @if($loop->first)
                                            <a onclick="setCstIpi()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                            <div style="width: 400px;"></div>
                                        </td>

                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual perc_icms" name="perc_icms[]" value="{{ $item->perc_icms }}">
                                            @if($loop->first)
                                            <a onclick="setPercIcms()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual perc_pis" name="perc_pis[]" value="{{ $item->perc_pis }}">
                                            @if($loop->first)
                                            <a onclick="setPercPis()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual perc_cofins" name="perc_cofins[]" value="{{ $item->perc_cofins }}">
                                            @if($loop->first)
                                            <a onclick="setPercCofins()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual perc_ipi" name="perc_ipi[]" value="{{ $item->perc_ipi }}">
                                            @if($loop->first)
                                            <a onclick="setPercIpi()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual perc_red_bc" name="perc_red_bc[]" value="{{ $item->perc_red_bc }}">
                                            @if($loop->first)
                                            <a onclick="setPercRedBc()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control cfop cfop_saida_estadual" name="cfop_estadual[]" value="{{ $item->cfop_estadual }}">
                                            @if($loop->first)
                                            <a onclick="setCfopSaidaEstadual()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control cfop cfop_saida_outro_estado" name="cfop_outro_estado[]" value="{{ $item->cfop_outro_estado }}">
                                            @if($loop->first)
                                            <a onclick="setCfopSaidaOutroEstado()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control cfop cfop_entrada_estadual" name="cfop_entrada_estadual[]" value="{{ $item->cfop_entrada_estadual }}">
                                            @if($loop->first)
                                            <a onclick="setCfopEntradaEstadual()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control cfop cfop_entrada_outro_estado" name="cfop_entrada_outro_estado[]" value="{{ $item->cfop_entrada_outro_estado }}">
                                            @if($loop->first)
                                            <a onclick="setCfopEntradaOutroEstado()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <select class="select2 cst_ibscbs" name="cst_ibscbs[]">
                                                @foreach(App\Models\Produto::listaCSTCbsIbs() as $key => $v)
                                                <option @if($key == $item->cst_ibscbs) selected @endif value="{{ $key }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            @if($loop->first)
                                            <a onclick="setCstIbsCbs()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                            <div style="width: 400px;"></div>
                                        </td>

                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control cclass_trib" name="cclass_trib[]" value="{{ $item->cclass_trib }}">
                                            @if($loop->first)
                                            <a onclick="setclassTrib()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual perc_ibs_uf" name="perc_ibs_uf[]" value="{{ $item->perc_ibs_uf }}">
                                            @if($loop->first)
                                            <a onclick="setPercIbsUf()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual perc_ibs_mun" name="perc_ibs_mun[]" value="{{ $item->perc_ibs_mun }}">
                                            @if($loop->first)
                                            <a onclick="setPercIbsMun()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual perc_cbs" name="perc_cbs[]" value="{{ $item->perc_cbs }}">
                                            @if($loop->first)
                                            <a onclick="setPercCbs()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual perc_dif" name="perc_dif[]" value="{{ $item->perc_dif }}">
                                            @if($loop->first)
                                            <a onclick="setPercDif()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control codigo_beneficio_fiscal" name="codigo_beneficio_fiscal[]" value="{{ $item->codigo_beneficio_fiscal }}">
                                            @if($loop->first)
                                            <a onclick="setCodigoBenf()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <select class="select2 modBCST" name="modBCST[]">
                                                @foreach(App\Models\Produto::modalidadesBCST() as $key => $v)
                                                <option @if($key == $item->modBCST) selected @endif value="{{ $key }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            @if($loop->first)
                                            <a onclick="setModBCST()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif

                                            <div style="width: 250px;"></div>

                                        </td>
                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual pICMSST" name="pICMSST[]" value="{{ $item->pICMSST }}">
                                            @if($loop->first)
                                            <a onclick="setPICMSST()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual pMVAST" name="pMVAST[]" value="{{ $item->pMVAST }}">
                                            @if($loop->first)
                                            <a onclick="setPMVAST()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual redBCST" name="redBCST[]" value="{{ $item->redBCST }}">
                                            @if($loop->first)
                                            <a onclick="setRedBCST()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual pICMSEfet" name="pICMSEfet[]" value="{{ $item->pICMSEfet }}">
                                            @if($loop->first)
                                            <a onclick="setPICMSEfet()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual pRedBCEfet" name="pRedBCEfet[]" value="{{ $item->pRedBCEfet }}">
                                            @if($loop->first)
                                            <a onclick="setPRedBCEfet()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <select class="form-select status" name="status[]">
                                                <option @if($item->status == 1) selected @endif value="1">Sim</option>
                                                <option @if($item->status == 0) selected @endif value="0">Não</option>
                                            </select>
                                            @if($loop->first)
                                            <a onclick="setStatus()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                            <div style="width: 200px;"></div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="20" class="text-center">Filtre para buscar os produtos</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>


                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success float-end mt-3">Salvar</button>
                    </div>

                    <br>
                </form>

            </div>

        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $('body').on('change', '#inp-padrao_id', function () {
        let padrao = $(this).val()
        if (padrao) {
            $.get(path_url + "api/produtos/padrao", {
                padrao: padrao
            })
            .done((result) => {
                console.log(result)

                // var newOption = new Option(result._ncm.descricao, result._ncm.codigo, 1, false);
                // $('.ncm').append(newOption);
                if(result._ncm){
                    $('.ncm').val(result._ncm.codigo)
                }
                $('.cest').val(result.cest)
                $('.perc_icms').val(result.perc_icms)
                $('.perc_pis').val(result.perc_pis)
                $('.perc_cofins').val(result.perc_cofins)
                $('.perc_ipi').val(result.perc_ipi)
                $('.cst_csosn').val(result.cst_csosn).change()
                $('.cst_pis').val(result.cst_pis).change()
                $('.cst_cofins').val(result.cst_cofins).change()
                $('.cst_ipi').val(result.cst_ipi).change()
                $('.cEnq').val(result.cEnq).change()
                $('.cfop_saida_estadual').val(result.cfop_estadual)
                $('.cfop_saida_outro_estado').val(result.cfop_outro_estado)

                $('.cfop_entrada_estadual').val(result.cfop_entrada_estadual)
                $('.cfop_entrada_outro_estado').val(result.cfop_entrada_outro_estado)
                $('.codigo_beneficio_fiscal').val(result.codigo_beneficio_fiscal)

                $('.codigo_beneficio_fiscal').val(result.codigo_beneficio_fiscal)
                $('.modBCST').val(result.modBCST).change()
                $('.pICMSST').val(result.pICMSST)
                $('.pMVAST').val(result.pMVAST)
            })
            .fail((err) => {
                console.log(err)
            })
        }
    });

    $('body').on('blur', '#inp-percentual_valor_venda', function () {

        let percentual = $(this).val()
        $('.valor_venda').each(function (e, x) {
            $vInp = $(this).next()
            let v = parseFloat($(this).val())
            let nv = v + (v*(percentual/100))
            console.log(nv)

            $vInp.val(convertFloatToMoeda(nv))

        })
    })

    $("#inp-percentual_valor_venda").mask("Z999.00", {
        translation: {
            '0': {pattern: /\d/},
            '9': {pattern: /\d/, optional: true},
            'Z': {pattern: /[\-\+]/, optional: true}
        }

    });

    function setValorVenda(){
        let v = $('.valor_venda').first().val()
        $('.valor_venda').val(v)
    }

    function setValorCompra(){
        let v = $('.valor_compra').first().val()
        $('.valor_compra').val(v)
    }
    function setPercIcms(){
        let v = $('.perc_icms').first().val()
        $('.perc_icms').val(v)
    }
    function setPercPis(){
        let v = $('.perc_pis').first().val()
        $('.perc_pis').val(v)
    }
    function setPercCofins(){
        let v = $('.perc_cofins').first().val()
        $('.perc_cofins').val(v)
    }
    function setPercIpi(){
        let v = $('.perc_ipi').first().val()
        $('.perc_ipi').val(v)
    }
    function setPercRedBc(){
        let v = $('.perc_red_bc').first().val()
        $('.perc_red_bc').val(v)
    }
    function setCfopSaidaEstadual(){
        let v = $('.cfop_saida_estadual').first().val()
        $('.cfop_saida_estadual').val(v)
    }
    function setCfopSaidaOutroEstado(){
        let v = $('.cfop_saida_outro_estado').first().val()
        $('.cfop_saida_outro_estado').val(v)
    }
    function setCfopEntradaEstadual(){
        let v = $('.cfop_entrada_estadual').first().val()
        $('.cfop_entrada_estadual').val(v)
    }

    function setCfopEntradaOutroEstado(){
        let v = $('.cfop_entrada_outro_estado').first().val()
        $('.cfop_entrada_outro_estado').val(v)
    }

    function setCstIbsCbs(){
        let v = $('.cst_ibscbs').first().val()
        $('.cst_ibscbs').val(v)
    }
    function setclassTrib(){
        let v = $('.cclass_trib').first().val()
        $('.cclass_trib').val(v)
    }
    function setPercIbsUf(){
        let v = $('.perc_ibs_uf').first().val()
        $('.perc_ibs_uf').val(v)
    }
    function setPercIbsMun(){
        let v = $('.perc_ibs_mun').first().val()
        $('.perc_ibs_mun').val(v)
    }
    function setPercCbs(){
        let v = $('.perc_cbs').first().val()
        $('.perc_cbs').val(v)
    }
    function setPercDif(){
        let v = $('.perc_dif').first().val()
        $('.perc_dif').val(v)
    }

    function setCstCsosn(){
        let v = $('.cst_csosn').first().val()
        $('.cst_csosn').val(v).change()
    }

    function setCstCsosn(){
        let v = $('.cst_csosn').first().val()
        $('.cst_csosn').val(v).change()
    }
    function setCstPis(){
        let v = $('.cst_pis').first().val()
        $('.cst_pis').val(v).change()
    }
    function setCstCofins(){
        let v = $('.cst_cofins').first().val()
        $('.cst_cofins').val(v).change()
    }
    function setCstIpi(){
        let v = $('.cst_ipi').first().val()
        $('.cst_ipi').val(v).change()
    }
    function setStatus(){
        let v = $('.status').first().val()
        $('.status').val(v).change()
    }
    function setCategoria(){
        let v = $('.categoria').first().val()
        $('.categoria').val(v).change()
    }
    function setLocal(){
        let v = $('.local').first().val()
        $('.local').val(v).change()
    }

    function setCodigoBenf(){
        let v = $('.codigo_beneficio_fiscal').first().val()
        $('.codigo_beneficio_fiscal').val(v)
    }
    function setModBCST(){
        let v = $('.modBCST').first().val()
        $('.modBCST').val(v).change()
    }
    function setPICMSST(){
        let v = $('.pICMSST').first().val()
        $('.pICMSST').val(v)
    }
    function setPMVAST(){
        let v = $('.pMVAST').first().val()
        $('.pMVAST').val(v)
    }

    function setRedBCST(){
        let v = $('.redBCST').first().val()
        $('.redBCST').val(v)
    }
    function setPICMSEfet(){
        let v = $('.pICMSEfet').first().val()
        $('.pICMSEfet').val(v)
    }
    function setPRedBCEfet(){
        let v = $('.pRedBCEfet').first().val()
        $('.pRedBCEfet').val(v)
    }
</script>
@endsection

