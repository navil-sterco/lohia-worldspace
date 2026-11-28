@include('includes.header')

<x-menu />

<section class="gallery_sec">
    <div class="container-lg">

        <div class="gallery_title">
            <h1 class="title21" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1200">Gallery</h1>
            <h2 class="title48" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200">
                {{ $galleryDetail['name'] ?? 'Gallery' }}
            </h2>
            <a href="{{ url()->previous() }}" class="back_btn">Back</a>
        </div>

        <div class="gal_lst galdtl_lst">
            @forelse($galleryDetail['detail_images'] as $index => $detailImage)
                <div class="gal_bx" data-aos="fade-up" data-aos-delay="{{ 300 + ($index * 50) }}" data-aos-duration="1200">
                    <figure>
                        <img src="{{ $detailImage }}" alt="{{ $galleryDetail['name'] ?? 'gallery' }}" class="img-fluid w-100">
                    </figure>
                    <div class="zoombtn">
                        <img src="{{ asset('frontend-assets/images/zoombtn.svg') }}" alt="zoom" class="img-fluid">
                    </div>
                    <a data-fancybox="gallery-detail" href="{{ $detailImage }}" class="overlap_btn">View</a>
                </div>
            @empty
                <p>No gallery detail images to show yet.</p>
            @endforelse
        </div>

    </div>
</section>

@include('includes.footer')
