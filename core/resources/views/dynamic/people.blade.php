@include('includes.header')

{!! $section['cms']['people_first_section_0'] ?? '' !!}
{!! $section['cms']['people_second_section_1'] ?? '' !!}


<section class="current_sec">
    <div class="container">
        <div class="crntopen_wraper">
            <div class="crntopen_caption">
                <h5 class="title21">Current Openings</h5>
                <h3 class="title48">PLEASE FIND BELOW THE CURRENT OPENINGS ACROSS MULTIPLE CITIES.</h3>
                <p>More roles are updated regularly.</p>
            </div>
            <div class="crnt_victor">
                <img src="{{ asset('frontend-assets/images/victor-dash13.svg') }}" alt="victor" class="img-fluid w-100">
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

                <div class="vacancy_bx">
                    <h6>{{ $job['title'] }}</h6>
                    <ul>
                        <li>Dept. - <strong>{{ $job['department'] }}</strong></li>
                        <li>
                            <img src="{{ asset('frontend-assets/images/map-marker.svg') }}" alt="map" class="img-fluid">
                            {{ $locations }}
                        </li>
                        <li><strong>{{ $totalOpenings }}</strong> openings</li>
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