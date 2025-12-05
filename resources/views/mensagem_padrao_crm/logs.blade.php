@extends('layouts.app', ['title' => 'Logs Mensagem'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                {!!Form::open()->fill(request()->all())
                ->get()
                !!}
                <div class="row mt-3">
                    <div class="col-md-4">
                        {!!Form::select('cliente_id', 'Cliente')
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::select('status', 'Status', [''  => 'Todos', 'pendente' => 'Pendente', 'enviado' => 'Enviado', 'erro' => 'Erro'])
                        ->attrs(['class' => 'form-select'])
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
                        {!!Form::select('tipo', 'Tipo', ['' => 'Selecione'] + \App\Models\MensagemPadraoCrm::tipos())
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>
                    <div class="col-md-4 text-left">
                        <br>
                        <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                        <a id="clear-filter" class="btn btn-danger" href="{{ route('mensagem-crm-logs.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                    </div>
                </div>
                {!!Form::close()!!}
                <div class="col-md-12 mt-3 table-responsive">

                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Cliente</th>
                                <th>Status</th>
                                <th>Data de cadastro</th>
                                <th>Data de agendamento</th>
                                <th>Tipo</th>

                                <th width="10%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                            <tr>

                                <td>{{ $item->cliente->razao_social }}</td>

                                <td>
                                    @if($item->status == 'pendente')
                                    <span class="badge bg-warning">PENDENTE</span>
                                    @elseif($item->status == 'enviado')
                                    <span class="badge bg-success">ENVIADO</span>
                                    @else
                                    <span class="badge bg-danger">ERRO</span>
                                    @endif
                                </td>
                                <td>{{ __data_pt($item->created_at) }}</td>
                                <td>{{ __data_pt($item->agendar_para, 0) }}</td>
                                <td>{{ $item->_tipo() }}</td>
                                <td>
                                    <form action="{{ route('mensagem-crm-logs.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                        @method('delete')
                                        @csrf

                                        <button type="button" class="btn btn-delete btn-sm btn-danger">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm" onclick="openModal('{{ $item->id }}')">
                                            <i class="ri-eye-line"></i>
                                        </button>

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
                    <br>

                </div>
                {!! $data->appends(request()->all())->links() !!}
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-log" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Mensagem Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script type="text/javascript">
    function openModal(id){
        $.get(path_url+'api/crm/modal-log-mensagem', {id: id})
        .done((res) => {
            $('#modal-log').modal('show')
            $('#modal-log .modal-body').html(res)
        }).fail((err) => {
            console.log(err)
        })
    }
</script>
@endsection

