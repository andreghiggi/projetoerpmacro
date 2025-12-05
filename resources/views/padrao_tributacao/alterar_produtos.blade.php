@extends('layouts.app', ['title' => 'Alterar Produtos'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Alterar Tributação</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('produtopadrao-tributacao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('produtopadrao-tributacao.set-tributacao')
        !!}
        <div class="pl-lg-4">

            <div class="col-md-4">
                {!!Form::select('padrao_id', 'Selecione o Padrão', ['' => 'Selecione'] + $padroes->pluck('descricao', 'id')->all())
                ->required()
                ->attrs(['class' => 'form-select select2'])
                !!}
            </div>
            <div class="form-trib d-none mt-2">
                @include('padrao_tributacao._forms', ['not_submit' => 1])

                <div class="card ">
                    <div class="row mt-4 m-2">
                        <p class="text-danger"><i class="ri-alert-line"></i>Desmaque os produtos que não deseja atualizar</p>
                        <h5>Produtos</h5>
                        <div class="form-check m-1 form-checkbox-success col-12">
                            <input type="checkbox" checked class="form-check-input" id="check-all">
                            <label class="form-check-label">Selecionar todos <strong>(<span class="contador-selecionados">0</span>)</strong></label>
                        </div>
                        @foreach($produtos as $p)

                        <div class="col-md-3 produtos-check">

                            <div class="form-check">
                                <input type="checkbox" checked name="produto_check[]" class="form-check-input prod-check" value="{{ $p->id }}" id="{{ $p->id }}">
                                <label class="form-check-label" for="{{ $p->id }}">{{ $p->nome }}</label>
                            </div>

                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12" style="text-align: right;">
            <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $(function(){ 
        $("#inp-padrao_id").change()
        contaSelecionados()
    })

    $(document).on("change", "#inp-padrao_id", function () {
        if($(this).val()){

            $.get(path_url + "api/produtos/padrao", {
                padrao: $(this).val()
            })
            .done((result) => {
                $('.form-trib').removeClass('d-none')

                if(result._ncm){
                    var newOption = new Option(result._ncm.descricao, result._ncm.codigo, 1, 1);
                    $('#inp-ncm').append(newOption);
                }
                // $('#inp-ncm').val(result.ncm)
                $('#inp-cest').val(result.cest)
                $('#inp-perc_icms').val(result.perc_icms)
                $('#inp-perc_pis').val(result.perc_pis)
                $('#inp-perc_cofins').val(result.perc_cofins)
                $('#inp-perc_ipi').val(result.perc_ipi)
                $('#inp-cst_csosn').val(result.cst_csosn).change()
                $('#inp-cst_pis').val(result.cst_pis).change()
                $('#inp-cst_cofins').val(result.cst_cofins).change()
                $('#inp-cst_ipi').val(result.cst_ipi).change()
                $('#inp-cEnq').val(result.cEnq).change()
                $('#inp-cfop_estadual').val(result.cfop_estadual)
                $('#inp-cfop_outro_estado').val(result.cfop_outro_estado)
                $('#inp-codigo_beneficio_fiscal').val(result.codigo_beneficio_fiscal)

                $('#inp-cfop_entrada_estadual').val(result.cfop_entrada_estadual)
                $('#inp-cfop_entrada_outro_estado').val(result.cfop_entrada_outro_estado)
            })
            .fail((err) => {
                console.log(err)
            })
        }

    })

    $(document).on("click", "#check-all", function () {
        if($(this).is(':checked')){
            $('.prod-check').prop('checked', 1)
        }else{
            $('.prod-check').prop('checked', 0)
        }

        setTimeout(() => {
            contaSelecionados()
        }, 100)
    })

    $('.prod-check').click(() => {
        contaSelecionados()
    })

    function contaSelecionados(){
        let total = $(".prod-check:checked").length;
        $(".contador-selecionados").text(total);
    }

    $(document).on("blur", "#inp-cfop_estadual", function () {

        let v = $(this).val().substring(1,4)
        $("#inp-cfop_outro_estado").val('6'+v)
        $("#inp-cfop_entrada_estadual").val('1'+v)
        $("#inp-cfop_entrada_outro_estado").val('2'+v)
    })

</script>
@endsection

