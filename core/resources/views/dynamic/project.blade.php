@include('includes.header')

<section class="projt_sec">
    <div class="container max-content-lg">
        <div class="projt_grid">
            <div class="projt_left">
                {!! $section['cms']['project_listing_0'] ?? '' !!}
                <div class="projt_filter">
                    <h6 class="title18">Filter by</h6>
                    <div class="filter_group">
                        <div class="dropdown_menu">
                            <button class="dropdown_toggle">
                                {{ $activeFilters['type'] ?? 'Project Type' }}
                            </button>
                            <ul class="dropdown_item">
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['type' => null, 'page' => null]) }}">
                                        All Types
                                    </a>
                                </li>
                                @foreach($filterOptions['types'] as $type)
                                    <li>
                                        <a href="{{ request()->fullUrlWithQuery(['type' => $type, 'page' => null]) }}"
                                            class="{{ ($activeFilters['type'] ?? null) === $type ? 'active' : '' }}">
                                            {{ $type }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="dropdown_menu">
                            <button class="dropdown_toggle">
                                {{ $activeFilters['location'] ?? 'Location' }}
                            </button>
                            <ul class="dropdown_item">
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['location' => null, 'page' => null]) }}">
                                        All Locations
                                    </a>
                                </li>
                                @foreach($filterOptions['locations'] as $location)
                                    <li>
                                        <a href="{{ request()->fullUrlWithQuery(['location' => $location, 'page' => null]) }}"
                                            class="{{ ($activeFilters['location'] ?? null) === $location ? 'active' : '' }}">
                                            {{ $location }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="dropdown_menu">
                            <button class="dropdown_toggle">
                                {{ $activeFilters['status'] ?? 'Project Status' }}
                            </button>
                            <ul class="dropdown_item">
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}">
                                        All Status
                                    </a>
                                </li>
                                @foreach($filterOptions['statuses'] as $status)
                                    <li>
                                        <a href="{{ request()->fullUrlWithQuery(['status' => $status, 'page' => null]) }}"
                                            class="{{ ($activeFilters['status'] ?? null) === $status ? 'active' : '' }}">
                                            {{ $status }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="projt_right">
                @forelse($projects as $project)
                    <div class="proj_item" data-type="{{ $project['type'] ?? '' }}"
                        data-location="{{ $project['location'] ?? '' }}" data-status="{{ $project['status'] ?? '' }}">
                        <div class="prjtl_grid">
                            <div class="prjtl_title">
                                <h3>{{ strtoupper($project['name'] ?? '') }}</h3>
                                <p>{{ $project['location'] ?? '' }}</p>
                            </div>
                            <div class="prjtl_type">
                                <h6>{{ $project['type'] ?? '' }}</h6>
                                <p>{{ $project['status'] ?? '' }}</p>
                            </div>
                        </div>
                        <figure>
                            <img src="{{ $project['image'] ?? asset('images/proj01.webp') }}" class="img-fluid w-100"
                                alt="{{ $project['name'] ?? 'project' }}" loading="lazy">
                        </figure>
                        <a href="{{ route('projects.detail', $project['slug']) }}" class="overlap_btn">View</a>
                    </div>
                @empty
                    <div class="no_projects">
                        <p>No projects found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if($projects->hasPages())
            <div class="projt_pagination">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</section>

@include('includes.footer')