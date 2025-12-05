<html>
<head>
	<style>
		@page { margin: 0cm 0cm; }

		body {
			margin: 1cm;
			font-family: "Arial", sans-serif;
			font-size: 12px;
			color: #222;
		}

		/* ===== CABEÇALHO ===== */
		header {
			margin-bottom: -10px;
			margin-top: -30px;
		}

		header table {
			width: 100%;
			border-collapse: collapse;
		}

		header td {
			vertical-align: middle;
			border: none !important; /* remove qualquer linha */
		}

		header .logo {
			width: 25%;
			text-align: left;
		}

		header .logo img {
			height: 45px;
		}

		header .titulo {
			width: 50%;
			text-align: center;
			font-weight: bold;
			font-size: 15px;
		}

		header .data {
			width: 25%;
			text-align: right;
			font-size: 11px;
			color: #666;
			white-space: nowrap;
		}

		/* ===== CONTEÚDO ===== */
		main {
			margin-top: 0.5cm;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			font-size: 11.5px;
		}

		thead {
			background: #2d2d2d;
			color: #fff;
		}

		th, td {
			border: 1px solid #ccc;
			padding: 6px 8px;
			text-align: center;
		}

		tbody tr:nth-child(even) {
			background: #f7f7f7;
		}

		tbody tr:hover {
			background: #eef3fa;
		}

		.text-left { text-align: left !important; }
		.text-right { text-align: right !important; }

		/* ===== RODAPÉ ===== */
		footer {
			position: absolute;
			bottom: 0.2cm;
			left: 1cm;
			right: 1cm;
			padding-top: 5px;
			border-top: 1px solid #bbb;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		footer img {
			height: 65px;
			opacity: 0.9;
			margin-bottom: -30px;
		}

		footer .site {
			font-size: 11px;
			color: #444;
			text-align: right;
		}
	</style>
</head>

<body>

	@if($loopFirst ?? true)
	<header>
		<table>
			<tr>
				<td class="logo">
					@php
					$config = \App\Models\Empresa::findOrFail(request()->empresa_id);
					@endphp

					@if($config->logo)
					<img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('uploads/logos/' . $config->logo))) }}" alt="Logo">
					@else
					<img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('logo.png'))) }}" alt="Logo">
					@endif
				</td>

				<td class="titulo">
					{{ $title }}
				</td>

				<td class="data">
					<strong>Emissão:</strong>{{ date('d/m/Y - H:i') }}
				</td>
			</tr>
		</table>
	</header>
	@endif

	<main>
		@yield('content')
	</main>

	<footer>
		<img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('logo.png'))) }}" alt="Logo rodapé">
		<div class="site">
			{{ env('SITE_SUPORTE') ?? 'https://slym.com.br' }}
		</div>
	</footer>

</body>
</html>
