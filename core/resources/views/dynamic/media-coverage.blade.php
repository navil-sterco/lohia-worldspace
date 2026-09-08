@include('includes.header')
<section class="mediacovr_sec">
    <div class="full-width">
        <div class="container max-content-lg">
            <div class="col-lg-10">
                <div class="sec_title">
                    <h1 class="title21" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200">Media Coverage</h1>
                    <h2 class="title48" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">LOREM IPSUM
                        DOLOR SIT AMET, CONSECTETUER ADIPISCING ELIT.</h2>
                </div>
                <div class="mediacovr_grid">

                    @foreach($media as $item)


                        <div class="mediacovr_bx">
                            <figure>
                                <img src="{{ $item['image'] }}" alt="media coverage"
                                    class="img-fluid reveal-left w-100">
                            </figure>
                            <a data-fancybox="gallery" href="{{ $item['image'] }}"
                                class="overlap_btn"></a>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</section>
@include('includes.footer')
