@include('includes.header')

<section class="prjtdtl_sec">
    <div class="container max-content-lg">
        <div class="prjtdtl_grid">
            <div class="prjtdtl_left">
                <div class="proj_victor">
                    <img src="{{ asset('frontend-assets/images/victor-dash18.svg') }}" alt="victor"
                        class="img-fluid w-100 reveal-top">
                </div>
                <div class="prjtdtl_title">
                    <h1 class="title21" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200">Collective</h1>
                    <h2 class="title72" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">{!! $collectiveDetail['detail']['name'] ?? '' !!}</h2>
                </div>
                <div class="prjtdtl_nav">
                    <ul>
                        <li data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200"><a href="#overview">Overview</a></li>
                        <li data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200"><a href="#offer">Offer</a></li>
                        <li data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200"><a href="#collaborators">Collaborators</a></li>
                        <li data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200"><a href="#project-gallery">Gallery</a></li>
                    </ul>
                </div>
                <div class="prjtdtl_btns" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1200">
                    <a href="{{ url('contact-us') }}" class="request_btn">Enquire Now</a>
                </div>
            </div>
            <div class="prjtdtl_right">
                {!! $collectiveDetail['cms']['collective_deatils'] ?? '' !!}
                {!! $collectiveDetail['cms']['project_detail_gallery_section'] ?? '' !!}

            </div>
        </div>
    </div>
</section>

@include('includes.footer')
