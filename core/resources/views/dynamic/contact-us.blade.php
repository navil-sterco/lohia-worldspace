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
                <a href="#" class="btn_down">Buying A Property</a>
                <h3 class="title48">LEAVE A MESSAGE TO BUY A PROPERTY</h3>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Please fix the errors below and try again.
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="contactform_wrap">
                    @csrf

                    <div class="form-group">
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Name"
                            class="form-control @error('name') is-invalid @enderror"
                        >
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input
                            type="text"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Email"
                            class="form-control @error('email') is-invalid @enderror"
                        >
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="input_group">
                            <div class="input_group_item">
                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="Phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                >
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="input_group_item">
                                <input
                                    type="text"
                                    name="location"
                                    value="{{ old('location') }}"
                                    placeholder="Location"
                                    class="form-control @error('location') is-invalid @enderror"
                                >
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
                                <option value="{{ $value }}" @selected(old('buyer_type') === $value)>{{ $label }}</option>
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
                                <option value="{{ $value }}" @selected(old('budget') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('budget')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select name="property_type" class="form-select @error('property_type') is-invalid @enderror">
                            <option value="">What type of property are you interested in?</option>
                            @foreach ($propertyTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('property_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('property_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <textarea
                            name="comment"
                            placeholder="Comment"
                            class="form-control @error('comment') is-invalid @enderror"
                        >{{ old('comment') }}</textarea>
                        @error('comment')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('.contactform_wrap');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'contact_buy_property' })
                    .then(function (token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    })
                    .catch(function () {
                        form.submit();
                    });
            });
        });
    });
</script>

@include('includes.footer')