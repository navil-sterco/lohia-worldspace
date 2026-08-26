@include('includes.header')

{!! $cms['home_banner_0'] ?? '' !!}
{!! $cms['home_about_1'] ?? '' !!}
{!! $cms['home_third_section_2'] ?? '' !!}
{!! $cms['home_fourth_section_3'] ?? '' !!}
{!! $cms['home_fifth_section_4'] ?? '' !!}
{!! $cms['home_sixth_section_5'] ?? '' !!}
{!! $cms['home_seventh_section_6'] ?? '' !!}

<section class="speak_sec">

    {!! $cms['home_testimonial_7'] ?? '' !!}

    <div class="container-lg">
        <div class="speak_wrapper">
            @if(!empty($modular['testimonials']))
                <div class="speak_swiper swiper">
                    <div class="swiper-wrapper">
                        @foreach ($modular['testimonials'] as $index => $testimonial)
                            @php
                                $video = $testimonial['video'] ?? '';
                                $slug = $testimonial['slug'] ?? '';
                            @endphp
                            <div class="swiper-slide">
                                <div class="speak_video commonvideo_wraper">

                                    <video class="desktop_video" muted autoplay playsinline data-slug="{{ $slug }}">
                                        @if($video)
                                            <source src="{{ $video }}" type="video/mp4">
                                        @endif
                                    </video>
                                    <button type="button" class="play_btn">
                                        <img src="{{ asset('/frontend-assets/images/pause.svg') }}" alt="pause"
                                            class="img-fluid">
                                        <img src="{{ asset('frontend-assets/images/videoplay-icon.svg') }}" alt="play"
                                            class="img-fluid">
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if(count($modular['testimonials']) > 1)
                    <div class="swiper-button-prev speak-prev"></div>
                    <div class="swiper-button-next speak-next"></div>
                @endif
            @endif
        </div>
    </div>
</section>

<section class="whatson_sec">
    <div class="container">
        <div class="sec_title">
            <h5 class="title18" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1200">Our Blog</h5>
            <h3 class="title48" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200">
                KEEP UP WITH OUR NEWEST DEVELOPMENTS AND CHECK OUT THE LATEST INDUSTRY TRENDS
            </h3>
        </div>
    </div>
    <div class="whatson_grid">
        @foreach (array_slice($modular['blogs'] ?? [], 0, 4) as $index => $blog)
            @php
                $date = !empty($blog['date'])
                    ? \Carbon\Carbon::parse($blog['date'])
                    : null;

                $name = $blog['name'] ?? '';
                $description = $blog['description'] ?? '';
                $image = $blog['home_image'] ?? '';
                $slug = $blog['slug'] ?? '';

                $victor = in_array($index, [0, 2, 3])
                    ? 'victor-dash07.svg'
                    : null;

                if ($index === 0) {
                    $victor = 'victor-dash06.svg';
                }
            @endphp

            <div class="whatson_item reveal-left">
                <div class="whatson_caption">
                    @if ($victor)
                        <div class="whatson_victor">
                            <img src="{{ asset('frontend-assets/images/' . $victor) }}" alt="victor" class="img-fluid w-100">
                        </div>
                    @endif
                    <div class="whatson_date">
                        @if ($date)
                            <div class="date_item">
                                <h3>{{ $date->format('d') }}</h3>
                                <p>
                                    <strong>{{ strtoupper($date->format('M')) }}</strong>
                                    {{ $date->format('Y') }}
                                </p>
                            </div>
                        @endif
                        <div class="whatson_write">
                            {{ $name }}
                        </div>
                    </div>
                </div>
                @if ($image)
                    <figure>
                        <img src="{{ $image }}" alt="{{ $name }}" class="img-fluid w-100">
                    </figure>
                @endif

                @if ($slug)
                    <a href="{{ url('blog/' . $slug) }}" class="overlap_btn">
                        <img src="{{ asset('frontend-assets/images/arrow-red-thin.svg') }}" alt="arrow" class="img-fluid">
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</section>

{!! $cms['home_social_wall_8'] ?? '' !!}

@include('includes.footer')