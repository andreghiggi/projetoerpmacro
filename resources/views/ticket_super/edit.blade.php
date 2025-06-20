@extends('layouts.app', ['title' => 'Editar solicitação'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar solicitação</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('ticket-super.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('ticket-super.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('ticket_super._forms')
        </div>
        {!!Form::close()!!}

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
