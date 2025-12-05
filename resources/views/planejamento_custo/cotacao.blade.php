@extends('layouts.app', ['title' => 'Cotação para Planejamento'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Cotação</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('planejamento-custo.show', [$item->id]) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <p class="text-danger">Adicione o fornecedor para criar a cotação</p>
        {!!Form::open()
        ->post()
        ->route('planejamento-custo.store-cotacao', [$item->id])
        !!}
        <div class="pl-lg-4">
            <div class="row">
                <div class="col-md-4">
                    <label>Fornecedor</label>
                    <div class="input-group flex-nowrap">
                        <select required id="inp-fornecedor_id" name="fornecedor_id" class="fornecedor_id"></select>
                    </div>
                </div>
                <div class="col-md-2">
                    <br>
                    <button class="btn btn-success">Adicionar</button>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
        <br>
        <div class="table-responsive">
            <table class="table table-striped table-centered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Fornecedor</th>
                        <th>Referência</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($item->cotacoes as $c)
                    <tr>
                        <td>{{ $c->fornecedor->info }}</td>
                        <td>{{ $c->referencia }}</td>
                        <td width="150">
                            @if($c->estado == 'aprovada')
                            <span class="bg-success text-white p-2" style="border-radius: 5px;">Aprovada</span>
                            @elseif($c->estado == 'rejeitada')
                            <span class="bg-danger text-white p-2" style="border-radius: 5px;">Rejeitada</span>
                            @elseif($c->estado == 'respondida')
                            <span class="bg-primary text-white p-2" style="border-radius: 5px;">Respondida</span>
                            @else
                            <span class="bg-info text-white p-2" style="border-radius: 5px;">Nova</span>
                            @endif
                        </td>
                        <td>{{ __moeda($c->valor_total) }}</td>
                        <td style="width: 200px">

                            <form action="{{ route('cotacoes.destroy', $item->id) }}" method="post" id="form-{{$c->id}}">
                                @method('delete')
                                @csrf

                                @if($item->estado != 'aprovada')
                                @can('cotacao_edit')
                                <a class="btn btn-warning btn-sm" href="{{ route('cotacoes.edit', $c->id) }}">
                                    <i class="ri-edit-line"></i>
                                </a>
                                @endcan

                                @can('cotacao_delete')
                                <button type="button" class="btn btn-delete btn-sm btn-danger">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                                @endcan
                                @endif

                                <a title="Link para responder cotação" target="_blank" class="btn btn-dark btn-sm" href="{{ route('cotacoes.resposta', $c->hash_link) }}">
                                    <i class="ri-links-fill"></i>
                                </a>
                                @if($c->estado == 'respondida' || $c->estado == 'aprovada')
                                <a title="Ver resposta" class="btn btn-primary btn-sm" href="{{ route('cotacoes.show', $c->id) }}">
                                    <i class="ri-eye-2-line"></i>
                                </a>
                                @endif

                            </form>

                            
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('modals._dimensao_item_planejamento')
@endsection
@section('js')
<script type="text/javascript" src="/js/planejamento_custo.js"></script>
@endsection
