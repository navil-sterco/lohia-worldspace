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
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'phone' => $item->phone,
                'location' => $item->location,
                'buyer_type' => $item->buyer_type,
                'budget' => $item->budget,
                'property_type' => $item->property_type,
                'comment' => $item->comment,
                'created_at' => $item->created_at,
            ];
        });

        return Inertia::render('ContactForm/Index', [
            'contactForms' => $contactForms,
            'searchTerm' => $search ?? '',
        ]);
    }

    public function destroy(Request $request, ContactForm $contactForm)
    {
        $contactForm->delete();
        return back()->with('success', 'Contact form deleted successfully!');
    }
}
