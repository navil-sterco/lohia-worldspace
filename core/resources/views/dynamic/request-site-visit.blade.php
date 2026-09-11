@include('includes.header')

<section class="contact_sec">
    <div class="container-lg">
        <div class="sec_title">
            <h1 class="title21">Request Site Visit</h1>
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

                <form action="{{ route('request-site-visit.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="originFrom" value="WEBSITE L1">
                    <div class="contactform_wrap">
                    <div class="form-group">
                        <input type="text" name="firstName" value="{{ old('firstName') }}" placeholder="Name"
                            class="form-control @error('firstName') is-invalid @enderror">
                        @error('firstName')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="input_group">
                            <div class="input_group_item">
                                <input type="text" name="mobilePhone" value="{{ old('mobilePhone') }}"
                                    placeholder="Phone" class="form-control @error('mobilePhone') is-invalid @enderror">
                                @error('mobilePhone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="input_group_item">
                                <input type="text" name="cityDesc" value="{{ old('cityDesc') }}"
                                    placeholder="City" class="form-control @error('cityDesc') is-invalid @enderror">
                                @error('cityDesc')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <select name="udF_16" class="form-select @error('udF_16') is-invalid @enderror">
                            <option value="" disabled @selected(!old('udF_16'))>Purpose of Enquiry</option>
                            @foreach ($purposeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('udF_16') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('udF_16')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select name="budgetFrom" class="form-select @error('budgetFrom') is-invalid @enderror">
                            <option value="" disabled @selected(!old('budgetFrom'))>Budget From</option>
                            @foreach ($budgetFromOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('budgetFrom') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('budgetFrom')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select name="budgetTo" class="form-select @error('budgetTo') is-invalid @enderror">
                            <option value="" disabled @selected(!old('budgetTo'))>Budget To</option>
                            @foreach ($budgetToOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('budgetTo') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('budgetTo')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select name="udF_17" class="form-select @error('udF_17') is-invalid @enderror">
                            <option value="" disabled @selected(!old('udF_17'))>Planned Timeline for Purchase</option>
                            @foreach ($timelineOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('udF_17') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('udF_17')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select name="udF_18" class="form-select @error('udF_18') is-invalid @enderror">
                            <option value="" disabled @selected(!old('udF_18'))>How Did You Hear About Us?</option>
                            @foreach ($hearAboutUsOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('udF_18') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('udF_18')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="text" name="udF_6" value="{{ old('udF_6') }}"
                            class="form-control @error('udF_6') is-invalid @enderror" placeholder="Preferred Site Visit Date & Time" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'"> 
                        @error('udF_6')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <textarea name="comments" placeholder="Write your Comments"
                            class="form-control @error('comments') is-invalid @enderror">{{ old('comments') }}</textarea>
                        @error('comments')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        @error('g-recaptcha-response')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                    <button type="submit" class="submit_btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('form');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

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
