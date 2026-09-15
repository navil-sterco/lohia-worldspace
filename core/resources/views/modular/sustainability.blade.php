@include('includes.header')

<x-menu />

{!! $cms['sustainability_first_section_0'] ?? '' !!}
{!! $cms['sustainability_second_section_1'] ?? '' !!}
{!! $cms['sustainability_third_section_2'] ?? '' !!}

<section class="prodcreat_sec">
    <div class="prodcrea_grid">
        {!! $cms['sustainability_fourth_section_3'] ?? '' !!}

        <div class="ldcrslide_wrapper">
            <div class="prodcr_swiper swiper">
                <div class="swiper-wrapper">
                    @php
                        $projects = collect($modular['projects'] ?? [])
                            ->map(function ($item) {
                                $item['url'] = 'projects/' . ($item['slug'] ?? '');
                                return $item;
                            });

                        $collective = collect($modular['collective'] ?? [])
                            ->map(function ($item) {
                                $item['url'] = 'collective/' . ($item['slug'] ?? '');
                                return $item;
                            });

                        $prodSlides = $projects->merge($collective);
                    @endphp

                    @foreach ($prodSlides as $item)
                        <div class="swiper-slide">
                            <div class="prodcr_slide reveal-left">
                                <figure>
                                    <img
                                        src="{{ $item['home_image'] ?? '' }}"
                                        class="img-fluid w-100"
                                        alt="{{ $item['name'] ?? 'Landmark Creations' }}"
                                    >
                                </figure>

                                <div class="prodcr_caption">
                                    <h6>{!! $item['name'] ?? '' !!}</h6>
                                    <h6>{{ $item['location'] ?? '' }}</h6>
                                </div>

                                <a href="{{ url($item['url']) }}" class="overlap_btn">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="swiper-button-prev landcr-prev"></div>
            <div class="swiper-button-next landcr-next"></div>
        </div>
    </div>
</section>

@include('includes.footer')