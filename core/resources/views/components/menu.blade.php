@props(['slug' => null])

@php
    if (!empty($slug)) {
        $baseSlug = $slug;
    } else {
        $currentPath = trim(request()->path(), '/');
        $baseSlug = explode('/', $currentPath)[0] ?? '';
    }

    $menu = insightMenu($baseSlug);
@endphp

@if(!empty($menu['items']) && $menu['items']->count() > 0)
    <section class="inner_menu">
        <div class="container max-content-md">
            <div class="dropdown_menu">
                @php
                    $activeItem = $menu['items']->firstWhere('slug', $baseSlug);
                    $toggleLabel = $activeItem->title ?? ($menu['parent']->title ?? 'Menu');
                @endphp

                <button class="dropdown_toggle" type="button">{{ $toggleLabel }}</button>

                <ul class="dropdown_item">
                    @foreach($menu['items'] as $item)
                        @php
                            $isActive = $item->slug === $baseSlug;
                        @endphp
                        <li class="{{ $isActive ? 'active' : '' }}">
                            <a href="{{ url($item->slug) }}"
                               @if($isActive) aria-current="page" @endif>
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endif