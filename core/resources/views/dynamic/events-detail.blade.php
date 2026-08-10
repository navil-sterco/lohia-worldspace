@include('includes.header')

<x-menu />

<section class="nwsdtl_sec">
    <div class="nwsdtl_wrapper">
        <div class="container max-content-md">
            <div class="nwsdtl_grid">
                <div class="nwsdtl_caption">
                    <div class="sec_title">
                        <h1 class="title21">INSIGHTS</h1>
                        <h2 class="title48">EVENTS</h2>
                    </div>
                    <div class="nwsdtl_title">
                        <div class="nws_victor">
                            <img src="{{ asset('frontend-assets/images/victor-dash15.svg') }}" alt="victor" class="img-fluid reveal-left w-100">
                        </div>
                        <div class="nws_date">
                            {{ \Carbon\Carbon::parse($eventDetail['date'])->format('d') }}<sup>{{ strtoupper(\Carbon\Carbon::parse($eventDetail['date'])->format('M')) }}</sup>
                        </div>
                        <h3 class="title48">{{ strtoupper($eventDetail['name']) }}</h3>
                    </div>
                    <div class="nwsdtl_over">
                        @foreach ($eventDetail['mapping_items']['detail'] ?? [] as $block)
                            <p>{{ $block['detail_paragraph'] }}</p>
                        @endforeach
                    </div>
                </div>
                <figure class="nws_figure">
                    <img src="{{ $eventDetail['detail_image'] ?? $eventDetail['image'] }}" alt="{{ $eventDetail['name'] }}" class="img-fluid w-100">
                </figure>
            </div>
        </div>
    </div>
</section>

@if (($relatedEvents ?? collect())->isNotEmpty())
<section class="nwsrelate_sec">
    <div class="container">
        <div class="sec_title">
            <h5 class="title21">Related Events</h5>
        </div>
        <div class="nwsrelate_swiper swiper">
            <div class="swiper-wrapper">
                @foreach ($relatedEvents as $item)
                    <div class="swiper-slide">
                        <div class="nwsrlt_bx">
                            <div class="nwsrlt_captn">
                                <div class="nws_date">
                                    {{ \Carbon\Carbon::parse($item['date'])->format('d') }}<sup>{{ strtoupper(\Carbon\Carbon::parse($item['date'])->format('M')) }}</sup>
                                </div>
                                <p>{{ $item['name'] }}</p>
                            </div>
                            <figure>
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid w-100">
                            </figure>
                            <a href="{{ route('events.show', $item['slug']) }}" class="overlap_btn">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@include('includes.footer')