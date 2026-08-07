@include('includes.header')

<link rel="stylesheet" type="text/css" href="css/inner-page.css">
<section class="faqpage_sec">
    <div class="full-width">
        <div class="container max-content-lg">
            <div class="col-lg-10">
                <div class="faq_caption">
                    {!! $section['cms']['faq_top_section_0'] ?? '' !!}
                    <div class="faq_accordion accordions">
                        @foreach ($faqs as $key => $faq)
                            <div class="accordions-item">
                                <button class="accordions-button" type="button">
                                    {{ $faq['question'] ?? $faq->question }}
                                </button>

                                <div class="accordions-collapse {{ $key === 0 ? 'active' : '' }}">
                                    <div class="accordions-body">
                                        <p>{!! $faq['answer'] ?? $faq->answer !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {!! $section['cms']['faq_disclaimer_1'] ?? '' !!}
                </div>
            </div>
        </div>
    </div>
</section>

@include('includes.footer')