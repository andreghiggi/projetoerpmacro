<!doctype html>
    <html lang="pt-br" @if(__tipoMenu() == 'horizontal') data-layout="topnav" @endif>


    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta content="Coderthemes" name="author" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{$title}}</title>

        <link rel="shortcut icon" href="/logo-sm.png">
        <script rel="stylesheet" src="/assets/js/config.js"></script>

        <link href="/assets/vendor/fullcalendar/main.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css">
        <link href="/assets/vendor/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />

        <link href="/assets/css/app.css" rel="stylesheet" type="text/css" id="app-style" />
        <!-- <link href="/assets/css/app-rtl.css" rel="stylesheet" type="text/css"/> -->
        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="/assets/css/toastr.min.css">
        <link rel="stylesheet" type="text/css" href="/css/style.css">

        <link href="/bs5-tour/css/bs5-intro-tour.css" rel="stylesheet"/>

        <link rel='stylesheet' href='/css/bootstrap-duallistbox.min.css'/>
        <link rel='stylesheet' href='/css/custom.css'/>
        <style type="text/css">
            :root {
                --leftbar-bg: url("{{ __tipoSmallHeader() }}");
            }
            
        </style>
        @yield('css')

    </head>
    <body>

        <div class="loader"></div>
        @if(isset(Auth::user()->empresa->empresa))
        <input type="hidden" value="{{ Auth::user()->empresa->empresa->id }}" id="empresa_id">
        @endif
        <input type="hidden" value="{{ Auth::user()->id }}" id="usuario_id">

        <div class="wrapper">
            <!-- ========== Topbar Start ========== -->
            <div class="navbar-custom">
                <div class="topbar container-fluid">
                    <div class="d-flex align-items-center gap-lg-2 gap-1" id="step1">

                        <!-- Topbar Brand Logo -->
                        <div class="logo-topbar">
                            <!-- Logo light -->
                            <a href="/" class="logo-light">
                                <span class="logo-lg">
                                    <img src="/logo-sm.png" alt="logo">

                                </span>
                                <span class="logo-sm">
                                    <img src="/logo-sm.png" alt="small logo">
                                </span>
                            </a>

                            <!-- Logo Dark -->
                            <a href="/" class="logo-dark">
                                <span class="logo-lg">
                                    <img src="/logo-sm.png" alt="dark logo">
                                </span>
                                <span class="logo-sm">
                                    <img src="/logo-sm.png" alt="small logo">
                                </span>
                            </a>
                        </div>

                        <!-- Sidebar Menu Toggle Button -->
                        <button class="button-toggle-menu">
                            <i class="ri-menu-2-fill"></i>
                        </button>

                        <!-- Horizontal Menu Toggle Button -->
                        <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </button>

                        <!-- Topbar Search Form -->
                        @if(__infoTopoMenu())
                        @if(Auth::user()->empresa && !__isContador())

                        <div class="app-search dropdown d-none d-lg-block">
                            <span class="badge border border-light shadow box-custom rounded">
                                <i class="ri-building-line text-primary"></i>
                                <span class="text-dark">EMPRESA:</span>
                                <span class="text-primary">{{ Auth::user()->empresa->empresa->nome }}</span>
                            </span>
                        </div>

                        <div class="app-search dropdown d-none d-lg-block">
                            <span class="badge border border-light shadow box-custom rounded">
                                <i class="ri-server-line text-primary"></i>
                                <span class="text-dark">AMBIENTE:</span>
                                <span class="text-primary">{{ Auth::user()->empresa->empresa->ambiente == 2 ? 'HOMOLOGAÇÃO' : 'PRODUÇÃO' }}</span>
                            </span>
                        </div>

                        @endif

                        @if(sizeof(Auth::user()->acessos) > 0)

                        <div class="app-search dropdown d-none d-lg-block">
                            <span class="badge border border-light shadow box-custom rounded">
                                <i class="ri-wifi-line text-primary"></i>
                                <span class="text-dark">IP:</span>

                                <span class="text-primary">{{ Auth::user()->acessos ? Auth::user()->acessos->first()->ip : '' }}</span>
                            </span>
                        </div>

                        @endif

                        @if(Auth::user()->empresa && __isContador())
                        <div class="d-lg-block d-none d-md-inline-block selecionar-empresa-contador">
                            <div class="app-search dropdown d-none d-lg-block">
                                <span class="badge border border-light shadow rounded">
                                    <i class="ri-building-2-fill text-danger"></i>
                                    <span class="text-dark">Empresa selecionada:</span>

                                    <span class="text-primary">
                                        {{ Auth::user()->empresa->empresa->empresa_selecionada != null ? Auth::user()->empresa->empresa->empresaSelecionada->info : '--' }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        @endif

                        @if(Auth::user()->empresa && Auth::user()->empresa->empresa->plano)
                        @if(Auth::user()->empresa->empresa->receber_com_boleto == 0)

                        <div class="app-search dropdown d-lg-block d-none d-md-inline-block">

                            <div class="app-search dropdown d-none d-lg-block">
                                <span class="badge border border-light shadow box-custom rounded">
                                    <i class="ri-vip-crown-fill text-warning"></i>
                                    <span class="text-dark">PLANO:</span>

                                    <span class="text-primary">
                                        {{ Auth::user()->empresa->empresa->plano->plano->nome }}</strong> - até:
                                        <strong>{{ __data_pt(Auth::user()->empresa->empresa->plano->data_expiracao, 0) }}</strong>
                                    </span>
                                </span>
                            </div>

                        </div>

                        <div class="app-search dropdown d-lg-block d-none d-md-inline-block">
                            @if(__usuarioEscolherPlano())
                            <a href="{{ route('upgrade.index') }}">

                                <span class="badge border border-light shadow box-custom rounded">
                                    <i class="ri-star-smile-fill text-danger"></i>
                                    <span class="text-dark">Fazer upgrade</span>
                                </span>
                            </a>

                            @else
                            @if(__periodoExpirar())
                            <a href="{{ route('upgrade.assinatura') }}">

                                <span class="badge border border-light box-custom shadow rounded">
                                    <i class="ri-award-fill text-danger"></i>
                                    <span class="text-dark">Renovar assinatura</span>
                                </span>
                            </a>
                            @endif
                            @endif
                        </div>


                        @if(env("APP_ENV") == "demo")
                        <div class="app-search dropdown d-lg-block d-none d-md-inline-block">
                            <div class="app-search dropdown d-none d-lg-block">
                                <button class="badge bg-success border border-light shadow p-1 rounded-3" id="click-modal-dev">
                                    <i class="ri-code-box-line text-white"></i>
                                    <span class="text-white">DADOS DO DESENVOLVEDOR</span>
                                </button>
                            </div>
                        </div>
                        @endif

                        @else

                        @if(env("APP_ENV") == "demo")
                        <div class="app-search dropdown d-lg-block d-none d-md-inline-block">
                            <div class="app-search dropdown d-none d-lg-block">
                                <button class="badge bg-success border border-light shadow p-1 rounded-3" id="click-modal-dev">
                                    <i class="ri-code-box-line text-white"></i>
                                    <span class="text-white">DADOS DO DESENVOLVEDOR</span>
                                </button>
                            </div>
                        </div>
                        @endif

                        @endif
                        @endif

                        <div class="app-search dropdown d-lg-block video d-none d-md-inline-block">
                        </div>
                        @endif

                        @if(session()->has('impersonate'))
                        <div class="app-search dropdown d-lg-block d-none d-md-inline-block">
                            <a href="{{ route('impersonate.stop') }}">
                                <span class="badge border border-light shadow box-custom rounded">
                                    <i class="ri-settings-fill text-success"></i>
                                    <span class="text-dark">Voltar para superadmin</span>
                                </span>
                            </a>   
                        </div>
                        @endif

                        @if(session()->has('impersonate_contador'))
                        <div class="app-search dropdown d-lg-block d-none d-md-inline-block">
                            <a href="{{ route('impersonate-contador.stop') }}">
                                <span class="badge border border-light shadow box-custom rounded">
                                    <i class="ri-settings-fill text-success"></i>
                                    <span class="text-dark">Voltar para menu contador</span>
                                </span>
                            </a>   
                        </div>
                        @endif
                    </div>

                    <ul class="topbar-menu d-flex align-items-center gap-3">
                        <!-- inicio alertas -->
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="ri-notification-3-fill fs-22"></i>
                                <div class="spinner-border spinner-border-sm text-danger" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                <span class="noti-icon-badge d-none"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0" style="">
                                <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fs-16 fw-medium"> Notificações</h6>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('notificacao.clear-all') }}" class="text-dark text-decoration-underline">
                                                <small>Limpar tudo</small>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div style="max-height: 300px;" data-simplebar="init"><div class="simplebar-wrapper" style="margin: 0px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden scroll;"><div class="simplebar-content alertas-main" style="padding: 0px;">


                                </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 432px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 208px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></div>


                            </div>
                        </li>

                        @if(!__isContador())
                        @if(__isActivePlan(Auth::user()->empresa, 'PDV'))
                        @can('pdv_create')
                        <li class="d-none d-sm-inline-block">
                            <a title="PDV" class="nav-link" href="{{ route('frontbox.create')}}">
                                <i class="ri-shopping-basket-2-fill fs-22"></i>
                            </a>
                        </li>

                        <li class="d-block d-md-none">
                            <a title="PDV" class="nav-link" href="{{ route('pdv-mobo.index')}}">
                                <i class="ri-shopping-basket-2-fill fs-22"></i>
                            </a>
                        </li>
                        @endcan
                        @endif
                        @endif

                        <li class="d-none d-sm-inline-block" id="step2">
                            <div class="nav-link" id="light-dark-mode">
                                <i class="ri-moon-fill fs-22"></i>
                            </div>
                        </li>

                        <li class="d-none d-md-inline-block">
                            <a class="nav-link" href="" data-toggle="fullscreen">
                                <i class="ri-fullscreen-line fs-22"></i>
                            </a>
                        </li>

                        <li class="dropdown me-md-2" id="step3">
                            <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <span class="account-user-avatar">

                                    <i class="ri-user-line fs-22"></i>

                                </span>
                                <span class="d-lg-flex flex-column gap-1 d-none">
                                    <h5 class="my-0"> {{ Auth::user()->name }}</h5>
                                    <h6 class="my-0 fw-normal">{{ Auth::user()->tipo }}</h6>

                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                                <!-- item-->
                                <div class=" dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Bem vindo!</h6>
                                </div>

                                @if(!__isContador())
                                <a href="{{ route('usuarios.profile', Auth::user()->id) }}" class="dropdown-item">
                                    <i class="ri-account-circle-fill align-middle me-1"></i>
                                    <span>Minha Conta</span>
                                </a>

                                <!-- item-->
                                <a href="{{ route('config.index') }}" class="dropdown-item">
                                    <i class="ri-settings-4-fill align-middle me-1"></i>
                                    <span>Configuração</span>
                                </a>

                                <a href="{{ route('ticket.index') }}" class="dropdown-item">
                                    <i class="ri-information-fill align-middle me-1"></i>
                                    <span>Abrir chamado</span>
                                </a>
                                @else

                                <a href="{{ route('contador.profile') }}" class="dropdown-item">
                                    <i class="ri-account-circle-fill align-middle me-1"></i>
                                    <span>Minha Conta</span>
                                </a>

                                @endif

                                <!-- item-->
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="ri-logout-box-line  align-middle me-1"></i>
                                    Sair
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- ========== Topbar End ========== -->

            <!-- init:: Menu -->
            @if(__tipoMenu() == 'horizontal')
            @include('layouts.menu.horizontal')
            @else
            @include('layouts.menu.vertical')
            @endif
            <!-- end:: Menu -->

            <div class="content-page">
                <div class="content" style="margin-left: -5px; margin-right: -5px;">
                    <div class="@if(__tipoMenu() == 'vertical') container-fluid @endif" @if(__tipoMenu() == 'horizontal') style="margin-top: -15px" @endif>

                        @yield('content')

                    </div>
                </div>
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <script>
                                    document.write(new Date().getFullYear())

                                </script> {{ env("APP_NAME") }}
                            </div>
                        </div>
                    </div>
                </footer>
            </div>

            @if(!isset($not_loading))
            <div class="control-loading">
                <div class="modal-loading loading-class"></div>
            </div>
            @endif

            <div class="modal fade" id="modal-dev" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">DADOS DO DESENVOLVEDOR</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h4>Marcos Mello - Slym Software</h4>
                            <a href="https://wa.me/5543920004769">WhatsApp <strong>43920004769</strong></a>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-empresas-contador" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Selecionar Empresa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Empresa</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-crm-notificacao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Notificações CRM</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-financeiro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Financeiro</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-alerta-certificado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Alerta de certificado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-notificacao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Notificações</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-notificacao-delivery" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Notificações de Delivery</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-notificacao-ecommerce" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Notificações de Ecommerce</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <button id="btn-top" class="btn btn-primary rounded-circle">
                <i class="ri-arrow-up-line fs-5"></i>
            </button>

            <script type="text/javascript">
                let prot = window.location.protocol;
                let host = window.location.host;
                const path_url = prot + "//" + host + "/";

            </script>
            <script src="/assets/js/vendor.min.js"></script>
            <script src="/assets/vendor/select2/js/select2.min.js"></script>
            <script src="/assets/js/toastr.min.js"></script>
            <script src="/assets/vendor/dropzone/dropzone.js"></script>
            <script src="/assets/js/pages/component.fileupload.js"></script>
            <script src="/assets/vendor/daterangepicker/moment.min.js"></script>
            <script src="/assets/vendor/daterangepicker/daterangepicker.js"></script>
            <script src="/assets/vendor/jquery-mask-plugin/jquery.mask.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js" integrity="sha512-F5Ul1uuyFlGnIT1dk2c4kB4DBdi5wnBJjVhL7gQlGh46Xn0VhvD8kgxLtjdZ5YN83gybk/aASUAlpdoWUjRR3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

            <script src="/assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js"></script>
            <script src="/assets/vendor/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js"></script>
            <script src="/assets/vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
            <script src="/assets/vendor/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

            <script src='/js/jquery.bootstrap-duallistbox.min.js'></script>

            <script type="text/javascript">
                var casas_decimais_qtd = '{{ __casas_decimais_quantidade() }}';
            </script>

            @if(__isContador())
            <script src="/js/contador.js"></script>
            @endif
            <script src="/js/uploadImagem.js"></script>
            <script type="text/javascript" src="/js/jquery.mask.min.js"></script>
            <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
            <script src="/assets/js/app.js"></script>
            <script type="text/javascript" src="/js/main.js"></script>
            <script src="/assets/vendor/flatpickr/flatpickr.min.js"></script>
            <script src="/assets/js/pages/demo.flatpickr.js"></script>

            @if(__isNotificacao(Auth::user()->empresa) && Auth::user()->notificacao_cardapio)
            <script src="/js/notificacao.js"></script>
            @endif

            @if(__isNotificacaoMarketPlace(Auth::user()->empresa) && Auth::user()->notificacao_marketplace)
            <script src="/js/notificacao_marketplace.js"></script>
            @endif

            @if(__isNotificacaoEcommerce(Auth::user()->empresa) && Auth::user()->notificacao_ecommerce)
            <script src="/js/notificacao_ecommerce.js"></script>
            @endif

            @yield('js')

            <script type="text/javascript">
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

                @if(session()->has('swal_success'))
                swal("Sucesso", '{{ session()->get('swal_success') }}', "success")
                @endif

                @if(session()->has('flash_success'))
                toastr.success('{{ session()->get('flash_success') }}');
                @endif

                @if(session()->has('flash_error'))
                toastr.error('{{ session()->get('flash_error') }}');
                @endif

                @if(session()->has('flash_warning'))
                toastr.warning('{{ session()->get('flash_warning') }}');
                @endif

                @if(!Auth::user()->sidebar_active)
                $(html).attr('data-sidenav-size', 'condensed')
                @endif

                @if(session()->has('flash_modal_crm'))
                modalCrmNotification()
                @endif

                @if(session()->has('flash_financeiro'))
                modalFinanceiro()
                @endif

                @if(session()->has('flash_alerta_certificado'))
                modalAlertaCertificado()
                @endif

                $(html).attr('data-topbar-color', '{{ __dataTopBar() }}')
                $(html).attr('data-menu-color', '{{ __dataMenuBar() }}')
                $(html).attr('data-bs-theme', '{{ __dataThemeDefault() }}')

                window.addEventListener("load", () => {
                    setTimeout(() => {
                        document.querySelector(".loader").classList.add("loader--hidden")
                    }, 100)
                })

                @if(__dataTopBar() == 'brand')
                $('.box-custom').addClass('bg-white')
                $('.ri-menu-2-fill').addClass('text-white')
                @endif

                function audioError(){
                    var audio = new Audio('/audio/error.mp3');
                    audio.addEventListener('canplaythrough', function() {
                        audio.play();
                    });
                }

                $('#click-modal-dev').click(() => {
                    $('#modal-dev').modal('show')
                })

                function modalCrmNotification(){
                    $.get(path_url+'api/crm/modal', {empresa_id: $('#empresa_id').val()})
                    .done((res) => {
                        $('#modal-crm-notificacao').modal('show')
                        $('#modal-crm-notificacao .modal-body').html(res)
                    }).fail((err) => {
                        console.log(err)
                    })
                }

                function modalFinanceiro(){
                    setTimeout(() => {
                        $.get(path_url+'api/financeiro-boleto/modal', {empresa_id: $('#empresa_id').val()})
                        .done((res) => {
                            // console.log(res)
                            $('#modal-financeiro').modal('show')
                            $('#modal-financeiro .modal-body').html(res)
                        }).fail((err) => {
                            console.log(err)
                        })
                    }, 1000)
                }

                function modalAlertaCertificado(){
                    setTimeout(() => {
                        $('#modal-alerta-certificado').modal('show')
                        $('#modal-alerta-certificado .modal-body').html('{{ session()->get('flash_alerta_certificado') }}')
                    }, 1000)
                }
            </script>

            <script src="/bs5-tour/js/bs5-intro-tour.js"></script>
            <script src="/js/tour.js"></script>
        </body>
        </html>
