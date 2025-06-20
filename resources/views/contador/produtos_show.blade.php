@extends('layouts.app', ['title' => 'Produto'])


@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Produto</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('contador-empresa.produtos') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('produtos.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('produtos._forms', ['not_submit' => 1])
        </div>
        {!!Form::close()!!}
    </div>
</div>

@section('js')
<script type="text/javascript" src="/js/produto.js"></script>

<script type="text/javascript">
    $(function(){
        $('input, select').each(function(){
            $(this).attr('disabled', 1)
        })

    })
</script>
<script src="/assets/vendor/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="/assets/js/pages/demo.form-wizard.js"></script>
@endsection

@endsection


