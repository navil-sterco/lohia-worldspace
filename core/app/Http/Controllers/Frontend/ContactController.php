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
            'buyerTypes' => $this->buyerTypes(),
            'budgetRanges' => $this->budgetRanges(),
            'propertyTypes' => $this->propertyTypes(),
        ]);
    }

    protected function buyerTypes(): array
    {
        return [
            'first_time_buyer' => 'First-Time Buyer',
            'investor' => 'Investor',
            'upgrading' => 'Upgrading',
            'downsizing' => 'Downsizing',
        ];
    }

    protected function budgetRanges(): array
    {
        return [
            'under_250k' => 'Under $250,000',
            '250k_500k' => '$250,000 - $500,000',
            '500k_1m' => '$500,000 - $1,000,000',
            '1m_plus' => '$1,000,000+',
        ];
    }

    protected function propertyTypes(): array
    {
        return [
            'single_family' => 'Single Family Home',
            'condo' => 'Condo / Apartment',
            'townhouse' => 'Townhouse',
            'land' => 'Land',
            'commercial' => 'Commercial',
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'location' => ['required', 'string', 'max:255'],
            'buyer_type' => ['required', 'string', 'in:' . implode(',', array_keys($this->buyerTypes()))],
            'budget' => ['required', 'string', 'in:' . implode(',', array_keys($this->budgetRanges()))],
            'property_type' => ['required', 'string', 'in:' . implode(',', array_keys($this->propertyTypes()))],
            'comment' => ['nullable', 'string', 'max:2000'],
            'g-recaptcha-response' => ['required', new Recaptcha()],
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Please enter your phone number.',
            'location.required' => 'Please enter your location.',
            'buyer_type.required' => 'Please select what type of buyer you are.',
            'budget.required' => 'Please select your budget.',
            'property_type.required' => 'Please select a property type.',
            'g-recaptcha-response.required' => 'Please confirm you are not a robot.',
        ]);
        
        unset($validated['g-recaptcha-response']);
        $validated['ip_address'] = $request->ip();

        $inquiry = ContactForm::create($validated);

        return view('thank-you.thank-you');
    }
}
