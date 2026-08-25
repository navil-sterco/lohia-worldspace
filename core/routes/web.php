<?php

use App\Http\Controllers\Admin\ContactFormController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ModuleEntryController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectionBuilderController;
use App\Http\Controllers\Admin\SeoSettingController;
use App\Http\Controllers\Admin\SupportInformationController;
use App\Http\Controllers\Admin\TabController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ChannelPartnerController;
use App\Http\Controllers\Frontend\CMSController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\frontend\EmiController;
use App\Http\Controllers\Frontend\EventController;
use App\Http\Controllers\Frontend\FaqController;
use App\Http\Controllers\Frontend\GalleryPageController;
use App\Http\Controllers\Frontend\JobApplicationController;
use App\Http\Controllers\Frontend\ModularPageController;
use App\Http\Controllers\Frontend\NewsController;
use App\Http\Controllers\Frontend\PeopleController;
use App\Http\Controllers\Frontend\ProjectsController;
use App\Http\Controllers\Frontend\TestimonialController;
use App\Models\Page;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/admin', function () {
    return redirect('login');
});

Route::get('/admin', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('admin.login');
});

Route::get('admin/login', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('admin.login');

Route::prefix('admin')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    //Tabs
    Route::resource('tabs', TabController::class);

    // Home Page
    Route::get('/home-page', [HomePageController::class, 'edit'])->name('home-page.edit');
    Route::post('/home-page', [HomePageController::class, 'update'])->name('home-page.update');

    // Page Sections
    Route::resource('page-sections', PageSectionController::class);
    Route::put('/page-sections/{pageSection}/toggle-status', [PageSectionController::class, 'toggleStatus'])->name('page-sections.toggle-status');

    // Modules
    Route::resource('modules', ModuleController::class)->except(['destroy']);
    Route::get('modules/{module}/destroy', [ModuleController::class, 'destroy'])->name('modules.destroy');

    // Module Entries
    Route::prefix('modules')->group(function () {
        Route::get('/{module}/entries', [ModuleEntryController::class, 'index'])->name('modules.entries.index');
        Route::get('/{module}/entries/create', [ModuleEntryController::class, 'create'])->name('modules.entries.create');
        Route::post('/{module}/entries', [ModuleEntryController::class, 'store'])->name('modules.entries.store');
        Route::get('/{module}/entries/{entry}', [ModuleEntryController::class, 'show'])->name('modules.entries.show');
        Route::get('/{module}/entries/{entry}/edit', [ModuleEntryController::class, 'edit'])->name('modules.entries.edit');
        Route::post('/{module}/entries/{entry}', [ModuleEntryController::class, 'update'])->name('modules.entries.update');
        Route::delete('/{module}/entries/{entry}', [ModuleEntryController::class, 'destroy'])->name('modules.entries.destroy');
        Route::get('/{module}/entries/{entry}/mapping', [ModuleEntryController::class, 'mapping'])->name('modules.entries.mapping');
        Route::post('/{module}/entries/{entry}/mapping', [ModuleEntryController::class, 'attachMapping'])->name('modules.entries.mapping.attach');
        Route::get('/{module}/entries/{entry}/sub-pages', [ModuleEntryController::class, 'pages'])->name('modules.entries.pages');
        Route::post('/{module}/entries/{entry}/sub-pages', [ModuleEntryController::class, 'storePage'])->name('modules.entries.pages.store');
        Route::get('/{module}/entries/{entry}/sub-pages/{page}/edit', [ModuleEntryController::class, 'editPage'])->name('modules.entries.pages.edit');
        Route::put('/{module}/entries/{entry}/sub-pages/{page}', [ModuleEntryController::class, 'updatePage'])->name('modules.entries.pages.update');
        Route::get('/{module}/entries/{entry}/sub-pages/{page}/sections', [ModuleEntryController::class, 'pageSections'])->name('modules.entries.pages.sections');
        Route::post('/{module}/entries/{entry}/sub-pages/{page}/sections', [ModuleEntryController::class, 'storePageSections'])->name('modules.entries.pages.sections.store');
        Route::get('/{module}/entries/{entry}/sub-pages-section/{pivot}', [ModuleEntryController::class, 'destroyPageSection'])->name('modules.entries.pages.sections.destroy');
        Route::delete('/{module}/entries/{entry}/sub-pages/{page}', [ModuleEntryController::class, 'detachPage'])->name('modules.entries.pages.detach');
        Route::get('/{module}/entries/{entry}/detail', [ModuleEntryController::class, 'detail'])->name('modules.entries.detail');
        Route::post('/{module}/entries/{entry}/detail', [ModuleEntryController::class, 'storeDetail'])->name('modules.entries.detail.store');
        Route::put('/{module}/entries/{entry}/toggle-publish', [ModuleEntryController::class, 'togglePublish'])->name('modules.entries.toggle-publish');
    });

    // Sections
    Route::get('/pages/{page}/sections', [SectionBuilderController::class, 'sections'])->name('pages.sections');
    Route::post('/pages/{page}/sections', [SectionBuilderController::class, 'storeSections'])->name('pages.sections.store');
    Route::get('/pages-section/{id}', [SectionBuilderController::class, 'destroySection'])->name('pages.sections.destroy');

    // Pages Routes
    Route::resource('pages', PageController::class);
    Route::prefix('pages')->group(function () {
        Route::put('/{page}/toggle-publish', [PageController::class, 'togglePublish'])->name('pages.toggle-publish');
        Route::get('/{pageId}/sections', [SectionBuilderController::class, 'create'])->name('pages.sections.create');
    });

    // Gallery
    Route::resource('gallery', GalleryController::class)->except(['destroy', 'edit', 'update']);
    Route::get('gallery/{gallery}/destroy', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    //Contact Form
    Route::get('/contact-forms', [ContactFormController::class, 'index'])->name('contact-forms.index');
    Route::get('contact-forms/{contactForm}/destroy', [ContactFormController::class, 'destroy'])->name('contact-forms.destroy');

    //Seo
    Route::resource('seo', SeoSettingController::class)->except(['destroy']);
    Route::prefix('seo')->group(function () {
        Route::get('/{seo}/destroy', [SeoSettingController::class, 'destroy'])->name('seo.destroy');
    });

    //Support Information
    Route::resource('support-information', SupportInformationController::class);
    Route::prefix('support-information')->group(function () {
        Route::get('/{supportInformation}/destroy', [SupportInformationController::class, 'destroy'])->name('support-information.destroy');
    });

    // Users
    Route::resource('users', UserController::class)->except(['show']);

    // Roles & Permissions
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('permissions', PermissionController::class)->except(['show']);
});

