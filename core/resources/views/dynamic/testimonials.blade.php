@include('includes.header')

<x-menu />

<section class="testim_sec">
    <div class="full-width">
        <div class="container max-content-lg">
            <div class="col-lg-10">
                <div class="testim_wrapper">
                    <h1 class="title21">Testimonials</h1>
                    <h2 class="title48">SPACES THAT SPEAK FOR THEMSELVES</h2>

                    <div class="testim_grid">
                        @forelse($testimonials as $testimonial)
                            <div class="video-wrap">
                                <video class="custom-video" playsinline preload="none"
                                    @if(!empty($testimonial['thumbnail_image']))
                                        poster="{{ $testimonial['thumbnail_image'] }}"
                                    @endif>
                                    @if(!empty($testimonial['video']))
                                        <source src="{{ $testimonial['video'] }}" type="video/mp4">
                                    @endif
                                </video>
                                <button class="video_play_btn" type="button">
                                    <img src="{{ asset('frontend-assets/images/videoplay-icon.svg') }}" alt="play" class="img-fluid">
                                </button>
                            </div>
                        @empty
                            <p>No testimonials to show yet.</p>
                        @endforelse
                    </div>

                    @if($testimonials->hasPages())
                        <div class="nws_pagination mt-5">
                            {{ $testimonials->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</section>

@include('includes.footer')