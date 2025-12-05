@extends('layouts.app', ['title' => 'Alterar estado'])
@section('content')

<div class="card mt-1">
    <div class="card-body">
        <div class="pl-lg-4">
            {!!Form::open()
            ->post()
            ->route('ordem-servico.update-estado', [$ordem->id])
            !!}
            @csrf
            <div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
                <h5>Estado Atual:
                    @if($ordem->estado == 'pd')
                    <span class="btn btn-warning btn-sm">Pendente</span>
                    @elseif($ordem->estado == 'ap')
                    <span class="btn btn-success btn-sm">Aprovado</span>
                    @elseif($ordem->estado == 'rp')
                    <span class="btn btn-danger btn-sm">Reprovado</span>
                    @else
                    <span class="btn btn-info btn-sm">Finalizado</span>
                    @endif
                </h5>


                @if($ordem->estado != 'fz' && $ordem->estado != 'rp')

                <div class="row">
                    <div class="col-md-2">
                        <label>Estado</label>
                        @if($ordem->estado == 'pd')
                        <select required class="form-select" id="estado" name="novo_estado">
                            <option value="ap">Aprovado</option>
                            <option value="rp">Reprovado</option>
                        </select>
                        @elseif($ordem->estado == 'ap')
                        <select class="form-select" id="estado" name="novo_estado">
                            <option value="fz">Finalizado</option>
                        </select>
                        @endif
                    </div>

                    <div class="col-md-2 d-none div-fatura">
                        {!!Form::select('faturar', 'Gerar faturamento', [0 => 'Não', 1 => 'Sim'])
                        ->attrs(['class' => 'form-select'])->required()
                        !!}
                    </div>

                    <div class="row div-parcelas d-none">
                        <div class="col-12 col-md-6">
                            <hr>
                            <h4 class="mt-3">Total da Ordem de Serviço <strong class="text-success">R$ {{ __moeda($ordem->valor) }}</strong></h4>

                            <div class="table-responsive">
                                <table class="table table-dynamic">
                                    <thead>
                                        <tr>
                                            <th>Tipo de pagamento</th>
                                            <th>Data vencimento</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="dynamic-form">
                                            <td style="width: 40%">
                                                <select name="tipo_pagamento[]" class="form-control tipo_pagamento select2">
                                                    <option value="">Selecione..</option>
                                                    @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                                                    <option value="{{$key}}">{{$c}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="date" class="form-control" name="data_vencimento[]" id="">
                                            </td>
                                            <td>
                                                <input type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]" id="valor">
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">Soma</td>
                                            <td class="soma_fatura">R$ 0,00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark btn-add-tr">
                                    <i class="ri-add-line"></i>
                                    Adicionar parcela
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group validated col-sm-4 col-lg-4">
                        <br>
                        <button type="submit" class="btn btn-success px-5">Salvar</button>
                    </div>
                </div>

                @elseif($ordem->estado == 'fz')
                <h5 class="text-success">Ordem de serviço finalizada!</h5>

                <div style="text-align: right; margin-top: -35px;">
                    <a href="{{ route('ordem-servico.show', [$ordem->id]) }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                @else
                <h5 class="text-danger">Ordem de serviço reprovada!</h5>
                <div style="text-align: right; margin-top: -35px;">
                    <a href="{{ route('ordem-servico.show', [$ordem->id]) }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                @endif

                
            </div>
            {!!Form::close()!!}
        </div>
    </div>
</div>

@endsection
@section('js')
<script type="text/javascript">
    $(function(){
        changeEstado()
        changeParcelas()
    })

    $('#estado').change(() => {
        changeEstado()
    })

    function changeEstado(){
        let estado = $('#estado').val()
        if(estado == 'fz'){
            $('.div-fatura').removeClass('d-none')
        }else{
            $('.div-fatura').addClass('d-none')
        }
    }

    $('#inp-faturar').change(() => {
        changeParcelas()
    })

    $("body").on("blur", ".valor_fatura", function () {
        let soma = 0
        $(".valor_fatura").each(function () {
            soma += convertMoedaToFloat($(this).val())
        })

        setTimeout(() => {
            $('.soma_fatura').text("R$ " + convertFloatToMoeda(soma))
        }, 10)
    })

    function changeParcelas(){
        let sel = $('#inp-faturar').val()
        if(sel == '1'){
            $('.div-parcelas').removeClass('d-none')
        }else{
            $('.div-parcelas').addClass('d-none')
        }
    }
</script>
@endsection