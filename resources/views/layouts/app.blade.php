<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.include.head')
    {{-- @notifyCss --}}
</head>

<body onload="startTime()" id="app-container" class="menu-sub-hidden right-menu ltr rounded">
    @if(Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
    </div>
@endif

@if(Session::has('error'))
    <div class="alert alert-danger">
        {{ Session::get('error') }}
    </div>
@endif
    
    @include('layouts.include.navbar')
    
    @include('layouts.include.menus')
    
    <main>
        @yield('content')
    </main>
    
    @include('layouts.include.footer')
    
    @include('layouts.include.script')
    {{-- @include('notify::messages') --}}
    
    {{-- @notifyJs --}}
</body>

</html>