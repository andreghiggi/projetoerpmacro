@foreach($banners as $key => $b)
<div class="hero__item bg-{{$key}} set-bg d-none" data-setbg="{{ $b->img }}">
    <div class="hero__text">
        @if($b->produto_id)
        <span>{{ $b->produto->nome }}</span>
        <h2 style="color: red">R$ {{ __moeda($b->produto->valor_delivery) }}</h2>
        @endif

        <p>{{ $b->descricao }}</p>
        @if($b->produto_id)
        <a href="{{ route('food.produto-detalhe', [$b->produto->hash_delivery, 'link='.$config->loja_id])}}" class="primary-btn btn-main">COMPRAR AGORA</a>
        @endif
    </div>
</div>
@endforeach


@section('js')
<script type="text/javascript">
    
</script>
@endsection
