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

    <div class="speak_wrapper">
        <div class="speak_swiper swiper">
            <div class="swiper-wrapper">

                @foreach($modular['testimonials'] as $testimonial)
                    <div class="swiper-slide">
                        <div class="speak_grid">
                            <div class="speak_figure">
                                <div class="speak_auth">
                                    <h4>
                                        {{ strtoupper($testimonial['name']) }}
                                        <strong>{{ strtoupper($testimonial['location']) }}</strong>
                                    </h4>
                                </div>
                                <figure>
                                    <img src="{{ $testimonial['image'] }}" alt="{{ $testimonial['name'] }}"
                                        class="img-fluid w-100">
                                </figure>
                            </div>

                            <div class="speak_caption">
                                <h3>{{ $testimonial['title'] }}</h3>
                                <p>{{ $testimonial['description'] }}</p>
                            </div>
                        </div>

                        <a href="{{ url('testimonials/' . $testimonial['slug']) }}" class="overlap_btn">
                            <img src="{{ asset('frontend-assets/images/arrow-right-white.svg') }}" alt="arrow"
                                class="img-fluid">
                        </a>
                    </div>
                @endforeach

            </div>
        </div>

        <div class="swiper-button-prev speak-prev"></div>
        <div class="swiper-button-next speak-next"></div>
    </div>
</section>

<section class="whatson_sec">
    <div class="container">
        <div class="sec_title">
            <h5 class="title18">What's on</h5>
            <h3 class="title48">
                KEEP UP WITH OUR NEWEST DEVELOPMENTS AND CHECK OUT THE LATEST INDUSTRY TRENDS
            </h3>
        </div>
    </div>

    <div class="whatson_grid">

        @foreach (array_slice($modular['news-and-events'] ?? [], 0, 4) as $index => $news)

            @php
                $date = !empty($news['date'])
                    ? \Carbon\Carbon::parse($news['date'])
                    : null;

                $name = $news['name'] ?? '';
                $description = $news['description'] ?? '';
                $image = $news['image'] ?? '';
                $slug = $news['slug'] ?? '';

                // Victor images for specific items
                $victor = in_array($index, [0, 2, 3])
                    ? 'victor-dash07.svg'
                    : null;

                if ($index === 0) {
                    $victor = 'victor-dash06.svg';
                }
            @endphp

            <div class="whatson_item">

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

                    <p>{{ $description }}</p>

                </div>

                @if ($image)
                    <figure>
                        <img src="{{ $image }}" alt="{{ $name }}" class="img-fluid w-100">
                    </figure>
                @endif

                @if ($slug)
                    <a href="{{ url('news-events/' . $slug) }}" class="overlap_btn">
                        <img src="{{ asset('frontend-assets/images/arrow-red-thin.svg') }}" alt="arrow" class="img-fluid">
                    </a>
                @endif

            </div>

        @endforeach

    </div>
</section>

{!! $cms['home_social_wall_8'] ?? '' !!}

@include('includes.footer')