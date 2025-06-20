<div class="modal fade modal-select-cliente" id="cliente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Selecionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div> 
            <div class="modal-body">
                <div class="row m-2">

                    <!-- <div class="col-12">
                        {!! Form::select('cliente_id', 'Cliente')
                        ->options(isset($cliente) ? [$cliente->id => $cliente->razao_social] : [])
                        !!}
                    </div> -->

                    <div class="input-group flex-nowrap">
                        <select id="inp-cliente_id" name="cliente_id" class="cliente_id">
                            @if(isset($item) && $item->cliente)
                            <option value="{{ $item->cliente_id }}">{{ $item->cliente->razao_social }}</option>
                            @endif
                        </select>
                        @can('clientes_create')
                        <button class="btn btn-dark btn-novo-cliente" type="button">
                            <i class="ri-add-circle-fill"></i>
                        </button>
                        @endcan
                    </div>

                    @if($cashback == 1)
                    <div class="cashback-div d-none row">
                        <p class="info_cash_back text-success"></p>

                        <div class="col-12">
                            <p>Valor de cashback disponível para uso: <strong class="text-success valor-cashback-disponivel">R$ 0,00</strong></p>

                        </div>

                        <div class="col-12 col-md-3">
                            {!! Form::text('valor_cashback', 'Valor de cashback')
                            ->attrs(['class' => 'moeda']) !!}
                        </div>

                        <div class="col-12 col-md-3">
                            {!! Form::select('permitir_credito', 'Permitir crédito', ['1' => 'Sim', '0' => 'Não'])
                            ->attrs(['class' => 'form-select']) !!}
                        </div>

                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success cliente-venda" data-bs-dismiss="modal">Selecionar</button>
                </div>
            </div> 
        </div> 
    </div> 
