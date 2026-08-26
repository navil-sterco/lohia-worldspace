@include('includes.header')

<section class="prjtdtl_sec">
    <div class="container max-content-lg">
        <div class="prjtdtl_grid">
            <div class="prjtdtl_left">
                <div class="proj_victor">
                    <img src="{{ asset('frontend-assets/images/victor-dash18.svg') }}" alt="victor"
                        class="img-fluid w-100">
                </div>
                <div class="prjtdtl_title">
                    <h1 class="title21">Collective</h1>
                    <h2 class="title72">{!! $collectiveDetail['detail']['name'] ?? '' !!}</h2>
                </div>
                <div class="prjtdtl_nav">
                    <ul>
                        <li><a href="#overview">Overview</a></li>
                        <li><a href="#offer">Offer</a></li>
                        <li><a href="#collaborators">Collaborators</a></li>
                        <li><a href="#collective-gallery">Gallery</a></li>
                    </ul>
                </div>
                <div class="prjtdtl_btns">
                    <a href="#" class="request_btn">Enquire Now</a>
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
