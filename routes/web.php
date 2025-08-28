<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\CareerController as AdminCareerController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PagesController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

// Static Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Projects
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

// Media
Route::get('/media', [MediaController::class, 'index'])->name('media.index');
Route::get('/media/{media:slug}', [MediaController::class, 'show'])->name('media.show');

// Careers
Route::get('/careers', [CareerController::class, 'index'])->name('careers.index');
Route::post('/careers/apply', [CareerController::class, 'apply'])
    ->name('careers.apply')
    ->middleware('throttle:5,1'); // Max 5 submissions per minute

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store')
    ->middleware('throttle:3,10'); // Max 3 submissions per 10 minutes

// Project 
Route::post('/projects/{project}/inquiry', [ContactController::class, 'storeProjectInquiry'])
    ->name('project.inquiry')
    ->middleware('throttle:3,10'); 

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('admin/login', [LoginController::class, 'login'])->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout Route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // Settings Routes
    Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.logo');
    Route::get('/settings/logos', [SettingsController::class, 'getLogos'])->name('settings.logos');
    Route::get('/settings/careers', [SettingsController::class, 'showCareersSettings'])->name('settings.careers');
    Route::post('/settings/careers', [SettingsController::class, 'updateCareersImages'])->name('settings.careers.update');
    Route::post('/settings/footer', [SettingsController::class, 'updateFooter'])->name('settings.footer');
    
    // Page Management Routes
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/home', [PagesController::class, 'home'])->name('home');
        Route::put('/home', [PagesController::class, 'updateHome'])->name('home.update');
        Route::get('/about', [PagesController::class, 'about'])->name('about');
        Route::put('/about', [PagesController::class, 'updateAbout'])->name('about.update');
        Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
        Route::put('/contact', [PagesController::class, 'updateContact'])->name('contact.update');
    });
    
    // Projects Management
    Route::resource('projects', AdminProjectController::class);
    
    // Media Management
    Route::resource('media', AdminMediaController::class)->parameters([
        'media' => 'medium'
    ]);

    // Contact Management
    Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::delete('contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
    Route::patch('contacts/{contact}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.status');
    Route::patch('contacts/mark-all-read', [AdminContactController::class, 'markAllAsRead'])->name('contacts.mark-all-read');

    // Careers Management
    Route::prefix('careers')->name('careers.')->group(function () {
        Route::get('/', [AdminCareerController::class, 'index'])->name('index');
        Route::get('/create', [AdminCareerController::class, 'create'])->name('create');
        Route::post('/', [AdminCareerController::class, 'store'])->name('store');
        Route::get('/{job}/edit', [AdminCareerController::class, 'edit'])->name('edit');
        Route::patch('/{job}', [AdminCareerController::class, 'update'])->name('update');
        Route::delete('/{job}', [AdminCareerController::class, 'destroy'])->name('destroy');
        Route::patch('/{job}/toggle', [AdminCareerController::class, 'toggleJobStatus'])->name('toggle');

        // Applications
        Route::prefix('applications')->name('applications.')->group(function () {
            Route::get('/{application}', [AdminCareerController::class, 'showApplication'])->name('show');
            Route::get('/{application}/cv', [AdminCareerController::class, 'downloadCV'])->name('download-cv');
            Route::patch('/{application}/status', [AdminCareerController::class, 'updateStatus'])->name('status');
        });
    });
});