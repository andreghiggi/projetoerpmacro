@extends('layouts.app', ['title' => 'Ordem de Produção'])
@section('css')
<style type="text/css">
    @page { size: auto;  margin: 0mm; }

    @media print {
        .print{
            margin: 10px;
        }
    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-body">
        <div class="pl-lg-4">
            <div class="ms">
                <div class="mt- d-print-none" style="text-align: right;">
                    <a href="{{ route('ordem-producao.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <div class="row">
                    <div class="col-6">
                        <h5>Estado:
                            @if($item->estado == 'novo')
                            <span class="badge bg-secondary text-light">Novo</span>
                            @elseif($item->estado == 'producao')
                            <span class="badge bg-primary text-light">Produção</span>
                            @elseif($item->estado == 'expedicao')
                            <span class="badge bg-dark text-light">Expedição</span>
                            @else
                            <span class="badge bg-success text-light">Finalizado</span>
                            @endif
                        </h5> 
                    </div>
                    <div class="col-6">

                        <h3 class="text-primary">OP #{{ $item->codigo_sequencial }}</h3>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 col-6">
                        <h5>Data de cadastro: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></h5>
                    </div>


                    <div class="col-md-3 col-6">
                        <h5>Data prevista de entrega: <strong class="text-primary">{{ $item->data_prevista_entrega ? __data_pt($item->data_prevista_entrega, 0) : '--' }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Funcionário: <strong class="text-primary">{{ $item->funcionario ? $item->funcionario->nome : '--' }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Usuário: <strong class="text-primary">{{ $item->usuario->name }}</strong></h5>
                    </div>

                    @if($item->observacao)
                    <div class="col-md-12 col-12">
                        <h5>Observação: <strong class="text-primary">{{ $item->observacao }}</strong></h5>
                    </div>
                    @endif
                </div>

                <button type="button" class="btn btn-dark btn-sm d-print-none" data-bs-toggle="modal" data-bs-target="#modal_alterar_estado"><i class="ri-refresh-line"></i>
                    Alterar Estado
                </button>
                <a target="_blank" class="btn btn-primary btn-sm d-print-none" href="{{ route('ordem-producao.imprimir', [$item->id]) }}"><i class="ri-printer-line"></i>
                    Imprimir
                </a>

                <a target="_blank" onclick="printEtiqueta('{{ $item->id }}')" class="btn btn-success btn-sm d-print-none" ><i class="ri-printer-line"></i>
                    Imprimir Etiquetas
                </a>

                <button class="btn btn-danger btn-sm d-print-none" data-bs-toggle="modal" data-bs-target="#modal_configuracao">
                    <i class="ri-settings-line"></i>
                    Configuração Etiquetas
                </button>

                <a class="btn btn-warning btn-sm d-print-none" href="{{ route('ordem-producao.edit', [$item->id]) }}">
                    <i class="ri-pencil-fill"></i>
                    Editar
                </a>

                <div class="row mb-2 mt-4">
                    <h5>Produção</h5>
                    <p class="text-primary">Itens em verde concluídos</p>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th>Nº Pedido</th>
                                    <th>Cliente</th>
                                    <th>Quantidade</th>
                                    <th>Observação</th>
                                    <th class="d-print-none"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->itens as $i)
                                <tr>
                                    <td style="width: 60%" @if($i->status) class="text-success" @endif>
                                        {{ $i->produto->nome }}
                                        @if($i->itemProducao)
                                        {{ $i->itemProducao->dimensao }} 
                                        @endif
                                    </td>
                                    @if($i->itemProducao)
                                    <td>{{ $i->itemProducao->itemNfe->nfe->numero_sequencial }}</td>
                                    <td>{{ $i->itemProducao->itemNfe->nfe->cliente->razao_social }}</td>
                                    @else
                                    <td>{{ $i->numero_pedido }}</td>
                                    <td>{{ $i->cliente->razao_social }}</td>
                                    @endif
                                    <td>
                                        @if(!$i->produto->unidadeDecimal())
                                        {{ number_format($i->quantidade, 0) }}
                                        @else
                                        {{ number_format($i->quantidade, 3) }}
                                        @endif
                                    </td>
                                    <td>{{ $i->observacao }}</td>
                                    <td class="d-print-none">
                                        @if(!$i->status)
                                        <a href="{{ route('ordem-producao-status-item', [$i->id]) }}" class="btn btn-sm btn-success">
                                            <i class="ri-checkbox-circle-line"></i>
                                        </a>
                                        @else
                                        <a href="{{ route('ordem-producao-status-item', [$i->id]) }}" class="btn btn-sm btn-danger">
                                            <i class="ri-close-circle-line"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_alterar_estado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="{{ route('ordem-producao.update-estado', [$item->id]) }}">
            @csrf
            @method('put')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Alterar Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            {!!Form::select('estado', 'Estado', App\Models\OrdemProducao::estados())
                            ->attrs(['class' => 'form-select'])
                            ->value($item->estado)
                            !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_configuracao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" action="{{ route('ordem-producao.config') }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Configuração de Etiquetas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            {!!Form::tel('margem_topo', 'Margem topo (px)')
                            ->attrs(['class' => 'percentual'])
                            ->value($config ? $config->margem_topo : '')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::tel('margem_lateral', 'Margem lateral (px)')
                            ->attrs(['class' => 'percentual'])
                            ->value($config ? $config->margem_lateral : '')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::tel('distancia_entre_etiquetas', 'Distância entre etiquetas (px)')
                            ->attrs(['class' => 'percentual'])
                            ->value($config ? $config->distancia_entre_etiquetas : '')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::tel('distancia_entre_linhas', 'Distância entre linhas (px)')
                            ->attrs(['class' => 'percentual'])
                            ->value($config ? $config->distancia_entre_linhas : '')
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::tel('largura_imagem', 'Largura da imagem (px)')
                            ->attrs(['class' => 'percentual'])
                            ->value($config ? $config->largura_imagem : '')
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::tel('altura_imagem', 'Altura da imagem (px)')
                            ->attrs(['class' => 'percentual'])
                            ->value($config ? $config->altura_imagem : '')
                            !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    function printEtiqueta(id){
        var disp_setting="toolbar=yes,location=no,";
        disp_setting+="directories=yes,menubar=yes,";
        disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

        var docprint=window.open(path_url+"ordem-producao-imprimir-etiquetas/"+id, "",disp_setting);

        docprint.focus();
    }
</script>
@endsection