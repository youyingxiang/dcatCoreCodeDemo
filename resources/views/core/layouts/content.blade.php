@section('content')
    {!! $content !!}
@endsection

@section('app')
    {!! \App\Core\Layout\Asset::make()->styleToHtml() !!}

    <div class="content-header">
        @yield('content-header')
    </div>

    <div class="content-body" id="app">


        @yield('content')

    </div>

    {!! \App\Core\Layout\Asset::make()->scriptToHtml() !!}
@endsection

@include('core.layouts.page')
