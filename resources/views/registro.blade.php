@extends('layouts.app', ['title' => 'Registro de Software'])
@section('content')
<div class="mt-1">
	<div class="card">
		<div class="card-body">
			<a target="_blank" href="https://busca.inpi.gov.br/pePI/jsp/programas/ProgramaSearchBasico.jsp">https://busca.inpi.gov.br/pePI/jsp/programas/ProgramaSearchBasico.jsp</a>
			<h5>Número do Pedido: <strong>{{ $codigo }}</strong></h5>
			<h5>Protocolo: <strong>{{ $protocolo }}</strong></h5>
			<br>
			<img style="width: 700px" src="/css/registro.png">
		</div>
	</div>
</div>
@endsection