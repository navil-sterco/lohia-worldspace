<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\ContactForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-contact')->only(['index', 'show']);
        $this->middleware('permission:create-contact')->only(['create', 'store']);
        $this->middleware('permission:edit-contact')->only(['edit', 'update']);
        $this->middleware('permission:delete-contact')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $contactForms = ContactForm::filter(['search' => $search])->orderBy('id', 'desc')->paginate(10)->withQueryString()->through(function ($item) {
            return [
                'id'            => $item->id,
                'interest'      => $item->interest,
                'name'          => $item->name,
                'email'         => $item->email,
                'phone'         => $item->phone,
                'location'      => $item->location,
                'buyer_type'    => $item->buyer_type,
                'budget'        => $item->budget,
                'property_type' => $item->property_type,
                'comment'       => $item->comment,
                'details'       => $item->details,
                'ip_address'    => $item->ip_address,
                'created_at'    => $item->created_at,
                'updated_at'    => $item->updated_at,
            ];
        });

        return Inertia::render('ContactForm/Index', [
            'contactForms'   => $contactForms,
            'searchTerm'     => $search ?? '',
            'interestLabels' => $this->interestOptions(),
            'buyerTypeLabels'    => $this->buyerTypes(),
            'budgetLabels'       => $this->budgetRanges(),
            'propertyTypeLabels' => $this->propertyTypes(),
        ]);
    }

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

    public function destroy(Request $request, ContactForm $contactForm)
    {
        $contactForm->delete();
        return back()->with('success', 'Contact form deleted successfully!');
    }
}
