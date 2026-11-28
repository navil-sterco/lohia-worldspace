@include('includes.header')

<section class="projt_sec">
    <div class="container max-content-lg">
        <div class="projt_grid">
            <div class="projt_left">
                {!! $section['cms']['project_listing_0'] ?? '' !!}
            </div>

            <div class="projt_right">
                @forelse($collectives as $collective)
                    <div class="proj_item" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
                        <div class="prjtl_grid">
                            <div class="prjtl_title">
                                <h3>{!! ($collective['name'] ?? '') !!}</h3>
                                <p>{{ $collective['location'] ?? '' }}</p>
                            </div>
                            <a href="{{ route('collective.detail', $collective['slug']) }}" class="overlap_btn">View</a>
                        </div>
                        <div class="projlist_swiper swiper">
                            <div class="swiper-wrapper">

                                @if(!empty($collective['mapping_items']['image_slider']))
                                    @foreach($collective['mapping_items']['image_slider'] as $slide)
                                        @if(!empty($slide['images']))
                                            <div class="swiper-slide">
                                                <figure>
                                                    <img src="{{ $slide['images'] }}"
                                                        class="img-fluid w-100"
                                                        alt="{{ $collective['name'] ?? 'Project' }}">
                                                </figure>
                                                <a href="{{ route('collective.detail', $collective['slug']) }}" class="overlap_btn">View</a>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="swiper-slide">
                                        <figure>
                                            <img src="{{ $collective['image'] ?? asset('frontend-assets/images/collective-list01.webp') }}"
                                                class="img-fluid w-100"
                                                alt="{{ $collective['name'] ?? 'Project' }}">
                                        </figure>
                                        <a href="{{ route('collective.detail', $collective['slug']) }}" class="overlap_btn">View</a>
                                    </div>
                                @endif

                            </div>
                        </div>                     
                        
                    </div>
                @empty
                    <div class="no_projects">
                        <p>No collective found. </p>
                    </div>
                @endforelse

            </div>
        </div>
          {!! $section['cms']['collective_logo_1'] ?? '' !!}
    </div>
</section>

@include('includes.footer')
