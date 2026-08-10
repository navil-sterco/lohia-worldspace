@include('includes.header')
<link rel="stylesheet" type="text/css" href="{{ asset('frontend-assets/css/inner-page.css') }}">

<x-menu />

<section class="nwsdtl_sec">
    <div class="nwsdtl_wrapper">
        <div class="container max-content-md">
            <div class="nwsdtl_grid">
                <div class="nwsdtl_caption">
                    <div class="sec_title">
                        <h1 class="title21">INSIGHTS</h1>
                        <h2 class="title48">BLOG</h2>
                    </div>
                    <div class="nwsdtl_title">
                        <div class="nws_victor">
                            <img src="{{ asset('frontend-assets/images/victor-dash15.svg') }}" alt="victor" class="img-fluid reveal-left w-100">
                        </div>
                        <div class="nws_date">
                            {{ \Carbon\Carbon::parse($blogDetail['detail']['date'])->format('d') }}<sup>{{ \Carbon\Carbon::parse($blogDetail['detail']['date'])->format('M') }}</sup>
                        </div>
                        <h3 class="title48">{{ $blogDetail['detail']['name'] }}</h3>
                    </div>
                    <div class="nwsdtl_over">
                        <p>{{ $blogDetail['detail']['description'] }}</p>

                        @if(!empty($blogDetail['detail']['mapping_items']['detail_main_description']))
                            @foreach($blogDetail['detail']['mapping_items']['detail_main_description'] as $block)
                                <p>{{ $block['description'] }}</p>
                            @endforeach
                        @endif
                    </div>
                </div>
                <figure class="nws_figure">
                    <img src="{{ $blogDetail['detail']['detail_image'] ?? $blogDetail['detail']['image'] ?? '' }}" alt="{{ $blogDetail['detail']['name'] }}" class="img-fluid w-100">
                </figure>
            </div>
        </div>

        <div class="nwsdtl_overview">
            <div class="container">
                {!! $blogDetail['cms']['editor'] ?? '' !!}
            </div>
        </div>
    </div>
</section>

@if($relatedBlogs->isNotEmpty())
<section class="nwsrelate_sec">
    <div class="container">
        <div class="sec_title">
            <h5 class="title21">Related Blog</h5>
        </div>
        <div class="nwsrelate_swiper swiper">
            <div class="swiper-wrapper">
                @foreach($relatedBlogs as $related)
                    <div class="swiper-slide">
                        <div class="nwsrlt_bx">
                            <div class="nwsrlt_captn">
                                <div class="nws_date">
                                    {{ \Carbon\Carbon::parse($related['date'])->format('d') }}<sup>{{ \Carbon\Carbon::parse($related['date'])->format('M') }}</sup>
                                </div>
                                <p>{{ $related['name'] }}</p>
                            </div>
                            <figure>
                                <img src="{{ $related['image'] ?? '' }}" alt="{{ $related['name'] }}" class="img-fluid w-100">
                            </figure>
                            <a href="{{ route('blog.show', $related['slug']) }}" class="overlap_btn">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@include('includes.footer')