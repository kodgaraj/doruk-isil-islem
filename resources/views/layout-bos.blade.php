<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Doruk Otomasyon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="shortcut icon" href="/img/favicon.png">
    <link href="../assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/doruk.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://unpkg.com/vue-select@3/dist/vue-select.css">

    <style>
        table td.en-kisa-uzunluk {
            max-width: 50px;
        }

        table td.kisa-uzunluk {
            min-width: 100px;
        }

        table td.orta-uzunluk {
            min-width: 150px;
        }

        table td.uzun-uzunluk {
            min-width: 200px;
        }

        table td.en-uzun-uzunluk {
            min-width: 275px;
        }

        table td.align-left {
            text-align: left !important;
        }

        table td.align-right {
            text-align: right !important;
        }

        table td.align-center {
            text-align: center !important;
        }
        .kg-resim-sec {
            transition: filter .3s;
            width: 64px;
            height: 64px;
            border: 1px solid #666;
            border-radius: 4px;
        }

        .kg-resim-sec:hover {
            position: relative;
            filter: contrast(30%);
            cursor: pointer;
        }
    </style>
    @yield('style')
</head>

<body data-layout="detached" data-topbar="colored">
    <div id="app" class="container-fluid">
        <div id="layout-wrapper" ref="layoutWrapper">
            <div class="row doruk-content">
                @yield('content')
            </div>
        </div>

        <div id="global-print-area" ref="globalPrintArea" style="display: none" v-show="yazdirmaObjesi.goster">
            <div v-html="yazdirmaObjesi.html"></div>
        </div>
    </div>

    @yield("script")

    <!-- JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="../assets/libs/jquery/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../assets/libs/node-waves/waves.min.js"></script>
    <script src="../assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>
    <script src="../assets/js/app.js"></script>

    <!-- axios cdn -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.2/axios.min.js"></script>
    <!-- v-mask -->
    <script src="https://cdn.jsdelivr.net/npm/v-mask/dist/v-mask.min.js"></script>
    <!-- v-money -->
    <script src="https://cdn.jsdelivr.net/npm/v-money@0.8.1/dist/v-money.min.js"></script>
    <!-- lodash -->
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <!-- momentjs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment-with-locales.min.js"></script>
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- or point to a specific vue-select release -->
    <script src="https://unpkg.com/vue-select@3.20.0/dist/vue-select.js"></script>

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>

    <script>
        moment.locale("tr");

        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        Vue.use(VMoney, { precision: 2 });
        Vue.use(VueMask.VueMaskPlugin);
        Vue.component('v-select', VueSelect.VueSelect);

        @if (in_array(request()->getHost(), ['localhost', 'localhost:8000', "dev.doruk.kodgaraj.com"]))
            let vm = 
        @endif
        new Vue({
            mixins: [mixinApp],
            el: '#app',
            data: {
                isNativeApp: !!window.isNativeApp,
                isMobile: window.innerWidth < 600,
                yazdirmaObjesi: {
                    goster: false,
                    html: ""
                },
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
                globalYazdir(html, options = {}) {
                    this.yazdirmaObjesi.html = html;
                    this.$refs.layoutWrapper.style.display = "none";
                    this.yazdirmaObjesi.goster = true;

                    if (typeof options.beforePrint === "function") {
                        options.beforePrint(this.$refs.globalPrintArea);
                    }

                    setTimeout(() => {
                        print();
                        this.$refs.layoutWrapper.style.display = "block";
                        this.yazdirmaObjesi.goster = false;
                        this.yazdirmaObjesi.html = "";

                        if (typeof options.afterPrint === "function") {
                            options.afterPrint();
                        }
                    }, 750)
                },
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