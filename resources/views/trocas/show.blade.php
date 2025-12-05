@extends('layouts.app', ['title' => 'Detalhes da Troca'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Detalhes da Troca</h4>
                <div style="text-align: right; margin-top: -35px;">
                    <a href="{{ route('trocas.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <hr class="mt-3">
                <div class="">
                    <h4>Cliente: <strong style="color: steelblue">{{ $item->nfce->cliente_id ? $item->nfce->cliente->razao_social : 'Consumidor Final'}}</strong></h4>
                    <label>Valor da venda original: <strong class="text-success">R$ {{ __moeda($item->valor_original) }}</strong></label><br>
                    <label>Valor da troca: <strong class="text-success">R$ {{ __moeda($item->valor_troca) }}</strong></label><br>
                    <label>Data da troca: <strong class="text-success">{{ __data_pt($item->created_at) }}</strong></label><br>

                    <a title="Imprimir não fiscal" onclick="imprimir('{{$item->id}}')" class="btn btn-primary btn-sm">
                        <i class="ri-printer-line"></i> Imprimir
                    </a>
                </div>
                <hr>
                <div class="col-lg-12 mt-4">
                    <h5>Itens da Venda</h5>
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th></th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->nfce->itens as $i)
                                <tr>
                                    <td><img class="img-60" src="{{ $i->produto->img }}"></td>

                                    <td>{{ $i->produto->nome }}</td>

                                    <td>
                                        @if(!$i->produto->unidadeDecimal())
                                        {{ number_format($i->quantidade, 0, '.', '') }}
                                        @else
                                        {{ number_format($i->quantidade, 3, '.', '') }}
                                        @endif
                                    </td>

                                    <td>{{ __moeda($i->valor_unitario) }}</td>
                                    <td>{{ __moeda($i->sub_total) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="col-lg-12 mt-4">
                    <h5>Itens Alterados</h5>
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th></th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->itens as $i)
                                <tr>
                                    <td><img class="img-60" src="{{ $i->produto->img }}"></td>
                                    <td>{{ $i->produto->nome }}</td>
                                    <td>
                                        @if(!$i->produto->unidadeDecimal())
                                        {{ number_format($i->quantidade, 0, '.', '') }}
                                        @else
                                        {{ number_format($i->quantidade, 3, '.', '') }}
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
<script type="text/javascript">
    function imprimir(id){
        var disp_setting="toolbar=yes,location=no,";
        disp_setting+="directories=yes,menubar=yes,";
        disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

        var docprint=window.open(path_url+"trocas/imprimir/"+id, "",disp_setting);

        docprint.focus();
    }
</script>
@endsection
@endsection

