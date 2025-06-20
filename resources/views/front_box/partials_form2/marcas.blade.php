<span class="list-group-item list-marca list-group-item-action marca-0 active" data-id="0">
	Todas as marcas
</span>
@foreach($marcas as $c)
<span class="list-group-item list-marca list-group-item-action marca-{{$c->id}}" data-id="{{$c->id}}">
	{{ $c->nome }}
</span>
@endforeach
<div class="mt-1 d-flex justify-content-center marcas-pagination">
	{!! $marcas->links() !!}
</div>
