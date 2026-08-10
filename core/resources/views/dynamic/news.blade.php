@include('includes.header')

<x-menu />

<section class="nws_sec">
    <div class="container-md">
        <div class="col-lg-8">
            <div class="sec_title">
                <h1 class="title21">INSIGHTS</h1>
                <h2 class="title48">NEWS</h2>
            </div>
        </div>
    </div>

    <div class="nws_wrapper">

        @if($firstNews)
            <div class="container max-content-md">
                <div class="col-lg-11">
                    <div class="nws_grid">
                        <div class="nws_victor">
                            <img src="{{ asset('frontend-assets/images/victor-dash14.svg') }}" alt="victor"
                                class="img-fluid reveal-left w-100">
                        </div>
                        <div class="nws_caption">
                            <div class="nws_date">
                                {{ \Carbon\Carbon::parse($firstNews['date'])->format('d') }}<sup>{{ \Carbon\Carbon::parse($firstNews['date'])->format('M') }}</sup>
                            </div>
                            <h3 class="title48">{{ $firstNews['name'] }}</h3>
                            <p>{{ $firstNews['description'] }}</p>
                        </div>
                        <figure class="nws_figure">
                            <img src="{{ $firstNews['image'] ?? ''}}" alt="{{ $firstNews['name'] }}" class="img-fluid w-100">
                        </figure>
                        @if(!empty($firstNews['link']))
                            <a href="{{ $firstNews['link'] }}" target="_blank" rel="noopener noreferrer" class="overlap_btn">
                                View
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="nwslist_wrapper">
            <div class="container">
                <div class="col-lg-10 mx-auto">

                    <div class="nwslist_grid">
                        @forelse($news as $item)
                            <div class="nws_bx">
                                <div class="nwsbx_captn">
                                    <div class="nws_date">
                                        {{ \Carbon\Carbon::parse($item['date'])->format('d') }}<sup>{{ \Carbon\Carbon::parse($item['date'])->format('M') }}</sup>
                                    </div>
                                    <p>{{ $item['name'] }}</p>
                                </div>
                                <figure>
                                    <img src="{{ $item['image'] ?? '' }}" alt="{{ $item['name'] }}" class="img-fluid w-100">
                                </figure>
                                @if(!empty($item['link']))
                                    <a href="{{ $item['link'] }}" target="_blank" rel="noopener noreferrer" class="overlap_btn">
                                        View
                                    </a>
                                @endif
                            </div>
                        @empty
                            <p>No news to show yet.</p>
                        @endforelse
                    </div>

                    @if($news->hasPages())
                        <div class="nws_pagination mt-5">
                            {{ $news->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</section>

@include('includes.footer')