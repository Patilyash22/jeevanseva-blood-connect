
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DonorController as AdminDonorController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Find donors
Route::get('/find-donor', [DonorController::class, 'index'])->name('donors.find');
Route::get('/view-donor/{id}', [DonorController::class, 'viewDonor'])->name('donors.view');

// Donor registration
Route::get('/donor-registration', [DonorController::class, 'showRegistrationForm'])->name('donor.register');
Route::post('/donor-registration', [DonorController::class, 'register']);
Route::get('/donor-registration/success', [DonorController::class, 'showRegistrationSuccess'])->name('donor.register.success');

// Blood requests
Route::get('/blood-requests', [BloodRequestController::class, 'index'])->name('blood-requests.index');
Route::get('/request-blood', [BloodRequestController::class, 'showRequestForm'])->name('blood-requests.create');
Route::post('/request-blood', [BloodRequestController::class, 'store'])->name('blood-requests.store');
Route::get('/blood-requests/{bloodRequest}', [BloodRequestController::class, 'show'])->name('blood-requests.show');

// User routes
Route::middleware(['auth'])->group(function () {
    Route::get('/user-dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/buy-credits', [UserController::class, 'showBuyCredits'])->name('credits.buy');
    Route::post('/buy-credits', [UserController::class, 'processCreditsPayment'])->name('credits.purchase');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Donor management
    Route::get('/donors', [AdminDonorController::class, 'index'])->name('donors.index');
    Route::get('/donors/create', [AdminDonorController::class, 'create'])->name('donors.create');
    Route::post('/donors', [AdminDonorController::class, 'store'])->name('donors.store');
    Route::get('/donors/{donor}/edit', [AdminDonorController::class, 'edit'])->name('donors.edit');
    Route::put('/donors/{donor}', [AdminDonorController::class, 'update'])->name('donors.update');
    Route::patch('/donors/{donor}/status', [AdminDonorController::class, 'updateStatus'])->name('donors.update-status');
    Route::delete('/donors/{donor}', [AdminDonorController::class, 'destroy'])->name('donors.destroy');
    Route::get('/donors/export', [AdminDonorController::class, 'export'])->name('donors.export');
    
    // User management
    Route::resource('users', AdminUserController::class);
    
    // FAQs management
    Route::resource('faqs', AdminFaqController::class);
    Route::post('/faqs/order', [AdminFaqController::class, 'updateOrder'])->name('faqs.order');
    
    // Testimonials management
    Route::resource('testimonials', AdminTestimonialController::class);
    Route::patch('/testimonials/{testimonial}/toggle-approval', [AdminTestimonialController::class, 'toggleApproval'])->name('testimonials.toggle-approval');
    
    // Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});
