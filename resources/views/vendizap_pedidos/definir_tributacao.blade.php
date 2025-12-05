@extends('layouts.app', ['title' => 'Tributação de Produtos'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Tributação Produtos</h4>
                <p class="text-danger">* Produtos com tributação indefinida, informe para continuar</p>

                <div class="col-md-3">
                    {!!Form::select('padrao_id', 'Padrão de tributação', ['' => 'Selecione'] + $padroes->pluck('descricao', 'id')->all())
                    ->attrs(['class' => 'form-select'])
                    ->value(isset($item) ? $item->padrao_id : ($padraoTributacao != null ? $padraoTributacao->id : ''))
                    !!}
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    <form method="post" action="{{ route('vendizap-pedidos.update-tributacao', [$item->id]) }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Produto</th>
                                        <th>CST/CSOSN</th> 
                                        <th>CST PIS</th> 
                                        <th>CST COFINS</th> 
                                        <th>CST IPI</th>
                                        <th>NCM</th>
                                        <th>CEST</th>
                                        <th>% ICMS</th> 
                                        <th>% PIS</th> 
                                        <th>% COFINS</th> 
                                        <th>% IPI</th> 
                                        <th>% RED. BC</th>
                                        <th>CFOP Saída estadual</th>
                                        <th>CFOP Saída outro estado</th>
                                        <th>CFOP Entrada estadual</th>
                                        <th>CFOP Entrada outro estado</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->itens as $i)
                                    <tr>
                                        <td>
                                            {{ $i->produto->nome }}
                                            <div style="width: 300px;"></div>
                                        </td>
                                        <input type="hidden" name="produto_id[]" value="{{ $i->produto_id }}">
                                        <td>
                                            <select required class="select2 cst_csosn" name="cst_csosn[]" style="width: 450px">
                                                @foreach(App\Models\Produto::listaCSTCSOSN() as $key => $v)
                                                <option @if($key == $i->produto->cst_csosn) selected @endif value="{{ $key }}">{{ $v }}</option>
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
                                                <option @if($key == $i->produto->cst_pis) selected @endif value="{{ $key }}">{{ $v }}</option>
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
                                                <option @if($key == $i->produto->cst_cofins) selected @endif value="{{ $key }}">{{ $v }}</option>
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
                                                <option @if($key == $i->produto->cst_ipi) selected @endif value="{{ $key }}">{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            @if($loop->first)
                                            <a onclick="setCstIpi()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                            <div style="width: 400px;"></div>
                                        </td>

                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual ncm" name="ncm[]" value="{{ $i->produto->ncm }}">
                                            @if($loop->first)
                                            <a onclick="setNcm()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual cest" name="cest[]" value="{{ $i->produto->cest }}">
                                            @if($loop->first)
                                            <a onclick="setCest()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual perc_icms" name="perc_icms[]" value="{{ $i->produto->perc_icms }}">
                                            @if($loop->first)
                                            <a onclick="setPercIcms()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual perc_pis" name="perc_pis[]" value="{{ $i->produto->perc_pis }}">
                                            @if($loop->first)
                                            <a onclick="setPercPis()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual perc_cofins" name="perc_cofins[]" value="{{ $i->produto->perc_cofins }}">
                                            @if($loop->first)
                                            <a onclick="setPercCofins()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control percentual perc_ipi" name="perc_ipi[]" value="{{ $i->produto->perc_ipi }}">
                                            @if($loop->first)
                                            <a onclick="setPercIpi()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input style="width: 150px" type="tel" class="form-control percentual perc_red_bc" name="perc_red_bc[]" value="{{ $i->produto->perc_red_bc }}">
                                            @if($loop->first)
                                            <a onclick="setPercRedBc()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control cfop cfop_estadual" name="cfop_estadual[]" value="{{ $i->produto->cfop_estadual }}">
                                            @if($loop->first)
                                            <a onclick="setCfopSaidaEstadual()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control cfop cfop_outro_estado" name="cfop_outro_estado[]" value="{{ $i->produto->cfop_outro_estado }}">
                                            @if($loop->first)
                                            <a onclick="setCfopSaidaOutroEstado()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control cfop cfop_entrada_estadual" name="cfop_entrada_estadual[]" value="{{ $i->produto->cfop_entrada_estadual }}">
                                            @if($loop->first)
                                            <a onclick="setCfopEntradaEstadual()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>
                                        <td>
                                            <input required style="width: 150px" type="tel" class="form-control cfop cfop_entrada_outro_estado" name="cfop_entrada_outro_estado[]" value="{{ $i->produto->cfop_entrada_outro_estado }}">
                                            @if($loop->first)
                                            <a onclick="setCfopEntradaOutroEstado()" style="font-size: 12px" href=#!>Definir para os demais itens</a>
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success float-end mt-3">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js')
<script type="text/javascript">

    function setNcm(){
        let v = $('.ncm').first().val()
        $('.ncm').val(v)
    }
    function setCest(){
        let v = $('.cest').first().val()
        $('.cest').val(v)
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

    setTimeout(() => {
        if($('#inp-padrao_id').val()){
            $('#inp-padrao_id').change()
        }
    }, 100)

    $(document).on("change", "#inp-padrao_id", function() {
        let padrao = $(this).val()
        if (padrao) {
            $.get(path_url + "api/produtos/padrao", {
                padrao: padrao
            })
            .done((result) => {
                console.log(result)

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
                $('.cfop_estadual').val(result.cfop_estadual)
                $('.cfop_outro_estado').val(result.cfop_outro_estado)

                $('.cfop_entrada_estadual').val(result.cfop_entrada_estadual)
                $('.cfop_entrada_outro_estado').val(result.cfop_entrada_outro_estado)
            })
            .fail((err) => {
                console.log(err)
            })
        }
    });
</script>
@endsection


