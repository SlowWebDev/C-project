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
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\UnifiedSeoController;

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

// Sitemap
Route::get('/sitemap.xml', function() {
    if (file_exists(public_path('sitemap.xml'))) {
        return response()->file(public_path('sitemap.xml'), [
            'Content-Type' => 'application/xml'
        ]);
    }
    return response('Sitemap not found', 404);
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
// Redirect login to admin.login for backward compatibility
Route::get('login', function() {
    return redirect()->route('admin.login');
})->name('login');

Route::middleware(['web', 'guest'])->group(function () {
    Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('admin/login', [LoginController::class, 'login'])
        ->name('admin.login.submit')
        ->middleware('throttle:5,1');
    Route::post('admin/register', [LoginController::class, 'register'])
        ->name('admin.register')
        ->middleware('throttle:3,1');
});

// 2FA Routes (require authentication but not full 2FA verification)
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {
    Route::get('2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('2fa/verify', [TwoFactorController::class, 'verify'])
        ->name('2fa.verify')
        ->middleware('throttle:5,1');
    Route::get('2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.show-verify');
    Route::post('2fa/verify-code', [TwoFactorController::class, 'processVerify'])
        ->name('2fa.process-verify')
        ->middleware('throttle:10,1');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', '2fa', 'security', 'track.activity'])->group(function () {
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
    
    // Security Management Routes
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [SecurityController::class, 'overview'])->name('overview');
    Route::patch('/password', [SecurityController::class, 'updatePassword'])
        ->name('update-password')
        ->middleware('throttle:3,5');
    Route::patch('/email', [SecurityController::class, 'updateEmail'])
        ->name('update-email')
        ->middleware('throttle:2,10');
        Route::get('/activity-logs', [SecurityController::class, 'activityLogs'])->name('activity-logs');
        Route::get('/security-events', [SecurityController::class, 'securityEvents'])->name('security-events');
        Route::get('/device-management', [SecurityController::class, 'deviceManagement'])->name('device-management');
        Route::patch('/devices/{device}/block', [SecurityController::class, 'blockDevice'])->name('device-block');
        Route::patch('/devices/{device}/unblock', [SecurityController::class, 'unblockDevice'])->name('device-unblock');
        Route::patch('/devices/{device}/trust', [SecurityController::class, 'trustDevice'])->name('device-trust');
        Route::patch('/devices/{device}/untrust', [SecurityController::class, 'untrustDevice'])->name('device-untrust');
        Route::get('/settings', [SecurityController::class, 'settings'])->name('settings');
    });
    
    // Unified SEO Management Routes
    Route::prefix('seo')->name('seo.')->group(function () {
        Route::get('/', [UnifiedSeoController::class, 'index'])->name('unified');
        Route::post('/update', [UnifiedSeoController::class, 'update'])->name('update');
        Route::post('/reset', [UnifiedSeoController::class, 'reset'])->name('reset');
        Route::get('/preview/{page}', [UnifiedSeoController::class, 'preview'])->name('preview');
    });
});