@extends('layouts.app', ['title' => 'Comissão Motoboy'])
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

                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('motoboy_id', 'Motoboy', ['' => 'Selecione'] + $motoboys->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'select2 form-select'])
                            ->value($motoboy != null ? $motoboy->id : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todos', '0' => 'Pendente', '1' => 'Pago'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('motoboys-comissao.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <form method="post" action="{{ route('motoboys-comissao.pay-multiple') }}" id="form-comissao">
                            @csrf
                            <button type="button" class="btn btn-success mb-1 btn-pay" disabled>
                                <i class="ri-wallet-fill"></i>
                                Pagar <strong class="total-pay">R$ 0,00</strong>
                            </button>
                            <table class="table table-striped table-centered mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="select-all">
                                        </th>
                                        <th>Motoboy</th>
                                        <th>Status</th>
                                        <th>Valor do pedido</th>
                                        <th>Valor da comissão</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse($data as $item)
                                    <tr>
                                        <td>
                                            @if(!$item->status)
                                            <input type="checkbox" name="check[]" value="{{ $item->id }}" class="select-check">
                                            @endif
                                        </td>
                                        <td>{{ $item->motoboy->nome }}</td>
                                        <td>
                                            @if($item->status)
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                            @else
                                            <i class="ri-close-circle-fill text-danger"></i>
                                            @endif
                                        </td>
                                        <td>{{ __moeda($item->valor_total_pedido) }}</td>
                                        <td>{{ __moeda($item->valor) }}</td>
                                        <td>{{ __data_pt($item->created_at) }}</td>
                                        
                                    </tr>

                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Nada encontrado</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-primary">R$ {{ __moeda($data->sum('valor_total_pedido')) }}</td>
                                        <td class="text-primary">R$ {{ __moeda($data->sum('valor')) }}</td>
                                        <td colspan="2"></td>

                                    </tr>
                                </tfoot>
                            </table>

                            @include('modals._modal_conta_pagar')

                        </form>
                    </div>
                </div>

                <br>
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <h4>Total de comissões pendentes: <strong class="text-danger">R$ {{ __moeda($sumComissaoPendente) }}</strong></h4>
                    </div>

                    <div class="col-lg-4 col-12">
                        <h4>Total de comissões pagas: <strong class="text-success">R$ {{ __moeda($sumComissaoPago) }}</strong></h4>
                    </div>
                    <div class="col-lg-4 col-12">
                        <h4>Total de vendas: <strong class="text-success">R$ {{ __moeda($sumPedidos) }}</strong></h4>
                    </div>
                </div>

                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript" src="/js/comissao_motoboy.js"></script>
@endsection
