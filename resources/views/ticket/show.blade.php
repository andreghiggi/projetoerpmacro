@extends('layouts.app', ['title' => 'Solicitação #'.$item->id])
@section('css')
<style type="text/css">
    img.logo1{
        width: 50px;
        border-radius: 50%;
        margin-right: 20px;
    }
    .input_container {
        border: 1px solid #e5e5e5;
    }

    input[type=file]::file-selector-button {
        background-color: #fff;
        color: #000;
        border: 0px;
        border-right: 1px solid #e5e5e5;
        padding: 10px 15px;
        margin-right: 20px;
        transition: .5s;
    }

    input[type=file]::file-selector-button:hover {
        background-color: #eee;
        border: 0px;
        border-right: 1px solid #e5e5e5;
    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Solicitação <strong class="text-success">#{{ $item->id }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('ticket.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row container">
            <div class="col-md-8">
                @foreach($item->mensagens as $m)
                <div class="row">
                    <div class="col-md-2 col-6" style="text-align: right;">
                        @if($m->resposta)
                        <img class="logo1" src="/logo.png">
                        @else
                        <img class="logo1" src="{{ $item->empresa->img }}">
                        @endif
                    </div>
                    <div class="col-md-9 col-6 mt-2">
                        @if($m->resposta)
                        <h5>{{ env("APP_NAME") }}</h5>
                        @else
                        <h5>{{ $item->empresa->nome }}</h5>
                        @endif

                        <h6>{{ __data_pt($m->created_at) }}</h6>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 col-6">
                    </div>
                    <div class="col-md-9 col-12 mt-2">
                        {!! $m->descricao !!}

                        @foreach($m->anexos as $key => $f)
                        <a target="_blank" href="{{ $f->file }}">Conteudo anexado {{$key+1}}</a><br>
                        @endforeach
                    </div>
                </div>
                <hr>
                @endforeach

                @if($item->status != 'resolvido')
                <form class="row g-2" method="post" action="{{ route('ticket.add-mensagem', [$item->id]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="col-md-12">
                        {!!Form::textarea('descricao', 'Descrição')
                        ->attrs(['rows' => '10', 'class' => 'tiny'])
                        !!}
                    </div>

                    <div class="col-md-8">
                        <label>Anexo</label>
                        <div class="input_container">
                            {!! Form::file('anexos[]', '')
                            ->attrs(['multiple' => 'true'])  
                            !!}
                        </div>
                    </div>

                    <div class="col-12 mt-2" style="text-align: right;">
                        <button type="submit" class="btn btn-success px-5" id="btn-store">Enviar</button>
                    </div>
                </form>
                @endif
            </div>
            <div class="col-md-4">
                <div class="card" style="background: #F7F7F7">
                    <div class="card-body">
                        <h5>Criado: <strong>{{ __data_pt($item->created_at) }}</strong></h5>
                        <h5 class="mt-4">Última atividade: <strong>{{ __data_pt($item->updated_at) }}</strong></h5>
                        <h5 class="mt-4">ID: <strong>#{{ $item->id }}</strong></h5>
                        <h5 class="mt-4">Status: 
                            @if($item->status == 'aberto')
                            <span class="p-1 bg-dark rounded text-white">aberto</span>
                            @elseif($item->status == 'respondida')
                            <span class="p-1 bg-warning rounded text-white">respondida</span>
                            @elseif($item->status == 'aguardando')
                            <span class="p-1 bg-danger rounded text-white">aguardando</span>
                            @else
                            <span class="p-1 bg-success rounded text-white">resolvido</span>
                            @endif
                        </h5>
                        <h5 class="mt-4">Departamento: <strong>{{ $item->departamento }}</strong></h5>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    $(function(){
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)
    })
</script>
@endsection


