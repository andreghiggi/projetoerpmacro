@extends('layouts.app', ['title' => 'Cotações'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('cotacao_create')
                    <a href="{{ route('cotacoes.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Cotação
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-1">
                        <div class="col-md-3">
                            {!!Form::select('fornecedor_id', 'Fornecedor')
                            ->options($fornecedor != null ? [$fornecedor->id => $fornecedor->info] : [])
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
                            {!!Form::text('referencia', 'Referência')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('estado', 'Estado',
                            ['novo' => 'Nova',
                            'rejeitada' => 'Rejeitada',
                            'respondida' => 'Respondida',
                            'aprovada' => 'Aprovada',
                            '' => 'Todas'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('gerado_compra', 'Gerado compra',
                            [
                            '0' => 'Não',
                            '1' => 'Sim',
                            '' => 'Todas'
                            ])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-lg-3 col-12">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('cotacoes.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fornecedor</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Valor total</th>
                                    <th>Estado</th>
                                    <th>Status</th>
                                    <th>Gerado compra</th>
                                    <th>Data de criação</th>
                                    <th>Data de resposta</th>
                                    <th>Referência</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Fornecedor">{{ $item->fornecedor ? $item->fornecedor->razao_social : "--" }}</td>
                                    <td data-label="CPF/CNPJ">{{ $item->fornecedor ? $item->fornecedor->cpf_cnpj : "--" }}</td>
                                    <td data-label="Valor total">{{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                    <td data-label="Estado">
                                        @if($item->estado == 'aprovada')
                                        <span class="bg-success text-white p-2" style="border-radius: 5px;">Aprovada</span>
                                        @elseif($item->estado == 'rejeitada')
                                        <span class="bg-danger text-white p-2" style="border-radius: 5px;">Rejeitada</span>
                                        @elseif($item->estado == 'respondida')
                                        <span class="bg-primary text-white p-2" style="border-radius: 5px;">Respondida</span>
                                        @else
                                        <span class="bg-info text-white p-2" style="border-radius: 5px;">Nova</span>
                                        @endif
                                    </td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Gerado compra">
                                        @if($item->nfe_id)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Data de criação">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Data de resposta">{{ $item->data_resposta ? __data_pt($item->data_resposta) : '--' }}</td>
                                    <td data-label="Referência">{{ $item->referencia }}</td>
                                    <td>
                                        <form style="width: 200px" action="{{ route('cotacoes.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            @if($item->estado != 'aprovada')
                                            @can('cotacao_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('cotacoes.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan

                                            @can('cotacao_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            @endif

                                            <a title="Link para responder cotação" target="_blank" class="btn btn-dark btn-sm" href="{{ route('cotacoes.resposta', $item->hash_link) }}">
                                                <i class="ri-links-fill"></i>
                                            </a>

                                            @if($item->estado == 'respondida' || $item->estado == 'aprovada')
                                            <a title="Ver resposta" class="btn btn-primary btn-sm" href="{{ route('cotacoes.show', $item->id) }}">
                                                <i class="ri-eye-2-line"></i>
                                            </a>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                    {!! $data->appends(request()->all())->links() !!}
                </div>
                <h5 class="mt-2">Soma: <strong class="text-success">R$ {{ __moeda($data->sum('valor_total')) }}</strong></h5>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cancelar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cancelar NFe <strong class="ref-numero"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        {!!Form::text('motivo-cancela', 'Motivo')
                        ->required()

                        !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="button" id="btn-cancelar" class="btn btn-danger">Cancelar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-corrigir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Corrigir NFe <strong class="ref-numero"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        {!!Form::text('motivo-corrigir', 'Motivo')
                        ->required()

                        !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="button" id="btn-corrigir" class="btn btn-warning">Corrigir</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    function info(motivo_rejeicao, chave, estado, recibo) {

        if (estado == 'rejeitado') {
            let text = "Motivo: " + motivo_rejeicao + "\n"
            text += "Chave: " + chave + "\n"
            swal("", text, "warning")
        } else {
            let text = "Chave: " + chave + "\n"
            text += "Recibo: " + recibo + "\n"
            swal("", text, "success")
        }
    }

</script>
<script type="text/javascript" src="/js/nfe_transmitir.js"></script>
@endsection
