@include('includes.header')

<section class="contact_sec">
    <div class="container-lg">
        <div class="sec_title">
            <h1 class="title21">Contact Us</h1>
        </div>
        <div class="contact_grid">
            {!! $section['cms']['contact_us_0'] ?? '' !!}

            <div class="contact_form">
                <h5 class="title21">What can we help you with</h5>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

               

                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf

                    {{-- Purpose dropdown --}}
                    <div class="form-group">
                        <input type="hidden" name="interest" id="interestSelect" value="{{ old('interest') }}"
                            class="@error('interest') is-invalid @enderror">

                        <div class="dropdown_menu">
                            <button type="button" class="dropdown_toggle" id="interestToggleBtn">
                                {{ old('interest') ? $interestOptions[old('interest')] : 'BUYING A PROPERTY' }}
                            </button>
                            <ul class="dropdown_item" id="interestDropdownList">
                                @foreach ($interestOptions as $value => $label)
                                    <li>
                                        @if ($value === 'vendor')
                                            <a href="{{ route('vendor.index') }}">{{ $label }}</a>
                                        @else
                                            <a href="#" data-value="{{ $value }}">{{ $label }}</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @error('interest')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- =========================================================
                         SECTION: Buying A Property
                    ========================================================== --}}
                    <div class="contactform_wrap" data-interest="buying_property">
                        <h3 class="title48">LEAVE A MESSAGE TO BUY A PROPERTY</h3>

                        <div class="form-group">
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="email" value="{{ old('email') }}" placeholder="Email"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                        placeholder="Phone" class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="location" value="{{ old('location') }}"
                                        placeholder="Location"
                                        class="form-control @error('location') is-invalid @enderror">
                                    @error('location')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <select name="buyer_type" class="form-select @error('buyer_type') is-invalid @enderror">
                                <option value="">What type of buyer are you?</option>
                                @foreach ($buyerTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('buyer_type') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('buyer_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <select name="budget" class="form-select @error('budget') is-invalid @enderror">
                                <option value="">What is your budget?</option>
                                @foreach ($budgetRanges as $value => $label)
                                    <option value="{{ $value }}" @selected(old('budget') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('budget')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <select name="property_type"
                                class="form-select @error('property_type') is-invalid @enderror">
                                <option value="">What type of property are you interested in?</option>
                                @foreach ($propertyTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('property_type') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('property_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <textarea name="comment" placeholder="Comment" class="form-control @error('comment') is-invalid @enderror">{{ old('comment') }}</textarea>
                            @error('comment')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- =========================================================
                         SECTION: Land Proposal
                    ========================================================== --}}
                    <div class="contactform_wrap" data-interest="land_proposal" style="display:none;">
                        <h3 class="title48">LEAVE A MESSAGE FOR LAND PROPOSAL</h3>

                        <div class="form-group">
                            <input type="text" name="name" placeholder="Name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="email" placeholder="Email"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="phone" placeholder="Phone"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="location" placeholder="Location"
                                        class="form-control @error('location') is-invalid @enderror">
                                    @error('location')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <textarea name="comment" placeholder="Comment" class="form-control @error('comment') is-invalid @enderror"></textarea>
                            @error('comment')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>




                    {{-- =========================================================
                         SECTION: Media Enquiries
                    ========================================================== --}}
                    <div class="contactform_wrap" data-interest="media" style="display:none;">
                        <h3 class="title48">LEAVE A MESSAGE FOR A MEDIA ENQUIRY</h3>

                        <div class="form-group">
                            <input type="text" name="name" placeholder="Name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="email" placeholder="Email"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="phone" placeholder="Phone"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="location" placeholder="Location"
                                        class="form-control @error('location') is-invalid @enderror">
                                    @error('location')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <textarea name="comment" placeholder="Comment" class="form-control @error('comment') is-invalid @enderror"></textarea>
                            @error('comment')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- =========================================================
                         SECTION: Investor Enquiries
                    ========================================================== --}}
                    <div class="contactform_wrap" data-interest="investor" style="display:none;">
                        <h3 class="title48">LEAVE A MESSAGE FOR INVESTOR ENQUIRIES</h3>

                        <div class="form-group">
                            <input type="text" name="name" placeholder="Name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="email" placeholder="Email"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="phone" placeholder="Phone"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="location" placeholder="Location"
                                        class="form-control @error('location') is-invalid @enderror">
                                    @error('location')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <textarea name="comment" placeholder="Comment" class="form-control @error('comment') is-invalid @enderror"></textarea>
                            @error('comment')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        @error('g-recaptcha-response')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="submit_btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('form');
        var hiddenInterest = document.querySelector('#interestSelect');
        var toggleBtn = document.querySelector('#interestToggleBtn');
        var dropdownLinks = document.querySelectorAll('#interestDropdownList a[data-value]');
        var sections = document.querySelectorAll('.contactform_wrap');

        // Default to the first option if nothing is set yet (fresh page load)
        if (!hiddenInterest.value && dropdownLinks.length) {
            hiddenInterest.value = dropdownLinks[0].dataset.value;
        }

        function toggleSections() {
            var current = hiddenInterest.value;
            sections.forEach(function(section) {
                var isVisible = section.dataset.interest === current;
                section.style.display = isVisible ? '' : 'none';
            });
        }

        dropdownLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var value = this.dataset.value;
                hiddenInterest.value = value;
                toggleBtn.textContent = this.textContent.trim();
                toggleSections();
            });
        });

        toggleSections();

        form.addEventListener('submit', function(e) {
            e.preventDefault();


            sections.forEach(function(section) {
                var isVisible = section.style.display !== 'none';
                var fields = section.querySelectorAll('input, select, textarea');
                fields.forEach(function(field) {
                    field.disabled = !isVisible;
                });
            });

            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {
                        action: 'contact_form'
                    })
                    .then(function(token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    })
                    .catch(function() {
                        form.submit();
                    });
            });
        });
    });
</script>
@include('includes.footer')
