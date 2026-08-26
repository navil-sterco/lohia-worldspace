@include('includes.header')

<section class="prjtdtl_sec">
    <div class="container max-content-lg">
        <div class="prjtdtl_grid">
            <div class="prjtdtl_left">
                <div class="proj_victor">
                    <img src="{{ asset('frontend-assets/images/victor-dash18.svg') }}" alt="victor"
                        class="img-fluid w-100">
                </div>
                <div class="prjtdtl_title">
                    <h1 class="title21" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1200">Projects</h1>
                    <h2 class="title72" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200">{!! $projectDetail['detail']['name'] ?? '' !!}</h2>
                </div>
                <div class="prjtdtl_nav" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
                    <ul>
                        <li><a href="#overview">Overview</a></li>
                        <li><a href="#feature">Features & Amenities</a></li>
                        <li><a href="#gallery">Gallery</a></li>
                        <li><a href="#customer-speak">Customer Speak</a></li>
                        <li><a href="#floor-plans">Site and Floor Plans</a></li>
                        <li><a href="#construction">Construction Updates</a></li>
                        <li><a href="#faqs">FAQs</a></li>
                    </ul>
                </div>

                <div class="prjtdtl_btns" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1200">
                    <a href="#" class="request_btn">Request Site Visit</a>
                    <a href="{!! $projectDetail['detail']['brochure'] ?? '' !!}" target="_blank"
                        class="pdf_btn">Brochure PDF</a>
                </div>
            </div>
            <div class="prjtdtl_right">
                {!! $projectDetail['cms']['project_detail_first_section'] ?? '' !!}

                {!! $projectDetail['cms']['project_detail_gallery_section'] ?? '' !!}

                @if(!empty($projectDetail['modular']['testimonials']))
                    <div class="speak_wrapper" id="customer-testimonials">
                        <h5 class="title21">Customers Speak</h5>
                        <div class="speak_swiper swiper">
                            <div class="swiper-wrapper">
                                @foreach($projectDetail['modular']['testimonials'] as $testimonial)
                                    <div class="swiper-slide">
                                        <div class="speak_video commonvideo_wraper">
                                            <video class="desktop_video" muted="false" autoplay playsinline
                                                @if(!empty($testimonial['thumbnail_image']))
                                                    poster="{{ $testimonial['thumbnail_image'] }}"
                                                @endif>
                                                @if(!empty($testimonial['video']))
                                                    <source src="{{ $testimonial['video'] }}" type="video/mp4">
                                                @endif
                                            </video>
                                            <button type="button" class="play_btn">
                                                <img src="{{ asset('frontend-assets/images/pause.svg') }}" alt="pause" class="img-fluid">
                                                <img src="{{ asset('frontend-assets/images/videoplay-icon.svg') }}" alt="play" class="img-fluid">
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-prev speak-prev"></div>
                            <div class="swiper-button-next speak-next"></div>
                        </div>
                    </div>
                @endif

                {!! $projectDetail['cms']['editor'] ?? '' !!}

                {!! $projectDetail['cms']['project_detail_construction_section'] ?? '' !!}

                @if(!empty($projectDetail['modular']['faqs']))
                    <div class="faq_wrap" id="faqs">
                        <h5>FAQs</h5>
                        <div class="accordions">
                            @foreach($projectDetail['modular']['faqs'] as $index => $faq)
                                <div class="accordions-item">
                                    <button class="accordions-button" type="button">
                                        {{ $faq['question'] ?? '' }}
                                    </button>
                                    <div id="collapse{{ $index }}"
                                        class="accordions-collapse {{ $index === 0 ? 'active' : '' }}">
                                        <div class="accordions-body">
                                            <p>
                                                {!! $faq['answer'] ?? '' !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@include('includes.footer')