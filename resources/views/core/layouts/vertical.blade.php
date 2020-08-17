<body>
    <div class="wrapper">
        @include('core.partials.sidebar')

        {{--@include('admin::partials.navbar')--}}

        <div class="app-content content">
            <div class="content-wrapper" style="top: 0;min-height: 900px;">
                @yield('app')
            </div>
        </div>
    </div>

    <footer class="main-footer pt-1">
        <p class="clearfix blue-grey lighten-2 mb-0 text-center">
            <span class="text-center d-block d-md-inline-block mt-25">
                Powered by
                <a target="_blank" href="https://github.com/jqhph/dcat-admin">Dcat Core Demo</a>
                <span>&nbsp;·&nbsp;</span>
                v{{ config('core.version') }}
            </span>

            <button class="btn btn-primary btn-icon scroll-top pull-right" style="position: fixed;bottom: 2%; right: 10px;display: none">
                <i class="feather icon-arrow-up"></i>
            </button>
        </p>
    </footer>

</body>

</html>
