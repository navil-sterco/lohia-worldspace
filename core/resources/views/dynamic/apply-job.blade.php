@include('includes.header')

@php
    $jobTitle = $job['detail']['title'] ?? null;
    $jobSlug  = request()->route('slug');
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
                        <div class="apply_victor">
                            <img src="{{ asset('frontend-assets/images/victor-dash11.svg') }}" alt="about" class="img-fluid reveal-left w-100 revealed-left">
                        </div>
                    </div>

                    <div class="applyfrm_wrapper">
                        <h5 class="title21">Please enter all the relevant information in the fields below.</h5>

                        <h3 class="title30">
                            Role/ Skill :
                            <strong>{{ $jobTitle ?? 'General Application' }}</strong>
                        </h3>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form class="aply_form" action="{{ route('apply-job.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf

                            <input type="hidden" name="job_slug" value="{{ $jobSlug }}">
                            <input type="hidden" name="job_title" value="{{ $jobTitle }}">

                            <div class="form-group">
                                <input type="text" name="name" class="form-control" placeholder="Name*" value="{{ old('name') }}">
                                @error('name') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <input type="text" name="email" class="form-control" placeholder="Email*" value="{{ old('email') }}">
                                @error('email') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <input type="text" name="phone" class="form-control" placeholder="Phone*" value="{{ old('phone') }}">
                                @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <select name="best_time_to_call" class="form-select">
                                    <option value="" @selected(old('best_time_to_call') === null)>-- Best time to Call --</option>
                                    <option value="Morning" @selected(old('best_time_to_call') === 'Morning')>Morning</option>
                                    <option value="Afternoon" @selected(old('best_time_to_call') === 'Afternoon')>Afternoon</option>
                                    <option value="Evening" @selected(old('best_time_to_call') === 'Evening')>Evening</option>
                                </select>
                                @error('best_time_to_call') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <div class="upload_file">
                                    <input type="file" name="cv" accept="image/png,image/jpg,image/webp,application/pdf" class="form-control" id="cv_input">
                                    <span class="file-name" id="cv_file_name">Upload CV</span>
                                </div>
                                @error('cv') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="applyform_btn">
                                <button type="submit" class="submit_btn">Apply Job</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('cv_input')?.addEventListener('change', function (e) {
        const nameEl = document.getElementById('cv_file_name');
        nameEl.textContent = e.target.files.length ? e.target.files[0].name : 'Upload CV';
    });
</script>

@include('includes.footer')