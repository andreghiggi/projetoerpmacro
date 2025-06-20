@extends('layouts.app', ['title' => 'Chat #'.$item->_id])
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

<div class="card mt-1 print">
    <div class="card-body">
        <div class="pl-lg-4">

            <div class="ms">
                <div class="mt-3 d-print-none" style="text-align: right;">
                    <a href="{{ route('mercado-livre-pedidos.show', [$item->id]) }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
            </div>

            <!-- <form class="row mt-4">
                <p class="text-danger">A DANFE da NFe será enviada por anexo, a mensagem é opcional</p>
                <div class="col-md-12">
                    {!!Form::textarea('mensagem', 'Mensagem')
                    ->attrs(['rows' => '5'])
                    !!}
                </div>
                <hr>
                <div class="col-12" style="text-align: right;">
                    <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
                </div>
            </form> -->

            <hr>
            <h5>Mensagens do chat</h5>

            @if($notaEmitida)
            <a href="{{ route('mercado-livre-chat.send-nfe', [$item->id]) }}" class="btn btn-danger btn-sm">
                <i class="ri-file-list-line"></i>
                Enviar nota fiscal
            </a>
            @endif
            @if($messages != null)
            <div class="row">
                <div class="card-body p-0">
                    <ul class="conversation-list p-3" data-simplebar="init" style="max-height: 520px;"><div class="simplebar-wrapper" style="margin: -20px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 20px;">

                        @foreach(array_reverse($messages) as $m)

                        <li class="clearfix @if($config->user_id != $m->from->user_id) odd @endif">

                            <div class="conversation-text">
                                <div class="ctext-wrap">
                                    <i>
                                        @if($config->user_id != $m->from->user_id)
                                        {{ $item->cliente_nome }}
                                        @else
                                        Você
                                        @endif
                                    </i>
                                    <p>
                                        {{ $m->text }}
                                        @if($m->message_attachments != null)
                                        @foreach($m->message_attachments as $file)
                                        arquivo: <strong class="text-success">{{ $file->filename }}</strong>
                                        @endforeach
                                        @endif
                                    </p>
                                    <i>{{ $m->_date }}</i>
                                </div>
                            </div>
                            
                        </li>
                        @endforeach
                        
                    </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 919px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 294px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></ul>

                    <div class="row">
                        <div class="col">
                            <div class="bg-light p-3 rounded">
                                <form method="post" class="needs-validation" action="{{ route('mercado-livre-chat.send', [$item->id]) }}" novalidate="" name="chat-form" id="chat-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col mb-2 mb-sm-0">
                                            <input name="mensagem" type="text" class="form-control border-0" placeholder="Escreva uma mensagem" required>
                                            <div class="invalid-feedback">
                                                Informe um texto
                                            </div>
                                        </div>
                                        <div class="col-sm-auto">
                                            <div class="btn-group">
                                                <button type="submit" class="btn btn-success chat-send w-100"><i class="ri-send-plane-2-line"></i></button>
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row-->
                                </form>
                            </div>
                        </div>
                        <!-- end col-->
                    </div>
                    <!-- end row -->
                </div>
            </div>
            @else
            <h5>Não foi possível carregar o chat</h5>
            @endif

        </div>
    </div>
</div>

@endsection

