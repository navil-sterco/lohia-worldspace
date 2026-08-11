@php
    $header = headerData();
    $sidebar = sidebar();
    $footer = footerData();
    $socialIcons = socialIcons();

    // Normalize current path once, reuse for every menu loop below
    $currentPath = trim(request()->path(), '/'); // '' for home, 'about-us', 'projects/lohia-one', etc.
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="author" content="Sterco Digitex">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'Lohia')</title>
    <meta name="description" content="@yield('description', 'Lohia Worldspace')">

    <link rel="shortcut icon" href="{{ url('frontend-assets/images/favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ url('frontend-assets/css/style.css') }}">
    @if(request()->is('/') || request()->is('home'))
        <link rel="stylesheet" href="{{ url('frontend-assets/css/home.css') }}">
    @else
        <link rel="stylesheet" href="{{ url('frontend-assets/css/inner-page.css') }}">
    @endif

    @stack('styles')
</head>

<body>
    <header class="main_header">
        <div class="container-fluid">
            <a href="{{ url('/') }}" class="site_navbrand">
                <img src="{{ url('frontend-assets/images/site-logo.svg') }}" alt="Lohia" class="img-fluid w-100">
            </a>

            <ul class="site_nav">
                @foreach($header as $menu)
                    @php
                        $menuSlug = trim($menu['slug'] ?? '', '/');
                        $isActive = $currentPath === $menuSlug
                            || ($menuSlug !== '' && Str::startsWith($currentPath, $menuSlug . '/'));
                    @endphp
                    <li class="{{ $isActive ? 'active' : '' }}">
                        <a href="{{ url($menu['slug']) }}"
                        @if(($menu['target_blank'] ?? false))
                            target="_blank" rel="noopener noreferrer"
                        @endif
                        @if($isActive) aria-current="page" @endif>
                            {{ $menu['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <button class="hamb_btn" type="button">
                <img src="{{ url('frontend-assets/images/hemburgure-icon.svg') }}" alt="Menu" class="img-fluid">
            </button>
        </div>
    </header>

    <div class="hamb_header">
        <div class="hamb_wrapper">
            <button class="hamb_close">
                <img src="{{ url('frontend-assets/images/hemburgure-close.svg') }}" alt="Close" class="img-fluid">
            </button>

            <div class="hamb_menu">
                <ul>
                    @foreach($sidebar as $menu)
                        @php
                            $menuSlug = trim($menu['slug'] ?? '', '/');
                            $isActive = $currentPath === $menuSlug
                                || ($menuSlug !== '' && Str::startsWith($currentPath, $menuSlug . '/'));
                        @endphp
                        <li class="{{ $isActive ? 'active' : '' }}">
                            <a href="{{ url($menu['slug']) }}"
                            @if(($menu['target_blank'] ?? false))
                                target="_blank" rel="noopener noreferrer"
                            @endif
                            @if($isActive) aria-current="page" @endif>{{ $menu['title'] }}</a>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ url('/request-site-visit') }}" class="theme_btn">
                    Request Site Visit
                </a>
            </div>

            <div class="hamb_quick">
                <ul>
                    @foreach($footer as $menu)
                        @php
                            $menuSlug = trim($menu['slug'] ?? '', '/');
                            $isActive = $currentPath === $menuSlug
                                || ($menuSlug !== '' && Str::startsWith($currentPath, $menuSlug . '/'));
                        @endphp
                        <li class="{{ $isActive ? 'active' : '' }}">
                            <a href="{{ url($menu['slug']) }}"
                                @if(($menu['target_blank'] ?? false))
                                    target="_blank" rel="noopener noreferrer"
                                @endif
                                @if($isActive) aria-current="page" @endif>{{ $menu['title'] }}</a>
                        </li>
                    @endforeach
                </ul>

                <div class="hamb_social">
                    @foreach($socialIcons as $social)
                        <a href="{{ $social['value'] }}" target="_blank">
                            <img src="{{ $social['image'] }}" alt="{{ ucfirst($social['key']) }}" class="img-fluid">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>