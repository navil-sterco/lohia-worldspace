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
                    <h1 class="title21">Projects</h1>
                    <h2 class="title72">{!! $projectDetail['detail']['name'] ?? '' !!}</h2>
                </div>
                <div class="prjtdtl_nav">
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

                <div class="prjtdtl_btns">
                    <a href="#" class="request_btn">Request Site Visit</a>
                    <a href="{!! $projectDetail['detail']['brochure'] ?? '' !!}" target="_blank"
                        class="pdf_btn">Brochure PDF</a>
                </div>
            </div>
            <div class="prjtdtl_right">
                {!! $projectDetail['cms']['project_detail_first_section'] ?? '' !!}

                {!! $projectDetail['cms']['project_detail_gallery_section'] ?? '' !!}

                @if(!empty($projectDetail['modular']['testimonials']))
                    <div class="speak_wrapper" id="customer-speak">
                        <div class="speak_swiper swiper">
                            <div class="swiper-wrapper">
                                @foreach($projectDetail['modular']['testimonials'] as $testimonial)
                                    <div class="swiper-slide">
                                        <div class="speak_grid">

                                            <div class="speak_figure">
                                                <div class="speak_auth">
                                                    <h4>
                                                        {{ $testimonial['name'] ?? '' }}
                                                        <strong>{{ $testimonial['location'] ?? '' }}</strong>
                                                    </h4>
                                                </div>

                                                <figure>
                                                    <img src="{{ $testimonial['image'] ?? '' }}"
                                                        alt="{{ $testimonial['name'] ?? 'Customer' }}" class="img-fluid w-100">
                                                </figure>
                                            </div>

                                            <div class="speak_caption">
                                                <h6>Customers Speak</h6>

                                                <h3>
                                                    {{ $testimonial['title'] ?? '' }}
                                                </h3>
                                            </div>

                                        </div>

                                        <a href="#" class="overlap_btn">
                                            <img src="{{ asset('frontend-assets/images/arrow-right-white.svg') }}" alt="arrow"
                                                class="img-fluid">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
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