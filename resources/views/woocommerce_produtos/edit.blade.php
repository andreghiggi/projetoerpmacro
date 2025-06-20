@extends('layouts.app', ['title' => 'Editar Produto Woocommerce'])
@section('css')
<style type="text/css">
    input:read-only {
        background-color: #CCCCCC;
    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar produto do Woocommerce</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('woocommerce-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->put()
        ->route('woocommerce-produtos.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('woocommerce_produtos._forms_edit')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@section('js')
<script src="/js/woocommerce_produtos.js"></script>
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    $(function(){
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 1000)
    })
</script>
@endsection
@endsection
