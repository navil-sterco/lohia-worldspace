@php
    $footer = footerData();
    $quickLinks = quickLinks();
    $socialIcons = socialIcons();
    $header = headerData();
    $sidebar = sidebar();

    $projects = projects();
@endphp

<div class="side_gupshup">
    <div class="gupshup_item">
        <img src="{{ url('frontend-assets/images/phone-icon.svg') }}" class="img-fluid" alt="Phone">
        <p><a href="tel:+917900790790"> +91 7900 790 790</a></p>
    </div>
    <div class="gupshup_item">
        <a target="_blank"  href="https://api.whatsapp.com/send?phone=917080906060&amp;text=Hi Lohia Worldspace,I need some info about Projects.">
            <img src="{{ url('frontend-assets/images/whatsapp-icon.svg') }}" class="img-fluid" alt="whatsapp">
        </a>
    </div>
    <div class="gupshup_item">
        <a href="mailto:hello@lohiaworldspace.com">
            <img src="{{ url('frontend-assets/images/email-icon.svg') }}" class="img-fluid" alt="Mail">
        </a>
    </div>
</div>

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
                            <li><a href="{{ url($menu['slug']) }}" @if ($menu['target_blank'] ?? false) target="_blank"
                            rel="noopener noreferrer" @endif>{{ $menu['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="other_link">
                    <h6>Other Links</h6>
                    <ul>
                        @foreach ($quickLinks as $menu)
                            <li><a href="{{ url($menu['slug']) }}" @if ($menu['target_blank'] ?? false) target="_blank"
                            rel="noopener noreferrer" @endif>{{ $menu['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="subscopy_grid">
                    <div class="subscribe_form">
                        <form id="subscribeForm" action="{{ route('newsletter.store') }}" method="POST">
                            @csrf
                            <input type="email" name="email" id="subscribeEmail" class="form-control"
                                placeholder="Subscribe for News" required>

                            <button type="submit">
                                <img src="{{ url('frontend-assets/images/arrow-right-red.svg') }}" alt="Arrow"
                                    class="img-fluid">
                            </button>
                        </form>
                    </div>
                    <div class="copyright">
                        <p>Copyright © {{ date('Y') }}</p>
                        <p>Website Design and Development by <a href="https://www.stercodigitex.com/"
                                target="_blank">Sterco</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="gwtp_logo">
        <a href="{{ url('/') }}">
            <img src="{{ url('frontend-assets/images/great-work-to-place-logo.webp') }}" alt="Great Work To Place" class="img-fluid w-100">
        </a>
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
            <img src="{{ asset('frontend-assets/images/victor-menumob01.svg') }}" alt="icon" class="img-fluid w-100">
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
            <img src="{{ asset('frontend-assets/images/victor-menumob01.svg') }}" alt="icon" class="img-fluid w-100">
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
            <img src="{{ asset('frontend-assets/images/victor-menumob01.svg') }}" alt="icon" class="img-fluid w-100">
        </div>

        @php
            $withChildren = [];
            $withoutChildren = [];

            foreach ($header as $menu) {
                $children = $menu['children'] ?? [];
                if (count($children) > 0) {
                    $withChildren[] = $menu;
                } else {
                    $withoutChildren[] = $menu;
                }
            }

            foreach ($sidebar as $menu) {
                $children = $menu['children'] ?? [];
                if (count($children) > 0) {
                    $withChildren[] = $menu;
                } else {
                    $withoutChildren[] = $menu;
                }
            }
        @endphp

        <ul class="mobile_menu">
            @foreach ($withChildren as $menu)
                @php
                    $slug = $menu['slug'] ?? '';
                    $title = $menu['title'] ?? '';
                    $targetBlank = !empty($menu['target_blank']);
                    $children = $menu['children'] ?? [];
                    $hasChildren = count($children) > 0;
                @endphp

                <li class="{{ $hasChildren ? ' menu_item' : '' }}">

                    <a href="{{ $hasChildren ? '#' : url($menu['overwrite_url'] ?? $slug) }}" {{ $targetBlank ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                        {{ $title }}
                    </a>

                    @if ($hasChildren)
                        <ul class="sub_menu">
                            @foreach ($children as $child)
                                @php
                                    $childSlug = $child['slug'] ?? '';
                                    $childTitle = $child['title'] ?? '';
                                    $childTargetBlank = !empty($child['target_blank']);
                                    $childUrl = $child['overwrite_url'] ?? $childSlug;
                                @endphp
                                <li>
                                    <a href="{{ url($childUrl) }}" {{ $childTargetBlank ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                        {{ $childTitle }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="mobile_quickmenu">
            <a href="{{ url('/request-site-visit') }}" class="btn_light">Request Site Visit</a>
            <ul>
                @foreach ($withoutChildren as $menu)
                    @php
                        $slug = $menu['slug'] ?? '';
                        $title = $menu['title'] ?? '';
                        $targetBlank = !empty($menu['target_blank']);
                    @endphp

                    <li>
                        <a href="{{ url($menu['overwrite_url'] ?? $slug) }}" {{ $targetBlank ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                            {{ $title }}
                        </a>
                    </li>
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