@php
    $footer = footerData();
    $quickLinks = quickLinks();
    $socialIcons = socialIcons();
    $header = headerData();
    $sidebar = sidebar();
    $projects = \app\Models\ModuleEntry::forModule(3, 'display_order', 'asc')->get()->map(fn($e) => $e->toCleanData());
@endphp

<footer class="main_footer">
    <div class="container-md">
        <div class="footer_grid">
            <div class="footer_left">
                <a href="{{ url('/') }}" class="footer_logo">
                    <img src="{{ url('frontend-assets/images/site-logo.svg') }}" alt="Lohia" class="img-fluid w-100">
                </a>

                <div class="ftr_social">
                    @foreach ($socialIcons as $social)
                        <a href="{{ $social['value'] }}" target="_blank">
                            <img src="{{ $social['image'] }}" alt="{{ ucfirst($social['key']) }}" class="img-fluid">
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="footer_right">
                <div class="footer_link">
                    <ul>
                        @foreach ($footer as $menu)
                            <li><a href="{{ url($menu['slug']) }}"
                                    @if ($menu['target_blank'] ?? false) target="_blank"
                            rel="noopener noreferrer" @endif>{{ $menu['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="other_link">
                    <h6>Other Links</h6>
                    <ul>
                        @foreach ($quickLinks as $menu)
                            <li><a href="{{ url($menu['slug']) }}"
                                    @if ($menu['target_blank'] ?? false) target="_blank"
                            rel="noopener noreferrer" @endif>{{ $menu['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="subscopy_grid">
                    <div class="subscribe_form">
                        <input type="text" class="form-control" placeholder="Subscribe for News">

                        <button type="button">
                            <img src="{{ url('frontend-assets/images/arrow-right-red.svg') }}" alt="Arrow"
                                class="img-fluid">
                        </button>
                    </div>

                    <div class="copyright">
                        <p>Copyright © {{ date('Y') }}</p>
                        <p>Website Design and Development by <a href="#" target="_blank">Sterco</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="mobmenu_panel">

    <div class="mobmenu_nav">
        <button type="button" data-target="menu_Tab01"><img
                src="{{ asset('frontend-assets/images/mob-project-icon.svg') }}" alt="icon" class="img-fluid">
            <strong>Projects</strong></button>
        <button type="button" data-target="menu_Tab02"><img
                src="{{ asset('frontend-assets/images/contactmenu-icon.svg') }}" alt="icon" class="img-fluid">
            <strong>Contact Us</strong></button>
        <button type="button" data-target="menu_Tab03"><img src="{{ asset('frontend-assets/images/menu-icon.svg') }}"
                alt="icon" class="img-fluid"> <strong>Menu</strong></button>
    </div>

    <div class="mobtab_content mobtab_project" data-id="menu_Tab01">
        <div class="menu_curve">
            <img src="{{ asset('frontend-assets/images/victor-menumob01.svg') }}" alt="icon"
                class="img-fluid w-100">
        </div>
        <div class="mobprj_wrapper">

            @foreach ($projects as $item)
                <div class="mobprj_bx">
                    <h6> {{ $item['name'] }} <strong>{{ $item['status'] }}</strong></h6>
                    <figure>
                        @if (!empty($item['image']))
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid w-100">
                        @endif
                    </figure>
                    <a href="{{ url('projects/' . $item['slug']) }}" class="overlap_btn">View</a>
                </div>
            @endforeach

        </div>
    </div>

    <div class="mobtab_content mobtab_contact" data-id="menu_Tab02">
        <div class="menu_curve">
            <img src="{{ asset('frontend-assets/images/victor-menumob01.svg') }}" alt="icon"
                class="img-fluid w-100">
        </div>
        <div class="mobcnt_wrapper">
            <div class="mobcnt_bx">
                <h5>CALL US</h5>
                <a href="tel:{{ supportInfo('phone') }}" class="cnt_phone">{{ supportInfo('phone') }}</a>
                <a href="mailto:{{ supportInfo('mail') }}" class="btn_light">{{ supportInfo('mail') }}</a>
            </div>
            <div class="mobcnt_bx">
                <h5>Corporate Office</h5>
                <h6>{{ supportInfo('city') }}</h6>
                <p>{{ supportInfo('address') }}</p>
            </div>
            <div class="mobcnt_bx">
                <h5>What can we help you with</h5>
                <a href="#" class="btn_light">Buying A Property</a>
            </div>
        </div>
    </div>

    <div class="mobtab_content mobtab_menu" data-id="menu_Tab03">
        <div class="menu_curve">
            <img src="{{ asset('frontend-assets/images/victor-menumob01.svg') }}" alt="icon"
                class="img-fluid w-100">
        </div>
        <ul class="mobile_menu">
            @foreach ($header as $menu)
                @php
                    $slug = $menu['slug'] ?? '';
                    $title = $menu['title'] ?? '';
                    $targetBlank = !empty($menu['target_blank']);
                @endphp

                <li>

                    <a href="{{ $slug }}"
                        @if ($targetBlank) target="_blank" rel="noopener noreferrer" @endif>
                        {{ $title }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mobile_quickmenu">
            <a href="{{ url('/request-site-visit') }}" class="btn_light">Request Site Visit</a>
            <ul>
                @foreach ($sidebar as $menu)
                    @php
                        $slug = $menu['slug'] ?? '';
                        $title = $menu['title'] ?? '';
                        $targetBlank = !empty($menu['target_blank']);

                    @endphp
                    <li><a href="{{ $slug }}">{{ $title }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>

</div>

@stack('scripts')

<script src="{{ url('frontend-assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ url('frontend-assets/js/aos.js') }}"></script>
<script src="{{ url('frontend-assets/js/swiper-bundle.min.js') }}"></script>
<script src="{{ url('frontend-assets/js/lenis.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancyapps-ui/6.1.14/fancybox/fancybox.umd.js"
    integrity="sha512-YnL214Fej6BefpeRYOeWviu5y5/cXvmYZKhi70eNz6mfSyucQK10Y2oLpUeeJr0ZzBdt9vZfTfCp1Hmy4iehrg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ url('frontend-assets/js/custom.js') }}"></script>
@if (request()->is('/') || request()->is('home'))
    <script src="{{ url('frontend-assets/js/home.js') }}"></script>
@endif

</body>

</html>
