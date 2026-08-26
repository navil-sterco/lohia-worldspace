@include('includes.header')

<section class="projt_sec">
    <div class="container max-content-lg">
        <div class="projt_grid">
            <div class="projt_left">
                {!! $section['cms']['project_listing_0'] ?? '' !!}
            </div>

            <div class="projt_right">
                @forelse($collectives as $collective)
                    <div class="proj_item">
                        <div class="prjtl_grid">
                            <div class="prjtl_title">
                                <h3>{{ strtoupper($collective['name'] ?? '') }}</h3>
                                <p>{{ $collective['location'] ?? '' }}</p>
                            </div>
                        </div>
                        <figure>
                            <img src="{{ $collective['image'] ?? asset('frontend-assets/images/collective-list01.webp') }}" class="img-fluid w-100" alt="project">
                        </figure>
                        <a href="{{ route('collective.detail', $collective['slug']) }}" class="overlap_btn">View</a>
                    </div>
                @empty
                    <div class="no_projects">
                        <p>No collective found. </p>
                    </div>
                @endforelse

            </div>
        </div>
          {!! $section['cms']['collective_logo_1'] ?? '' !!}
    </div>
</section>

@include('includes.footer')
