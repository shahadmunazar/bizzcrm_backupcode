<?php
use App\Http\Controllers\Admin\{AdminFrontendController, ManageUserController};
use App\Http\Controllers\Attendance\{AttendaceFrontendController, BiometricController};
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Deals\DealsFrontendController;
use App\Http\Controllers\Employee\{AttendaceController, CrmSourceController, HolidayController, HolidayTypeController, ShiftController, LeaveTypeController, LeaveController, LeaveTransactionController, EmployeeFrontendController, EmployeManageController, LetterGenerationController};
use App\Http\Controllers\{FacebookController, GoogleController, OrgChartController};
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Leads\{BulkUploadController, LeadsController, LeadsFrontendController, ReportController, StatsController};
use App\Http\Controllers\FieldTracking\CustomerController;
use App\Http\Controllers\FieldTracking\DashboardController;
use App\Http\Controllers\FieldTracking\ActivityController;
use App\Http\Controllers\FieldTracking\PaymentController;
use App\Http\Controllers\FieldTracking\TourController;
use App\Http\Controllers\FieldTracking\TargetController;
use App\Http\Controllers\FieldTracking\PerformanceDashboardController;
use App\Http\Controllers\FieldTracking\LocationTrackingFrontendController;
use App\Http\Controllers\NotificationController; // Added
use App\Http\Controllers\PayRoll\{PayRollController, SalaryController, SalaryReportController, SalarySettingController};
use App\Http\Controllers\Project\ProjectFrontendController;
use App\Http\Controllers\Setting\{BankController, CompanyBranchController, FieldSyncController, LeadIntegrationController, SettingsController, MailFilterController};
use App\Http\Controllers\Accounts\{InvoiceController, AccountController, LedgerController};
use App\Http\Controllers\Setting\{SystemController, WorkModeController, PaymentModeController};
use App\Http\Controllers\SuperAdmin\{DepartmentController, SuperadminFrontendController, AccountDeletionRequestController};
use App\Http\Controllers\AccountDeletionController;
use App\Http\Controllers\Tracking\TrackingFrontendController;
use App\Http\Controllers\WhatsAppBusinessController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\WhatsApp\WhatsAppController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Employee\{AssetController, AssetTypeController};
use App\Http\Controllers\Call\CallDashboardController;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\{Artisan, DB, Route};
Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
});
Route::get('/run-api-fetch', function () {
    Artisan::call('api:fetch');
    return response()->json([
        'status' => 'success',
        'message' => 'api:fetch command executed successfully.',
        'output' => Artisan::output()
    ]);
});

Route::get('/clear-all-caches', function () {
    $errors = [];
    try {
        // Clear Application Cache
        \Illuminate\Support\Facades\Cache::flush();
    } catch (\Exception $e) { $errors[] = 'Cache flush failed: ' . $e->getMessage(); }

    try {
        // Clear Cache Locks
        if (\Illuminate\Support\Facades\Schema::hasTable('cache_locks')) {
            \Illuminate\Support\Facades\DB::table('cache_locks')->truncate();
        }
    } catch (\Exception $e) { $errors[] = 'Cache locks truncate failed: ' . $e->getMessage(); }

    try {
        // Clear Compiled Views
        $viewPaths = \Illuminate\Support\Facades\File::glob(storage_path('framework/views/*.php'));
        \Illuminate\Support\Facades\File::delete($viewPaths);
    } catch (\Exception $e) { $errors[] = 'View cache delete failed: ' . $e->getMessage(); }

    try {
        // Clear Route, Config, and Event caches
        \Illuminate\Support\Facades\File::delete([
            app()->getCachedConfigPath(),
            app()->getCachedRoutesPath(),
            app()->getCachedEventsPath(),
            app()->getCachedServicesPath(),
            app()->getCachedPackagesPath(),
        ]);
    } catch (\Exception $e) { $errors[] = 'Config/Route cache delete failed: ' . $e->getMessage(); }

    // Attempt to run Artisan commands as a fallback/bonus
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    } catch (\Exception $e) {
        $errors[] = 'Artisan optimize:clear failed: ' . $e->getMessage();
    }

    return response()->json([
        'status' => empty($errors) ? 'success' : 'partial_success',
        'message' => 'Cache clearing process finished.',
        'errors' => $errors
    ]);
});
Route::get('/auto-login', [AuthController::class, 'autoLogin'])->name('auto.login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('post.register');
Route::get("search-country", [AuthController::class, 'SearchCountry'])->name('search.country');
Route::post('/resend-verfication-email', [AuthController::class, 'resendverificationEmail'])->name('resend.verification');
Route::post('/login', [AuthController::class, 'login'])->name('post.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/cancel-api-logout', [AuthController::class, 'cancelApiLogout']);

Route::get("/login-mobile", [AuthController::class, 'login_mobile'])->name('login.mobile');
Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
Route::get("/forget-password", [AuthController::class, 'forget_password'])->name('forget.password');
Route::post("/forget-email", [AuthController::class, 'forget_Email'])->name('forget.email');
Route::get('/forget-password/{token}', [AuthController::class, 'reset_page'])->name('forget.page');
Route::post('reset-password-submit', [AuthController::class, 'Reset_Password'])->name('reset.password.submit');
Route::get('/verify-email/{token}', [AuthController::class, 'verify'])->name('verify.email');

// Facebook OAuth Callback Routes (public, no auth required)
Route::get('/auth/facebook/callback', [AuthController::class, 'facebookCallback'])->name('facebook.callback.auth');
Route::get('/login/facebook/callback', [AuthController::class, 'facebookCallback'])->name('facebook.callback.login');
Route::get('/auth/facebook/connect', [FacebookController::class, 'connect'])->name('facebook.connect');
Route::get('{box_slug}/auth/facebook/connect', [FacebookController::class, 'connect'])->name('facebook.connect.box');

// Google OAuth Callback Routes (public, no auth required)
Route::get('/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
Route::get('/auth/google/login', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/login-callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('google.login.callback');

// Facebook Login Routes
Route::get('/auth/facebook/login', [\App\Http\Controllers\Auth\FacebookAuthController::class, 'redirect'])->name('facebook.login');
Route::get('/auth/facebook/login-callback', [\App\Http\Controllers\Auth\FacebookAuthController::class, 'callback'])->name('facebook.login.callback');

Route::get('/auth/google/connect', [GoogleController::class, 'connect'])->name('google.connect');
Route::get('{box_slug}/auth/google/connect', [GoogleController::class, 'connect'])->name('google.connect.box');
Route::get('{box_slug}/google/sync-emails', [GoogleController::class, 'syncEmails'])->name('google.sync_emails');
Route::post('{box_slug}/google/send-email', [GoogleController::class, 'sendEmail'])->name('google.send_email');
Route::get('{box_slug}/google/download-attachment/{message_id}/{attachment_id}/{filename}', [GoogleController::class, 'downloadAttachment'])->name('google.download_attachment');
Route::post('{box_slug}/google/disconnect/{id}', [GoogleController::class, 'disconnect'])->name('google.disconnect');
Route::get('{box_slug}/google/view-email-html/{id}', [GoogleController::class, 'viewEmailHtml'])->name('google.view_email_html');

// Facebook Webhook Routes (public, no auth required)
Route::get('/webhook/facebook', [FacebookController::class, 'webhookValidate'])->name('facebook.webhook.validate');
Route::post('/webhook/facebook', [FacebookController::class, 'webhook'])->name('facebook.webhook');

// Custom Webhook Route for CRM Integrations
Route::post('/api/webhook/{id}', [LeadIntegrationController::class, 'webhook'])->name('settings.webhook');
Route::post('/api/callerdesk', [LeadIntegrationController::class, 'callerdesk'])->name('leadintegration.callerdesk');

// WhatsApp Business Webhook Routes (public, no auth required)
Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'webhookValidate'])->name('whatsapp.webhook.validate');
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'webhook'])->name('whatsapp.webhook');



Route::get('/', [FrontendController::class, 'facebook_index'])->name('home');
Route::get('/features', [FrontendController::class, 'features'])->name('frontend.features');
Route::get('/pricing', [FrontendController::class, 'Pricing'])->name('frontend.pricing');
Route::get('/pricing-plans', [FrontendController::class, 'Pricing'])->name('pricing');
Route::get('/privacy-policy', [FrontendController::class, 'PrivicayPolicy'])->name('privacy_policy');
Route::get('/delete-account', [FrontendController::class, 'deleteAccount'])->name('delete_account');

Route::get('/terms-and-conditions', [FrontendController::class, 'termsCondition'])->name('terms_and_conditions');
Route::get('/refund-and-cancellation', [FrontendController::class, 'RefundCancellation'])->name('refund_and_cancellation');
Route::get('/faq', [FrontendController::class, 'faq'])->name('frontend.faq');
Route::get('/faq-data', [FrontendController::class, 'faqApi'])->name('frontend.faq.api');

// Vendor Public Routes
Route::get('vendor-login', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_login'])->name('superadmin.vendors.login');
Route::post('vendor-login', [\App\Http\Controllers\VendorAuthController::class, 'login'])->name('post.vendor.login');
Route::get('vendor-signup', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_signup'])->name('superadmin.vendors.signup');
Route::post('vendor-signup', [\App\Http\Controllers\VendorAuthController::class, 'register'])->name('post.vendor.register');

// Public route for bulk report files (accessible without box_slug or auth)
// URL format: /bulk_reports/{bulkUploadId}/{fileName}
Route::get('bulk_reports/{bulkUploadId}/{fileName}', [BulkUploadController::class, 'serveReportFile'])->name('bulk.report.file');

// Vendor Panel Routes
Route::prefix('vendor-panel')->name('vendor.')->middleware(['auth'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'index'])->name('dashboard');
    Route::get('transaction', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'transaction'])->name('transaction');
    Route::get('commission', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'commission'])->name('commission');
    Route::get('referrals', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'referrals'])->name('referrals');
    Route::get('referral_details/{id}', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'referral_details'])->name('referral_details');

    // API Routes for AJAX loading
    Route::get('api/dashboard', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'api_dashboard'])->name('api.dashboard');
    Route::get('api/transaction', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'api_transaction'])->name('api.transaction');
    Route::get('api/referrals', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'api_referrals'])->name('api.referrals');
    Route::get('api/referral_details/{id}', [\App\Http\Controllers\VendorPanel\VendorDashboardController::class, 'api_referral_details'])->name('api.referral_details');
});

