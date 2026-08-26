<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Models\Page;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $page = Page::published()->bySlug('contact-us')->firstOrFail();
        $viewData = $page->getModularPageData();
        return view('dynamic.contact-us', [
            'page' => $page,
            'section' => $viewData,
            'interestOptions' => $this->interestOptions(),
            'buyerTypes' => $this->buyerTypes(),
            'budgetRanges' => $this->budgetRanges(),
            'propertyTypes' => $this->propertyTypes(),
        ]);
    }

    /**
     * Purpose / interest dropdown options.
     * Add/remove options here — Blade will pick it up automatically.
     */
    protected function interestOptions(): array
    {
        return [
            'buying_property' => 'BUYING A PROPERTY',
            'land_proposal'   => 'LAND PROPOSAL',
            'vendor'          => 'BECOME A LOHIA WORLDSPACE VENDOR',
            'media'           => 'MEDIA ENQUIRIES',
            'investor'        => 'INVESTOR ENQUIRIES',
        ];
    }

    protected function buyerTypes(): array
    {
        return [
            'first_time_buyer' => 'First-Time Buyer',
            'investor'         => 'Investor',
            'upgrading'        => 'Upgrading',
            'downsizing'       => 'Downsizing',
        ];
    }

    protected function budgetRanges(): array
    {
        return [
            'under_250k' => 'Under $250,000',
            '250k_500k'  => '$250,000 - $500,000',
            '500k_1m'    => '$500,000 - $1,000,000',
            '1m_plus'    => '$1,000,000+',
        ];
    }

    protected function propertyTypes(): array
    {
        return [
            'single_family' => 'Single Family Home',
            'condo'         => 'Condo / Apartment',
            'townhouse'     => 'Townhouse',
            'land'          => 'Land',
            'commercial'    => 'Commercial',
        ];
    }

    protected function vendorCategories(): array
    {
        return [
            'Manufacturer' => 'Manufacturer',
            'Trader'       => 'Trader',
            'Dealer'       => 'Dealer',
        ];
    }

    /**
     * Fields shared by every section, always validated.
     */
    protected function commonRules(): array
    {
        return [
            'interest' => ['required', 'string', 'in:' . implode(',', array_keys($this->interestOptions()))],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255'],
            'phone'    => ['required', 'string', 'max:20'],
            'comment'  => ['nullable', 'string', 'max:2000'],
            // 'g-recaptcha-response' => ['required', new Recaptcha()],
        ];
    }

    /**
     * Fields specific to each section, validated only when that
     * section's interest is submitted.
     */
    protected function sectionRules(?string $interest): array
    {
        return match ($interest) {
            'buying_property' => [
                'location'      => ['required', 'string', 'max:255'],
                'buyer_type'    => ['required', 'string', 'in:' . implode(',', array_keys($this->buyerTypes()))],
                'budget'        => ['required', 'string', 'in:' . implode(',', array_keys($this->budgetRanges()))],
                'property_type' => ['required', 'string', 'in:' . implode(',', array_keys($this->propertyTypes()))],
            ],

            'land_proposal' => [
                'location' => ['required', 'string', 'max:255'],
            ],

            'vendor' => [
                'vendor_company_name'            => ['required', 'string', 'max:255'],
                'vendor_address'                 => ['required', 'string', 'max:255'],
                'vendor_contact_person'          => ['required', 'string', 'max:255'],
                'vendor_phone'                   => ['required', 'string', 'max:20'],
                'vendor_fax'                      => ['required', 'string', 'max:20'],
                'vendor_email'                   => ['required', 'email', 'max:255'],
                'vendor_ownership_detail'        => ['required', 'string', 'max:255'],
                'vendor_category'                => ['required', 'string', 'in:' . implode(',', array_keys($this->vendorCategories()))],
                'vendor_registration_no'         => ['required', 'string', 'max:100'],
                'vendor_registration_date'       => ['required', 'date'],
                'vendor_gst'                      => ['required', 'string', 'max:50'],
                'vendor_pan'                      => ['required', 'string', 'max:20'],
                'vendor_pf'                        => ['required', 'string', 'max:50'],
                'vendor_esi'                       => ['required', 'string', 'max:50'],
                'vendor_bank_name'                => ['required', 'string', 'max:255'],
                'vendor_bank_branch'              => ['required', 'string', 'max:255'],
                'vendor_bank_account'             => ['required', 'string', 'max:100'],
                'vendor_account_type'             => ['required', 'string', 'max:50'],
                'vendor_ifsc'                     => ['required', 'string', 'max:20'],
                'vendor_trade_member'             => ['required', 'string', 'max:500'],
                'vendor_parent_companies'         => ['required', 'string', 'max:500'],
                'vendor_facility_area'            => ['required', 'string', 'max:255'],
                'vendor_total_employees'          => ['required', 'string', 'max:20'],
                'vendor_permanent_staff'          => ['required', 'string', 'max:20'],
                'vendor_audited_accounts'         => ['required', 'in:YES,NO'],
                'vendor_service_backup'           => ['required', 'string', 'max:500'],
                'vendor_credit_period'            => ['required', 'string', 'max:255'],
                'vendor_years_experience'         => ['required', 'string', 'max:255'],
                'vendor_other_industries'         => ['required', 'string', 'max:500'],
                'vendor_qms_since'                => ['required', 'string', 'max:255'],
                'vendor_certification_authority'  => ['required', 'string', 'max:255'],
                'vendor_iso14001'                  => ['required', 'string', 'max:255'],
                'vendor_ohsas18001'                => ['required', 'string', 'max:255'],
                'vendor_quality_tools'             => ['required', 'string', 'max:500'],
                'vendor_other_info'                => ['nullable', 'string', 'max:500'],
                'vendor_completed_by'             => ['required', 'string', 'max:255'],
                'vendor_completed_by_mobile'      => ['required', 'string', 'max:20'],
            ],

            'media' => [
                'location' => ['required', 'string', 'max:255'],
            ],

            'investor' => [
                'location' => ['required', 'string', 'max:255'],
            ],

            default => [],
        };
    }

    protected function validationMessages(): array
    {
        return [
            'interest.required' => 'Please select what we can help you with.',
            'name.required'     => 'Please enter your name.',
            'email.required'    => 'Please enter your email address.',
            'email.email'       => 'Please enter a valid email address.',
            'phone.required'    => 'Please enter your phone number.',
            'location.required' => 'Please enter your location.',
            'buyer_type.required'    => 'Please select what type of buyer you are.',
            'budget.required'        => 'Please select your budget.',
            'property_type.required' => 'Please select a property type.',
            'vendor_category.required' => 'Please select the category you belong to.',
            'vendor_registration_date.date' => 'Please enter a valid registration date.',
            'g-recaptcha-response.required' => 'Please confirm you are not a robot.',
            '*.required' => 'This field is required.',
        ];
    }

    public function store(Request $request)
    {

        $interest = $request->input('interest');

        $rules = $this->commonRules() + $this->sectionRules($interest);

        $validated = $request->validate($rules, $this->validationMessages());

        unset($validated['g-recaptcha-response']);


        if ($interest === 'vendor' && $request->filled('vendor_phone_code')) {
            $code = trim($request->input('vendor_phone_code'));
            $number = trim($validated['vendor_phone'] ?? '');
            if (!str_starts_with($number, $code)) {
                $validated['vendor_phone'] = trim($code . ' ' . $number);
            }
        }


        $common = collect($validated)
            ->only(['interest', 'name', 'email', 'phone', 'location', 'buyer_type', 'budget', 'property_type', 'comment'])
            ->toArray();


        $details = collect($validated)
            ->except(array_keys($common))
            ->toArray();

        $inquiry = ContactForm::create($common + [
            'details'    => $details ?: null,
            'ip_address' => $request->ip(),
        ]);

        return view('thank-you.thank-you');
    }

    public function vendor()
    {
        return view('dynamic.vendor-form');
    }
}
