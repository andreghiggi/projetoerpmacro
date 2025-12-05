<!doctype html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{$title}}</title>

        <link rel="shortcut icon" href="/logo-sm.png">
        <link href="/assets/vendor/fullcalendar/main.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/daterangepicker/daterangepicker.css">
        <link href="/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css">
        <script rel="stylesheet" src="/assets/js/config.js"></script>
        <link href="/assets/css/app.css" rel="stylesheet" type="text/css" id="app-style" />
        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="/assets/css/toastr.min.css">
        <link rel="stylesheet" type="text/css" href="/css/style_pdv.css">
        <link rel='stylesheet' href='/css/bootstrap-duallistbox.min.css'/>

        @yield('css')

    </head>
    <body>
        <div class="loader"></div>

        @if(isset(Auth::user()->empresa->empresa))
        <input type="hidden" value="{{ Auth::user()->empresa->empresa->id }}" id="empresa_id">
        @endif
        <input type="hidden" value="{{ Auth::user()->id }}" id="usuario_id">

        <div class="wrapper">
            <div class="content">
                @yield('content')
            </div>
        </div>

        {{-- <button type="button" class="nav-link btn-fullscreen" data-toggle="fullscreen">
            <i class="ri-fullscreen-line fs-22"></i>
        </button> --}}

        @if(!isset($not_loading))
        <div class="modal-loading loading-class"></div>
        @endif

        @include('modals._suprimento_caixa', ['not_submit' => true])
        @include('modals._sangria_caixa', ['not_submit' => true])
        
        <script type="text/javascript">
            let prot = window.location.protocol;
            let host = window.location.host;
            const path_url = prot + "//" + host + "/";
            var casas_decimais_qtd = '{{ __casas_decimais_quantidade() }}';
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
        <script src="/js/uploadImagem.js"></script>
        <script type="text/javascript" src="/js/jquery.mask.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
        <script src='/js/jquery.bootstrap-duallistbox.min.js'></script>

        <script src="/assets/js/app.js"></script>
        <script type="text/javascript" src="/js/main.js"></script>

        <script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>   
        <script src="/assets/vendor/flatpickr/flatpickr.min.js"></script>
        <script src="/assets/js/pages/demo.flatpickr.js"></script>
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
            @if(session()->has('flash_success'))
            toastr.success('{{ session()->get('flash_success') }}');
            @endif

            @if(session()->has('flash_error'))
            toastr.error('{{ session()->get('flash_error') }}');
            @endif

            @if(session()->has('flash_warning'))
            toastr.warning('{{ session()->get('flash_warning') }}');
            @endif

            window.addEventListener("load", () => {
                document.querySelector(".loader").classList.add("loader--hidden")
            })
        </script>

    </body>
    </html>