//  Multi-tenant routes with {box_slug}
Route::group(['prefix' => '{box_slug}', 'middleware' => ['auth']], function () {

    Route::get('/whatsapp-tester', [WhatsAppWebhookController::class, 'testerPage'])->name('whatsapp.tester');
    Route::post('/whatsapp-tester/simulate', [WhatsAppWebhookController::class, 'simulateIncoming'])->name('whatsapp.tester.simulate');
    // Tracking
    Route::prefix('tracking')->middleware('check.roles.box:tracking')->group(
        function () {
            Route::get('dashboard', [TrackingFrontendController::class, 'index'])->name('tracking.dashboard');
        }
    );

    // Fieldstracking Static Routes
    Route::prefix('fieldstracking')->name('fieldstracking.')->group(function () {
        Route::get('customer-list', [CustomerController::class, 'customerList'])->name('customer-list');
        Route::get('activities/by-target', [CrmSourceController::class, 'getActivitiesByTarget'])->name('common.activities');
        Route::get('customer/list/data', [CustomerController::class, 'customerListData'])->name('customer.data');
        Route::get('customer/search-leads', [CustomerController::class, 'searchLeads'])->name('customer.searchLeads');
        Route::get('customer/store', [CustomerController::class, 'store'])->name('customer.store.get'); // Not really get but ok
        Route::post('customer/store', [CustomerController::class, 'store'])->name('customer.store');
        Route::get('customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
        Route::delete('customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');
        Route::post('customer/check-in', [CustomerController::class, 'storeCheckIn'])->name('customer.checkin.store');
        Route::get('customer/{customer_id}/check-ins', [CustomerController::class, 'checkInHistory'])->name('customer.checkin.history');
        
        // Location Tracking Dashboard
        Route::get('location-dashboard', [LocationTrackingFrontendController::class, 'index'])->name('location.dashboard');

        Route::get('payment-admin', [PaymentController::class, 'paymentAdmin'])->name('payment-admin');
        Route::get('payment-gateways', [PaymentController::class, 'paymentGateways'])->name('payment-gateways');
        Route::get('payment', [PaymentController::class, 'payment'])->name('payment');
        Route::get('payments', [PaymentController::class, 'payments'])->name('payments');
        Route::get('payments/data', [PaymentController::class, 'getPaymentsData'])->name('payments.data');
        Route::get('target-template', [TargetController::class, 'targetTemplate'])->name('target-template');
        Route::post('target-template/store', [TargetController::class, 'storeTemplate'])->name('target-template.store');
        Route::get('target-api/templates/{id}', [TargetController::class, 'getTemplate']);
        Route::post('target-template/update/{id}', [TargetController::class, 'updateTemplate'])->name('target-template.update');
        Route::post('target-template/delete/{id}', [TargetController::class, 'deleteTemplate'])->name('target-template.delete');

        Route::post('target-assignment/store', [TargetController::class, 'storeAssignment'])->name('target-assignment.store');

        Route::get('target-api/departments', [TargetController::class, 'getDepartments'])->name('target-api.departments');
        Route::get('target-api/templates', [TargetController::class, 'getTemplates'])->name('target-api.templates');
        Route::get('target-api/employees', [TargetController::class, 'getEmployees'])->name('target-api.employees');
        Route::get('target-api/teams', [TargetController::class, 'getTeams'])->name('target-api.teams');
        Route::get('target-api/assignments', [TargetController::class, 'getAllAssignments'])->name('target-api.assignments');
        Route::get('target-api/my-assignments', [TargetController::class, 'getMyAssignments'])->name('target-api.my-assignments');
        Route::get('my-target', [TargetController::class, 'myTarget'])->name('my-target');
        Route::get('team-targets', [TargetController::class, 'teamTargets'])->name('team-targets');
        
        // Performance Dashboards
        Route::get('employee-dashboard', [PerformanceDashboardController::class, 'employeeDashboard'])->name('employee-dashboard');
        Route::get('performance-dashboard', [PerformanceDashboardController::class, 'performanceDashboard'])->name('performance-dashboard');
        
        // Performance Dashboards API
        Route::get('dashboard-api/departments', [PerformanceDashboardController::class, 'getDepartments'])->name('dashboard-api.departments');
        Route::get('dashboard-api/teams', [PerformanceDashboardController::class, 'getTeams'])->name('dashboard-api.teams');
        Route::get('dashboard-api/employees', [PerformanceDashboardController::class, 'getEmployees'])->name('dashboard-api.employees');
        Route::get('dashboard-api/performance', [PerformanceDashboardController::class, 'getPerformance'])->name('dashboard-api.performance');

        Route::get('team-activity-detail', [ActivityController::class, 'teamActivityDetail'])->name('team-activity-detail');
        Route::get('team-activity', [ActivityController::class, 'teamActivity'])->name('team-activity');
        Route::post('team-activity-data', [ActivityController::class, 'teamActivityData'])->name('team-activity-data');
        Route::get('tour-bulk-uploading', [TourController::class, 'tourBulkUploading'])->name('tour-bulk-uploading');
        Route::get('tour-list', [TourController::class, 'tourList'])->name('tour-list');
        Route::post('tour-data', [TourController::class, 'tourListData'])->name('tour-data');
        Route::post('tour-form-data', [TourController::class, 'getFormData'])->name('tour-form-data');
        Route::post('tour-leads-data', [TourController::class, 'getLeadsData'])->name('tour-leads-data');
        Route::post('tour-store', [TourController::class, 'store'])->name('tour-store');
        Route::post('tour-get-customers', [TourController::class, 'getCustomers'])->name('tour-get-customers');
        Route::get('tour-plan', [TourController::class, 'tourPlan'])->name('tour-plan');
        Route::get('tracking-dashboard', [DashboardController::class, 'trackingDashboard'])->name('tracking-dashboard');
        
        // Location Tracking API for Web Dashboards
        Route::post('location', [\App\Http\Controllers\Api\LocationTrackingApiController::class, 'store'])->name('location.store');
        Route::get('location/current', [\App\Http\Controllers\Api\LocationTrackingApiController::class, 'current'])->name('location.current');
        Route::get('location/history', [\App\Http\Controllers\Api\LocationTrackingApiController::class, 'history'])->name('location.history');
        Route::get('location/route', [\App\Http\Controllers\Api\LocationTrackingApiController::class, 'route'])->name('location.route');

        Route::get('tracking-history', [ActivityController::class, 'trackingHistory'])->name('tracking-history');
        Route::get('tracking-history-data', [ActivityController::class, 'trackingHistoryData'])->name('tracking-history.data');
    });
    // Products
    Route::middleware('check.roles.box:admin,employee,superadmin')->group(function () {
        Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('accounts.products.index');
        Route::get('/products/download-template', [\App\Http\Controllers\ProductController::class, 'downloadTemplate'])->name('accounts.products.download_template');
        Route::post('/products/import', [\App\Http\Controllers\ProductController::class, 'import'])->name('accounts.products.import');
        Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('accounts.products.create');
        Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('accounts.products.store');
        Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('accounts.products.edit');
        Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('accounts.products.update');
        Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('accounts.products.destroy');
        Route::post('/products/bulk-delete', [\App\Http\Controllers\ProductController::class, 'bulkDestroy'])->name('accounts.products.bulk_delete');
        Route::get('/products/attributes/list', [\App\Http\Controllers\ProductAttributeController::class, 'list'])->name('accounts.products.attributes.list');
        Route::post('/products/attributes/company', [\App\Http\Controllers\ProductAttributeController::class, 'storeCompany'])->name('accounts.products.attributes.company');
        Route::post('/products/attributes/salt', [\App\Http\Controllers\ProductAttributeController::class, 'storeSalt'])->name('accounts.products.attributes.salt');
        Route::post('/products/attributes/hsn', [\App\Http\Controllers\ProductAttributeController::class, 'storeHsn'])->name('accounts.products.attributes.hsn');
        // Bulk Imports API
        Route::get('/imports/status/{type}', [\App\Http\Controllers\BulkImportController::class, 'status'])->name('accounts.imports.status');

        // Product HSNs
        Route::get('/product-hsns', [\App\Http\Controllers\ProductHsnController::class, 'index'])->name('accounts.product_hsns.index');
        Route::get('/product-hsns/download-template', [\App\Http\Controllers\ProductHsnController::class, 'downloadTemplate'])->name('accounts.product_hsns.download_template');
        Route::post('/product-hsns/import', [\App\Http\Controllers\ProductHsnController::class, 'import'])->name('accounts.product_hsns.import');
        Route::post('/product-hsns', [\App\Http\Controllers\ProductHsnController::class, 'store'])->name('accounts.product_hsns.store');
        Route::put('/product-hsns/{id}', [\App\Http\Controllers\ProductHsnController::class, 'update'])->name('accounts.product_hsns.update');
        Route::post('/product-hsns/bulk-delete', [\App\Http\Controllers\ProductHsnController::class, 'bulkDestroy'])->name('accounts.product_hsns.bulk_destroy');
        Route::delete('/product-hsns/{id}', [\App\Http\Controllers\ProductHsnController::class, 'destroy'])->name('accounts.product_hsns.destroy');
        Route::get('/product-hsns/{id}/history', [\App\Http\Controllers\ProductHsnController::class, 'history'])->name('accounts.product_hsns.history');
        Route::get('/product-hsns/get-details/{code}', [\App\Http\Controllers\ProductHsnController::class, 'getDetailsByCode'])->name('accounts.product_hsns.get_details');

        // Product Salts
        Route::get('/product-salts', [\App\Http\Controllers\ProductSaltController::class, 'index'])->name('accounts.product_salts.index');
        Route::get('/product-salts/download-template', [\App\Http\Controllers\ProductSaltController::class, 'downloadTemplate'])->name('accounts.product_salts.download_template');
        Route::post('/product-salts/import', [\App\Http\Controllers\ProductSaltController::class, 'import'])->name('accounts.product_salts.import');
        Route::post('/product-salts', [\App\Http\Controllers\ProductSaltController::class, 'store'])->name('accounts.product_salts.store');
        Route::put('/product-salts/{id}', [\App\Http\Controllers\ProductSaltController::class, 'update'])->name('accounts.product_salts.update');
        Route::post('/product-salts/bulk-delete', [\App\Http\Controllers\ProductSaltController::class, 'bulkDestroy'])->name('accounts.product_salts.bulk_destroy');
        Route::delete('/product-salts/{id}', [\App\Http\Controllers\ProductSaltController::class, 'destroy'])->name('accounts.product_salts.destroy');
    });

    // Deals
    Route::prefix('deals')->middleware('check.roles.box:deals')->group(
        function () {
            Route::get('dashboard', [DealsFrontendController::class, 'index'])->name('deals.dashboard');
        }
    );

    // Leads
    // Route::prefix('leads')->middleware('check.roles.box:leads')->group(function () {
    //     Route::get('dashboard', [LeadsFrontendController::class, 'index'])->name('leads.dashboard');
    // });

    // Projects
    Route::prefix('projects')->middleware('check.roles.box:project')->group(
        function () {
            Route::get('dashboard', [ProjectFrontendController::class, 'index'])->name('project.dashboard');
        }
    );

    // WhatsApp Integration
    Route::prefix('whatsapp')->middleware('check.subscription:WhatsApp API Integration')->group(function () {
        Route::get('/tester', [WhatsAppWebhookController::class, 'testerPage'])->name('whatsapp.tester.prefixed');
        Route::post('/tester/simulate', [WhatsAppWebhookController::class, 'simulateIncoming'])->name('whatsapp.tester.simulate.prefixed');
        Route::get('/', [WhatsAppController::class, 'index'])->name('whatsapp.index');
        Route::get('/dashboard', [WhatsAppController::class, 'dashboard'])->name('whatsapp.dashboard');
        
        // Business Profile
        Route::get('/profile', [WhatsAppController::class, 'getBusinessProfile'])->name('whatsapp.profile.get');
        Route::post('/profile', [WhatsAppController::class, 'updateBusinessProfile'])->name('whatsapp.profile.update');
        
        Route::post('/api/generate-token', [WhatsAppController::class, 'generateApiToken'])->name('whatsapp.api.generate_token');
        Route::post('/api/save-webhook', [WhatsAppController::class, 'saveWebhookUrl'])->name('whatsapp.api.save_webhook');
        Route::get('/api/recent-incoming', [WhatsAppController::class, 'getRecentIncomingMessages'])->name('whatsapp.api.recent_incoming');
        Route::get('/api/templates', [WhatsAppController::class, 'getApiTemplates'])->name('whatsapp.api.templates');
        Route::post('/api/templates/{id}/config', [WhatsAppController::class, 'saveApiTemplateConfig'])->name('whatsapp.api.templates.config');
        Route::get('/link-generator', [WhatsAppController::class, 'linkGenerator'])->name('whatsapp.link_generator');
        Route::get('/button-generator', [WhatsAppController::class, 'buttonGenerator'])->name('whatsapp.button_generator');
        Route::post('/generator/save', [WhatsAppController::class, 'saveGeneratedWidget'])->name('whatsapp.generator.save');
        Route::post('/select-account', [WhatsAppController::class, 'selectAccount'])->name('whatsapp.select_account');
        Route::get('/disconnect', [WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect');
        Route::post('/disconnect-single', [WhatsAppController::class, 'disconnectSingle'])->name('whatsapp.disconnect_single');
        Route::get('/chats/sidebar', [WhatsAppController::class, 'getChatSidebar'])->name('whatsapp.chats.sidebar');
        Route::get('/chats/{lead_id?}', [WhatsAppController::class, 'index'])->name('whatsapp.chats');
        Route::get('/chats/{lead_id}/messages', [WhatsAppController::class, 'getMessages'])->name('whatsapp.chats.messages');
        Route::post('/chats/{lead_id}/convert', [WhatsAppController::class, 'convertChatToLead'])->name('whatsapp.chats.convert');
        Route::post('/chats/send', [WhatsAppController::class, 'sendMessage'])->name('whatsapp.chats.send');
        Route::post('/messages/react', [WhatsAppController::class, 'reactMessage'])->name('whatsapp.messages.react');
        Route::post('/messages/pin', [WhatsAppController::class, 'pinMessage'])->name('whatsapp.messages.pin');
        Route::post('/messages/unpin', [WhatsAppController::class, 'unpinMessage'])->name('whatsapp.messages.unpin');
        Route::post('/messages/star', [WhatsAppController::class, 'starMessage'])->name('whatsapp.messages.star');
        Route::post('/messages/{id}/delete', [WhatsAppController::class, 'deleteMessage'])->name('whatsapp.messages.delete');
        Route::post('/chats/send-media', [WhatsAppController::class, 'sendMedia'])->name('whatsapp.chats.send_media');
        Route::get('/create-project', [WhatsAppController::class, 'createProject'])->name('whatsapp.create_project');
        Route::post('/create-project', [WhatsAppController::class, 'storeProject'])->name('whatsapp.store_project');
        Route::post('/link-project-account', [WhatsAppController::class, 'linkAccount'])->name('whatsapp.link_account');
        Route::post('/sync-meta', [WhatsAppController::class, 'syncMeta'])->name('whatsapp.sync_meta');
        Route::get('/create-template', [WhatsAppController::class, 'createTemplate'])->name('whatsapp.create_template');
        Route::get('/template/create/{id?}', [WhatsAppBusinessController::class, 'create'])->name('whatsapp.business.template.create');
        Route::get('/chatbots/demo-builder', [\App\Http\Controllers\WhatsApp\WhatsAppChatbotController::class, 'demoBuilder'])->name('whatsapp.chatbots.demo');
        Route::get('/chatbots', [\App\Http\Controllers\WhatsApp\WhatsAppChatbotController::class, 'index'])->name('whatsapp.chatbots.index');
        Route::get('/chatbots/create', [\App\Http\Controllers\WhatsApp\WhatsAppChatbotController::class, 'create'])->name('whatsapp.chatbots.create');
        Route::post('/chatbots', [\App\Http\Controllers\WhatsApp\WhatsAppChatbotController::class, 'store'])->name('whatsapp.chatbots.store');
        Route::get('/chatbots/{id}/edit', [\App\Http\Controllers\WhatsApp\WhatsAppChatbotController::class, 'edit'])->name('whatsapp.chatbots.edit');
        Route::put('/chatbots/{id}', [\App\Http\Controllers\WhatsApp\WhatsAppChatbotController::class, 'update'])->name('whatsapp.chatbots.update');
        Route::delete('/chatbots/{id}', [\App\Http\Controllers\WhatsApp\WhatsAppChatbotController::class, 'destroy'])->name('whatsapp.chatbots.destroy');
        Route::get('/campaign', [WhatsAppController::class, 'campaign'])->name('whatsapp.campaign');
        Route::get('/campaign-data', [WhatsAppController::class, 'getCampaignsData'])->name('whatsapp.campaign.list_data');
        Route::get('/campaign-details', [WhatsAppController::class, 'campaign_details'])->name('whatsapp.campaign_details');
        Route::get('/campaign-complete', [WhatsAppController::class, 'campaign_complete'])->name('whatsapp.campaign_complete');
        Route::get('/campaign-archieve', [WhatsAppController::class, 'campaign_archive'])->name('whatsapp.campaign_archieve');
        Route::post('/template/store', [WhatsAppBusinessController::class, 'storeTemplate'])->name('whatsapp.business.template.store');
        Route::get('/template/sync', [WhatsAppBusinessController::class, 'syncTemplates'])->name('whatsapp.business.template.sync');
        Route::get('/template/list', [WhatsAppBusinessController::class, 'listTemplates'])->name('whatsapp.business.template.list');
        Route::post('/template/show', [WhatsAppBusinessController::class, 'showTemplate'])->name('whatsapp.business.template.show');
        Route::post('/template/update', [WhatsAppBusinessController::class, 'update'])->name('whatsapp.business.template.update');
        Route::post('/template/toggle-status', [WhatsAppBusinessController::class, 'toggleStatus'])->name('whatsapp.business.template.toggle-status');
        Route::post('/template/destroy', [WhatsAppBusinessController::class, 'destroy'])->name('whatsapp.business.template.destroy');
        Route::get('/template/export/pdf', [WhatsAppBusinessController::class, 'exportPDF'])->name('whatsapp.business.template.export.pdf');
        Route::get('/template/export/excel', [WhatsAppBusinessController::class, 'exportExcel'])->name('whatsapp.business.template.export.excel');
        Route::get('/template/meta-library', [WhatsAppBusinessController::class, 'fetchMetaLibrary'])->name('whatsapp.business.template.meta-library');
        
        Route::get('/template/design', [WhatsAppBusinessController::class, 'templateDesign'])->name('whatsapp.business.template.design');
        Route::get('/template/mapping/{id}', [WhatsAppBusinessController::class, 'mapping'])->name('whatsapp.business.template.mapping');
        Route::post('/template/mapping/{id}', [WhatsAppBusinessController::class, 'saveMapping'])->name('whatsapp.business.template.save_mapping');
        Route::get('/template/fields', [WhatsAppBusinessController::class, 'getMappingFields'])->name('whatsapp.business.template.fields');
        // Quick Replies
        Route::get('/quick-replies/search', [\App\Http\Controllers\WhatsApp\WhatsAppQuickReplyController::class, 'search'])->name('whatsapp.quick_replies.search');
        Route::get('/quick-replies', [\App\Http\Controllers\WhatsApp\WhatsAppQuickReplyController::class, 'index'])->name('whatsapp.quick_replies.index');
        Route::post('/quick-replies', [\App\Http\Controllers\WhatsApp\WhatsAppQuickReplyController::class, 'store'])->name('whatsapp.quick_replies.store');
        Route::put('/quick-replies/{id}', [\App\Http\Controllers\WhatsApp\WhatsAppQuickReplyController::class, 'update'])->name('whatsapp.quick_replies.update');
        Route::delete('/quick-replies/{id}', [\App\Http\Controllers\WhatsApp\WhatsAppQuickReplyController::class, 'destroy'])->name('whatsapp.quick_replies.destroy');
        
        // Campaigns
        Route::post('/campaign/store', [WhatsAppController::class, 'store_campaign'])->name('whatsapp.campaign.store');
        Route::post('/campaign/upload-excel', [WhatsAppController::class, 'importExcelCampaign'])->name('whatsapp.campaign.excel');
        Route::post('/campaign/preview-excel', [WhatsAppController::class, 'previewExcelCampaign'])->name('whatsapp.campaign.preview');
        Route::get('/campaign/{id}/progress', [WhatsAppController::class, 'getCampaignProgress'])->name('whatsapp.campaign.progress');
        Route::get('/template/{id}/variables', [WhatsAppController::class, 'getTemplateVariables'])->name('whatsapp.template.variables');
        Route::post('/campaign/number/{id}/convert', [WhatsAppController::class, 'convertToLead'])->name('whatsapp.campaign.convert-lead');
        Route::post('/campaign/bulk-convert', [WhatsAppController::class, 'bulkConvertToLead'])->name('whatsapp.campaign.bulk-convert');
        Route::get('/campaign/{id}/data', [WhatsAppController::class, 'getCampaignContactsData'])->name('whatsapp.campaign.data');
        Route::get('/campaign/{id}/export-details', [WhatsAppController::class, 'exportCampaignDetails'])->name('whatsapp.campaign.export_details');
        Route::delete('/campaign/{id}', [WhatsAppController::class, 'destroyCampaign'])->name('whatsapp.campaign.destroy');
        Route::post('/campaign/bulk-delete', [WhatsAppController::class, 'bulkDestroyCampaign'])->name('whatsapp.campaign.bulk-delete');
        Route::get('/campaign/{id}', [WhatsAppController::class, 'showCampaign'])->name('whatsapp.campaign.show');
        Route::put('/campaign/{id}', [WhatsAppController::class, 'updateCampaign'])->name('whatsapp.campaign.update');
        Route::get('/campaign/{id}/messages', [WhatsAppController::class, 'getCampaignMessages'])->name('whatsapp.campaign.messages');
        Route::post('/campaign/{id}/assign-chatbot', [WhatsAppController::class, 'assignChatbot'])->name('whatsapp.campaign.assign-chatbot');
        Route::post('/campaign/{id}/status', [WhatsAppController::class, 'changeCampaignStatus'])->name('whatsapp.campaign.status');
        Route::post('/campaign/{id}/send', [WhatsAppController::class, 'startSendingCampaign'])->name('whatsapp.campaign.start');
        
        Route::get('/campaign-export/excel', [WhatsAppController::class, 'exportCampaignExcel'])->name('whatsapp.campaign.export.excel');
        Route::get('/campaign-export/pdf', [WhatsAppController::class, 'exportCampaignPDF'])->name('whatsapp.campaign.export.pdf');
        
        // Notifications
        Route::get('/notifications/count', [WhatsAppController::class, 'getNotificationCount'])->name('whatsapp.notifications.count');
        Route::get('/notifications/unread', [WhatsAppController::class, 'getUnreadMessages'])->name('whatsapp.notifications.unread');
        Route::get('/fetch-contacts', [WhatsAppController::class, 'fetchContacts'])->name('whatsapp.fetch_contacts');
        Route::post('/chats/clear', [WhatsAppController::class, 'clearChat'])->name('whatsapp.chats.clear');
        Route::post('/chats/delete', [WhatsAppController::class, 'deleteChat'])->name('whatsapp.chats.delete');
        Route::post('/chats/block', [WhatsAppController::class, 'blockUser'])->name('whatsapp.chats.block');
        Route::post('/chats/update-settings', [WhatsAppController::class, 'updateChatSettings'])->name('whatsapp.chats.update_settings');
        Route::post('/send-template-direct', [WhatsAppController::class, 'sendTemplateDirect'])->name('whatsapp.send_template_direct');
        
        // Activity Log Routes
        Route::get('/leads/{lead_id}/activity-data', [WhatsAppController::class, 'getLeadActivityData'])->name('whatsapp.leads.activity_data');
        Route::get('/leads/{lead_id}/activities', [WhatsAppController::class, 'getLeadActivities'])->name('whatsapp.leads.activities');
        Route::post('/leads/{lead_id}/activities/store', [WhatsAppController::class, 'storeLeadActivity'])->name('whatsapp.leads.activities.store');
    });

    // Call Logs
    Route::prefix('call-logs')->group(function () {
        Route::get('/', [\App\Http\Controllers\CallLogController::class, 'index'])->name('call_logs.index');
        Route::get('/data/table', [\App\Http\Controllers\CallLogController::class, 'getLogsDataTable'])->name('call_logs.data');
        Route::get('/export', [\App\Http\Controllers\CallLogController::class, 'export'])->name('call_logs.export');
        Route::post('/store', [\App\Http\Controllers\CallLogController::class, 'store'])->name('call_logs.store');
    });

    // Attendance
    Route::prefix('attendance')->middleware('check.roles.box:admin,superadmin')->group(
        function () {
            // Route::get('dashboard', [EmployeeFrontendController::class, 'employe_dashboard'])->name('employee.dashboard');
            // Route::get('employee', [AttendaceFrontendController::class, 'attendance_employee'])->name('employee.attendace');
        }
    );

    // Superadmin
    Route::prefix('superadmin')->middleware('check.roles.box:superadmin')->group(
        function () {
            // Account Deletion Requests Admin
            Route::prefix('account-deletion-requests')->group(function () {
                Route::get('/', [AccountDeletionRequestController::class, 'index'])->name('superadmin.account-deletion-requests.index');
                Route::get('/{id}', [AccountDeletionRequestController::class, 'show'])->name('superadmin.account-deletion-requests.show');
                Route::post('/{id}/approve', [AccountDeletionRequestController::class, 'approve'])->name('superadmin.account-deletion-requests.approve');
                Route::post('/{id}/reject', [AccountDeletionRequestController::class, 'reject'])->name('superadmin.account-deletion-requests.reject');
            });
            
            Route::get('dashboard', [SuperadminFrontendController::class, 'index'])->name('superadmin.dashboard');
            Route::get('company', [SuperadminFrontendController::class, 'company'])->name('superadmin.company');
            Route::get('company/impersonate/{target_box_id}', [SuperadminFrontendController::class, 'impersonate'])->name('superadmin.company.impersonate');
            Route::get('company/leave-impersonation', [SuperadminFrontendController::class, 'leaveImpersonation'])->name('superadmin.company.leave-impersonation');
            Route::get('/get-company', [SuperadminFrontendController::class, 'get_company'])->name('get.company');
            Route::post('company/update/{id}', [SuperadminFrontendController::class, 'updateCompany'])->name('superadmin.company.update');
            Route::get('subscription', [SuperadminFrontendController::class, 'subscription'])->name('superadmin.subscription');
            Route::get('subscription/preview/{id}', [SuperadminFrontendController::class, 'previewSubscription'])->name('superadmin.subscription.preview');
            Route::post('subscription/update-status/{id}', [SuperadminFrontendController::class, 'updateSubscriptionStatus'])->name('superadmin.subscription.update-status');
            Route::get('subscription/data-for-assignment', [SuperadminFrontendController::class, 'getCompaniesAndPlans'])->name('superadmin.subscription.data-for-assignment');
            Route::post('subscription/assign-offline', [SuperadminFrontendController::class, 'assignOfflinePlan'])->name('superadmin.subscription.assign-offline');
            Route::post('subscription/bulk-assign-offline', [SuperadminFrontendController::class, 'bulkAssignOfflinePlan'])->name('superadmin.subscription.bulk-assign-offline');

            // Master Industry Management inside Superadmin
            Route::get('master-industry', [SuperadminFrontendController::class, 'master_industry'])->name('superadmin.master_industry');
            Route::get('get-master-industry-list', [SuperadminFrontendController::class, 'get_master_industry_list'])->name('superadmin.get_master_industry_list');
            Route::post('master-industry', [SuperadminFrontendController::class, 'store_master_industry'])->name('superadmin.master_industry.store');
            Route::put('master-industry/{id}', [SuperadminFrontendController::class, 'update_master_industry'])->name('superadmin.master_industry.update');
            Route::delete('master-industry/{id}', [SuperadminFrontendController::class, 'destroy_master_industry'])->name('superadmin.master_industry.destroy');

            // Plans / Packages Management
            Route::get('packages', [SuperadminFrontendController::class, 'packages'])->name('superadmin.packages');
            Route::post('packages', [SuperadminFrontendController::class, 'store_package'])->name('superadmin.packages.store');
            Route::post('packages/bulk-delete', [SuperadminFrontendController::class, 'bulk_destroy_package'])->name('superadmin.packages.bulk_destroy');
            Route::put('packages/{id}', [SuperadminFrontendController::class, 'update_package'])->name('superadmin.packages.update');
            Route::delete('packages/{id}', [SuperadminFrontendController::class, 'destroy_package'])->name('superadmin.packages.destroy');

            // Coupons Management
            Route::get('coupons', [SuperadminFrontendController::class, 'coupons'])->name('superadmin.coupons');
            Route::post('coupons', [SuperadminFrontendController::class, 'store_coupon'])->name('superadmin.coupons.store');
            Route::put('coupons/{id}', [SuperadminFrontendController::class, 'update_coupon'])->name('superadmin.coupons.update');
            Route::delete('coupons/{id}', [SuperadminFrontendController::class, 'destroy_coupon'])->name('superadmin.coupons.destroy');

            // FAQs Management
            Route::get('faqs', [\App\Http\Controllers\SuperAdmin\FaqController::class, 'index'])->name('superadmin.faqs');
            Route::post('faqs', [\App\Http\Controllers\SuperAdmin\FaqController::class, 'store'])->name('superadmin.faqs.store');
            Route::put('faqs/{id}', [\App\Http\Controllers\SuperAdmin\FaqController::class, 'update'])->name('superadmin.faqs.update');
            Route::delete('faqs/{id}', [\App\Http\Controllers\SuperAdmin\FaqController::class, 'destroy'])->name('superadmin.faqs.destroy');
            Route::post('faqs/toggle-status/{id}', [\App\Http\Controllers\SuperAdmin\FaqController::class, 'toggleStatus'])->name('superadmin.faqs.toggle-status');
            Route::post('faqs/reorder', [\App\Http\Controllers\SuperAdmin\FaqController::class, 'reorder'])->name('superadmin.faqs.reorder');

            // Master Features Management
            Route::get('package-features-page', [SuperadminFrontendController::class, 'package_features_page'])->name('superadmin.package-features.page');
            Route::post('package-features/toggle-status/{id}', [SuperadminFrontendController::class, 'toggle_feature_status'])->name('superadmin.package-features.toggle-status');
            Route::get('package-features', [SuperadminFrontendController::class, 'get_master_features'])->name('superadmin.package-features.index');
            Route::post('package-features', [SuperadminFrontendController::class, 'store_master_feature'])->name('superadmin.package-features.store');
            Route::put('package-features/{id}', [SuperadminFrontendController::class, 'update_master_feature'])->name('superadmin.package-features.update');
            Route::delete('package-features/{id}', [SuperadminFrontendController::class, 'destroy_master_feature'])->name('superadmin.package-features.destroy');

            Route::get('purchase-transaction', [SuperadminFrontendController::class, 'purchase_transaction'])->name('superadmin.purchase_transaction');
            Route::get('realtime-logs', [SuperadminFrontendController::class, 'realtime_logs'])->name('superadmin.realtime_logs');
            Route::get('realtime-logs-data', [SuperadminFrontendController::class, 'get_realtime_logs_data'])->name('superadmin.realtime_logs_data');
            Route::get('realtime-logs-history', [SuperadminFrontendController::class, 'realtime_logs_history'])->name('superadmin.realtime_logs_history');
            // Route::get('organization-structure', [SuperadminFrontendController::class, 'organization_structure'])->name('superadmin.organizational_organizational');
            Route::post('store-department', [DepartmentController::class, 'ManageDepartmentWithTeams'])->name('departments.store');
            Route::get('get-department-teams', [DepartmentController::class, 'GetDepartmentTeams'])->name('get.department');

            // Vendors Management
            Route::prefix('vendors')->group(function () {
                Route::get('/', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendors'])->name('superadmin.vendors.index');
                Route::get('detail/{id}', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_detail'])->name('superadmin.vendors.detail');
                Route::get('data/{id}', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_data'])->name('superadmin.vendors.data');
                Route::get('dashboard', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_dashboard'])->name('superadmin.vendors.dashboard');
                Route::get('transaction', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_transaction'])->name('superadmin.vendors.transaction');
                
                // API Routes for AJAX loading
                Route::get('api/dashboard', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'api_dashboard'])->name('superadmin.vendors.api.dashboard');
                Route::get('api/transaction', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'api_transaction'])->name('superadmin.vendors.api.transaction');
                Route::get('api/list', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'api_list'])->name('superadmin.vendors.api.list');
                Route::get('api/detail/{id}', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'api_detail'])->name('superadmin.vendors.api.detail');
                
                // Vendor Approval Routes
                Route::post('{id}/approve', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_approve'])->name('superadmin.vendors.approve');
                Route::post('{id}/reject', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_reject'])->name('superadmin.vendors.reject');
                Route::post('{id}/target', [\App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'vendor_update_target'])->name('superadmin.vendors.target');

                // Vendor Percentages
                Route::get('percentages', [\App\Http\Controllers\SuperAdmin\VendorPercentageController::class, 'index'])->name('superadmin.vendors.percentages');
                Route::post('percentages', [\App\Http\Controllers\SuperAdmin\VendorPercentageController::class, 'store'])->name('superadmin.vendors.percentages.store');
            });
        }
    );

    // Checkout Routes (Inside box group but outside restrictive role-check)
    Route::get('/checkout/{planId}', [App\Http\Controllers\CheckoutController::class, 'subscribe'])->name('box.checkout.subscribe');
    Route::post('/checkout/apply-coupon/{planId}', [App\Http\Controllers\CheckoutController::class, 'applyCoupon'])->name('box.checkout.apply_coupon');
    Route::post('/checkout/process/{planId}', [App\Http\Controllers\CheckoutController::class, 'processPayment'])->name('box.checkout.process');
    Route::post('/checkout/success', [App\Http\Controllers\CheckoutController::class, 'handleSuccess'])->name('box.checkout.callback');

    // Finance & Expense Management Module
    Route::prefix('finance')->name('finance.')->middleware(['check.roles.box:admin,employee,superadmin'])->group(function () {
        // Dashboards
        Route::get('/dashboard', [\App\Http\Controllers\Finance\FinanceDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [\App\Http\Controllers\Finance\FinanceDashboardController::class, 'getData'])->name('dashboard.data');

      
        // Master Settings (Admin only)
        Route::middleware(['check.roles.box:admin,employee,superadmin'])->group(function() {
            Route::get('settings', [\App\Http\Controllers\Finance\MasterSettingsController::class, 'index'])->name('settings.index');
            
            // Redirect old URLs to settings
            Route::get('allowance-types', fn() => redirect()->route('finance.settings.index', ['box_slug' => request()->route('box_slug')]));
            Route::get('expense-categories', fn() => redirect()->route('finance.settings.index', ['box_slug' => request()->route('box_slug')]));
            Route::get('approval-rules', fn() => redirect()->route('finance.settings.index', ['box_slug' => request()->route('box_slug')]));

            Route::get('allowance-types/data', [\App\Http\Controllers\Finance\AllowanceTypeController::class, 'getData'])->name('allowance-types.data');
            Route::post('allowance-types/{id}/status', [\App\Http\Controllers\Finance\AllowanceTypeController::class, 'toggleStatus'])->name('allowance-types.status');
            Route::resource('allowance-types', \App\Http\Controllers\Finance\AllowanceTypeController::class)->only(['store', 'update', 'destroy']);

            Route::get('expense-categories/data', [\App\Http\Controllers\Finance\ExpenseCategoryController::class, 'getData'])->name('expense-categories.data');
            Route::post('expense-categories/{id}/status', [\App\Http\Controllers\Finance\ExpenseCategoryController::class, 'toggleStatus'])->name('expense-categories.status');
            Route::resource('expense-categories', \App\Http\Controllers\Finance\ExpenseCategoryController::class)->only(['store', 'update', 'destroy']);

            Route::get('approval-rules/data', [\App\Http\Controllers\Finance\ExpenseApprovalRuleController::class, 'getData'])->name('approval-rules.data');
            Route::post('approval-rules/{id}/status', [\App\Http\Controllers\Finance\ExpenseApprovalRuleController::class, 'toggleStatus'])->name('approval-rules.status');
            Route::resource('approval-rules', \App\Http\Controllers\Finance\ExpenseApprovalRuleController::class)->only(['store', 'update', 'destroy']);
        });

        // Expenses (Employee functions)
        Route::get('/expenses/data', [\App\Http\Controllers\Finance\ExpenseController::class, 'getData'])->name('expenses.data');
        Route::resource('expenses', \App\Http\Controllers\Finance\ExpenseController::class);

        // Expense Approvals (Manager / Finance / Admin)
        Route::get('/approvals/data', [\App\Http\Controllers\Finance\ExpenseApprovalController::class, 'getData'])->name('approvals.data');
        Route::get('/approvals', [\App\Http\Controllers\Finance\ExpenseApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/{expense}', [\App\Http\Controllers\Finance\ExpenseApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approvals/{expense}', [\App\Http\Controllers\Finance\ExpenseApprovalController::class, 'store'])->name('approvals.store');

        // Reimbursements (Finance / Admin)
        Route::get('/reimbursements/data', [\App\Http\Controllers\Finance\ReimbursementController::class, 'getData'])->name('reimbursements.data');
        Route::resource('reimbursements', \App\Http\Controllers\Finance\ReimbursementController::class)->only(['index', 'store']);
    });

    // Account Deletion User Flow (AJAX SPA) - Only Admin can request
    Route::prefix('account/delete')->middleware(['check.roles.box:admin'])->group(function () {
        Route::get('/', [AccountDeletionController::class, 'index'])->name('account.delete.index');
        Route::post('/', [AccountDeletionController::class, 'submit'])->name('account.delete.submit');
        Route::post('/otp/verify', [AccountDeletionController::class, 'verifyOtp'])->name('account.delete.verify');
        Route::post('/otp/resend', [AccountDeletionController::class, 'resendOtp'])->name('account.delete.resend');
        Route::post('/cancel', [AccountDeletionController::class, 'cancel'])->name('account.delete.cancel');
    });

    Route::prefix('/')->middleware(['check.roles.box:admin,employee,superadmin', 'check.subscription'])->group(
        function () {
            // User Preferences & AI Personalization
            Route::get('preferences', [\App\Http\Controllers\PreferenceController::class, 'index'])->name('preferences.index');
            Route::post('preferences/parse-ai', [\App\Http\Controllers\PreferenceController::class, 'parseAi'])->name('preferences.parse_ai');
            Route::post('preferences/reset', [\App\Http\Controllers\PreferenceController::class, 'reset'])->name('preferences.reset');

            // AI Smart Activity Filter & Ask CRM
            Route::post('/smart-views', [\App\Http\Controllers\Api\SmartViewController::class, 'store'])->name('smart_views.store');
            Route::get('/smart-views/{smartView}/records/{module}', [\App\Http\Controllers\Api\SmartViewController::class, 'showRecords'])->name('smart_views.records');
            Route::post('/ask-crm', [\App\Http\Controllers\Api\AskCrmController::class, 'search'])->name('ask_crm.search');
            Route::get('/ai-test', function ($box_slug) {
                $box = \App\Models\Box::where('slug', $box_slug)->first();
                $boxId = $box ? $box->id : null;
                
                $moduleFields = [
                    'activity' => \Illuminate\Support\Facades\Schema::getColumnListing('activities'),
                    'lead' => \Illuminate\Support\Facades\Schema::getColumnListing('leads'),
                    'whatsapp' => \Illuminate\Support\Facades\Schema::getColumnListing('whatsapp_messages'),
                    'project' => \Illuminate\Support\Facades\Schema::getColumnListing('projects'),
                    'deal' => \Illuminate\Support\Facades\Schema::getColumnListing('deals'),
                    'all' => ['created_at'],
                ];

                if ($boxId) {
                    $customFields = \App\Models\BoxCustomField::where('box_id', $boxId)
                        ->where('status', 1)
                        ->get()
                        ->groupBy(function($item) {
                            return strtolower($item->object);
                        });

                    foreach ($customFields as $module => $fields) {
                        if (isset($moduleFields[$module])) {
                            $moduleFields[$module] = array_merge(
                                $moduleFields[$module], 
                                $fields->pluck('col_name')->toArray()
                            );
                        }
                    }
                }
                
                $savedViews = \App\Models\SmartView::where('box_id', $boxId)->orderBy('id', 'desc')->get();

                return view('ai-test', compact('moduleFields', 'savedViews'));
            });

            Route::get('dashboard', [AdminFrontendController::class, 'index'])->name('admin.dashboard');
            // Route::get('dashboard', [SuperadminFrontendController::class, 'index'])->name('superadmin.dashboard'); -- Moved to Superadmin Group
            Route::get('subscription-plan', [SubscriptionController::class, 'index'])->name('subscription.index');
            Route::post('subscription-plan/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
            Route::get('subscription-plan/razorpay-details', [SubscriptionController::class, 'razorpayDetails'])->name('subscription.razorpay_details');
            Route::get('subscription-plan/invoices-refunds', [SubscriptionController::class, 'getInvoicesAndRefunds'])->name('subscription.invoices_refunds');
            Route::get('employee-dashboard', [EmployeeFrontendController::class, 'employe_dashboard'])->name('employee.dashboard');
            Route::get('employee-dashboard-notifications', [EmployeeFrontendController::class, 'getLatestNotifications'])->name('employee.dashboard.notifications');
            Route::get('employee-dashboard-team-members', [EmployeeFrontendController::class, 'recentTeamMembers'])->name('employee.dashboard.teamMembers');
            Route::get('employee-attendace', [AttendaceFrontendController::class, 'attendance_employee'])->name('employee.attendace');
            Route::post('attendance/process', [AttendaceFrontendController::class, 'process_attendance'])->name('employee.attendace.process');
            Route::get('biometric-machine', [AttendaceFrontendController::class, 'biometric_machine'])->name('employee.biometric.machine');
            
            // Mobile Device Register Routes
            Route::get('device-register', [App\Http\Controllers\DeviceRegisterController::class, 'index'])->name('device-register.index');
            Route::post('device-register', [App\Http\Controllers\DeviceRegisterController::class, 'store'])->name('device-register.store');
            Route::get('device-register/{id}/edit', [App\Http\Controllers\DeviceRegisterController::class, 'edit'])->name('device-register.edit');
            Route::post('device-register/update', [App\Http\Controllers\DeviceRegisterController::class, 'update'])->name('device-register.update');
            Route::post('device-register/approve/{id}', [App\Http\Controllers\DeviceRegisterController::class, 'approve'])->name('device-register.approve');
            Route::post('device-register/revoke/{id}', [App\Http\Controllers\DeviceRegisterController::class, 'revoke'])->name('device-register.revoke');
            Route::delete('device-register/{id}', [App\Http\Controllers\DeviceRegisterController::class, 'destroy'])->name('device-register.destroy');
            
            // Mobile Attendance Reports
            Route::get('mobile-attendance/fingerprints', [App\Http\Controllers\MobileAttendanceReportController::class, 'fingerprints'])->name('mobile-attendance.fingerprints');
            Route::post('mobile-attendance/fingerprints/revoke/{id}', [App\Http\Controllers\MobileAttendanceReportController::class, 'revokeFingerprint'])->name('mobile-attendance.fingerprints.revoke');
            Route::delete('mobile-attendance/fingerprints/{id}', [App\Http\Controllers\MobileAttendanceReportController::class, 'destroyFingerprint'])->name('mobile-attendance.fingerprints.destroy');
            Route::get('mobile-attendance/punches', [App\Http\Controllers\MobileAttendanceReportController::class, 'punches'])->name('mobile-attendance.punches');
            
            Route::get('attendance/admin', [AttendaceFrontendController::class, 'admin_attendace'])->name('employee.attendace.admin');
            Route::get('attendance/team', [AttendaceFrontendController::class, 'team_attendace'])->name('employee.attendace.team');

            Route::get('organization-structure', [SuperadminFrontendController::class, 'organization_structure'])->name('manage_users.organizational_structure');
            // Call Dashboard
            Route::middleware(['check.subscription:Call Records'])->group(
                function () {
                Route::get('call-dashboard', [CallDashboardController::class, 'call_dashboard'])->name('call.dashboard');
                Route::get('call-dashboard/data', [CallDashboardController::class, 'dashboardData'])->name('call.dashboard.data');
            }
            );
            // Accounts
            Route::get('/ledger', [LedgerController::class, 'index'])->name('accounts.ledger.index');
            Route::get('/ledger/{lead_id}', [LedgerController::class, 'show'])->name('accounts.ledger.show');

            Route::get('/receipts', [AccountController::class, 'receipts'])->name('accounts.receipts');
            Route::get('/receipts-form-data', [AccountController::class, 'getReceiptFormData'])->name('accounts.receipts.form-data');
            Route::post('/receipts', [AccountController::class, 'store'])->name('accounts.receipts.store');
            Route::post('/receipts-show', [AccountController::class, 'show'])->name('accounts.receipts.show');
            Route::post('/receipts-update', [AccountController::class, 'update'])->name('accounts.receipts.update');
            Route::post('/receipts-destroy', [AccountController::class, 'destroy'])->name('accounts.receipts.destroy');
            Route::post('/receipts-export', [AccountController::class, 'exportReceipts'])->name('accounts.receipts.export');
            Route::get('/receipts-export-status', [AccountController::class, 'checkExportStatus'])->name('accounts.receipts.export.status');
            Route::get('/receipts-export-download', [AccountController::class, 'downloadExport'])->name('accounts.receipts.export.download');
            // Invoices
                        Route::get('/invoice-templates/create', [\App\Http\Controllers\Accounts\InvoiceTemplateController::class, 'create'])->name('accounts.invoice_templates.create');
            Route::post('/invoice-templates/store', [\App\Http\Controllers\Accounts\InvoiceTemplateController::class, 'store'])->name('accounts.invoice_templates.store');
            Route::get('/invoice-templates/preview/{style}', [\App\Http\Controllers\Accounts\InvoiceTemplateController::class, 'preview'])->name('accounts.invoice_templates.preview');
            Route::get('/invoice-templates/edit/{style}', [\App\Http\Controllers\Accounts\InvoiceTemplateController::class, 'editConfig'])->name('accounts.invoice_templates.edit');
            Route::post('/invoice-templates/update/{style}', [\App\Http\Controllers\Accounts\InvoiceTemplateController::class, 'updateConfig'])->name('accounts.invoice_templates.update');

            
            // Invoice Templates Gallery
            Route::get('/invoice-templates', [\App\Http\Controllers\Accounts\InvoiceTemplateController::class, 'index'])->name('accounts.invoice_templates');
            Route::post('/invoice-templates/set-active', [\App\Http\Controllers\Accounts\InvoiceTemplateController::class, 'setActive'])->name('accounts.invoice_templates.set_active');
            Route::get('/invoice-templates/preview/{style}', [\App\Http\Controllers\Accounts\InvoiceTemplateController::class, 'preview'])->name('accounts.invoice_templates.preview');

            Route::get('/invoice', [InvoiceController::class, 'index'])->name('accounts.invoice');
            Route::get('/invoices', [InvoiceController::class, 'list'])->name('accounts.invoices');
            Route::get('/proforma-invoices', [InvoiceController::class, 'proformaList'])->name('accounts.proforma_invoices');
            Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('accounts.invoices.store');
            Route::post('/invoices/update/{id}', [InvoiceController::class, 'update'])->name('accounts.invoices.update');
            Route::post('/invoices/convert/{id}', [InvoiceController::class, 'convertToInvoice'])->name('accounts.invoices.convert');
            Route::post('/invoices/delete/{id}', [InvoiceController::class, 'destroy'])->name('accounts.invoices.destroy');
            Route::get('/ajax-invoices', [InvoiceController::class, 'getInvoicesData'])->name('accounts.ajax_invoices');
            Route::get('/ajax-clients', [InvoiceController::class, 'ajaxClients'])->name('accounts.ajax_clients');
            Route::get('/ajax-products', [InvoiceController::class, 'ajaxProducts'])->name('accounts.ajax_products');
            Route::get('/ajax-leads', [InvoiceController::class, 'ajaxLeads'])->name('accounts.ajax_leads');
            Route::get('/ajax-payment-modes', [InvoiceController::class, 'ajaxPaymentModes'])->name('accounts.ajax_payment_modes');
            Route::get('/invoice-details/{id}', [InvoiceController::class, 'details'])->name('accounts.invoice_details');
            Route::get('/invoice-print/{id}', [InvoiceController::class, 'print'])->name('accounts.invoice_print');
            Route::get('/invoice-download/{id}', [InvoiceController::class, 'downloadPdf'])->name('accounts.invoice_download');
            Route::get('/invoice-grid', [InvoiceController::class, 'grid'])->name('accounts.invoice_grid');
            Route::get('/invoice-settings', [InvoiceController::class, 'settings'])->name('accounts.invoice_settings');
            Route::post('/invoice-settings/save', [InvoiceController::class, 'saveSettings'])->name('accounts.invoice_settings.save');



            // Organization structure routes using OrgChartController
            Route::get(
                'get-organization-structure',
                [OrgChartController::class, 'getOrgChartData']
            )->name('organization.get-structure');

            Route::post(
                'organization-create-node',
                [OrgChartController::class, 'addNode']
            )->name('organization.create-node');

            Route::post(
                'organization-update-node-name',
                [OrgChartController::class, 'updateNode']
            )->name('organization.update-node-name');

            Route::post(
                'organization-update-structure',
                [OrgChartController::class, 'moveNode']
            )->name('organization.update-structure');

            Route::post(
                'organization-delete-node',
                [OrgChartController::class, 'deleteNode']
            )->name('organization.delete-node');
            Route::get('get-node-permissions', [SuperadminFrontendController::class, 'get_node_permissions'])->name('organization.get-node-permissions');
            Route::post('save-node-permissions', [SuperadminFrontendController::class, 'save_node_permissions'])->name('organization.save-node-permissions');
            Route::get('get-box-employees', [SuperadminFrontendController::class, 'get_box_employees'])->name('organization.get-box-employees');
            Route::get('get-node-personnel', [SuperadminFrontendController::class, 'get_node_personnel'])->name('organization.get-node-personnel');
            Route::post('assign-personnel', [SuperadminFrontendController::class, 'assign_personnel'])->name('organization.assign-personnel');
            Route::post('unassign-personnel', [SuperadminFrontendController::class, 'unassign_personnel'])->name('organization.unassign-personnel');
            Route::post('orgchart/delete', [SuperadminFrontendController::class, 'orgchart_delete'])->name('orgchart.delete');
            Route::post('orgchart/update', [SuperadminFrontendController::class, 'orgchart_update'])->name('orgchart.update');
            Route::post('orgchart/move', [SuperadminFrontendController::class, 'orgchart_move'])->name('orgchart.move');
            Route::get('employee-lists', [EmployeeFrontendController::class, 'employess'])->name('manage_users.employee_list');
            Route::get('employee-grid', [EmployeeFrontendController::class, 'employee_grid'])->name('employee.grid');
            Route::get('details/', [EmployeeFrontendController::class, 'employee_details'])->name('employee.details');
            
            // Manage Users additional pages
            Route::get('manage-users/todo', [EmployeeFrontendController::class, 'todo'])->name('manage_users.todo');
            Route::get('manage-users/todo/stats', [EmployeeFrontendController::class, 'taskStats'])->name('manage_users.todo.stats');
            Route::post('manage-users/todo', [EmployeeFrontendController::class, 'storeTodo'])->name('manage_users.todo.store');
            Route::put('manage-users/todo/{id}', [EmployeeFrontendController::class, 'updateTodo'])->name('manage_users.todo.update');
            Route::delete('manage-users/todo/{id}', [EmployeeFrontendController::class, 'destroyTodo'])->name('manage_users.todo.destroy');
                        Route::get('manage-users/projects/options/project-types', [\App\Http\Controllers\Employee\ProjectController::class, 'getProjectTypes'])->name('manage_users.projects.options.project_types');
            Route::get('manage-users/projects/options/project-categories', [\App\Http\Controllers\Employee\ProjectController::class, 'getProjectCategories'])->name('manage_users.projects.options.project_categories');
            Route::get('manage-users/projects/options/users', [\App\Http\Controllers\Employee\ProjectController::class, 'getUsers'])->name('manage_users.projects.options.users');
            Route::get('manage-users/projects/options/leads', [\App\Http\Controllers\Employee\ProjectController::class, 'getLeads'])->name('manage_users.projects.options.leads');

            Route::get('manage-users/projects/export/excel', [\App\Http\Controllers\Employee\ProjectController::class, 'exportExcel'])->name('manage_users.projects.export.excel');
            Route::get('manage-users/projects/export/pdf', [\App\Http\Controllers\Employee\ProjectController::class, 'exportPDF'])->name('manage_users.projects.export.pdf');
            Route::get('manage-users/projects/contacts', [\App\Http\Controllers\Employee\ProjectController::class, 'contactsIndex'])->name('manage_users.projects.contacts');
            Route::post('manage-users/projects/contacts', [\App\Http\Controllers\Employee\ProjectController::class, 'storeContact'])->name('manage_users.projects.contacts.store');
            Route::delete('manage-users/projects/contacts/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'destroyContact'])->name('manage_users.projects.contacts.destroy');
            Route::get('manage-users/projects/contacts/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'showContact'])->name('manage_users.projects.contacts.show');
            Route::post('manage-users/projects/contacts/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'updateContact'])->name('manage_users.projects.contacts.update');


            Route::get('manage-users/projects/{id}/contacts-json', [\App\Http\Controllers\Employee\ProjectController::class, 'getContactsJson'])->name('manage_users.projects.contacts.json');
            Route::get('manage-users/projects', [\App\Http\Controllers\Employee\ProjectController::class, 'index'])->name('manage_users.projects');
            Route::post('manage-users/projects', [\App\Http\Controllers\Employee\ProjectController::class, 'store'])->name('manage_users.projects.store');
            Route::get('manage-users/projects/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'show'])->name('manage_users.projects.show');
            Route::put('manage-users/projects/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'update'])->name('manage_users.projects.update');
            Route::delete('manage-users/projects/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'destroy'])->name('manage_users.projects.destroy');
                    Route::get('manage-users/project-types', [\App\Http\Controllers\Setting\ProjectSettingsController::class, 'types'])->name('settings.project_types');
                    Route::post('manage-users/project-types', [\App\Http\Controllers\Setting\ProjectSettingsController::class, 'storeType'])->name('settings.project_types.store');
                    Route::put('manage-users/project-types', [\App\Http\Controllers\Setting\ProjectSettingsController::class, 'updateType'])->name('settings.project_types.update');
                    Route::delete('manage-users/project-types', [\App\Http\Controllers\Setting\ProjectSettingsController::class, 'destroyType'])->name('settings.project_types.destroy');

                    Route::get('manage-users/project-categories', [\App\Http\Controllers\Setting\ProjectSettingsController::class, 'categories'])->name('settings.project_categories');
                    Route::post('manage-users/project-categories', [\App\Http\Controllers\Setting\ProjectSettingsController::class, 'storeCategory'])->name('settings.project_categories.store');
                    Route::put('manage-users/project-categories', [\App\Http\Controllers\Setting\ProjectSettingsController::class, 'updateCategory'])->name('settings.project_categories.update');
                    Route::delete('manage-users/project-categories', [\App\Http\Controllers\Setting\ProjectSettingsController::class, 'destroyCategory'])->name('settings.project_categories.destroy');

            Route::get('manage-users/deals', [EmployeeFrontendController::class, 'deals'])->name('manage_users.deals');
            Route::get('manage-users/deals-json', [EmployeeFrontendController::class, 'getDealsJson'])->name('manage_users.deals.json');
            Route::get('manage-users/deals-export', [EmployeeFrontendController::class, 'exportDeals'])->name('manage_users.deals.export');
            Route::get('manage-users/deals-form-options', [EmployeeFrontendController::class, 'getDealFormOptions'])->name('manage_users.deals.form_options');
            Route::get('manage-users/deals-leads-search', [EmployeeFrontendController::class, 'searchLeads'])->name('manage_users.deals.leads_search');
            Route::get('manage-users/pipelines-json', [EmployeeFrontendController::class, 'getPipelinesJson'])->name('manage_users.pipelines.json');
            Route::get('manage-users/projects-json', [EmployeeFrontendController::class, 'getProjectsJson'])->name('manage_users.projects.json');
            Route::get('manage-users/users-json', [EmployeeFrontendController::class, 'getUsersJson'])->name('manage_users.users.json');
            Route::post('manage-users/pipelines', [EmployeeFrontendController::class, 'storePipeline'])->name('manage_users.pipelines.store');
            Route::put('manage-users/pipelines/{id}', [EmployeeFrontendController::class, 'updatePipeline'])->name('manage_users.pipelines.update');
            Route::delete('manage-users/pipelines/{id}', [EmployeeFrontendController::class, 'destroyPipeline'])->name('manage_users.pipelines.destroy');
            Route::post('manage-users/deals', [EmployeeFrontendController::class, 'storeDeal'])->name('manage_users.deals.store');
            Route::put('manage-users/deals/{id}', [EmployeeFrontendController::class, 'updateDeal'])->name('manage_users.deals.update');
            Route::delete('manage-users/deals/{id}', [EmployeeFrontendController::class, 'destroyDeal'])->name('manage_users.deals.destroy');

            // Deal Settings routes
            Route::controller(\App\Http\Controllers\Employee\DealSettingsController::class)->prefix('manage-users/deal-settings')->name('manage_users.deal_settings.')->group(function () {
                Route::get('/json', 'getAllJson')->name('json');
                Route::get('/', 'index')->name('index');
                
                Route::post('/status', 'storeStatus')->name('status.store');
                Route::put('/status/{id}', 'updateStatus')->name('status.update');
                Route::delete('/status/{id}', 'destroyStatus')->name('status.destroy');

                Route::post('/period', 'storePeriod')->name('period.store');
                Route::put('/period/{id}', 'updatePeriod')->name('period.update');
                Route::delete('/period/{id}', 'destroyPeriod')->name('period.destroy');

                Route::post('/source', 'storeSource')->name('source.store');
                Route::put('/source/{id}', 'updateSource')->name('source.update');
                Route::delete('/source/{id}', 'destroySource')->name('source.destroy');

                Route::post('/priority', 'storePriority')->name('priority.store');
                Route::put('/priority/{id}', 'updatePriority')->name('priority.update');
                Route::delete('/priority/{id}', 'destroyPriority')->name('priority.destroy');

                Route::post('/tag', 'storeTag')->name('tag.store');
                Route::put('/tag/{id}', 'updateTag')->name('tag.update');
                Route::delete('/tag/{id}', 'destroyTag')->name('tag.destroy');
            });
            Route::middleware(['check.subscription:Attendance'])->group(
                function () {
                    Route::post('store-shift', [ShiftController::class, 'store_shift'])->name('shifts.store');
                    Route::match(['get', 'post'], '/get-shift-data', [ShiftController::class, 'Shiftdata'])->name('get.shift.data');
                    Route::get('/get-shift-details', [ShiftController::class, 'ShiftDetails'])->name('get.shift.details');
                    Route::get('/get-attendance-breaks', [AttendaceController::class, 'getAttendanceBreaks'])->name('get.attendance.breaks');
                    Route::put('/update-shift', [ShiftController::class, 'updateShift'])->name('Update.shift');
                    Route::delete('/delete-shift-emp', [ShiftController::class, 'deleteShift'])->name('delete.shift');
                    Route::get('/get-assign-shift', [ShiftController::class, 'attendance_shift'])->name('get.assign.shift');
                    Route::post('/assign-employee-shift', [ShiftController::class, 'shiftemployeassign'])->name('assign.employee.shift');
                    Route::post('/generate-shift-employees', [ShiftController::class, 'generate_shift_for_all_employees'])->name('generate.shift.employees');
                    Route::post('/auto-punch-out', [AttendaceController::class, 'auto_attendance'])->name('auto.attendance');
                    Route::get('/attendance-shift', [AttendaceFrontendController::class, 'attendance_shift'])->name('employee.shift');
                    Route::get('employee-dashboard-stats', [AttendaceController::class, 'employeeDashboardStats'])->name('employess.dashboard.stats');
                    Route::get('employee-dashboard-total-tasks', [AttendaceController::class, 'EmployeeTotalTasksStats'])->name('employess.dashboard.total_tasks');
                    Route::get('employee-dashboard-overview', [AttendaceController::class, 'attendanceOverview'])->name('employess.dashboard.overview');
                    Route::get('employee-leave-states', [LeaveController::class, 'employeeLeaveStats'])->name('employee.leave.stats');
                    //get department for fetch
                    Route::get('get-department', [AttendaceController::class, 'AttendanceDepartment'])->name('get.attendance.department');

                }
            );
            //Start Holiday Type
            Route::middleware(['check.subscription:Attendance'])->group(
                function () {
                Route::get('/holiday-type', [AttendaceFrontendController::class, 'holiday_type'])->name('employee.holiday_type');
                Route::get('/fetch-holiday-types', [HolidayTypeController::class, 'getHolidayTypes'])->name('holiday.fetch');
                Route::post('store-holiday-type', [HolidayTypeController::class, 'holidayStore'])->name('HolidayStore');
                Route::get('/get-holiday-type', [HolidayTypeController::class, 'HolidayPrefill'])->name('holiday.prefill');
                Route::get('/get-all-region', [HolidayController::class, 'GetAllRegional'])->name('get.all.regional');
                Route::put('/update-holiday-type', [HolidayTypeController::class, 'holidayUpdate'])->name('holidayUpdate');
                Route::delete('/delete-holiday-type', [HolidayTypeController::class, 'deleteHoliday'])->name('holidayDelete');
            }
            );
            Route::middleware(['check.subscription:HR Documents'])->group(
                function () {
                    Route::get('assets', [EmployeeFrontendController::class, 'employee_assets'])->name('hr.employess.assets');
                    Route::get('assets/list', [AssetController::class, 'index'])->name('hr.asset.list');
                    Route::get('assets/available', [AssetController::class, 'available'])->name('hr.asset.available');
                    Route::get('assets/history', [AssetController::class, 'history'])->name('hr.asset.history');
                    Route::post('assets/assign', [AssetController::class, 'assignAsset'])->name('hr.asset.assign');
                    Route::put('assets/history', [AssetController::class, 'updateHistory'])->name('hr.asset.history.update');
                    Route::delete('assets/history', [AssetController::class, 'destroyHistory'])->name('hr.asset.history.delete');
                    Route::post('assets', [AssetController::class, 'store'])->name('hr.asset.store');
                    Route::put('assets', [AssetController::class, 'update'])->name('hr.asset.update');
                    Route::delete('assets', [AssetController::class, 'destroy'])->name('hr.asset.delete');

                    Route::get('assets/asset-type/list', [AssetTypeController::class, 'index'])->name('hr.asset.type.list');
                    Route::post('assets/asset-type', [AssetTypeController::class, 'store'])->name('hr.asset.type.store');
                    Route::put('assets/asset-type', [AssetTypeController::class, 'update'])->name('hr.asset.type.update');
                    Route::delete('assets/asset-type', [AssetTypeController::class, 'destroy'])->name('hr.asset.type.delete');

                    Route::get('hr-documents', [EmployeeFrontendController::class, 'employee_hr_documents'])->name('hr.employess.hr.documents');
                    Route::post('hr-documents', [EmployeeFrontendController::class, 'store_hr_document'])->name('hr.employess.hr.documents.store');
                    Route::get('hr-documents/delete/{id}', [EmployeeFrontendController::class, 'delete_hr_document'])->name('hr.employess.hr.documents.delete');
                    
                    Route::get('letter-generation', [LetterGenerationController::class, 'index'])->name('hr.employeess.letter.generation');
                    Route::get('letter-generation/api/letter-types', [LetterGenerationController::class, 'getLetterTypesAPI'])->name('letter.api.types');
                    Route::get('letter-generation/api/letter-heads', [LetterGenerationController::class, 'getLetterHeadsAPI'])->name('letter.api.heads');
                    Route::get('letter-generation/api/signatories', [LetterGenerationController::class, 'getSignatoriesAPI'])->name('letter.api.signatories');
                    Route::get('letter-generation/tab/{tabName}', [LetterGenerationController::class, 'getTabHTML'])->name('letter.tab.get');

                    // Job Applicants
                    Route::get('job-applicants', [\App\Http\Controllers\Employee\JobApplicantController::class, 'index'])->name('hr.job.applicants.index');
                    Route::post('job-applicants/data', [\App\Http\Controllers\Employee\JobApplicantController::class, 'index'])->name('hr.job.applicants.data');
                    Route::get('job-applicants/create', [\App\Http\Controllers\Employee\JobApplicantController::class, 'create'])->name('hr.job.applicants.create');
                    Route::post('job-applicants', [\App\Http\Controllers\Employee\JobApplicantController::class, 'store'])->name('hr.job.applicants.store');
                    Route::get('job-applicants/{id}/edit', [\App\Http\Controllers\Employee\JobApplicantController::class, 'edit'])->name('hr.job.applicants.edit');
                    Route::get('job-applicants/get-details/{id}', [\App\Http\Controllers\Employee\JobApplicantController::class, 'getDetails'])->name('hr.job.applicants.get-details');
                    Route::put('job-applicants/{id}', [\App\Http\Controllers\Employee\JobApplicantController::class, 'update'])->name('hr.job.applicants.update');
                    Route::delete('job-applicants/{id}', [\App\Http\Controllers\Employee\JobApplicantController::class, 'destroy'])->name('hr.job.applicants.delete');
                    
                    Route::post('/letter-signatory/store', [LetterGenerationController::class, 'storeSignatory'])->name('letter.signatory.store');
                    Route::post('/letter-signatory/update/{id}', [LetterGenerationController::class, 'updateSignatory'])->name('letter.signatory.update');
                    Route::get('/letter-signatory/delete/{id}', [LetterGenerationController::class, 'deleteSignatory'])->name('letter.signatory.delete');
                    
                    Route::post('/letter-head/store', [LetterGenerationController::class, 'storeLetterHead'])->name('letter.head.store');
                    Route::post('/letter-head/update/{id}', [LetterGenerationController::class, 'updateLetterHead'])->name('letter.head.update');
                    Route::get('/letter-head/delete/{id}', [LetterGenerationController::class, 'deleteLetterHead'])->name('letter.head.delete');
                    
                    Route::post('/letter-template/store', [LetterGenerationController::class, 'storeTemplate'])->name('letter.template.store');
                    Route::post('/letter-template/update/{id}', [LetterGenerationController::class, 'updateTemplate'])->name('letter.template.update');
                    Route::get('/letter-template/get', [LetterGenerationController::class, 'getTemplates'])->name('letter.template.get');
                    Route::get('/letter-template/content/{id}', [LetterGenerationController::class, 'getTemplateContent']);

                    // Letter Types
                    Route::post('/letter-type/store', [LetterGenerationController::class, 'storeLetterType'])->name('letter.type.store');
                    Route::post('/letter-type/update/{id}', [LetterGenerationController::class, 'updateLetterType'])->name('letter.type.update');
                    Route::get('/letter-type/delete/{id}', [LetterGenerationController::class, 'deleteLetterType'])->name('letter.type.delete');

                    // Generated Letters
                    Route::post('employee-letter/preview', [LetterGenerationController::class, 'previewLetter'])->name('employee.letter.preview');
                    Route::post('employee-letter/generate', [LetterGenerationController::class, 'generateLetter'])->name('employee.letter.generate');
                    Route::get('employee-letter/delete/{id}', [LetterGenerationController::class, 'deleteLetter'])->name('employee.letter.delete');
                }
            );
            //end Holiday Type
            // HR / Attendance / Employee Module
            Route::middleware(['check.subscription:Attendance'])->group(
                function () {
                Route::get('leaves', [EmployeeFrontendController::class, 'leaves'])->name('employee.leaves');
                Route::get('leaves-employee', [EmployeeFrontendController::class, 'leaves_employee'])->name('employee.leaves_employee');
                Route::get('leaves-admin', [EmployeeFrontendController::class, 'Leave_EmployeeAdmin'])->name('employee.leave.admin');
                Route::get('leave-settings', [EmployeeFrontendController::class, 'leave_settings'])->name('employee.leave_settings');
                Route::get('get-employess-request-datas', [LeaveController::class, 'getEmployeeRequestData'])->name('get.data.employee.request');
                Route::post('admin-leave-update-status', [LeaveController::class, 'UpdateAdminRequest'])->name('admin.leave.update.status');
                Route::get('get-leave-type-request', [LeaveTypeController::class, 'LeaveTyperequest'])->name('leave.type.request');
                Route::get('get-leave-balance', [LeaveController::class, 'GetLeaveBlance'])->name('get.leave.balance');
                Route::post('leave-request-store', [LeaveController::class, 'LeaveRequestStore'])->name('leave.request.store');
                Route::post('leave-request-update', [LeaveController::class, 'LeaveRequestUpdate'])->name('leave.request.update');
                Route::delete('leave-request-delete', [LeaveController::class, 'LeaveRequestDelete'])->name('leave.request.delete');
            }
            );

            Route::middleware(['check.subscription:Attendance'])->group(
                function () {

                    Route::get('report', [EmployeeFrontendController::class, 'employee_report'])->name('employee.report');
                    Route::put('/employee/change-password', [AuthController::class, 'changePassword'])
                        ->name('employee.change-password');
                    Route::get('attendance-calendar', [AttendaceFrontendController::class, 'attendace_controller'])->name('employee.attendance.calendar');
                    Route::get('get-attendace-data', [AttendaceController::class, 'GetCalendarData'])->name('get.calender.data');
                    Route::get('get-admin-request-datas', [AttendaceController::class, 'getAdminRequestdata'])->name('get.data.admin.request');
                    Route::post('saved-attendance-correction', [AttendaceController::class, 'SavedAttendanceCoreection'])->name('saved.correction');
                    Route::get('/admin-dashboard-attendance-list', [AttendaceController::class, 'AdminAttendaceList'])->name('admin.attendance.list');
                    Route::get('/admin-dashboard-total-tasks', [AttendaceController::class, 'AdminTotalTasksStats'])->name('admin.total_tasks.stats');
                    Route::get('/users-task-progress', [AttendaceController::class, 'UserTaskProgress'])->name('users.task.progress');
                    Route::get('/users-task-progress-data', [AttendaceController::class, 'UserTaskProgressData'])->name('users.task.progress.data');
                    Route::get("check-employee-attendace", [AttendaceController::class, 'check_attendance'])->name('check.attendance');
                    Route::get('get-upcoming-birthdays', [AttendaceController::class, 'GetUpcomingBirthday'])->name('get.upcoming.birthday');
                    Route::get("check-attendance-summary", [AttendaceController::class, 'checkattendace_summary'])->name('check.attendance.summary');
                    Route::get("fetch-employee-attendace-records", [AttendaceController::class, 'fetchAllAttendanceRecords'])->name('fetch.check.attendance');
                    Route::get("individual-attendance-summary", [AttendaceController::class, 'individualattendace_summary'])->name('individual.attendance.summary');
                    Route::post('mark-attendace', [AttendaceController::class, 'mark_attendace'])->name('mark.attendace');
                    Route::post('take-break', [AttendaceController::class, 'take_break'])->name('break.toggle');
                    Route::get('total-absenties-today', [AttendaceController::class, 'today_absenties'])->name('today.absenties');
                    Route::get('attendace-employee', [AttendaceController::class, 'attendace_employee'])->name('attendace.employee');
                    Route::get('team-attendace-employee', [AttendaceController::class, 'team_attendace_employee'])->name('team.attendace.employee');

                    Route::get('attendace-admin-stats', [AttendaceController::class, 'attendace_admin_stats'])->name('attendance.admin.states');
                    Route::get('get-dashboard-statistics', [AttendaceController::class, 'getDashboardStatistics'])->name('get.dashboard.statistics');
                    Route::post('attendance/export', [AttendaceController::class, 'export_attendace_admin'])->name('export.attendace');
                    Route::post('attendance-pdf-export', [AttendaceController::class, 'export_attendace_pdf'])->name('export.attendace.pdf');
                    Route::get('attendance-report-view', [AttendaceController::class, 'AttendaceVIew'])->name('attendance.report.view');
                    Route::put('update-attendace-correction', [AttendaceController::class, 'update_attendance_correction'])->name('update.corrections');
                    Route::post('edit-attendance', [AttendaceController::class, 'edit_attendace'])->name('edit_attendance');
                    Route::post('/create-employee', [EmployeManageController::class, 'store_employee'])->name('store.employee');
                    Route::get("/list-all-employee", [EmployeManageController::class, 'fetch_employee'])->name('fetch.employee');
                    Route::get("/grid-all-employee", [EmployeManageController::class, 'Gridfetch_employee'])->name('gridfetch.employee');
                    Route::get("/get-permissions", [EmployeManageController::class, 'getPermissions'])->name('get.permissions');
                    Route::get("/user-permissions", [\App\Http\Controllers\Employee\UserPermissionController::class, 'getUserPermissions'])->name('user.permissions');
                    Route::get("/get-employee-details", [EmployeManageController::class, 'employee_details_admin'])->name('employee.details.admin');
                    Route::get("/get-box-users", [EmployeManageController::class, 'getBoxUsers'])->name('employee.get.box.users');
                    Route::get("/get-employee-types", [EmployeManageController::class, 'getEmployeeTypes'])->name('employee.get.types');
                    Route::put('update-employee-details', [EmployeManageController::class, 'employee_update'])->name('employee.update');
                    Route::post('update-employee-details-admin', [EmployeManageController::class, 'employee_update'])->name('employee.update.admin');
                    Route::post('toggle-location-tracking', [EmployeManageController::class, 'toggleLocationTracking'])->name('employee.toggle-location-tracking');
                    Route::get('get-all-machines', [EmployeManageController::class, 'get_all_machine'])->name('get.all.machines');
                    Route::post('map-to-employee-machine', [EmployeManageController::class, 'mapToAssign'])->name('map.to.assing');
                    Route::post('resend-email-verification', [EmployeManageController::class, 'ResendEmployeeVerifcation'])->name('employees.resend.verification');
                    Route::put('update-employee-family', [EmployeManageController::class, 'employee_family'])->name('employee.family');
                    Route::put('update-employee-education', [EmployeManageController::class, 'employee_education'])->name('employee.education');
                    Route::put('update-employee-experience', [EmployeManageController::class, 'employee_experience'])->name('employee.experience');
                    Route::put('update-employee-bank-safety', [EmployeManageController::class, 'employee_bank_safety'])->name('employee.bank_safety');
                    Route::put('update-bank-employee', [EmployeManageController::class, 'update_bank_employee'])->name('employee.bank.update');
                    Route::put('update-emergency-safety', [EmployeManageController::class, 'employee_emergency'])->name('employee.emergency');
                    Route::post('/employees/export-excel', [EmployeManageController::class, 'exportExcel'])->name('employees.export.excel');
                    Route::get('/employees/export-pdf', [EmployeManageController::class, 'exportPDF'])->name('employees.export.pdf');
                    Route::put("/toggle-status", [EmployeManageController::class, 'employetoggle'])->name('employee.toggle');
                    Route::delete('delete-bank-details', [EmployeManageController::class, 'delete_bank'])->name('delete.bank');
                    Route::delete('delete-family-info', [EmployeManageController::class, 'delete_family_info'])->name('delete.family');
                    Route::delete('delete-education', [EmployeManageController::class, 'delete_emp_education'])->name('delete.education');
                    Route::delete('delete-experience', [EmployeManageController::class, 'delete_emp_experience'])->name('delete.experience');
                    Route::delete('delete-emergency', [EmployeManageController::class, 'delete_emp_emergency'])->name('delete.emergency');
                    Route::delete('delete-shift', [EmployeManageController::class, 'delete_shift_employe'])->name('delete.shift.emp');
                    Route::delete('remove-office-assignment', [EmployeManageController::class, 'removeOfficeAssignment'])->name('employee.remove.office');
                    Route::get('/manage-department', [EmployeManageController::class, 'department_manage'])->name('department.manage');
                    // delete.education
        
                    Route::delete('/delete-employee', [EmployeManageController::class, 'delete_employee'])->name('delete.employee');
                }
            );
            Route::get('/get-teams', [DepartmentController::class, 'getDepartment'])->name('get.teams');
            Route::get('/activity-type', [CrmSourceController::class, 'show'])->name('activity-type.show');
            Route::get('/get-activity-type', [CrmSourceController::class, 'GetActivityTypes'])->name('getActivtiyType');
            Route::post('/activity-store', [CrmSourceController::class, 'storeActivityType'])->name('activityType.store');
            // Route::post('/activity-status', [CrmSourceController::class, 'storeActivityStatus'])->name('ActivityStatus.store');
            Route::post('/activity-update', [CrmSourceController::class, 'updateActivitytype'])->name('activityType.update');
            // Route::post('/activity-status-update', [CrmSourceController::class, 'updateActivityStatus'])->name('ActivityStatus.update');
            Route::delete('delete-biometric', [EmployeManageController::class, 'DeleteAssignBio'])->name('delete.biometric.mapping');
            Route::post('/leads-sub-status-store', [CrmSourceController::class, 'StoreLeadsubstatus'])->name('Leadsubstatus.store');
            Route::post('/leads-sub-status-update', [CrmSourceController::class, 'updateLeadSubStatus'])->name('LeadSubStatus.update');
            Route::delete('/leads-sub-status-delete', [CrmSourceController::class, 'destroyLeadSubStatys'])->name('LeadSubStatys.delete');
            Route::delete('/activity-delete', [CrmSourceController::class, 'destroyActivityTypeActual'])->name('activityType.delete');
            Route::get('activity-status', [CrmSourceController::class, 'activityStatuus'])->name('activitystatus.show');
            Route::get('/get-activity-status-list', [CrmSourceController::class, 'getActivtiyStatus'])->name('getActivtiyStatus');
            Route::post('/activity-status-store', [CrmSourceController::class, 'storeActivitStatus'])->name('ActivitStatus.store');
            Route::post('/activity-status-update', [CrmSourceController::class, 'updateActivityStatus'])->name('activityStatus.update');
            Route::delete('/activity-status-delete', [CrmSourceController::class, 'destroyActivityStatus'])->name('activityStatus.delete');
            Route::get('/get-source', [CrmSourceController::class, 'get_source'])->name('get.source');
            Route::get('/get-activities-list', [CrmSourceController::class, 'getActivitiesList'])->name('get.activities.list');
            Route::post('/source-toggle-status', [CrmSourceController::class, 'toggleStatus'])->name('source.toggle');
            Route::post('/source-update', [CrmSourceController::class, 'update'])->name('source.update');
            Route::delete('/source-delete', [CrmSourceController::class, 'delete'])->name('source.delete');
            Route::post('/creat-source', [CrmSourceController::class, 'store_source'])->name('store.source');
            Route::put('/update', [CrmSourceController::class, 'update_source'])->name('update.source');
            Route::delete('/delete-source', [CrmSourceController::class, 'delete_source'])->name('delete.source');
            Route::get('/get-contactt-stage', [CrmSourceController::class, 'get_contact_stage'])->name('get.contact-stage');

            Route::get('/leads-sub-status', [CrmSourceController::class, 'CrmLeadsSubStatuus'])->name('app.settings.leads.leads_sub_status');
            Route::get('/get-leads-substatus', [CrmSourceController::class, 'GetLeadSubStatus'])->name('get.GetLeadSubStatus');
            Route::get('/get-lost-reason', [CrmSourceController::class, 'get_lostreason'])->name('get.lost_reason');
            Route::get('/get-industry', [CrmSourceController::class, 'get_industry'])->name('get.get_industry');
            Route::get('/get-calls', [CrmSourceController::class, 'get_calls'])->name('get.calls');
            Route::post('/mass-delete', [CrmSourceController::class, 'massDestroy'])->name('mass.delete');

            Route::get('machine-punch-in', [AttendaceController::class, 'MachinePunchIn'])->name('employee.machine');
            Route::get('get-all-machine', [AttendaceController::class, 'GetAllMachines'])->name('get.all.machine');
            Route::post('machine-punch-user', [AttendaceController::class, 'PunchInUser'])->name('machine.punch.user');
            Route::post('process-biometric-attendance', [AttendaceController::class, 'ProcessBiometricAttendance'])->name('process.biometric.attendance');
            //
            //industry
            //PayRoll
            Route::middleware(['check.subscription:Salary'])->group(
                function () {
                Route::get('/payrol', [PayRollController::class, 'payroll'])->name('payroll.index');
                Route::get('/loan-setting', [PayRollController::class, 'loan_settings'])->name('payroll.loan.settings');
                Route::get('loan-tracker', [PayRollController::class, 'loan_tracker'])->name('payroll.loan.tracker');
                Route::get('/salary-setting', [PayRollController::class, 'PayRollSetting'])->name('payroll.setting');
                Route::get('salary', [EmployeeFrontendController::class, 'employee_salary'])->name('payroll.salary');
                Route::get('/employee-salary', [PayRollController::class, 'employe_salary'])->name('payroll.employee.salary');
                Route::get('/salary-report', [PayRollController::class, 'employe_salary_report'])->name('payroll.employee.report');
                Route::post('generate-salary-report', [SalaryReportController::class, 'GenearetSalaryReport'])->name('generate.salary.report');
                Route::get('get-generate-salary-report', [SalaryReportController::class, 'GetSalaryReport'])->name('get.salary.report');
                Route::get('salary-report-details', [SalaryReportController::class, 'getSalaryReportDetails'])->name('salary.report.details');
                Route::get('salary-reports/export', [SalaryReportController::class, 'export'])->name('salary.report.export');
                Route::post('salary-report-delete', [SalaryReportController::class, 'deleteSalaryReport'])->name('salary.report.delete');
                Route::get('/get-all-employee', [PayRollController::class, 'GetAllemployee'])->name('get.salary.employee');
                Route::get('/search-employees', [SalaryController::class, 'searchEmployees'])->name('search.employees');
                Route::post('/salaries/store', [SalaryController::class, 'store'])->name('salaries.store');
                Route::post('/payroll/generate-slip', [SalaryController::class, 'generateSlip'])->name('payroll.generate.slip');
                Route::get('/payroll/salaries/list', [SalaryController::class, 'list'])->name('salaries.list'); // List all salary settings (for Yajra DataTable AJAX)
                Route::get('/payroll/salaries/filter-options', [SalaryController::class, 'getFilterOptions'])->name('salaries.filter.options'); // Get filter options (departments, designations)
                Route::get('/payroll/salaries/export', [SalaryController::class, 'export'])->name('employe.salary.export');
                Route::get('salary-settings/list', [SalarySettingController::class, 'list'])->name('salary-settings.list');
                Route::get('salary-settings/get-all', [SalarySettingController::class, 'getAll'])->name('salary-settings.get-all');
                Route::get('salary-settings/get-details', [SalarySettingController::class, 'getDetails'])->name('salary-settings.get-details');
                Route::post('salary-settings/calculate', [SalarySettingController::class, 'calculateSalary'])->name('salary-settings.calculate');
                Route::post('salary-settings/store', [SalarySettingController::class, 'store'])->name('salary-settings.store');
                Route::post('salary-settings/update', [SalarySettingController::class, 'update'])->name('salary-settings.update');
                Route::post('salary-settings/check-delete', [SalarySettingController::class, 'checkDelete'])->name('salary-settings.check-delete');
                Route::post('salary-settings/delete', [SalarySettingController::class, 'destroy'])->name('salary-settings.destroy');
                Route::post('salary-settings/edit', [SalarySettingController::class, 'edit'])->name('salary-settings.edit');
                Route::get('/employee-salary-details', [SalaryController::class, 'getSalary'])->name('employe.salary.details');
                Route::put('/salary-update', [SalaryController::class, 'update'])->name('salary.update');
                Route::post('/salary-delete', [SalaryController::class, 'salary_delete'])->name('salary.delete');
                Route::get('/get-all-employess', [SalaryController::class, 'getAllSalaryEmployee'])->name('get.all.select.employee');
                Route::get('/salary-slip', [PayRollController::class, 'salary_slip'])->name('payroll.salary.slip');
                Route::get('/payroll-items', [PayRollController::class, 'payroll_items'])->name('payroll.payroll.items');
                Route::get('/payroll-overtime', [PayRollController::class, 'payroll_overtime'])->name('payroll.over.time');
                Route::get('/payroll-deduction', [PayRollController::class, 'payroll_deduction'])->name('payroll.deduction');

                // payroll addition Items
                Route::post('/store-payroll-addition', [PayRollController::class, 'StorePayrollAddition'])->name('store.payroll.addition');
                Route::get('/payroll/list', [PayrollController::class, 'getPayrollList'])->name('payroll.list');
                Route::put('/update-payroll-addition', [PayRollController::class, 'PayRollUpdate'])->name('payroll.update');
                Route::delete('/delete-payroll-addition', [PayRollController::class, 'PayRollDelete'])->name('payroll.destroy');
                //payroll overtime items
    
                Route::post('/store-payroll-overtime', [PayRollController::class, 'StorePayrollovertime'])->name('store.payroll.overtime');
                Route::get('/payroll-list-overtime', [PayrollController::class, 'getPayrollList_overtime'])->name('payroll.list.overtime');
                Route::put('/update-payroll-overtime', [PayRollController::class, 'PayRollUpdate_overtime'])->name('payroll.update.overtime');
                Route::delete('/delete-payroll-overtime', [PayRollController::class, 'PayRollDelete_overtime'])->name('payroll.destroy.overtime');

                //payroll deduction items
    
                Route::post('/store-payroll-deduction', [PayRollController::class, 'StorePayrolldeduction'])->name('store.payroll.deduction');
                Route::get('/payroll-list-deduction', [PayrollController::class, 'getPayrollList_deduction'])->name('payroll.list.deduction');
                Route::put('/update-payroll-deduction', [PayRollController::class, 'PayRollUpdate_deduction'])->name('payroll.update.deduction');
                Route::delete('/delete-payroll-deduction', [PayRollController::class, 'PayRollDelete_deduction'])->name('payroll.destroy.deduction');
            }
            );

            Route::middleware(['check.subscription:Lead Integration'])->group(
                function () {
                    Route::get('/leads/dashboard', [LeadsFrontendController::class, 'index'])->name('leads.dashboard');
                    Route::get('/leads/dashboard/data', [LeadsFrontendController::class, 'dashboardData'])->name('leads.dashboard.data');

                    Route::get('/leads/user-dashboard', [LeadsFrontendController::class, 'userDashboard'])->name('leads.user.dashboard');
                    Route::get('/leads/user-dashboard/data', [LeadsFrontendController::class, 'userDashboardData'])->name('leads.user.dashboard.data');
                    Route::post('/leads/upload-summernote-file', [LeadsFrontendController::class, 'uploadSummernoteFile'])->name('leads.upload.summernote.file');
                    Route::get('/leads-list', [SystemController::class, 'index'])->name('leads.details');
                    Route::get('/leads-list-all', [SystemController::class, 'index_all'])->name('leads.details.all');
                    Route::get('/leads-list-starred', [SystemController::class, 'index_starred'])->name('leads.details.starred');
                    Route::get('/leads-list-trash', [SystemController::class, 'index_trash'])->name('leads.details.trash');
                    Route::get('/leads-list-duplicate', [SystemController::class, 'index_duplicate'])->name('leads.details.duplicate');
                    Route::get('/leads-list-working-location', [SystemController::class, 'index_working_location'])->name('leads.details.working-location');
                    Route::get('/leads/get-add-form-fields', [SystemController::class, 'getAddLeadFormFields'])->name('leads.add-form-fields');
                    Route::get('/manage-form', [SystemController::class, 'ManageForm'])->name('app.settings.manage.form');
                    Route::get('/manage-form/get-fields', [SystemController::class, 'getManageFormFields'])->name('app.settings.manage.form.fields');
                    Route::post('/leads-reorder-settings-system', [SystemController::class, 'systemReorder'])->name('admin.system-fields.reorder');
                    Route::get('/admin/fields', [SystemController::class, 'getFields'])->name('admin.fields');
                    Route::post('/fields/change-status', [SystemController::class, 'changefromStatus'])->name('fields.changefromStatus');
                    Route::delete('/admin/fields/delete', [SystemController::class, 'deleteSystemField'])->name('admin.fields.delete');
                    Route::post('/fields/toggle-required', [SystemController::class, 'toggleRequired'])->name('fields.toggleRequired');
                    Route::post('/admin/fields/update', [SystemController::class, 'updateFields'])->name('update.fields');
                    Route::post('/get-leads-data', [LeadsController::class, 'getLeads'])->name('get.leads');
                    Route::post('/get-leads-data-all', [LeadsController::class, 'getLeadsAll'])->name('get.leads.all');
                    Route::post('/get-leads-data-trash', [LeadsController::class, 'getLeadsTrash'])->name('get.leads.trash');
                    Route::post('/get-leads-data-duplicate', [LeadsController::class, 'getLeadsDuplicate'])->name('get.leads.duplicate');
                    Route::get('/get-working-locations-list', [LeadsController::class, 'getWorkingLocationsList'])->name('get.working.locations.list');
                    Route::get('/get-leads-job-status/{jobId}', [LeadsController::class, 'getFetchJobStatus'])->name('get.leads.job.status');
                    Route::post('/leads', [LeadsController::class, 'store'])->name('leads.store');
                    Route::get('/lead-approvals', [\App\Http\Controllers\Leads\LeadApprovalController::class, 'index'])->name('lead.approvals.index');
                    Route::post('/get-lead-approvals', [\App\Http\Controllers\Leads\LeadApprovalController::class, 'getApprovals'])->name('get.lead.approvals');
                    Route::post('/lead-approvals/approve', [\App\Http\Controllers\Leads\LeadApprovalController::class, 'approve'])->name('lead.approvals.approve');
                    Route::post('/lead-approvals/reject', [\App\Http\Controllers\Leads\LeadApprovalController::class, 'reject'])->name('lead.approvals.reject');
                    Route::post('/leads/update', [LeadsController::class, 'update'])->name('leads.update');
                    Route::get('/dependent-data', [LeadsController::class, 'getDependentData'])->name('gets.depenedent');
                    Route::get('/get-leads-details', [LeadsController::class, 'GetLeadsDetails'])->name('get.leads.details');
                    Route::get('/leads-column-preferences', [LeadsController::class, 'getColumnPreferences'])->name('leads.column.preferences');
                    Route::post('/leads-column-preferences', [LeadsController::class, 'saveColumnPreferences'])->name('leads.column.preferences.save');
                    Route::get('/get-sources-list', [LeadsController::class, 'getSourcesList'])->name('get.sources.list');
                    Route::get('/get-lead-status-list', [LeadsController::class, 'getLeadStatusList'])->name('get.lead.status.list');
                    Route::get('/get-lead-substatus-list', [LeadsController::class, 'getLeadSubStatusList'])->name('get.lead.substatus.list');
                    Route::get('/get-users-list', [LeadsController::class, 'getUsersList'])->name('get.users.list');
                    Route::post('/leads/bulk-assign', [LeadsController::class, 'bulkAssign'])->name('leads.bulk.assign');
                    Route::post('/leads/bulk-update-status', [LeadsController::class, 'bulkUpdateStatus'])->name('leads.bulk.update.status');
                    Route::post('/leads/bulk-update-source', [LeadsController::class, 'bulkUpdateSource'])->name('leads.bulk.update.source');
                    Route::post('/leads/bulk-trash', [LeadsController::class, 'bulkTrash'])->name('leads.bulk.trash');
                    Route::post('/leads/bulk-restore', [LeadsController::class, 'bulkRestore'])->name('leads.bulk.restore');
                    Route::post('/leads/bulk-delete', [LeadsController::class, 'bulkDelete'])->name('leads.bulk.delete');
                    Route::post('/leads/toggle-star', [LeadsController::class, 'toggleStar'])->name('leads.toggle_star');
                    Route::post('/leads/duplicate', [LeadsController::class, 'duplicate'])->name('leads.duplicate');
                    Route::post('/leads/merge-duplicates', [LeadsController::class, 'mergeDuplicates'])->name('leads.merge_duplicates');
                    Route::post('/leads/export', [LeadsController::class, 'exportLeads'])->name('leads.export');
                    Route::get('/get-team-members', [LeadsController::class, 'getTeamMembers'])->name('get.team.members');
                }
            );
            // Custom Leads Reports (My Reports)
            Route::middleware(['check.subscription:Reminders'])->group(
                function () {
                Route::get('reminder-list', [LeadsController::class, 'ReminderList'])->name('reports.ReminderList');
                Route::get('followups/today', [LeadsController::class, 'TodaysFollowUp'])->name('reports.TodaysFollowUp');
                Route::get('followups/pending', [LeadsController::class, 'PendingFollowUps'])->name('reports.PendingFollowUps');
                Route::get('followups/upcoming', [LeadsController::class, 'UpcomingFollowUps'])->name('reports.UpcomingFollowUps');
                Route::get('today-dsr', [LeadsController::class, 'TodayDSR'])->name('reports.TodayDSR');
                Route::get('this-month-dsr', [LeadsController::class, 'ThisMonthDSR'])->name('reports.ThisMonthDSR');
                Route::get('all-dsr', [LeadsController::class, 'AllDSR'])->name('reports.AllDSR');
                Route::get('all-activity-leads', [LeadsController::class, 'AllActivityLeads'])->name('reports.AllActivityLeads');
                Route::post('/get-reminder-leads-data', [LeadsController::class, 'getReminderLeads'])->name('get.reminder.leads');
                Route::post('/get-dsr-leads', [LeadsController::class, 'getDSRLeads'])->name('get.dsr.leads');
                Route::post('/get-activity-leads', [LeadsController::class, 'getActivityLeads'])->name('get.activity.leads');
                Route::get('/my-reports', [ReportController::class, 'index'])->name('reports.index');
                Route::get('/source-stats', [StatsController::class, 'sourceStats'])->name('reports.source_stats')->middleware('permission.box:reports.source_stats.read');
                Route::get('/source-stats-data', [StatsController::class, 'sourceStatsAjax'])->name('reports.source_stats_ajax')->middleware('permission.box:reports.source_stats.read');
                Route::get('/status-stats', [StatsController::class, 'statusStats'])->name('reports.status_stats')->middleware('permission.box:reports.status_stats.read');
                Route::get('/status-stats-data', [StatsController::class, 'statusStatsAjax'])->name('reports.status_stats_ajax')->middleware('permission.box:reports.status_stats.read');
                Route::get('/task-stats', [StatsController::class, 'taskStats'])->name('reports.task_stats')->middleware('permission.box:reports.task_stats.read');
                Route::get('/task-stats-ajax', [StatsController::class, 'taskStatsAjax'])->name('reports.task_stats_ajax')->middleware('permission.box:reports.task_stats.read');
                Route::get('/date-task-stats', [StatsController::class, 'dateTaskStats'])->name('reports.date_task_stats')->middleware('permission.box:reports.date_task_stats.read');
                Route::get('/date-task-stats-ajax', [StatsController::class, 'dateTaskStatsAjax'])->name('reports.date_task_stats_ajax')->middleware('permission.box:reports.date_task_stats.read');
                Route::get('/common-filters-ajax', [StatsController::class, 'commonFiltersAjax'])->name('reports.common_filters_ajax')->middleware('permission.box:reports.source_stats.read');
                Route::get('/user-stats', [StatsController::class, 'userStats'])->name('reports.user_stats')->middleware('permission.box:reports.user_stats.read');
                Route::get('/user-stats-ajax', [StatsController::class, 'userStatsAjax'])->name('reports.user_stats_ajax')->middleware('permission.box:reports.user_stats.read');
                Route::post('/my-reports', [ReportController::class, 'store'])->name('reports.store');
                Route::post('/my-reports/{id}', [ReportController::class, 'update'])->name('reports.update');
                Route::delete('/my-reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
            }
            );
            Route::get('leads-details', [LeadsController::class, 'LeadsPageDetails'])->name('LeadsDetailsPage');
            Route::get('company-details', [LeadsController::class, 'leadscompanydetails'])->name('details.leadscompanydetails');
            Route::get('get-activity-settings-type', [LeadsController::class, 'getActivityTypeForActivity'])->name('type.getActivityTypeForActivity');
            // Tasks routes
            Route::get('tasks', [\App\Http\Controllers\TasksController::class, 'index'])->name('tasks.index');
            Route::post('get-tasks-data', [\App\Http\Controllers\TasksController::class, 'getTasks'])->name('get.tasks');
            // Activity routes
            Route::get('receipts/lead', [\App\Http\Controllers\Accounts\AccountController::class, 'getLeadReceipts'])->name('receipts.by_lead');
            Route::get('activities', [\App\Http\Controllers\Leads\ActivityController::class, 'getActivities'])->name('activities.get');
            Route::post('activities', [\App\Http\Controllers\Leads\ActivityController::class, 'store'])->name('activities.store');
            Route::put('activities/{id}', [\App\Http\Controllers\Leads\ActivityController::class, 'update'])->name('activities.update');
            Route::delete('activities/{id}', [\App\Http\Controllers\Leads\ActivityController::class, 'destroy'])->name('activities.destroy');
            Route::get('activity-options', [\App\Http\Controllers\Leads\ActivityController::class, 'getActivityOptions'])->name('activities.options');
            Route::get('activity-types', [\App\Http\Controllers\Leads\ActivityController::class, 'getActivityTypes'])->name('activities.types');
            Route::get('activity-statuses', [\App\Http\Controllers\Leads\ActivityController::class, 'getActivityStatuses'])->name('activities.statuses');
            Route::get('activity-users', [\App\Http\Controllers\Leads\ActivityController::class, 'getActivityUsers'])->name('activities.users');
            Route::get('activity-lead-statuses', [\App\Http\Controllers\Leads\ActivityController::class, 'getLeadStatuses'])->name('activities.lead_statuses');
            Route::get('activity-lead-sub-statuses', [\App\Http\Controllers\Leads\ActivityController::class, 'getLeadSubStatuses'])->name('activities.sub-statuses');
            Route::get('activity-file/{path}', [\App\Http\Controllers\Leads\ActivityController::class, 'downloadFile'])->name('activities.file.download')->where('path', '.*');
            Route::get('activity-states', [\App\Http\Controllers\Leads\ActivityController::class, 'getStates'])->name('activities.states');
            Route::get('activity-cities', [\App\Http\Controllers\Leads\ActivityController::class, 'getCities'])->name('activities.cities');
            Route::get('activity-divisions', [\App\Http\Controllers\Leads\ActivityController::class, 'getDivisions'])->name('activities.divisions');
            Route::post('activity-save-location', [\App\Http\Controllers\Leads\ActivityController::class, 'saveWorkingLocation'])->name('activities.save_location');
            Route::post('activity-add-location', [\App\Http\Controllers\Leads\ActivityController::class, 'addWorkingLocation'])->name('activities.add_location');
            Route::get('activity-get-locations', [\App\Http\Controllers\Leads\ActivityController::class, 'getWorkingLocations'])->name('activities.get_locations');
            Route::get('activity-get-working-data', [\App\Http\Controllers\Leads\ActivityController::class, 'getWorkingLocationData'])->name('activities.get_working_data');
            Route::delete('activity-delete-location', [\App\Http\Controllers\Leads\ActivityController::class, 'deleteWorkingLocation'])->name('activities.delete_location');
            Route::get('manage-users', [ManageUserController::class, 'index'])->name('manage.users');
            Route::get('users-details/', [ManageUserController::class, 'users_details'])->name('users.details');
            Route::post('users-resend-email-verification', [ManageUserController::class, 'ResendusersVerifcation'])->name('users.resend.verification');
            Route::post('/create-users', [ManageUserController::class, 'store_users'])->name('store.users');
            Route::delete('/delete-users', [ManageUserController::class, 'delete_users'])->name('delete.users');
            Route::put("/users-toggle-status", [EmployeManageController::class, 'userstoggle'])->name('users.toggle');
            Route::get("/get-empoyee_id", [EmployeManageController::class, 'checkemploye_id'])->name('checkEmployeePrefix');
            Route::put('/update-employeid', [EmployeManageController::class, 'UpdateEmploye_id'])->name('updateEmployeeId');
            Route::get('get-all-labels', [BulkUploadController::class, 'getAllMapings'])->name('get.all.lable.uploade');
            Route::get('bulk-uploads', [BulkUploadController::class, 'getBulkUploads'])->name('bulk.uploads.list');
            Route::post('bulk-upload-csv', [BulkUploadController::class, 'uploadCsv'])->name('bulk.upload.csv');
            Route::post('bulk-save-mapping', [BulkUploadController::class, 'saveMapping'])->name('bulk.save.mapping');
            Route::post('bulk-process-import', [BulkUploadController::class, 'processImport'])->name('bulk.process.import');
            Route::post('bulk-stop-import', [BulkUploadController::class, 'stopImport'])->name('bulk.stop.import');
            Route::delete('bulk-upload-delete', [BulkUploadController::class, 'deleteBulkUpload'])->name('bulk.upload.delete');
            Route::delete('bulk-upload-delete-all', [BulkUploadController::class, 'deleteAllBulkUploads'])->name('bulk.upload.delete.all');
            Route::get('bulk-download-sample', [BulkUploadController::class, 'downloadSample'])->name('bulk.download.sample');
            Route::get('bulk-import-statistics', [BulkUploadController::class, 'getImportStatistics'])->name('bulk.import.statistics');
            Route::get('bulk-upload-headers', [BulkUploadController::class, 'getUploadHeaders'])->name('bulk.upload.headers');
            Route::get('bulk-uploads-data', [BulkUploadController::class, 'bulkUploadsData'])->name('bulk.uploads.data');
            Route::post('bulk-report-request', [BulkUploadController::class, 'requestReport'])->name('bulk.report.request');
            Route::get('bulk-report-status', [BulkUploadController::class, 'reportStatus'])->name('bulk.report.status');
            Route::get('bulk-report-download', [BulkUploadController::class, 'downloadReport'])->name('bulk.report.download');
            Route::middleware(['check.subscription:Attendance'])->group(
                function () {
                    Route::get('holiday', [AttendaceFrontendController::class, 'holiday_attendance'])->name('employee.holiday.page');
                    Route::post('/holiday-post', [HolidayController::class, 'holiday_post'])->name('holiday.post');
                    Route::get('/get-holiday-data', [HolidayController::class, 'getHolidayData'])->name('get.holiday.data');
                    Route::post('/holiday-update', [HolidayController::class, 'holiday_update'])->name('holiday.update');
                    Route::delete('/holiday-delete', [HolidayController::class, 'holiday_delete'])->name('holiday.delete');
                }
            );
            //
            Route::middleware(['check.subscription:Attendance'])->group(
                function () {
                Route::get('leave-type', [AttendaceFrontendController::class, 'lead_type'])->name('employee.leads_type');
                Route::post('/leave-types-store', [LeaveTypeController::class, 'leave_type_store'])->name('leave-types.store');
                Route::post('/credit-leaves', [LeaveController::class, 'CreditLeaves'])->name('credit.leaves');
                Route::get('/leave-types-data', [LeaveTypeController::class, 'getDataLeaveType'])->name('leave-types.data');
                Route::post('/leave-types-update', [LeaveTypeController::class, 'leave_type_update'])->name('leave-types.update');
                Route::delete('/leave-types-delete', [LeaveTypeController::class, 'leave_destroy'])->name('leave-types.destroy');
                Route::post('toggle-leave-type-status', [LeaveTypeController::class, 'ToggleLeaveStatus'])->name('toggle.status.leave.typpe');
                Route::get('/leave-transaction', [AttendaceFrontendController::class, 'Leave_Transaction'])->name('employee.leave_transaction');
                Route::get('/leave-transaction-all', [AttendaceFrontendController::class, 'Leave_Transaction_all'])->name('employee.leave_transaction.all');
                Route::get('/team-transaction-all', [AttendaceFrontendController::class, 'Leave_Transaction_all_team'])->name('employee.leave_transaction.team');
                Route::get('/leave-transaction-data', [LeaveTransactionController::class, 'Leave_dataTransaction'])->name('leave_transaction.data');
                Route::get('/calculate-leave-transactions-all', [LeaveTransactionController::class, 'calculate_leave_transactions_all'])->name('calculate.leave_transactions.all');
                Route::get('/calculate-team-leave-transactions-all', [LeaveTransactionController::class, 'calculate_team_leave_transactions_all'])->name('calculate.team_leave_transactions.all');
                Route::post('/leave-transaction-store', [LeaveTransactionController::class, 'store_leave_transaction'])->name('leave.transaction.store');
            }
            );
            //custom field
            Route::get('roles-permissions', [EmployeManageController::class, 'role_permission'])->name('roles-permissions');
            Route::prefix('settings')->group(
                function () {
                    // General Settings
                    Route::get('/companies', [SettingsController::class, 'companies'])->name('settings.general.companies');
                    Route::get('/get-companies', [SettingsController::class, 'getCompanies'])->name('settings.general.companies.data');
                    Route::post('/companies', [SettingsController::class, 'storeCompany'])->name('settings.general.companies.store');
                    Route::put('/companies', [SettingsController::class, 'updateCompany'])->name('settings.general.companies.update');
                    Route::delete('/companies', [SettingsController::class, 'destroyCompany'])->name('settings.general.companies.destroy');
                    Route::get('/companies-grid', [SettingsController::class, 'companiesGrid'])->name('settings.general.companies_grid');
                    Route::get('/companies/export', [SettingsController::class, 'exportCompanies'])->name('settings.general.companies.export');
                    Route::get('/company-details', [SettingsController::class, 'companyDetails'])->name('settings.general.companys_details');
                    Route::get('/company-currency', [SettingsController::class, 'companycurrency'])->name('website.settings.companys_currency');
                    Route::post('/company-currency/update', [SettingsController::class, 'updateCompanyCurrency'])->name('settings.general.companys_currency.update');
                    Route::get('/hsns', [SettingsController::class, 'hsns'])->name('settings.general.hsns');
                    Route::get('/packages', [SettingsController::class, 'packages'])->name('settings.general.packages');
                    Route::get('/general-company-settings', [SettingsController::class, 'company'])->name('settings.general.companys');
                    Route::get('/activities', [SettingsController::class, 'activityType'])->name('settings.general.activities');
                    Route::get('/sources', [SettingsController::class, 'leadSources'])->name('settings.general.sources');
                    Route::get('/lead-status-check', [SettingsController::class, 'leadStatus'])->name('settings.general.lead_status');
                    Route::get('/integrations', [SettingsController::class, 'integrations'])->name('settings.integrations.integrations');
                    Route::post('/integrations/facebook/save', [SettingsController::class, 'saveFacebookIntegration'])->name('settings.integrations.facebook.save');
                    Route::post('/mail-filters/store', [\App\Http\Controllers\Setting\MailFilterController::class, 'store'])->name('settings.mail_filters.store');
                    Route::get('/mail-filters/form-data', [\App\Http\Controllers\Setting\MailFilterController::class, 'getFormData'])->name('settings.mail_filters.form_data');
                    Route::get('/email-inboxes/{inbox_id}/filters', [\App\Http\Controllers\Setting\MailFilterController::class, 'index'])->name('settings.mail_filters.index');
                    // WhatsApp Business API routes
                    Route::middleware(['check.subscription:WhatsApp API Integration'])->group(
                        function () {
                        Route::get('/whatsapp-business/list', [WhatsAppBusinessController::class, 'index'])->name('whatsapp.business.list');
                        Route::get('/whatsapp-business/phone-numbers/{accountId}', [WhatsAppBusinessController::class, 'getPhoneNumbers'])->name('whatsapp.business.phone-numbers');
                        Route::get('/whatsapp/ai-writer', [WhatsAppBusinessController::class, 'aiWriter'])->name('whatsapp.ai_writer');
                        Route::get('/whatsapp/ai-writer/log/{id}', [WhatsAppBusinessController::class, 'getAiLog'])->name('whatsapp.ai_log');
                        Route::post('/whatsapp/ai-writer/generate', [WhatsAppBusinessController::class, 'generateAiContent'])->name('whatsapp.ai_generate');
                        Route::post('/whatsapp-business/refresh', [WhatsAppBusinessController::class, 'refresh'])->name('whatsapp.business.refresh');
                        Route::get('/whatsapp-business/check-active', [WhatsAppBusinessController::class, 'checkActiveAccounts'])->name('whatsapp.business.checkActive');
                        Route::get('/whatsapp-business/check-token', [WhatsAppBusinessController::class, 'checkTokenStatus'])->name('whatsapp.business.checkToken');
                        Route::get('/whatsapp-business/check-connection', [WhatsAppBusinessController::class, 'checkConnectionStatus'])->name('whatsapp.business.checkConnection');
                        Route::get('/whatsapp-business/check-status', [WhatsAppBusinessController::class, 'checkAccountStatus'])->name('whatsapp.business.checkStatus');
                        Route::get('/whatsapp-business/check-status/{wabaId}', [WhatsAppBusinessController::class, 'checkAccountStatus'])->name('whatsapp.business.checkStatusById');
                        Route::post('/whatsapp-business/{wabaId}/register-webhook', [WhatsAppBusinessController::class, 'registerWebhook'])->name('whatsapp.business.registerWebhook');
                        Route::get('/whatsapp-business/{wabaId}/check-webhook', [WhatsAppBusinessController::class, 'checkWebhookStatus'])->name('whatsapp.business.checkWebhook');
                        Route::get('/whatsapp-business/{id}', [WhatsAppBusinessController::class, 'show'])->name('whatsapp.business.show');
                    }
                    );
                    Route::get('/labels', [SettingsController::class, 'labels'])->name('settings.integrations.labels');
                    // Facebook Routes
                    Route::get('/facebook/dashboard', [FacebookController::class, 'dashboard'])->name('facebook.dashboard');
                    Route::get('/facebook/dashboard-data', [FacebookController::class, 'getDashboardData'])->name('facebook.dashboard_data');
                    Route::post('/facebook/disconnect-account', [FacebookController::class, 'disconnectAccount'])->name('facebook.disconnect_account');
                    Route::get('/facebook/leads', [FacebookController::class, 'facebookLeads'])->name('facebook.leads');
                    Route::get('/facebook/leads/list', [FacebookController::class, 'facebookLeadsList'])->name('facebook.leads.list');
                    Route::post('/facebook/leads/delete', [FacebookController::class, 'deleteLeads'])->name('facebook.leads.delete');
                    Route::post('/facebook/recalculate-counts', [FacebookController::class, 'recalculateCounts'])->name('facebook.recalculate-counts');
                    Route::match(['get', 'post'], '/facebook/list', [FacebookController::class, 'index'])->name('facebook.list');
                    Route::get('/facebook/{id}', [FacebookController::class, 'show'])->name('facebook.show');
                    Route::post('/facebook/updateForm', [FacebookController::class, 'updateForm'])->name('facebook.update-form');
                    Route::get('/facebook/connect_page/{id}', [FacebookController::class, 'connectWebhook'])->name('facebook.connectWebhook');
                    Route::get('/facebook/check_webhook/{id}', [FacebookController::class, 'checkWebhookStatus'])->name('facebook.checkWebhookStatus');
                    Route::get('/facebook/check_all_webhooks', [FacebookController::class, 'checkAllWebhooks'])->name('facebook.checkAllWebhooks');
                    Route::get('/facebook/check-connection', [FacebookController::class, 'checkConnectionStatus'])->name('facebook.checkConnection');
                    Route::get('/facebook/business-integration', [FacebookController::class, 'getBusinessIntegration'])->name('facebook.businessIntegration');
                    Route::get('/facebook/integration-details', [FacebookController::class, 'getSavedIntegrationDetails'])->name('facebook.integrationDetails');
                    Route::post('/facebook/sync-forms', [FacebookController::class, 'syncForms'])->name('facebook.sync-forms');
                    Route::post('/facebook/sync-leads', [FacebookController::class, 'syncLeads'])->name('facebook.sync-leads');
                    Route::post('/facebook/disconnect', [FacebookController::class, 'disconnect'])->name('facebook.disconnect');
                    Route::get('/bulk-uploads', [SettingsController::class, 'bulk_uploades'])->name('settings.integrations.bulk.uploads');
                    Route::get('/compaign', [SettingsController::class, 'compaign_settings'])->name('settings.integrations.compaign.settings');
                    Route::get('/compaign-details', [SettingsController::class, 'compaing_details'])->name('settings.integrations.compaign.details');
                    Route::get('/facebook-form', [SettingsController::class, 'facebook_form'])->name('settings.integrations.facebook.form');
                    Route::get('/create-project', [SettingsController::class, 'create_project'])->name('settings.integrations.create.project');
                    Route::get('/teams', [SettingsController::class, 'teams'])->name('settings.general.teams');
                    Route::middleware(['check.subscription:Custom Fields'])->group(
                        function () {
                            Route::get('/custom-fields', [SettingsController::class, 'index'])->name('settings.general.custom_fields');
                        }
                    );
                    Route::get('/email-inboxes', [SettingsController::class, 'emailInboxes'])->name('settings.general.email_inboxes');
                    Route::get('/email-messages', [SettingsController::class, 'emailMessages'])->name('settings.general.email_messages');
                    Route::get('/get-email-messages', [SettingsController::class, 'getEmailMessages'])->name('settings.get.email_messages');
                    Route::get('/get-email-message-detail/{id}', [SettingsController::class, 'getEmailMessageDetail'])->name('settings.get.email_message_detail');
                    Route::post('/add-gmail-message-as-lead', [SettingsController::class, 'addGmailMessageAsLead'])->name('settings.post.add_gmail_as_lead');
                    Route::get('/api-keys', [SettingsController::class, 'apiKeys'])->name('settings.general.api_keys');
                    Route::get('/get-box-resources', [LeadIntegrationController::class, 'getResources'])->name('settings.get.box.resources');
                    // Leave Transaction Export Routes
                    Route::get('/leave-transaction/export', [LeaveTransactionController::class, 'exportLeaveTransactions'])->name('leave_transaction.export');
                    Route::get('/leave-transaction/export/download/{file}', [LeaveTransactionController::class, 'downloadLeaveExport'])->name('leave_transaction.export.download');
                    Route::get('/get-crm-integrations', [LeadIntegrationController::class, 'index'])->name('settings.get.crm_integrations');
                    Route::get('/get-states-by-country', [LeadIntegrationController::class, 'getStatesByCountry'])->name('settings.get.states');
                    Route::get('/get-cities-by-state', [LeadIntegrationController::class, 'getCitiesByState'])->name('settings.get.cities');
                    Route::post('/add-api', [LeadIntegrationController::class, 'addApi'])->name('settings.add.api');
                    Route::post('/toggle-crm-integration', [LeadIntegrationController::class, 'toggleStatus'])->name('settings.toggle.crm_integration');
                    Route::post('/update-crm-integration', [LeadIntegrationController::class, 'toggleStatus'])->name('settings.update.crm_integration');
                    Route::post('/regenerate-integration-token', [LeadIntegrationController::class, 'regenerateToken'])->name('settings.regenerate.crm_token');
                    Route::get('/roles-permissions', [SettingsController::class, 'rolesPermissions'])->name('manage_users.roles_permissions');
                    Route::get('/departments', [SettingsController::class, 'departments'])->name('manage_users.departments');
                    Route::get('/departments/list', [SettingsController::class, 'getDepartmentsList'])->name('manage_users.departments.list');
                    Route::post('/departments/store', [SettingsController::class, 'storeDepartment'])->name('manage_users.departments.store');
                    Route::put('/departments/update/{id}', [SettingsController::class, 'updateDepartment'])->name('manage_users.departments.update');
                    Route::delete('/departments/delete/{id}', [SettingsController::class, 'deleteDepartment'])->name('manage_users.departments.delete');
                    Route::get('/designations', [SettingsController::class, 'designations'])->name('manage_users.designations');
                    Route::get('/designations/list', [SettingsController::class, 'getDesignationsList'])->name('manage_users.designations.list');
                    Route::post('/designations/store', [SettingsController::class, 'storeDesignation'])->name('manage_users.designations.store');
                    Route::put('/designations/update/{id}', [SettingsController::class, 'updateDesignation'])->name('manage_users.designations.update');
                    Route::delete('/designations/delete/{id}', [SettingsController::class, 'deleteDesignation'])->name('manage_users.designations.delete');
                    Route::get('/permissions', [SettingsController::class, 'permissions'])->name('manage_users.permissions');
                    Route::post('/permissions', [SettingsController::class, 'storePermission'])->name('user.permissions.store');
                    Route::post('/update-role-permissions', [SettingsController::class, 'updateRolePermissions'])->name('user.permissions.updateRole');
                    Route::post('/sync-permissions', [SettingsController::class, 'syncPermissions'])->name('user.permissions.sync');
                    // Website Settings
                    Route::get('/lead-apis', [LeadIntegrationController::class, 'leadApis'])->name('website.settings.lead_apis');
                    Route::post('/lead-apis/sync-legacy', [LeadIntegrationController::class, 'syncLegacyApis'])->name('website.settings.sync_legacy_apis');
                    Route::get('/localization', [SettingsController::class, 'localization'])->name('website.settings.localization');
                    Route::get('/prefixes', [SettingsController::class, 'prefixes'])->name('website.settings.prefixes');
                    Route::get('/preference', [SettingsController::class, 'preference'])->name('website.settings.preference');
                    Route::get('/column-preferences', [SettingsController::class, 'getColumnPreferences'])->name('settings.column.preferences.get');
                    Route::post('/column-preferences', [SettingsController::class, 'saveColumnPreferences'])->name('settings.column.preferences.save');
                    Route::get('/appearance', [SettingsController::class, 'appearance'])->name('website.settings.appearance');
                    Route::get('/language', [SettingsController::class, 'language'])->name('website.settings.language');
                    Route::get('/company-settings', [SettingsController::class, 'CompanySettings'])->name('website.settings.company');
                    Route::middleware(['check.subscription:Attendance'])->group(
                        function () {
                            Route::get('/attendace-module', [SettingsController::class, 'attendace_module'])->name('website.settings.attendance.module');
                            Route::get('/attendace-module/get', [SettingsController::class, 'getAttendanceModuleSettings'])->name('website.settings.attendance.module.get');
                            Route::post('/attendace-module/save', [SettingsController::class, 'saveAttendanceModuleSettings'])->name('website.settings.attendance.module.save');
                        }
                    );
                    Route::get('/branchs-settings', [SettingsController::class, 'branchSettings'])->name('website.settings.branch');
                    Route::get('/employee-type-settings', [SettingsController::class, 'employeeTypeSettings'])->name('manage_users.employee_type');
                    Route::get('employee-type-list', [SettingsController::class, 'getEmployeeTypes'])->name('employee-type.list');
                    Route::post('employee-type-store', [SettingsController::class, 'storeEmployeeType'])->name('employee-type.store');
                    Route::post('employee-type-update', [SettingsController::class, 'updateEmployeeType'])->name('employee-type.update');
                    Route::post('employee-type-delete', [SettingsController::class, 'deleteEmployeeType'])->name('employee-type.delete');
                    Route::get('/work-mode-settings', [SettingsController::class, 'work_mode_Settings'])->name('manage_users.work_mode');
                    Route::post('/company/settings-update', [SettingsController::class, 'updateCompanySettings'])->name('website.settings.company.update');
                    Route::get('/get-all-country', [SettingsController::class, 'SearchCountry'])->name('get.all.country');
                    Route::post('get-states', [SettingsController::class, 'getStatesSettings'])->name('company.states.select');
                    Route::post('get-cities', [SettingsController::class, 'getCitiesSettings'])->name('company.city.select');
                    Route::post('work-mode-settings-list', [WorkModeController::class, 'work_mode_index'])->name('work-mode-settings.list');
                    Route::post('work-mode-settings-store', [WorkModeController::class, 'work_mode_store'])->name('work-mode-settings.store');
                    Route::post('work-mode-settings-update', [WorkModeController::class, 'work_mode_update'])->name('work-mode-settings.update');
                    Route::post('work-mode-settings-delete', [WorkModeController::class, 'work_mode_destroy'])->name('work-mode-settings.delete');
                    Route::get('get-all-work-mode', [EmployeManageController::class, 'GetAllWorkMode'])->name('get.all.workmode');
                    Route::post('updated-new-work-mode', [EmployeManageController::class, 'UpdateWorkMode'])->name('update.work.mode');
                    Route::get('get-all-branch', [EmployeManageController::class, 'GetAllBranchOffice'])->name('get.all.branchOffice');
                    Route::post('company-branches-list', [CompanyBranchController::class, 'branchindex'])->name('company-branches.list');
                    Route::post('company-branches-store', [CompanyBranchController::class, 'branchstore'])->name('company-branches.store');
                    Route::post('company-branches-update', [CompanyBranchController::class, 'branchupdate'])->name('company-branches.update');
                    Route::post('company-branches-delete', [CompanyBranchController::class, 'branchdestroy'])->name('company-branches.delete');
                    Route::get('/get-all-branches', [BiometricController::class, 'getAllBranch'])->name('getall.biometric');
                    // App Settings
                    Route::get('/timezones', [BiometricController::class, 'getTimezones'])->name('timezones');
                    Route::get('/biometric-get', [BiometricController::class, 'getBiometric'])->name('biometric.index'); // Get all
                    Route::post('/biometric-store', [BiometricController::class, 'store'])->name('biometric.store'); // Create
                    Route::put('/biometric-update', [BiometricController::class, 'update'])->name('biometric.update'); // Update
                    Route::delete('/biometric-delete', [BiometricController::class, 'destroy'])->name('biometric.destroy'); // Delete
                    Route::post('/biometric-export', [BiometricController::class, 'export'])->name('biometric.export.start');
                    Route::get('/biometric-download/{file}', [BiometricController::class, 'downloadExport'])->name('biometric.export.download');
                    Route::get('/invoice-settings', [SettingsController::class, 'invoiceSettings'])->name('app.settings.invoice');
                    Route::get('/printers', [SettingsController::class, 'printers'])->name('app.settings.printers');
                    Route::get('get-custom-fields', [SettingsController::class, 'customFields'])->name('app.settings.custom_fields.index');
                    Route::get('get-salary-custom-fields', [SettingsController::class, 'salaryCustomFields'])->name('app.settings.custom_fields.salary');
                    Route::post('toggle-custom-status', [SettingsController::class, 'toggleCustomStatus'])->name('settings.custom.toggle');
                    Route::post('/settings/custom-fields', [SettingsController::class, 'storeCustomField'])->name('settings.custom_fields.store');
                    Route::post('/custom-fields/reorder', [SettingsController::class, 'reorder'])->name('settings.custom_fields.reorder');
                    Route::post('/settings/custom-update', [SettingsController::class, 'update'])->name('settings.custom_fields.update');
                    Route::delete('/settings/custom-delete', [SettingsController::class, 'DeleteCustom'])->name('settings.custom.delete');
                    Route::get('/crm-setings-sources', [CrmSourceController::class, 'source'])->name('app.settings.source');
                    Route::get('/crm-lost-reason', [CrmSourceController::class, 'lost_reason'])->name('app.settings.lost.reason');
                    Route::get('/lead-status', [CrmSourceController::class, 'contact_stage'])->name('app.settings.contact.stage');
                    Route::get('/crm-industry', [CrmSourceController::class, 'industry'])->name('app.settings.industry');
                    Route::middleware(['check.subscription:Reminders'])->group(
                        function () {
                            Route::get('/activity-settings', [CrmSourceController::class, 'calls'])->name('app.settings.calls');
                        }
                    );
                    // System Settings
                    Route::get('/email-settings', [SettingsController::class, 'emailSettings'])->name('system.settings.email');
                    Route::get('/notifications/get', [NotificationController::class, 'getNotifications'])->name('notifications.get');
                    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark_read');
                    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_read');
                    Route::post('/notifications/send-overall', [NotificationController::class, 'sendOverallNotification'])->name('notifications.send_overall');
                    Route::middleware(['check.subscription:SMS Integration'])->group(
                        function () {
                            Route::get('/sms-gateways', [SettingsController::class, 'smsGateways'])->name('system.settings.sms_gateways');
                        }
                    );
                    Route::get('/gdpr-cookies', [SettingsController::class, 'gdprCookies'])->name('system.settings.gdpr');
                    // Financial Settings
                    Route::get('/payment-gateways', [SettingsController::class, 'paymentGateways'])->name('financial.settings.payment_gateways');
                    Route::get('/bank-accounts', [BankController::class, 'index'])->name('financial.settings.bank_accounts');
                    Route::post('/bank-accounts', [BankController::class, 'store'])->name('financial.settings.bank_accounts.store');
                    Route::put('/bank-accounts', [BankController::class, 'update'])->name('financial.settings.bank_accounts.update');
                    Route::delete('/bank-accounts', [BankController::class, 'destroy'])->name('financial.settings.bank_accounts.destroy');
                    Route::get('/payment-modes', [PaymentModeController::class, 'index'])->name('financial.settings.payment_modes');
                    Route::post('/payment-modes', [PaymentModeController::class, 'store'])->name('financial.settings.payment_modes.store');
                    Route::put('/payment-modes', [PaymentModeController::class, 'update'])->name('financial.settings.payment_modes.update');
                    Route::delete('/payment-modes', [PaymentModeController::class, 'destroy'])->name('financial.settings.payment_modes.destroy');
                    Route::get('/divisions', [\App\Http\Controllers\Setting\DivisionController::class, 'index'])->name('financial.settings.divisions');
                    Route::post('/divisions', [\App\Http\Controllers\Setting\DivisionController::class, 'store'])->name('financial.settings.divisions.store');
                    Route::put('/divisions', [\App\Http\Controllers\Setting\DivisionController::class, 'update'])->name('financial.settings.divisions.update');
                    Route::delete('/divisions', [\App\Http\Controllers\Setting\DivisionController::class, 'destroy'])->name('financial.settings.divisions.destroy');
                    Route::get('/products', [\App\Http\Controllers\Setting\ProductController::class, 'index'])->name('settings.products');
                    Route::post('/products', [\App\Http\Controllers\Setting\ProductController::class, 'store'])->name('settings.products.store');
                    Route::put('/products', [\App\Http\Controllers\Setting\ProductController::class, 'update'])->name('settings.products.update');
                    Route::delete('/products', [\App\Http\Controllers\Setting\ProductController::class, 'destroy'])->name('settings.products.destroy');
                    Route::get('/tax-rates', [SettingsController::class, 'taxRates'])->name('financial.settings.tax_rates');
                    Route::get('/tax-rates-list', [\App\Http\Controllers\Setting\TaxRateController::class, 'index'])->name('financial.settings.tax_rates.list');
                    Route::post('/tax-rates-store', [\App\Http\Controllers\Setting\TaxRateController::class, 'store'])->name('financial.settings.tax_rates.store');
                    Route::post('/tax-rates-update', [\App\Http\Controllers\Setting\TaxRateController::class, 'update'])->name('financial.settings.tax_rates.update');
                    Route::delete('/tax-rates-delete', [\App\Http\Controllers\Setting\TaxRateController::class, 'destroy'])->name('financial.settings.tax_rates.delete');
                    Route::get('/currencies', [SettingsController::class, 'currencies'])->name('financial.settings.currencies');
                    Route::get('/box-settings', [SettingsController::class, 'box_settings'])->name('other.settings.box_settings');
                    Route::get('get-working-data', [SettingsController::class, 'box_settings_get'])->name('settings.worktime.get');
                    Route::post('save-working-data', [SettingsController::class, 'box_settings_save'])->name('settings.worktime.save');
                    Route::get('/sync-lead-fields', [FieldSyncController::class, 'sync'])->name('settings.sync.lead_fields');
                    // Other
                    Route::get('/storage', [SettingsController::class, 'storage'])->name('other.settings.storage');
                    Route::get('/ban-ip-address', [SettingsController::class, 'banIpAddress'])->name('other.settings.ban_ip');
                }
            );
        }
    );
}); // Duplicate and Merge Routes

// Under Maintenance, Coming Soon, and Error Page Routes
Route::get('/error-404.html', function () {
    return response()->view('errors.404', [], 404);
})->name('error.404');

Route::get('/error-500.html', function () {
    return response()->view('errors.500', [], 500);
})->name('error.500');

Route::get('/under-maintenance.html', function () {
    return view('under-maintenance');
})->name('under-maintenance');

Route::get('/coming-soon.html', function () {
    return view('coming-soon');
})->name('coming-soon');


// Global Route for Non-auth / Website interactions matching checkout params
Route::get('/checkout/{planId}', [App\Http\Controllers\CheckoutController::class, 'subscribeGlobal'])->name('checkout.subscribe');
Route::post('/checkout/apply-coupon/{planId}', [App\Http\Controllers\CheckoutController::class, 'applyCouponGlobal'])->name('checkout.apply_coupon');
Route::post('/checkout/process/{planId}', [App\Http\Controllers\CheckoutController::class, 'processPaymentGlobal'])->name('checkout.process');
Route::post('/checkout/success', [App\Http\Controllers\CheckoutController::class, 'handleSuccessGlobal'])->name('checkout.callback');

// require __DIR__.'/test_view.php';
