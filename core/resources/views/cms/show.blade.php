@include('includes.header')

@if($relatedPages->isNotEmpty())
	<section class="inner_menu">
		<div class="container max-content-md">
			<div class="dropdown_menu">
				<button class="dropdown_toggle">{{ $page->title }}</button>
				<ul class="dropdown_item">
					@foreach($relatedPages as $relatedPage)
						<li>
							<a href="{{ $relatedPage->slug }}"
								class="{{ $relatedPage->slug === $page->slug ? 'active' : '' }}">
								{{ $relatedPage->title }}
							</a>
						</li>
					@endforeach
				</ul>
			</div>
		</div>
	</section>
@endif

{!! $renderedHtml !!}

@include('includes.footer')