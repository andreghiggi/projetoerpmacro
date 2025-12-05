
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <title>{{ $config->nome_restaurante }} - {{ $title }}</title>
    <meta name="description" content="SELF-SERVICE/QUENTINHAS/HAMBURGUER">
    <meta name="keywords" content="{{ $config->nome_restaurante }}">
    <meta property="og:title" content="{{ $config->nome_restaurante }}">
    <meta property="og:description" content="{{ $config->descricao_restaurante_pt }}">

    <link rel="shortcut icon" href="{{ $config->logo_img }}" />

    <style type="text/css">
        :root{ --main: {{ $config->cor_principal }} !important; }
    </style>

    <link rel="stylesheet" href="/food-files/css/bootstrap.min.css">
    <link rel="stylesheet" href="/food-files/css/class.css">
    <link rel="stylesheet" href="/food-files/css/forms.css">
    <link rel="stylesheet" href="/food-files/css/typography.css">
    <link rel="stylesheet" href="/food-files/css/template.css">
    <link rel="stylesheet" href="/food-files/css/theme.css">
    <link rel="stylesheet" href="/food-files/css/default.css">
    <link rel="stylesheet" href="/food-files/css/upgrade.css">
    <link rel="stylesheet" href="/food-files/css/novo.css">
    <link rel="stylesheet" href="/food-files/css/LineIcons.min.css">
    <link rel="stylesheet" href="/food-files/css/style.min.css">
    <link rel="stylesheet" href="/food-files/css/jquery.sidr.light.min.css">
    <link rel="stylesheet" href="/food-files/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="/food-files/css/mp.css">

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/assets/css/toastr.min.css">

    <script src="/food-files/js/jquery.min.js"></script>
    <script src="/food-files/js/jquery.fancybox.min.js"></script>
    <script src="/food-files/js/alertify.min.js"></script>
    <link rel="stylesheet" href="/food-files/css/alertify.min.css">
    <link rel="stylesheet" href="/food-files/css/semantic.min.css">
    <link rel="stylesheet" href="/food-files/css/messagebox.1.css">

    <script src="/food-files/js/messagebox.1.js"></script>
    <script src="/food-files/js/loadingoverlay.js"></script>
    <script src="/food-files/js/produto_modal.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    @yield('css')

