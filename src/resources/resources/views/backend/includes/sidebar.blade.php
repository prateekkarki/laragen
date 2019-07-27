<div class="main-sidebar sidebar-style-2">

<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="{{ route('backend.dashboard') }}">{{ config('app.name', 'Laragen Dashboard') }}</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ route('backend.dashboard') }}">Lg</a>
    </div>
    
    @include('backend.includes.main_menu')
</aside>
    
</div>