//Projects
Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectsController::class, 'detail'])->name('projects.detail');

//People
Route::get('/people', [PeopleController::class, 'index'])->name('people.index');

//Contact
Route::get('/contact-us', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact-us', [ContactController::class, 'store'])->name('contact.store');

// Emi
Route::get('/emi-calculator', [EmiController::class, 'index'])->name('emi.index');

//News
Route::get('/news', [NewsController::class, 'index'])->name('news.index');

//Testimonials
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');

//Gallery
Route::get('/gallery', [GalleryPageController::class, 'index'])->name('gallery.index');

//Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'detail'])->name('blog.show');

//Job details
Route::get('/jobs/{slug}', [PeopleController::class, 'detail'])->name('jobs.show');
Route::get('/apply-job/{slug?}', [JobApplicationController::class, 'create'])->name('apply-job');
Route::post('/apply-job', [JobApplicationController::class, 'store'])->name('apply-job.store');

//Events
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'detail'])->name('events.show');

//FAQ
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

//Channel Partners
Route::get('/channel-partners', [ChannelPartnerController::class, 'index'])->name('channel-partners.index');

Route::get('/{slug?}', function ($slug = 'home') {
    $page = Page::where('slug', $slug)->firstOrFail();

    if ($page->page_type === 'modular') {
        return app(ModularPageController::class)->index($slug);
    }

    return app(CMSController::class)->index($slug);
})->name('pages.frontend.show');

require __DIR__ . '/auth.php';