</head>
<body>

    <input type="hidden" value="{{ isset($_SESSION['session_cart_cardapio']) ? $_SESSION['session_cart_cardapio'] : '' }}" id="session_cart_cardapio">
    <input type="hidden" value="{{ isset($_SESSION['session_cart_user']) ? $_SESSION['session_cart_user'] : '' }}" id="session_cart_user">
    <div id="main" class="body-estabelecimento">

        <div class="header">
            <div class="minitop hidden-xs hidden-sm">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-badges-desktop">
                                <div class="info-badge">
                                    <i class="lni lni-user"></i>
                                    <span>Olá 
                                        @isset($_SESSION['session_nome_cardapio'])
                                        <strong>{{ $_SESSION['session_nome_cardapio'] }}</strong>
                                        @endif

                                    </span>
                                    <div class="clear"></div>
                                    
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="top">
                <div class="container">
                    <div class="row align-middle hidden-sm hidden-xs">
                        <div class="col-md-3">
                            <div class="brand">
                                <a href="{{ route('cardapio.index', ['link='.$link]) }}">
                                    <img src="{{ $config->logo_img }}" alt="{{ $config->nome_restaurante }}" />
                                    <span>{{ $config->nome_restaurante }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="search-bar">
                                        <div class="clear"></div>
                                        <form class="align-middle" method="GET" action="{{ route('cardapio.pesquisa') }}">
                                            <input type="text" name="pesquisa" placeholder="Digite sua busca..." value="{{ isset($pesquisa) ? $pesquisa : '' }}" />
                                            <input type="hidden" name="link" value="{{ $link }}" />
                                            <button>
                                                <i class="lni lni-search-alt"></i>
                                            </button>
                                            <div class="clear"></div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <a class="holder-shop-bag pull-right" href="{{ route('cardapio.carrinho', ['link='.$link]) }}" title="Meu carrinho">
                                        <div>
                                            <div class="shop-bag">
                                                <i class="icone icone-sacola"></i>
                                                <span class="counter"> </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-middle-mobile visible-sm visible-xs">
                        <div class="col-md-3 col-sm-3 col-xs-3">
                            <div class="user-menu pull-left">
                                <a href="#" class="sidrLeft" href="#sidebarLeft" title="Menu">
                                    <i class="lni lni-menu"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 nopadd">
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3">

                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="_token_aux" value="{{ csrf_token() }}">
            <div class="navigator naver hidden-sm hidden-xs">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <nav class="navbar pull-left">
                                <ul class="nav navbar-nav">
                                    <li class="active"><a href="{{ route('cardapio.index', ['link='.$link]) }}"><i class="lni lni-home"></i> Ínicio</a></li>
                                    <li class="active"><a href="{{ route('cardapio.conta', ['link='.$link]) }}"><i class="lni lni-radio-button"></i> Minha conta</a></li>
                                    
                                    <li class="dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                            Categorias
                                            <i class="lni lni-chevron-down icon-right"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href="{{ route('cardapio.index', ['link='.$link]) }}">Todas</a></li>
                                            @foreach($categorias as $c)
                                            @if($c->produtos && sizeof($c->produtos) > 0)
                                            <li><a href="{{ route('cardapio.produtos-categoria', [$c->hash_delivery, 'link='.$link]) }}">{{ $c->nome }}</a></li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                    <li><a href="{{ route('cardapio.ofertas', ['link='.$link]) }}"><i class="lni lni-ticket"></i> Ofertas</a></li>

                                </ul>
                            </nav>
                            
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebars">
            <div id="sidebarLeft">
                <div class="sidebar">
                    <div class="sidebar-header">
                        <i class="close-sidebar lni lni-close" onclick="$.sidr('close', 'sidrLeft');"></i>
                        <div class="clear"></div>
                    </div>
                    <div class="sidebar-content">
                        <div class="sidebar-info">

                            <div class="content">
                                <a href="{{ route('cardapio.ofertas', ['link='.$link]) }}"><i class="lni lni-shift-right"></i> Ofertas</a>
                            </div>
                            <div class="content">
                                <a href="{{ route('cardapio.conta', ['link='.$link]) }}"><i class="lni lni-shift-right"></i> Meus Pedidos</a>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div><div id="modalalerta" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        <div id="modalcarrinho" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><i class="lni lni-close"></i></button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        <style>
            .menufixado{
                background-color:white;
                position: fixed;
                top: 65px;
                height: 50px;
                background-color:white;
                z-index:1
            }
        </style>
        <div class="sceneElement">
            <div class="container nopadd visible-xs visible-sm">
                <div class="grudado">
                    <div class="avatar">
                        <div class="holder">
                            <a href="{{ route('cardapio.index', ['link='.$link]) }}">
                                <img style="z-index:9" src="{{ $config->logo_img }}" alt="{{ $config->nome_restaurante }}" title="{{ $config->nome_restaurante }}" />
                            </a>
                        </div>
                    </div>
                </div>
                <div class="app-infos">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="title">{{ $config->nome_restaurante }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <span class="description">{{ $config->descricao_restaurante_pt }}</span>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="middle minfit" id="middle">

            @yield('content')
        </div>
    </div>
    <div class="footer hidden-sm">
        <div class="footer-info">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <span>{{ $config->rua }}, Nº {{ $config->numero }}, <br/>Bairro: {{ $config->bairro }}, CEP: {{ $config->cep }}, {{ $config->cidade->info }}<br/></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <a class="watermark" target="_blank">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="mdpedidos">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header fixed-bottom">
                    <h3 class="modal-title pull-left" id="tituloProduto">Pedidos</h3>
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="corpoPedidos"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="botao-acao btn text-white" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="mdpedidosdetalhes">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header fixed-bottom">
                    <h3 class="modal-title pull-left" id="tituloProduto">Detalhes do Pedido</h3>
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="corpoPedidosDetalhes"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="botao-acao btn text-white" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="mdcarrinho">
        <div class="modal-dialog" role="document" style="width: 90%">
            <div class="modal-content">
                <div class="modal-header fixed-bottom">
                    <h3 class="modal-title pull-left" id="tituloProduto">Carrinho</h3>
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="corpocarrino"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="floating-menu bg_sistema pull-center" style="text-align:center; padding:15px;">
        <div class="social">
            <a href="{{ route('cardapio.index', ['link='.$link]) }}" target="_self" style="text-align:center;margin-right:15px;">
                <table>
                    <tbody>
                        <tr>
                            <td style="text-align:center"><i class="lni lni-home"></i></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;color:white">Início</td>
                        </tr>
                    </tbody>
                </table>
            </a>

            <a href="{{ route('cardapio.conta', ['link='.$link]) }}" target="_self" style="text-align:center;margin-right:15px;">
                <table>
                    <tbody>
                        <tr>
                            <td style="text-align:center" valign="top"><i class="lni lni-list"></i> <span class="badge counterp" style="background-color:#ff0000d9;margin-right:-30px;margin-top:0px;display:none;position:absolute">0</span></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px;color:white">Minha conta</td>
                        </tr>
                    </tbody>
                </table>
            </a>

        </div>

    </div>
    <style>
      .floating-menu {
        background:white;
        padding: 5px;;
        width: 100%;
        z-index: 100;
        position: fixed;
        bottom: 0;
        right: 0;
    }

    .floating-menu a{
        font-size: 0.9em;
        display: block;
        margin: 0 0.5em;
        margin-top:5px;
    }
</style>
<script>


</script>
</div>
<div class="modal" tabindex="-1" role="dialog" id="mdProduto">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header fixed-bottom">
                <h3 class="modal-title pull-left" id="tituloProduto">Detalhes</h3>
                <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                    <i class="lni lni-close"></i>
                </button>
            </div>
            <div class="modal-body" style="max-height: 85vh;width:100%; overflow:scroll" id="corpoPagina">
            </div>
        </div>
    </div>
</div>

<div class="modal modal-loading" tabindex="-1" role="dialog">

</div>

<script src="/food-files/js/bootstrap.min.js"></script>
<script src="/food-files/js/jquery.sidr.min.js"></script>
<script src="/food-files/js/maskmoney.min.js"></script>
<script src="/food-files/js/jquery.maskedinput.min.js"></script>
<script src="/food-files/js/jquery.validate.min.js"></script>
<script src="/food-files/js/jquery.sticky.min.js"></script>
<script src="/food-files/js/template.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
<script src="/assets/js/toastr.min.js"></script>

@yield('js')

</body>
<script>
    let prot = window.location.protocol;
    let host = window.location.host;
    const path_url = prot + "//" + host + "/";
    var funcionamento = '1';
    sacola_count();      
    $(document).ready(function() {
        $('#mdcarrinho').one('hidden.bs.modal', function () {
            sacola_count();     
        })
        $('#mdProduto').one('hidden.bs.modal', function () {
            sacola_count();     
        })

    });

    function sacola_count() {
        $.post(path_url + 'api/delivery-link/carrinho-count-cardapio', 
        { 
            session_cart_cardapio: $('#session_cart_cardapio').val(),
            session_cart_user: $('#session_cart_user').val(),
        })
        .done(function(data) {

            if(data){
                $(".shop-bag .counter").html(data);
                $(".social .counter").css('display', 'inline-block');
                $(".social .counter").html(data);
            }else{
                $(".shop-bag .counter").html('0');
                $(".social .counter").html('0');
            }
        });
    }

    var buscaPedidos = null;

    function carregaPagina(link, md='mdProduto',corpo='corpoPagina'){
        $('#'+corpo).html('');

        $('#'+md).modal('show');
        clearInterval(buscaPedidos);
        buscaPedidos = null;

        if(md == 'mdpedidos'){
            if(buscaPedidos != null){
            }
            buscaPedidos = setInterval(function(){
                $.get(link, function (html) {
                    $('#'+corpo).html(html);
                    $('#_token_api').val($('#_token_aux').val())
                    $.LoadingOverlay("hide");

                });
            },5000); 
        }
        $.LoadingOverlay("show");
        $.get(link, function (html) {
            $('#'+corpo).html(html);
            $('#_token_api').val($('#_token_aux').val())
            $.LoadingOverlay("hide");

        });
    }

    toastr.options = {
        "progressBar": true
        , "onclick": null
        , "showDuration": "300"
        , "hideDuration": "1000"
        , "timeOut": "10000"
        , "extendedTimeOut": "1000"
        , "showEasing": "swing"
        , "hideEasing": "linear"
        , "showMethod": "fadeIn"
        , "hideMethod": "fadeOut"
    }
    @if(session()->has('flash_success'))
    toastr.success('{{ session()->get('flash_success') }}');
    @endif

    @if(session()->has('flash_error'))
    toastr.error('{{ session()->get('flash_error') }}');
    @endif

    @if(session()->has('flash_warning'))
    toastr.warning('{{ session()->get('flash_warning') }}');
    @endif

    function promptInstall() {
        if('serviceWorker' in navigator) {
            navigator.serviceWorker
            .register('serviceworker.js')
            .then(function() { console.log('Service Worker Registered'); });
        }
    }

    let deferredPrompt;


</script>
</html>
<script>
    function centraliza_menu(){
    }
    function getOffset(el) {
        const rect = el.getBoundingClientRect();
        return {
            left: rect.left + window.scrollX,
            top: rect.top + window.scrollY
        };
    }
    function showCategoria(id,el){

        var a = menu_topo.getElementsByTagName('a');
        for(var i=0; i<a.length;i++){
            a[i].classList.remove('active');
        }
        el.classList.add('active')

        var topo = getOffset(document.getElementById(id)).top-150
        window.scrollTo({
            top: topo,
            behavior: 'smooth',
        })
        $('.tv-infinite-menu').animate({ scrollLeft: el.offsetLeft-15 }, 500 );
    }
    var categorias = document.getElementsByClassName('categoria');

    $(window).scroll(function(){

        var top = $(window).scrollTop();

        if(top > 320){
            $('.search-bar-mobile').addClass('menufixado')
        } else {
            $('.search-bar-mobile').removeClass('menufixado')
        }
    });

    function testa(str,bus) {
        if(str.toUpperCase().includes(bus.toUpperCase())){
            return true;
        }else{
            return false;
        }
    }

    function MessageBoxPrompt(message){
        return $.MessageBox({
            buttonDone  : "Ok",
            buttonFail  : "Não",
            input   : true,
            message : message
        });
    }

    function copiartxt(campo){
        campo.select();
        campo.setSelectionRange(0, 9999999999)
        document.execCommand("copy");
        $.MessageBox('Copiado!')
    }

    $( document ).ready(function() {

        if(funcionamento != '1'){
            $.MessageBox("Aberto para pedidos.</br>Mas você pode conferir nosso cardápio .");
        }

        setInterval(function () {
            if(document.getElementById('next_banner') != undefined){
                console.log(next_banner);
                next_banner.click();
            }
        }, 4000);
    });

</script>