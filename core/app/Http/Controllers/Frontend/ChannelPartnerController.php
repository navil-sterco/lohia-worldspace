<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Page;
use App\Models\ChannelPartner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChannelPartnerController extends Controller
{
    public function index()
    {
        $page = Page::published()->bySlug('channel-partners')->firstOrFail();
        $viewData = $page->getModularPageData();

        return view('dynamic.channel-partners', [
            'page' => $page,
            'section' => $viewData,
            'organizationTypes' => $this->organizationTypes(),
            'associationOptions' => $this->associationOptions(),
            'businessTypes' => $this->businessTypes(),
        ]);
    }

    protected function organizationTypes(): array
    {
        return [
            'Sole Proprietorship' => 'Sole Proprietorship',
            'Partnership' => 'Partnership',
            'Private Limited' => 'Private Limited',
            'Public Limited' => 'Public Limited',
            'Individuals' => 'Individuals',
            'Other' => 'Other',
        ];
    }

    protected function associationOptions(): array
    {
        return [
            'BRAI' => 'BRAI',
            'CREA' => 'CREA',
            'NAR' => 'NAR',
            'Other' => 'Other',
        ];
    }

    protected function businessTypes(): array
    {
        return [
            'Land Sourcing' => 'Land Sourcing',
            'Commercial Sales' => 'Commercial Sales',
            'Residential Sales' => 'Residential Sales',
            'Other' => 'Other',
        ];
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'pan' => ['required', 'string', 'max:20'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'work_profile' => ['nullable', 'string', 'max:2000'],
            'other_organizations' => ['nullable', 'string', 'max:2000'],
            'region_of_operations' => ['nullable', 'string', 'max:255'],
            'sales_team_member_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'date_of_establishment' => ['required', 'string', 'max:100'],
            'organization_type' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->organizationTypes()))],
            'association_member' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->associationOptions()))],
            'business_type' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->businessTypes()))],
            'registration_certificate' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:5120'],
            'rera_certificate' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:5120'],
            'registered_address' => ['nullable', 'string', 'max:255'],
            'company_pan_details' => ['nullable', 'string', 'max:50'],
            'gst_certificate' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:5120'],
            'g-recaptcha-response' => ['required'],
        ];
    }

    protected function validationMessages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'pan.required' => 'Please enter your PAN.',
            'phone.required' => 'Please enter your phone number.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'company_name.required' => 'Please enter the name of the company.',
            'date_of_establishment.required' => 'Please enter the date of establishment.',
            '*.mimes' => 'Only PNG, JPG, WEBP, or PDF files are allowed.',
            '*.max' => 'File must not exceed 5MB.',
            'g-recaptcha-response.required' => 'Please confirm you are not a robot.',
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->validationMessages());

        unset($validated['g-recaptcha-response']);

        if ($request->hasFile('registration_certificate')) {
            $image = $request->file('registration_certificate');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/channel-partner/registration/'), $imageName);
            $validated['registration_certificate_path'] = 'assets/img/channel-partner/registration/' . $imageName;
        }

        if ($request->hasFile('rera_certificate')) {
            $image = $request->file('rera_certificate');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/channel-partner/rera/'), $imageName);
            $validated['rera_certificate_path'] = 'assets/img/channel-partner/rera/' . $imageName;
        }

        if ($request->hasFile('gst_certificate')) {
            $image = $request->file('gst_certificate');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/channel-partner/gst/'), $imageName);
            $validated['gst_certificate_path'] = 'assets/img/channel-partner/gst/' . $imageName;
        }

        unset($validated['registration_certificate'], $validated['rera_certificate'], $validated['gst_certificate']);

        ChannelPartner::create($validated + [
            'ip_address' => $request->ip(),
        ]);

        return view('thank-you.thank-you');
    }


}