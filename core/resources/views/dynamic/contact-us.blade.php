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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Please fix the errors below and try again.
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
                        SECTION: Become A Vendor
                    ========================================================== --}}
                    {{-- <div class="contactform_wrap" data-interest="vendor" style="display:none;">
                        <h3 class="title48">FILL UP TO A LOHIA WORLDSPACE VENDOR</h3>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> COMPANY DETAILS</p>
                            <hr>
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_company_name"
                                value="{{ old('vendor_company_name') }}" placeholder="Name of the Company"
                                class="form-control @error('vendor_company_name') is-invalid @enderror">
                            @error('vendor_company_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_address" value="{{ old('vendor_address') }}"
                                placeholder="Address of Registered Office"
                                class="form-control @error('vendor_address') is-invalid @enderror">
                            @error('vendor_address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> CONTACT PERSON, TELEPHONE, FAX, EMAIL</p>
                            <hr>
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_contact_person"
                                value="{{ old('vendor_contact_person') }}" placeholder="Contact Person"
                                class="form-control @error('vendor_contact_person') is-invalid @enderror">
                            @error('vendor_contact_person')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_phone" value="{{ old('vendor_phone') }}"
                                        placeholder="Phone Number"
                                        class="form-control @error('vendor_phone') is-invalid @enderror">
                                    @error('vendor_phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="vendor_fax" value="{{ old('vendor_fax') }}"
                                        placeholder="FAX"
                                        class="form-control @error('vendor_fax') is-invalid @enderror">
                                    @error('vendor_fax')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="email" name="vendor_email" value="{{ old('vendor_email') }}"
                                placeholder="Email Address"
                                class="form-control @error('vendor_email') is-invalid @enderror">
                            @error('vendor_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> DETAILS OF OWNERSHIP</p>
                            <hr>
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_ownership_detail"
                                value="{{ old('vendor_ownership_detail') }}" placeholder="Details of Ownership"
                                class="form-control @error('vendor_ownership_detail') is-invalid @enderror">
                            @error('vendor_ownership_detail')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <select name="vendor_category"
                                class="form-select @error('vendor_category') is-invalid @enderror">
                                <option value="">The category you belong to</option>
                                <option value="Manufacturer" @selected(old('vendor_category') === 'Manufacturer')>Manufacturer</option>
                                <option value="Trader" @selected(old('vendor_category') === 'Trader')>Trader</option>
                                <option value="Dealer" @selected(old('vendor_category') === 'Dealer')>Dealer</option>
                            </select>
                            @error('vendor_category')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> REGISTRATION DETAILS (WITH VARIOUS GOVT.
                                AUTHORITIES)</p>
                            <hr>
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_registration_no"
                                        value="{{ old('vendor_registration_no') }}" placeholder="Registration No."
                                        class="form-control @error('vendor_registration_no') is-invalid @enderror">
                                    @error('vendor_registration_no')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="date" name="vendor_registration_date"
                                        value="{{ old('vendor_registration_date') }}" placeholder="Registration Date"
                                        class="form-control @error('vendor_registration_date') is-invalid @enderror">
                                    @error('vendor_registration_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_gst" value="{{ old('vendor_gst') }}"
                                        placeholder="GST No."
                                        class="form-control @error('vendor_gst') is-invalid @enderror">
                                    @error('vendor_gst')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="vendor_pan" value="{{ old('vendor_pan') }}"
                                        placeholder="PAN No."
                                        class="form-control @error('vendor_pan') is-invalid @enderror">
                                    @error('vendor_pan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_pf" value="{{ old('vendor_pf') }}"
                                        placeholder="P.F No."
                                        class="form-control @error('vendor_pf') is-invalid @enderror">
                                    @error('vendor_pf')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="vendor_esi" value="{{ old('vendor_esi') }}"
                                        placeholder="E.S.I Number"
                                        class="form-control @error('vendor_esi') is-invalid @enderror">
                                    @error('vendor_esi')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_bank_name" value="{{ old('vendor_bank_name') }}"
                                placeholder="Bank Name"
                                class="form-control @error('vendor_bank_name') is-invalid @enderror">
                            @error('vendor_bank_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_bank_branch"
                                        value="{{ old('vendor_bank_branch') }}" placeholder="Bank Branch"
                                        class="form-control @error('vendor_bank_branch') is-invalid @enderror">
                                    @error('vendor_bank_branch')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="vendor_bank_account"
                                        value="{{ old('vendor_bank_account') }}" placeholder="Bank A/c No."
                                        class="form-control @error('vendor_bank_account') is-invalid @enderror">
                                    @error('vendor_bank_account')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_account_type"
                                        value="{{ old('vendor_account_type') }}" placeholder="Bank A/c Type"
                                        class="form-control @error('vendor_account_type') is-invalid @enderror">
                                    @error('vendor_account_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="vendor_ifsc" value="{{ old('vendor_ifsc') }}"
                                        placeholder="IFSC No."
                                        class="form-control @error('vendor_ifsc') is-invalid @enderror">
                                    @error('vendor_ifsc')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> ARE YOU A MEMBER OF ANY TRADE BODIES /
                                ASSOCIATIONS?</p>
                            <input type="text" name="vendor_trade_member"
                                value="{{ old('vendor_trade_member') }}"
                                placeholder="Mention the names, Reg. No. and date"
                                class="form-control @error('vendor_trade_member') is-invalid @enderror">
                            @error('vendor_trade_member')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> SUMMARY OF SERVICES OR PRODUCTS AND CAPABILITY
                            </p>
                            <input type="text" name="vendor_parent_companies"
                                value="{{ old('vendor_parent_companies') }}"
                                placeholder="Names of Parent, Associate and Subsidiary Companies"
                                class="form-control @error('vendor_parent_companies') is-invalid @enderror">
                            @error('vendor_parent_companies')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> DESCRIPTION OF FACILITIES (IF APPLICABLE)</p>
                            <input type="text" name="vendor_facility_area"
                                value="{{ old('vendor_facility_area') }}"
                                placeholder="Technical/Manufacturing/Workshop Floor Areas (m2)"
                                class="form-control @error('vendor_facility_area') is-invalid @enderror">
                            @error('vendor_facility_area')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> DETAILS OF EMPLOYEES</p>
                            <hr>
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_total_employees"
                                        value="{{ old('vendor_total_employees') }}"
                                        placeholder="Total No of Employees"
                                        class="form-control @error('vendor_total_employees') is-invalid @enderror">
                                    @error('vendor_total_employees')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="vendor_permanent_staff"
                                        value="{{ old('vendor_permanent_staff') }}"
                                        placeholder="Number of Permanent Staff"
                                        class="form-control @error('vendor_permanent_staff') is-invalid @enderror">
                                    @error('vendor_permanent_staff')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <p>Do you have audited accounts for the previous 2 years?</p>
                            <select name="vendor_audited_accounts"
                                class="form-select @error('vendor_audited_accounts') is-invalid @enderror">
                                <option value="YES" @selected(old('vendor_audited_accounts') === 'YES')>YES</option>
                                <option value="NO" @selected(old('vendor_audited_accounts') === 'NO')>NO</option>
                            </select>
                            @error('vendor_audited_accounts')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_service_backup"
                                value="{{ old('vendor_service_backup') }}"
                                placeholder="Furnish details of post supply service back up provided by you"
                                class="form-control @error('vendor_service_backup') is-invalid @enderror">
                            @error('vendor_service_backup')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_credit_period"
                                value="{{ old('vendor_credit_period') }}"
                                placeholder="What is your maximum credit period that can be offered to us?"
                                class="form-control @error('vendor_credit_period') is-invalid @enderror">
                            @error('vendor_credit_period')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> COMPANY EXPERIENCE</p>
                            <hr>
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_years_experience"
                                value="{{ old('vendor_years_experience') }}"
                                placeholder="How long have you been in business?"
                                class="form-control @error('vendor_years_experience') is-invalid @enderror">
                            @error('vendor_years_experience')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_other_industries"
                                value="{{ old('vendor_other_industries') }}"
                                placeholder="Which other Industries do you supply the product to?"
                                class="form-control @error('vendor_other_industries') is-invalid @enderror">
                            @error('vendor_other_industries')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> QUALITY MANAGEMENT SYSTEM<br>
                                WHETHER QMS AS PER ISO 9001:2000 IS IMPLEMENTED IN YOUR ORGANIZATION?</p>
                            <hr>
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_qms_since" value="{{ old('vendor_qms_since') }}"
                                placeholder="If yes, since when is it implemented?"
                                class="form-control @error('vendor_qms_since') is-invalid @enderror">
                            @error('vendor_qms_since')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_certification_authority"
                                value="{{ old('vendor_certification_authority') }}"
                                placeholder="Mention the Certification authority and Certificate No."
                                class="form-control @error('vendor_certification_authority') is-invalid @enderror">
                            @error('vendor_certification_authority')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_iso14001"
                                        value="{{ old('vendor_iso14001') }}"
                                        placeholder="Do you follow EMS as per ISO 14001:2004?"
                                        class="form-control @error('vendor_iso14001') is-invalid @enderror">
                                    @error('vendor_iso14001')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="vendor_ohsas18001"
                                        value="{{ old('vendor_ohsas18001') }}"
                                        placeholder="Do you follow OHSAS as per ISO 18001:2007?"
                                        class="form-control @error('vendor_ohsas18001') is-invalid @enderror">
                                    @error('vendor_ohsas18001')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_quality_tools"
                                value="{{ old('vendor_quality_tools') }}"
                                placeholder="Do you follow any Product/Process Quality system improvement tools like Lean, TQM etc.?"
                                class="form-control @error('vendor_quality_tools') is-invalid @enderror">
                            @error('vendor_quality_tools')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="text" name="vendor_other_info" value="{{ old('vendor_other_info') }}"
                                placeholder="Any Other Information"
                                class="form-control @error('vendor_other_info') is-invalid @enderror">
                            @error('vendor_other_info')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <p><i class="fa fa-angle-double-right"></i> NAME, DESIGNATION, SIGNATURE OF THE PERSON
                                COMPLETING THIS QUESTIONNAIRE</p>
                            <hr>
                        </div>

                        <div class="form-group">
                            <div class="input_group">
                                <div class="input_group_item">
                                    <input type="text" name="vendor_completed_by"
                                        value="{{ old('vendor_completed_by') }}" placeholder="Contact Person"
                                        class="form-control @error('vendor_completed_by') is-invalid @enderror">
                                    @error('vendor_completed_by')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input_group_item">
                                    <input type="text" name="vendor_completed_by_mobile"
                                        value="{{ old('vendor_completed_by_mobile') }}" placeholder="Mobile No."
                                        class="form-control @error('vendor_completed_by_mobile') is-invalid @enderror">
                                    @error('vendor_completed_by_mobile')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <textarea name="comment" placeholder="Comment" class="form-control @error('comment') is-invalid @enderror">{{ old('comment') }}</textarea>
                            @error('comment')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div> --}}

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('form');
        var hiddenInterest = document.querySelector('#interestSelect');
        var toggleBtn = document.querySelector('#interestToggleBtn');
        var dropdownLinks = document.querySelectorAll('#interestDropdownList a[data-value]'); // <-- changed
        var sections = document.querySelectorAll('.contactform_wrap');

        // Default to the first option if nothing is set yet (fresh page load)
        if (!hiddenInterest.value && dropdownLinks.length) {
            hiddenInterest.value = dropdownLinks[0].dataset.value;
        }

        function toggleSections() {
            var current = hiddenInterest.value;
            sections.forEach(function(section) {
                section.style.display = (section.dataset.interest === current) ? '' : 'none';
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
