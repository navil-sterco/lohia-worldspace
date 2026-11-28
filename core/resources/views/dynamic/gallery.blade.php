@include('includes.header')

<x-menu />

<section class="gallery_sec">
    <div class="container-lg">

        <div class="gallery_title">
            <h1 class="title21" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1200">Gallery</h1>
            <h2 class="title48" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200">A glimpse into the world we create.</h2>
        </div>

        <div class="gal_lst">
            @forelse($gallery as $item)
                <div class="gal_bx" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
                    <figure>
                        <img src="{{ $item['image'] ?? '' }}" alt="{{ $item['name'] ?? 'gallery' }}" class="img-fluid w-100">
                    </figure>
                    <h6>Bhoomi Poojan</h6>
                    <div class="zoombtn">
                        <img src="{{ asset('frontend-assets/images/zoombtn.svg') }}" alt="zoom" class="img-fluid">
                    </div>
                    @if(!empty($item['detail_images']))
                        <a href="{{ route('gallery.detail', $item['slug']) }}" class="overlap_btn">View</a>
                    @else
                        <a data-fancybox="gallery" href="{{ $item['image'] ?? '' }}" class="overlap_btn">View</a>
                    @endif
                </div>
            @empty
                <p>No gallery images to show yet.</p>
            @endforelse
        </div>

        @if($gallery->hasPages())
            <div class="common_pagination">
                {{ $gallery->links() }}
            </div>
        @endif

    </div>
</section>

@include('includes.footer')