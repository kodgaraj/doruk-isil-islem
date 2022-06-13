<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Doruk Otomasyon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/img/favicon.png">
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/doruk.css" rel="stylesheet" type="text/css" />

    <style>
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
    </style>
    @yield('style')
</head>

<body data-layout="detached" data-topbar="colored">
    <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>

    <div id="app" class="container-fluid">
        <div id="layout-wrapper">
            <div class="vertical-menu">
                <div class="h-100">
                    <!--- Sidemenu close button -->
                    <div v-if="sidebarButonDurum" class="position-absolute top-0 end-0 m-2">
                        <button @click="sidebarAcKapat(false)" class="btn btn-outline-danger btn-sm">
                            <i class="mdi mdi-close-circle-outline"></i>
                        </button>
                    </div>
                    <div class="user-wid text-center py-4">
                        <div class="text-center">
                            <img src="img/doruk-logo.png" alt="">
                        </div>
                    </div>
                    <div id="sidebar-menu">
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li>
                                <a href="{{ route('home') }}" class="waves-effect">
                                    <i class="mdi mdi-home"></i> Anasayfa
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('siparis-formu') }}" class=" waves-effect">
                                    <i class="mdi mdi-tag-plus-outline"></i> Sipariş Formu
                                </a>
                            </li>
                            <li>
                                <a href="{{ route("isil-islemler") }}" class=" waves-effect">
                                    <i class="mdi mdi-stove"></i> Isıl İşlem Formları
                                </a>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="mdi mdi-account-multiple-outline"></i> Yönetim
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <li><a href="#">Fırınlar</a></li>
                                    <li><a href="#">Sepetler</a></li>
                                    <li><a href="#">Firmalar</a></li>
                                    <li><a href="#">Kullanıcılar</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="main-content">
                <div class="page-content mb-5">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <!-- sidebar button -->
                                <div class="d-inline-flex align-items-center justify-content-between">
                                    <div v-if="sidebarButonDurum" class="me-2">
                                        <button @click="sidebarAcKapat(true)" class="btn btn-outline-light btn-sm">
                                            <i class="fas fa-bars"></i>
                                        </button>
                                    </div>
                                    <a href="{{ route('home') }}" class="waves-effect">
                                        <h4 class="page-title mb-0 font-size-18">ISIL İŞLEM TAKİP OTOMASYONU</h4>
                                    </a>
                                </div>
                                <div class="page-title-right">
                                    <div class="float-end">
                                        <div class="dropdown d-none d-lg-inline-block ms-1">
                                            <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                                                <i class="mdi mdi-fullscreen"></i>
                                            </button>
                                        </div>
                                        <div class="dropdown d-inline-block">
                                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-bell-outline"></i>
                                                <span class="badge rounded-pill bg-danger">1</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                                                <div class="p-3">
                                                    <div class="row align-items-center">
                                                        <div class="col">
                                                            <h6 class="m-0"> Bildirimler </h6>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a href="#!" class="small"> Tümünü gör</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div data-simplebar style="max-height: 230px;">
                                                    <a href="" class="text-reset notification-item">
                                                        <div class="d-flex align-items-start">
                                                            <div class="avatar-xs me-3">
                                                                <span class="avatar-title bg-primary rounded-circle font-size-16">
                                                                <i class="bx bx-badge-check"></i>
                                                            </span>
                                                            </div>
                                                            <div class="flex-1">
                                                                <h6 class="mt-0 mb-1">Bildirim başlığı</h6>
                                                                <div class="font-size-12 text-muted">
                                                                    <p class="mb-1">Bildirim açıklaması</p>
                                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 12.05.2022 15.18.55</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown d-inline-block">
                                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="ms-1">Admin</span>
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item d-block" href="#"><i
                                                    class="bx bx-wrench font-size-16 align-middle me-1"></i> Ayarlar</a>

                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i
                                                    class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> Çıkış</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row doruk-content mt-5 pt-3">
                        @yield('content')
                    </div>
                </div>
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-6">
                                &copy; @{{ new Date().getFullYear() }}
                            </div>
                            <div class="col-6">
                                <div class="text-end">
                                    KodGaraj
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    @yield("script")

    <!-- JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>
    <script src="assets/js/app.js"></script>

    <!-- axios cdn -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <!-- v-mask -->
    <script src="https://cdn.jsdelivr.net/npm/v-mask/dist/v-mask.min.js"></script>
    <!-- lodash -->
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <!-- momentjs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment-with-locales.min.js"></script>
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- or point to a specific vue-select release -->
    <script src="https://unpkg.com/vue-select@3"></script>
    <link rel="stylesheet" href="https://unpkg.com/vue-select@3/dist/vue-select.css">

    <script>
        moment.locale("tr");

        Vue.use(VueMask.VueMaskPlugin);
        Vue.component('v-select', VueSelect.VueSelect);

        new Vue({
            mixins: [mixinApp],
            el: '#app',
            data: {
                yukleniyor: false,
                sidebarButonDurum: false,
            },
            computed: {
                m() {
                    return moment;
                }
            },
            mounted() {
                this.$nextTick(() => {
                    this.sidebarButonDurum = window.innerWidth < 992;
                });
            },
            methods: {
                uyariAc(obje) {
                    Swal.fire({
                        title: obje.baslik,
                        text: obje.mesaj,
                        type: obje.tur,
                        ...obje.ozellikler
                    });
                },
                yukleniyorDurum(durum) {
                    this.yukleniyor = durum;
                },
                sidebarAcKapat(durum = null) {
                    if (durum === null) {
                        document.body.classList.toggle('sidebar-enable');
                    } else {
                        if (durum) {
                            document.body.classList.add('sidebar-enable');
                        } else {
                            document.body.classList.remove('sidebar-enable');
                        }
                    }
                },
            },
        });
    </script>

</body>

</html>