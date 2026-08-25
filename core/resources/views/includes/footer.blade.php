@php
    $footer = footerData();
    $quickLinks = quickLinks();
    $socialIcons = socialIcons();
@endphp

<footer class="main_footer">
    <div class="container-md">
        <div class="footer_grid">
            <div class="footer_left">
                <a href="{{ url('/') }}" class="footer_logo">
                    <img src="{{ url('frontend-assets/images/site-logo.svg') }}" alt="Lohia" class="img-fluid w-100">
                </a>

                <div class="ftr_social">
                    @foreach($socialIcons as $social)
                        <a href="{{ $social['value'] }}" target="_blank">
                            <img src="{{ $social['image'] }}" alt="{{ ucfirst($social['key']) }}" class="img-fluid">
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="footer_right">
                <div class="footer_link">
                    <ul>
                        @foreach($footer as $menu)
                            <li><a href="{{ url($menu['slug']) }}" @if(($menu['target_blank'] ?? false)) target="_blank"
                            rel="noopener noreferrer" @endif>{{ $menu['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="other_link">
                    <h6>Other Links</h6>
                    <ul>
                        @foreach($quickLinks as $menu)
                            <li><a href="{{ url($menu['slug']) }}" @if(($menu['target_blank'] ?? false)) target="_blank"
                            rel="noopener noreferrer" @endif>{{ $menu['title'] }}</a></li>
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

@stack('scripts')

<script src="{{ url('frontend-assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ url('frontend-assets/js/aos.js') }}"></script>
<script src="{{ url('frontend-assets/js/swiper-bundle.min.js') }}"></script>
<script src="{{ url('frontend-assets/js/lenis.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancyapps-ui/6.1.14/fancybox/fancybox.umd.js" integrity="sha512-YnL214Fej6BefpeRYOeWviu5y5/cXvmYZKhi70eNz6mfSyucQK10Y2oLpUeeJr0ZzBdt9vZfTfCp1Hmy4iehrg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ url('frontend-assets/js/custom.js') }}"></script>
@if(request()->is('/') || request()->is('home'))
    <script src="{{ url('frontend-assets/js/home.js') }}"></script>
@endif

</body>

</html>
