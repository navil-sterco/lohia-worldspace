@include('includes.header')

<x-menu />

<section class="gallery_sec">
    <div class="container-lg">

        <div class="gallery_title">
            <h1 class="title21">Gallery</h1>
            <h2 class="title48">Lorem Ipsum is simply dummy text</h2>
        </div>

        <div class="gal_lst">
            @forelse($gallery as $item)
                <div class="gal_bx">
                    <figure>
                        <img src="{{ $item['image'] ?? '' }}" alt="{{ $item['name'] ?? 'gallery' }}" class="img-fluid w-100">
                    </figure>
                    <div class="zoombtn">
                        <img src="{{ asset('frontend-assets/images/zoombtn.svg') }}" alt="zoom" class="img-fluid">
                    </div>
                    <a data-fancybox="gallery" href="{{ $item['image'] ?? '' }}" class="overlap_btn">View</a>
                </div>
            @empty
                <p>No gallery images to show yet.</p>
            @endforelse
        </div>

        @if($gallery->hasPages())
            <div class="nws_pagination mt-5">
                {{ $gallery->links() }}
            </div>
        @endif

    </div>
</section>

@include('includes.footer')