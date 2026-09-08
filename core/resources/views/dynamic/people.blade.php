@include('includes.header')

<x-menu />

{!! $section['cms']['people_first_section_0'] ?? '' !!}
{!! $section['cms']['people_second_section_1'] ?? '' !!}


<section class="current_sec">
    <div class="container">
        <div class="crntopen_wraper">
            <div class="crntopen_caption">
                <h5 class="title21" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1200">Current Openings</h5>
                <h3 class="title48" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200">COME BUILD WITH US</h3>
                <p data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">We're always looking for people who hold themselves to the same standard we hold our spaces — <br>if that's you, we'd like to hear from you.</p>
            </div>
            <div class="crnt_victor">
                <img src="{{ asset('frontend-assets/images/victor-dash13.svg') }}" alt="victor" class="img-fluid w-100 reveal-left">
            </div>
        </div>

        <div class="crnt_vacancy">
            @forelse ($openings as $job)
                @php
                    $openingList = $job['mapping_items']['opening'] ?? [];

                    $locations = collect($openingList)
                        ->map(fn($o) => trim(($o['count'] ?? '') . ' ' . ($o['loacation'] ?? '')))
                        ->filter()
                        ->implode(', ');

                    $totalOpenings = collect($openingList)->sum(fn($o) => (int) ($o['count'] ?? 0));
                @endphp

                <div class="vacancy_bx" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1200">
                    <h6>{{ $job['title'] }}</h6>
                    <ul>
                        <li>Dept. - <strong>{{ $job['department'] }}</strong></li>
                        <li>
                            <img src="{{ asset('frontend-assets/images/map-marker.svg') }}" alt="map" class="img-fluid">
                            {{ $locations }}
                        </li>
                        <li><strong>{{ $totalOpenings }}</strong> openings</li>
                        <li><a href="{{ route('jobs.show', $job['slug']) }}" class="arrow_btn"><img src="{{ asset('frontend-assets/images/arrow-right-black.svg') }}" alt="icon" class="img-fluid"></a></li>
                    </ul>
                </div>
            @empty
                <p>No current openings at the moment.</p>
            @endforelse
        </div>

        @if ($openings->hasPages())
            <div class="crnt_pagination">
                {{ $openings->links() }}
            </div>
        @endif
    </div>
</section>

@include('includes.footer')