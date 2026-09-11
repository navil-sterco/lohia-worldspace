<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\RequestSiteVisit;
use Illuminate\Http\Request;

class RequestSiteVisitController extends Controller
{
    public function index()
    {
        $page = Page::published()->bySlug('request-site-visit')->firstOrFail();
        $viewData = $page->getModularPageData();

        return view('dynamic.request-site-visit', [
            'page' => $page,
            'section' => $viewData,
            'purposeOptions' => $this->purposeOptions(),
            'budgetFromOptions' => $this->budgetFromOptions(),
            'budgetToOptions' => $this->budgetToOptions(),
            'timelineOptions' => $this->timelineOptions(),
            'hearAboutUsOptions' => $this->hearAboutUsOptions(),
        ]);
    }

    protected function purposeOptions(): array
    {
        return [
            'Buying for Self-Use' => 'Buying for Self-Use',
            'Buying for Parents/Family' => 'Buying for Parents/Family',
            'Investment Purpose' => 'Investment Purpose',
            'Just Exploring' => 'Just Exploring',
        ];
    }

    protected function budgetFromOptions(): array
    {
        return [
            '5000000.000' => '50L',
            '10000000.000' => '1Cr',
            '15000000.000' => '1.5Cr',
            '20000000.000' => '2Cr',
        ];
    }

    protected function budgetToOptions(): array
    {
        return [
            '10000000.000' => '1Cr',
            '15000000.000' => '1.5Cr',
            '20000000.000' => '2Cr',
            '50000000.000' => '5Cr',
        ];
    }

    protected function timelineOptions(): array
    {
        return [
            'Within 3 months' => 'Within 3 months',
            '3–6 months' => '3–6 months',
            '6–12 months' => '6–12 months',
            'Not Decided / Just Exploring' => 'Not Decided / Just Exploring',
        ];
    }

    protected function hearAboutUsOptions(): array
    {
        return [
            'Facebook / Instagram' => 'Facebook / Instagram',
            'Google Search' => 'Google Search',
            'Friend / Family' => 'Friend / Family',
            'Newspaper / Outdoor' => 'Newspaper / Outdoor',
            'Other' => 'Other',
        ];
    }

    protected function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobilePhone' => ['required', 'string', 'max:20'],
            'cityDesc' => ['required', 'string', 'max:255'],
            'udF_16' => ['required', 'string', 'in:' . implode(',', array_keys($this->purposeOptions()))],
            'budgetFrom' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->budgetFromOptions()))],
            'budgetTo' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->budgetToOptions()))],
            'udF_17' => ['required', 'string', 'in:' . implode(',', array_keys($this->timelineOptions()))],
            'udF_18' => ['required', 'string', 'in:' . implode(',', array_keys($this->hearAboutUsOptions()))],
            'udF_6' => ['required', 'date'],
            'comments' => ['required', 'string', 'max:2000'],
            'g-recaptcha-response' => ['required'],
        ];
    }

    protected function validationMessages(): array
    {
        return [
            'firstName.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'mobilePhone.required' => 'Please enter your phone number.',
            'cityDesc.required' => 'Please enter your city.',
            'udF_16.required' => 'Please select the purpose of your enquiry.',
            'udF_17.required' => 'Please select your planned timeline.',
            'udF_18.required' => 'Please tell us how you heard about us.',
            'udF_6.required' => 'Please select a preferred site visit date.',
            'comments.required' => 'Please write your comments.',
            'g-recaptcha-response.required' => 'Please confirm you are not a robot.',
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->validationMessages());

        unset($validated['g-recaptcha-response']);

        RequestSiteVisit::create([
            'first_name' => $validated['firstName'],
            'email' => $validated['email'],
            'mobile_phone' => $validated['mobilePhone'],
            'city_desc' => $validated['cityDesc'],
            'udf_16' => $validated['udF_16'],
            'budget_from' => $validated['budgetFrom'] ?? null,
            'budget_to' => $validated['budgetTo'] ?? null,
            'udf_17' => $validated['udF_17'],
            'udf_18' => $validated['udF_18'],
            'udf_6' => $validated['udF_6'],
            'comments' => $validated['comments'],
            'origin_from' => $request->input('originFrom', 'WEBSITE L1'),
            'ip_address' => $request->ip(),
        ]);

        return view('thank-you.thank-you');
    }
}