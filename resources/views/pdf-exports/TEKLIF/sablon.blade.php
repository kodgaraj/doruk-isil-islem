@extends('layout-bos-yeni')
@section('style')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://unpkg.com/quill-table-ui@1.0.5/dist/index.css" rel="stylesheet">
<link rel="stylesheet" href="/css/print.css">

<style>

    .printable-page {
        margin: 0 !important;
    }

    .printable-area-content {
        min-height: 580px;
        width: 100% !important;
    }
</style>
@endsection
@section('content')

    <div ref="teklif" class="a4" style="background: white; color: black; font-size: 12px;">
        <!-- 1. sayfa -->
        <div v-for="(html, index) in data.icerik_html" class="printable-page" :id="index">
            <div class="row ql-editor d-flex justify-content-center align-items-center">
                <div v-html="html"></div>
            </div>
        </div>

    </div>
@endsection

@section("script")
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        let mixinApp = {
            data() {
                return {
                    data: @json($teklif),
                    yazdir: false,
                };
            }

        };
    </script>
@endsection
