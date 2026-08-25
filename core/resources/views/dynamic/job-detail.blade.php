@include('includes.header')
<link rel="stylesheet" type="text/css" href="{{ asset('css/inner-page.css') }}">

@php
    $detail      = $job['detail'] ?? [];
    $cms         = $job['cms'] ?? [];
    $title       = $detail['title'] ?? '';
    $department  = $detail['department'] ?? '';
    $experience  = $detail['minimum_experience'] ?? '';
    $openings    = $detail['mapping_items']['opening'] ?? [];
    $editorHtml  = $cms['editor'] ?? '';
    $jobSlug     = request()->route('slug');

    $locations = collect($openings)->pluck('loacation')->filter()->implode(', ');
    $openingCount = collect($openings)->sum(fn ($o) => (int) ($o['count'] ?? 0));
@endphp

<x-menu slug="people" />

<section class="apply_sec">
    <div class="container-lg">
        <div class="col-lg-8">
            <div class="sec_title">
                <h1 class="title21">Apply Now</h1>
                <h2 class="title48">LOREM IPSUM DOLOR SIT AMET, CONSECTETUER ADIPISCING ELIT.</h2>
            </div>
        </div>
    </div>

    <div class="apply_wrapper">
        <div class="container max-content-lg">
            <div class="col-lg-11">
                <div class="apply_grid">
                    <div class="apply_caption">
                        <blockquote class="title30">We believe in the value of great design and utilise design thinking as a tool to create better, more sustainable spaces, systems, services and experiences for people.</blockquote>
                        <p>Drop a mail at <a href="mailto:hr@lohiaworldspace.com">hr@lohiaworldspace.com</a> We'll be happy to hear from you.</p>
                        <p>Alternatively, choose from our list of challenges. And apply for the one you’re ready to take head on.</p>
                        <a href="{{ route('apply-job', ['slug' => $jobSlug]) }}" class="theme_btn">Apply Job</a>
                        <div class="apply_victor">
                            <img src="{{ asset('frontend-assets/images/victor-dash11.svg') }}" alt="about" class="img-fluid reveal-left w-100 revealed-left">
                        </div>
                    </div>

                    <div class="applyfrm_wrapper">
                        <div class="jobdtl_caption">
                            <h3 class="title30">{{ $title }}</h3>

                            <ul>
                                @if($locations)
                                    <li><img src="{{ asset('frontend-assets/images/location-marker.svg') }}" alt="icon" class="img-fluid"> {{ $locations }}</li>
                                @endif

                                @if($experience)
                                    <li><img src="{{ asset('frontend-assets/images/calendar-day.svg') }}" alt="icon" class="img-fluid"> {{ $experience }}</li>
                                @endif

                                @if($department)
                                    <li><img src="{{ asset('frontend-assets/images/time-icon.svg') }}" alt="icon" class="img-fluid"> {{ $department }}</li>
                                @endif
                            </ul>

                            <div class="job_discription">
                                <h5 class="title21">JOB DESCRIPTION</h5>

                                {!! $editorHtml !!}
                                <a href="{{ route('apply-job', ['slug' => $jobSlug]) }}" class="btn_light">Apply Job</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('includes.footer')