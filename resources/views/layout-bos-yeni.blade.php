<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Doruk Otomasyon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="shortcut icon" href="/img/favicon.png">
    <link href="{{URL::asset("assets/css/bootstrap.min.css")}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{URL::asset("assets/css/icons.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{URL::asset("assets/css/doruk.css")}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://unpkg.com/vue-select@3/dist/vue-select.css">

    @yield('style')
</head>

<body data-layout="detached" data-topbar="colored">
    <div id="app" class="container-fluid">
        <div id="layout-wrapper" ref="layoutWrapper">
            <div class="row doruk-content">
                @yield('content')
            </div>
        </div>
    </div>

    @yield("script")

    <!-- JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="{{URL::asset("assets/libs/jquery/jquery.min.js")}}"></script>
    <script src="{{URL::asset("assets/libs/bootstrap/js/bootstrap.bundle.min.js")}}"></script>
    <script src="{{URL::asset("assets/libs/metismenu/metisMenu.min.js")}}"></script>
    <script src="{{URL::asset("assets/libs/simplebar/simplebar.min.js")}}"></script>
    <script src="{{URL::asset("assets/libs/node-waves/waves.min.js")}}"></script>
    <script src="{{URL::asset("assets/libs/jquery-sparkline/jquery.sparkline.min.js")}}"></script>
    <script src="{{URL::asset("assets/js/app.js")}}"></script>

    <!-- axios cdn -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.2/axios.min.js"></script>
    <!-- momentjs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment-with-locales.min.js"></script>


    <script>
        moment.locale("tr");

        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


        @if (in_array(request()->getHost(), ['localhost', 'localhost:8000', "dev.doruk.kodgaraj.com"]))
            let vm =
        @endif
        new Vue({
            mixins: [mixinApp],
            el: '#app',
            data: {
                isNativeApp: !!window.isNativeApp,
                isMobile: window.innerWidth < 600,
            },
            computed: {
                m() {
                    return moment;
                }
            },
            mounted() {
                this.$nextTick(() => {
                    this.isMobile = window.innerWidth < 600;
                });
            },
            methods: {

            },
        });
    </script>

    <style>
        /* width */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #DDD;
            border-radius: 4px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #929292cc;
            border-radius: 4px;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #939393;
        }

        .sidenav#doruk-side-nav {
            height: 100%; /* 100% Full-height */
            width: 0; /* 0 width - change this with JavaScript */
            position: fixed; /* Stay in place */
            z-index: 10; /* Stay on top */
            top: 0; /* Stay at the top */
            left: 0;
            background-color: #ffffff; /* Black*/
            overflow-x: hidden; /* Disable horizontal scroll */
            padding-top: 25px; /* Place content 60px from the top */
            transition: 0.3s; /* 0.5 second transition effect to slide in the sidenav */
            /* box-shadow: 0px 0 5px grey; */
        }

        .doruk-side-nav-overlay {
            position: fixed; /* Stay in place */
            z-index: 5; /* Sit on top */
            top: 0; /* Stay at the top */
            left: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: #000000; /* Black*/
            opacity: 0; /* Set opacity to 0.5 */
            display: none;
            transition: all .3s;
        }

        .sidenav#doruk-side-nav a {
            padding: 8px 8px 8px 32px;
            display: block;
            white-space: nowrap;
            transition: 0.3s;
        }

        .sidenav#doruk-side-nav .closebtn {
            position: absolute;
            top: 0;
            right: 16px;
            font-size: 36px;
            margin-left: 50px;
        }

        .sidenav#doruk-side-nav li {
            padding-right: 12px;
        }

        @media print {
            .page-break-after {
                page-break-after: always;
            }
        }
    </style>

</body>

</html>
