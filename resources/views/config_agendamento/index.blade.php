@extends('layouts.app', ['title' => 'Configuração de Agendamento'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração de Agendamento</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('config-agendamento.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('config_agendamento._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>

<div class="modal fade" id="modal_wpp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('config.agendamento.teste-wpp') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Enviar Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mt-2">
                            <label>Número WhatsApp</label>
                            <input class="form-control fone" type="text" name="telefone">
                        </div>

                        <div class="col-md-12 mt-2">
                            <label>Mensagem</label>
                            <input class="form-control" type="text" name="mensagem">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="mt-3 ms-auto">
                        <button type="submit" class="btn btn-success px-3 float-end btn-modal-alterar">Enviar Mensagem</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
