@extends('layouts.app', ['title' => 'Manifesto'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12">
                        <a href="{{ route('manifesto.novaConsulta') }}" class="btn btn-dark">
                            <i class="ri-refresh-line"></i>
                            Nova Consulta de Documentos
                        </a>
                    </div>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('tipo', 'Tipo',
                            [
                            '' => 'Todos',
                            1 => 'Ciência',
                            2 => 'Confirmada',
                            3 => 'Desconhecido',
                            4 => 'Op. não Realizada'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-lg-4 col-12">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('manifesto.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Documento</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Num. Protocolo</th>
                                    <th>Chave</th>
                                    <th>Estado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->documento }}</td>
                                    <td>{{ __moeda($item->valor) }}</td>
                                    <td>{{ __data_pt($item->data_emissao) }}</td>
                                    <td>{{ $item->num_prot }}</td>
                                    <td>{{ $item->chave }}</td>
                                    <td>{{ $item->estado() }}</td>
                                    <td>
                                        @if($item->tipo == 1 || $item->tipo == 2)
                                        <a href="{{ route('manifesto.download', [$item->id]) }}" class="btn btn-success btn-sm w-100">Completa</a>
                                        <a target="_blank" href="{{ route('manifesto.danfe', [$item->id]) }}" class="btn btn-primary btn-sm w-100 mt-1">Imprimir</a>

                                        @elseif($item->tipo == 3)
                                        <a class="btn btn-danger w-100">Desconhecida</a>
                                        @elseif($item->tipo == 4)
                                        <a class="btn btn-warning w-100">Não realizada</a>
                                        @endif
                                        @if($item->tipo != 2)
                                        <a class="btn btn-info btn-sm w-100 mt-1" onclick="setChave('{{$item->chave}}')" data-toggle="modal" data-target="#modal-evento">Manifestar</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $data->appends(request()->all())->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-evento" aria-modal="true" role="dialog" style="overflow:scroll;" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="post" action="{{ route('manifesto.manifestar') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Manifestação NFe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="chave" id="chave">
                <div class="col-md-6">
                    {!! Form::select('tipo', 'Tipo', [1 => "Ciencia", 2 => "Confirmação", 3 => "Desconhecimento", 4 => "Operação não realizada"])
                    ->attrs(['class' => 'form-select']) !!}
                </div>

                <div class="col-md-12 just d-none mt-3">
                    {!! Form::text('justificativa', 'Justificativa') !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info px-5">Manifestar</button>
            </div>
        </form>
    </div>
</div>

@section('js')
<script type="text/javascript">
    function setChave(chave) {
        $('#chave').val(chave)
        $('#modal-evento').modal('show')
    }

    $(document).on("change", "#inp-tipo", function() {
        if ($(this).val() > 2) {
            $('.just').removeClass('d-none')
        } else {
            $('.just').addClass('d-none')
        }
    })

</script>
@endsection

@endsection
