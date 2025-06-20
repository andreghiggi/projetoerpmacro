<span class="list-group-item list-categoria list-group-item-action categoria-0 active" data-id="0">
	Todas as categorias
</span>
@foreach($categorias as $c)
<span class="list-group-item list-categoria list-group-item-action categoria-{{$c->id}}" data-id="{{$c->id}}">
	{{ $c->nome }}
</span>
@endforeach
<div class="mt-1 d-flex justify-content-center categorias-pagination">
	{!! $categorias->links() !!}
</div>
