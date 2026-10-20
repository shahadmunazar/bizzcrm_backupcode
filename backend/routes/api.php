<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{ApiAuthController,ActivityApiController,ReceiptApiController,WorkingLocationApiController,LeadApiController,WhatsAppApiController,CustomerApiController};
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\Call\CallDashboardController;
use App\Http\Controllers\Api\LocationTrackingApiController;
use App\Http\Controllers\Api\FieldTrackingActivityApiController;
use App\Http\Controllers\Api\FieldTrackingTourApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// Authentication Routes
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
Route::get('/search-country', [ApiAuthController::class, 'SearchCountry']);
Route::get('/countries', [ApiAuthController::class, 'getCountries']);
Route::get('/countries/{country_id}/states', [ApiAuthController::class, 'getStates']);
Route::get('/states', [ApiAuthController::class, 'getStates']);
Route::get('/states/{state_id}/cities', [ApiAuthController::class, 'getCities']);
Route::get('/cities', [ApiAuthController::class, 'getCities']);
Route::get('/industries', [ApiAuthController::class, 'getIndustries']);
Route::post('/resend-verification-email', [ApiAuthController::class, 'resendVerificationEmail']);
Route::post('/send-otp', [ApiAuthController::class, 'sendOtp']);
Route::post('/verify-otp', [ApiAuthController::class, 'verifyOtp']);
Route::post('/forget-password-email', [ApiAuthController::class, 'forgetEmail']);
Route::post('/reset-password-submit', [ApiAuthController::class, 'Reset_Password']);
Route::get('/verify-email/{token}', [ApiAuthController::class, 'verify']);
Route::post('/change-password/send-otp', [ApiAuthController::class, 'sendChangePasswordOtp']);
Route::post('/change-password/verify-otp', [ApiAuthController::class, 'verifyChangePasswordOtp']);
Route::post('/change-password/update', [ApiAuthController::class, 'updatePasswordWithToken']);

// Developer API Routes (Token based, No Box Slug needed in URL since token identifies Box)
Route::group(['prefix' => '/whatsapp'], function () {
    Route::post('/send', [WhatsAppApiController::class, 'send']);
    Route::get('/messages', [WhatsAppApiController::class, 'messages']);
    Route::get('/dashboard', [WhatsAppApiController::class, 'dashboard']);
    Route::get('/accounts', [WhatsAppApiController::class, 'accounts']);
    Route::post('/connect', [WhatsAppApiController::class, 'connectAccount']);
    Route::get('/chats', [WhatsAppApiController::class, 'chats']);
    Route::get('/chats/{lead_id}/messages', [WhatsAppApiController::class, 'chatMessages']);
    Route::post('/chats/send-media', [WhatsAppApiController::class, 'sendMedia']);
    Route::post('/messages/{id}/react', [WhatsAppApiController::class, 'reactMessage']);
    Route::get('/projects', [WhatsAppApiController::class, 'projects']);
    Route::post('/projects', [WhatsAppApiController::class, 'storeProject']);
    Route::get('/templates', [WhatsAppApiController::class, 'templates']);
    Route::get('/chatbots', [WhatsAppApiController::class, 'chatbots']);
    Route::get('/quick-replies', [WhatsAppApiController::class, 'quickReplies']);
    Route::get('/campaigns', [WhatsAppApiController::class, 'campaigns']);
    Route::post('/campaigns', [WhatsAppApiController::class, 'storeCampaign']);
});

// Protected Routes under Multi-tenant Box Slug
Route::group(['prefix' => '{box_slug}', 'middleware' => ['auth:sanctum', 'check.roles.box:admin,employee,superadmin']], function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::post('/cancel-web-logout', [ApiAuthController::class, 'cancelWebLogout']);
    Route::post('/cancel-api-logout', [ApiAuthController::class, 'cancelApiLogout']);

    // Location Tracking API
    Route::post('/location', [LocationTrackingApiController::class, 'store']);
    Route::get('/location/dashboard', [LocationTrackingApiController::class, 'dashboard']);
    Route::get('/location/current', [LocationTrackingApiController::class, 'current']);
    Route::get('/location/history', [LocationTrackingApiController::class, 'history']);
    Route::get('/location/route', [LocationTrackingApiController::class, 'route']);

    Route::get('/me', [ApiAuthController::class, 'me']);
    
    // Subscription APIs
    Route::get('/subscription/dashboard', [\App\Http\Controllers\Api\SubscriptionApiController::class, 'dashboard']);
    Route::get('/subscription/invoices', [\App\Http\Controllers\Api\SubscriptionApiController::class, 'invoices']);

    Route::get('/account/delete-link', [\App\Http\Controllers\Api\AccountDeletionController::class, 'getAccountDeleteLink']);
    Route::post('/check-permission', [ApiAuthController::class, 'checkPermission']);
    Route::get('/sidebar-menus', [ApiAuthController::class, 'getSidebarMenus']);
    Route::get('/users', [ApiAuthController::class, 'getBoxUsers'])->middleware('permission.box:user_management.employee_list.read');
    Route::get('/call-logs', [CallLogController::class, 'apiIndex'])->middleware('permission.box:app_settings.calls.read');
    Route::post('/call-logs', [CallLogController::class, 'apiStore'])->middleware('permission.box:app_settings.calls.create');
    Route::get('/call-logs/sync-count', [CallLogController::class, 'apiSyncCount'])->middleware('permission.box:app_settings.calls.read');
    Route::get('/call-logs/status', [CallLogController::class, 'status'])->middleware('permission.box:app_settings.calls.read');
    Route::get('/call-dashboard/data', [CallDashboardController::class, 'apiDashboardData'])->middleware('permission.box:app_settings.calls.read');
    // Leads API
    Route::get('/leads/form-options', [LeadApiController::class, 'getFormOptions'])->middleware('permission.box:leads.list.read');
    Route::get('/leads/form-fields', [LeadApiController::class, 'getFormFields'])->middleware('permission.box:leads.list.read');
    Route::get('/leads/countries', [LeadApiController::class, 'getCountries'])->middleware('permission.box:leads.list.read');
    Route::get('/leads/countries/{country_id}/states', [LeadApiController::class, 'getStates'])->middleware('permission.box:leads.list.read');
    Route::get('/leads/states/{state_id}/cities', [LeadApiController::class, 'getCities'])->middleware('permission.box:leads.list.read');
    // Lead Details - Activities (Static Endpoints)
    Route::get('/leads/activity-options', [ActivityApiController::class, 'getActivityOptions'])->middleware('permission.box:leads.list.read');
    Route::get('/leads/activity-types', [ActivityApiController::class, 'getActivityTypes'])->middleware('permission.box:leads.list.read');
    Route::get('/leads/activity-statuses', [ActivityApiController::class, 'getActivityStatuses'])->middleware('permission.box:leads.list.read');
    Route::get('/activities', [ActivityApiController::class, 'getAllActivities'])->middleware('permission.box:activities_records.all_activity_leads.read');
    Route::post('/activities', [ActivityApiController::class, 'storeActivity'])->middleware('permission.box:activities_records.all_activity_leads.create');
    // Leads Dashboard API
    Route::get('/leads/dashboard/data', [\App\Http\Controllers\Leads\LeadsFrontendController::class, 'dashboardData'])->middleware('permission.box:leads.dashboard.read');

    // Leads Team Hierarchy API
    Route::get('/leads/team-members', [LeadApiController::class, 'getTeamMembers'])->middleware('permission.box:leads.list.read');
    Route::get('/leads/teams', [LeadApiController::class, 'getTeamMembers'])->middleware('permission.box:leads.list.read');

    Route::get('/leads', [LeadApiController::class, 'index'])->middleware('permission.box:leads.list.read');
    Route::post('/leads', [LeadApiController::class, 'store'])->middleware('permission.box:leads.list.create');
    Route::get('/leads/{lead_id}', [LeadApiController::class, 'show'])->middleware('permission.box:leads.list.read');
    Route::put('/leads/{lead_id}', [LeadApiController::class, 'update'])->middleware('permission.box:leads.list.update');
    Route::delete('/leads/{lead_id}', [LeadApiController::class, 'destroy'])->middleware('permission.box:leads.list.delete');
    Route::get('/leads/{lead_id}/activities', [ActivityApiController::class, 'index'])->middleware('permission.box:leads.list.read');
    Route::post('/leads/{lead_id}/activities', [ActivityApiController::class, 'store'])->middleware('permission.box:leads.list.update');
    Route::put('/leads/{lead_id}/activities/{activity_id}', [ActivityApiController::class, 'update'])->middleware('permission.box:leads.list.update');
    Route::delete('/leads/{lead_id}/activities/{activity_id}', [ActivityApiController::class, 'destroy'])->middleware('permission.box:leads.list.update');
    Route::patch('/leads/{lead_id}/activities/{activity_id}/status', [ActivityApiController::class, 'changeStatus'])->middleware('permission.box:leads.list.update');
    
    // Customers API (Field Tracking)
    Route::get('/customers/search-leads', [CustomerApiController::class, 'searchLeads']);
    Route::get('/customers', [CustomerApiController::class, 'index']);
    Route::post('/customers', [CustomerApiController::class, 'store']);
    Route::get('/customers/{id}', [CustomerApiController::class, 'show']);
    Route::delete('/customers/{id}', [CustomerApiController::class, 'destroy']);
    Route::post('/customers/check-in', [CustomerApiController::class, 'storeCheckIn']);
    Route::get('/customers/{id}/check-ins', [CustomerApiController::class, 'checkInHistory']);
    
    // Fieldstracking Team Activity & Tracking History API
    Route::post('/fieldstracking/team-activity-data', [FieldTrackingActivityApiController::class, 'teamActivityData']);
    Route::get('/fieldstracking/tracking-history-data', [FieldTrackingActivityApiController::class, 'trackingHistoryData']);
    
    // Fieldstracking Tour API
    Route::post('/fieldstracking/tour-form-data', [FieldTrackingTourApiController::class, 'getFormData']);
    Route::post('/fieldstracking/tour-leads-data', [FieldTrackingTourApiController::class, 'getLeadsData']);
    Route::post('/fieldstracking/tour-store', [FieldTrackingTourApiController::class, 'store']);
    Route::post('/fieldstracking/tour-get-customers', [FieldTrackingTourApiController::class, 'getCustomers']);
    Route::post('/fieldstracking/tour-data', [FieldTrackingTourApiController::class, 'tourListData']);
    
    // Google API endpoints
    Route::post('/google/send-email', [\App\Http\Controllers\GoogleController::class, 'sendEmail'])->middleware('permission.box:general_settings.email_messages.create');
    Route::get('/google/get-email-messages', [\App\Http\Controllers\Setting\SettingsController::class, 'getEmailMessages'])->middleware('permission.box:general_settings.email_messages.read');

    // Lead Details - Receipts
    Route::get('/leads/receipt-form-data', [ReceiptApiController::class, 'getReceiptFormData'])->middleware('permission.box:accounts.receipts.read');
    Route::get('/leads/{lead_id}/receipts', [ReceiptApiController::class, 'index'])->middleware('permission.box:accounts.receipts.read');
    Route::post('/leads/{lead_id}/receipts', [ReceiptApiController::class, 'store'])->middleware('permission.box:accounts.receipts.create');
    Route::put('/leads/{lead_id}/receipts/{receipt_id}', [ReceiptApiController::class, 'update'])->middleware('permission.box:accounts.receipts.update');
    Route::delete('/leads/{lead_id}/receipts/{receipt_id}', [ReceiptApiController::class, 'destroy'])->middleware('permission.box:accounts.receipts.delete');

    // Lead Details - Working Locations
    Route::get('/leads/location-states', [WorkingLocationApiController::class, 'getStates']);
    Route::get('/leads/location-cities', [WorkingLocationApiController::class, 'getCities']);
    Route::get('/leads/location-divisions', [WorkingLocationApiController::class, 'getDivisions']);
    Route::get('/leads/{lead_id}/working-locations', [WorkingLocationApiController::class, 'index'])->middleware('permission.box:leads.working_location.read');
    Route::post('/leads/{lead_id}/working-locations', [WorkingLocationApiController::class, 'store'])->middleware('permission.box:leads.working_location.create');
    Route::put('/leads/{lead_id}/working-locations/{location_id}', [WorkingLocationApiController::class, 'update'])->middleware('permission.box:leads.working_location.update');
    Route::delete('/leads/{lead_id}/working-locations/{location_id}', [WorkingLocationApiController::class, 'destroy'])->middleware('permission.box:leads.working_location.delete');
    // Mobile Device Registration for Attendance
    Route::get('/attendance/device', [\App\Http\Controllers\Api\AttendanceDeviceController::class, 'checkStatus'])->middleware('permission.box:attendance.device_register.read');
    Route::post('/attendance/device', [\App\Http\Controllers\Api\AttendanceDeviceController::class, 'enroll'])->middleware('permission.box:attendance.device_register.create');
    Route::post('/attendance/device/fingerprint', [\App\Http\Controllers\Api\AttendanceDeviceController::class, 'bindFingerprint'])->middleware('permission.box:attendance.device_register.update');
    Route::post('/attendance/device/punch', [\App\Http\Controllers\Api\AttendanceDeviceController::class, 'punch'])->middleware('permission.box:attendance.device_register.create');
    Route::post('/device/register-fcm-token', [\App\Http\Controllers\Api\DeviceController::class, 'registerToken']);

    // Notifications API
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationApiController::class, 'index']);
    Route::get('/notifications/unread-count', [\App\Http\Controllers\Api\NotificationApiController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationApiController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationApiController::class, 'markAllAsRead']);
    Route::get('/employee-dashboard-notifications', [\App\Http\Controllers\Api\NotificationApiController::class, 'dashboardNotifications']);

    // Admin Attendance API
    Route::get('/attendance/admin', [\App\Http\Controllers\Api\AttendanceApiController::class, 'adminAttendance'])->middleware('permission.box:attendance.admin.read');
    Route::get('/attendance/team', [\App\Http\Controllers\Api\AttendanceApiController::class, 'teamAttendance'])->middleware('permission.box:attendance.team_admin.read');

    // Unified Admin / CRM Main Dashboard API
    Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\AdminFrontendController::class, 'apiDashboardComplete'])->middleware('permission.box:attendance.admin.read|user_management.employee_list.read');
    Route::get('/admin/dashboard/data', [\App\Http\Controllers\Admin\AdminFrontendController::class, 'apiDashboardComplete'])->middleware('permission.box:attendance.admin.read|user_management.employee_list.read');

    // Unified Admin & Team Attendance Dashboard APIs
    Route::get('/dashboard/admin-attendance-list', [\App\Http\Controllers\Employee\AttendaceController::class, 'AdminAttendaceList'])->middleware('permission.box:attendance.admin.read');
    Route::get('/attendance/dashboard/departments', [\App\Http\Controllers\Employee\AttendaceController::class, 'AttendanceDepartment'])->middleware('permission.box:attendance.admin.read|attendance.team_admin.read');
    Route::get('/attendance/dashboard/today-absentees', [\App\Http\Controllers\Employee\AttendaceController::class, 'today_absenties'])->middleware('permission.box:attendance.admin.read|attendance.team_admin.read');
    Route::get('/attendance/dashboard/stats', [\App\Http\Controllers\Employee\AttendaceController::class, 'attendace_admin_stats'])->middleware('permission.box:attendance.admin.read|attendance.team_admin.read');
    
    // Note: Admin and Team have different table fetch methods, but we could unify them if the controller handled it. For now, they remain separate routes or they can use the same route if we pass a parameter. Actually, the frontend calls 'attendace.employee' for Admin and 'team.attendace.employee' for Team. So they must remain separate if we don't want to change frontend code.
    Route::get('/attendance/admin/employee-data', [\App\Http\Controllers\Employee\AttendaceController::class, 'attendace_employee'])->middleware('permission.box:attendance.admin.read');
    Route::get('/attendance/team/employee-data', [\App\Http\Controllers\Employee\AttendaceController::class, 'team_attendace_employee'])->middleware('permission.box:attendance.team_admin.read');

    Route::post('/attendance/dashboard/export-pdf', [\App\Http\Controllers\Employee\AttendaceController::class, 'export_attendace_pdf'])->middleware('permission.box:attendance.admin.read|attendance.team_admin.read');
    Route::put('/attendance/dashboard/update-correction', [\App\Http\Controllers\Employee\AttendaceController::class, 'update_attendance_correction'])->middleware('permission.box:attendance.admin.update|attendance.team_admin.update');
    Route::post('/attendance/dashboard/edit', [\App\Http\Controllers\Employee\AttendaceController::class, 'edit_attendace'])->middleware('permission.box:attendance.admin.update|attendance.team_admin.update');
    Route::get('/attendance/dashboard/individual-summary', [\App\Http\Controllers\Employee\AttendaceController::class, 'individualattendace_summary'])->middleware('permission.box:attendance.admin.read|attendance.team_admin.read');
    Route::post('/attendance/dashboard/process', [\App\Http\Controllers\Attendance\AttendaceFrontendController::class, 'process_attendance'])->middleware('permission.box:attendance.admin.update|attendance.team_admin.update');

    // Column Preferences APIs (used across data tables)
    Route::get('/settings/column-preferences', [\App\Http\Controllers\Setting\SettingsController::class, 'getColumnPreferences']);
    Route::post('/settings/column-preferences', [\App\Http\Controllers\Setting\SettingsController::class, 'saveColumnPreferences']);

    // Biometric Machine API
    Route::get('/biometric-machines/timezones', [\App\Http\Controllers\Api\BiometricMachineApiController::class, 'getTimezones'])->middleware('permission.box:attendance.biometric.read');
    Route::get('/biometric-machines/branches', [\App\Http\Controllers\Api\BiometricMachineApiController::class, 'getAllBranch'])->middleware('permission.box:attendance.biometric.read');
    Route::get('/biometric-machines', [\App\Http\Controllers\Api\BiometricMachineApiController::class, 'getBiometric'])->middleware('permission.box:attendance.biometric.read');
    Route::get('/biometric-machines/{id}', [\App\Http\Controllers\Api\BiometricMachineApiController::class, 'show'])->middleware('permission.box:attendance.biometric.read');
    Route::post('/biometric-machines', [\App\Http\Controllers\Api\BiometricMachineApiController::class, 'store'])->middleware('permission.box:attendance.biometric.create');
    Route::put('/biometric-machines', [\App\Http\Controllers\Api\BiometricMachineApiController::class, 'update'])->middleware('permission.box:attendance.biometric.update');
    Route::delete('/biometric-machines', [\App\Http\Controllers\Api\BiometricMachineApiController::class, 'destroy'])->middleware('permission.box:attendance.biometric.delete');

    // Employee Attendance APIs
    Route::get('/attendance/employee/records', [\App\Http\Controllers\Api\AttendanceApiController::class, 'employeeAttendance'])->middleware('permission.box:attendance.employee.read');
    Route::get('/attendance/employee/summary', [\App\Http\Controllers\Employee\AttendaceController::class, 'checkattendace_summary'])->middleware('permission.box:attendance.employee.read');
    Route::get('/attendance/employee/check-status', [\App\Http\Controllers\Employee\AttendaceController::class, 'check_attendance'])->middleware('permission.box:attendance.employee.read');
    Route::post('/attendance/employee/mark', [\App\Http\Controllers\Employee\AttendaceController::class, 'mark_attendace'])->middleware('permission.box:attendance.employee.create');
    Route::post('/attendance/employee/break', [\App\Http\Controllers\Employee\AttendaceController::class, 'take_break'])->middleware('permission.box:attendance.employee.create');
    Route::post('/attendance/employee/correction', [\App\Http\Controllers\Employee\AttendaceController::class, 'SavedAttendanceCoreection'])->middleware('permission.box:attendance.employee.create');
    Route::get('/attendance/employee/breaks', [\App\Http\Controllers\Employee\AttendaceController::class, 'getAttendanceBreaks'])->middleware('permission.box:attendance.employee.read');
    Route::post('/attendance/employee/auto-punch-out', [\App\Http\Controllers\Employee\AttendaceController::class, 'auto_attendance']); // No permission needed for auto punch
    Route::get('/attendance/employee/calendar', [\App\Http\Controllers\Employee\AttendaceController::class, 'GetCalendarData'])->middleware('permission.box:attendance.employee.read');
    
    // Employee Dashboard APIs
    Route::get('/dashboard', [\App\Http\Controllers\Employee\EmployeeFrontendController::class, 'apiDashboardComplete'])->middleware('permission.box:attendance.employee.read');
    Route::get('/dashboard/info', [\App\Http\Controllers\Employee\EmployeeFrontendController::class, 'apiDashboardInfo'])->middleware('permission.box:attendance.employee.read');
    Route::get('/dashboard/notifications', [\App\Http\Controllers\Employee\EmployeeFrontendController::class, 'getLatestNotifications'])->middleware('permission.box:attendance.employee.read');
    Route::get('/dashboard/team-members', [\App\Http\Controllers\Employee\EmployeeFrontendController::class, 'recentTeamMembers'])->middleware('permission.box:attendance.employee.read');
    Route::get('/dashboard/stats', [\App\Http\Controllers\Employee\AttendaceController::class, 'employeeDashboardStats'])->middleware('permission.box:attendance.employee.read');
    Route::get('/dashboard/overview', [\App\Http\Controllers\Employee\AttendaceController::class, 'attendanceOverview'])->middleware('permission.box:attendance.employee.read');

    // Leave APIs
    Route::get('/leaves', [\App\Http\Controllers\Employee\LeaveController::class, 'getEmployeeRequestData'])->middleware('permission.box:attendance.leaves.read|attendance.admin_leaves.read');
    Route::get('/leaves/stats', [\App\Http\Controllers\Employee\LeaveController::class, 'employeeLeaveStats'])->middleware('permission.box:attendance.leaves.read');
    Route::get('/leaves/records', [\App\Http\Controllers\Employee\LeaveController::class, 'getEmployeeRequestData'])->middleware('permission.box:attendance.leaves.read');
    Route::get('/leaves/types', [\App\Http\Controllers\Employee\LeaveTypeController::class, 'LeaveTyperequest'])->middleware('permission.box:attendance.leaves.read');
    Route::get('/leaves/balance', [\App\Http\Controllers\Employee\LeaveController::class, 'GetLeaveBlance'])->middleware('permission.box:attendance.leaves.read');
    Route::post('/leaves/request', [\App\Http\Controllers\Employee\LeaveController::class, 'LeaveRequestStore'])->middleware('permission.box:attendance.leaves.create');
    Route::put('/leaves/request', [\App\Http\Controllers\Employee\LeaveController::class, 'LeaveRequestUpdate'])->middleware('permission.box:attendance.leaves.update');
    Route::delete('/leaves/request', [\App\Http\Controllers\Employee\LeaveController::class, 'LeaveRequestDelete'])->middleware('permission.box:attendance.leaves.delete');
    
    // Admin Leave APIs
    Route::get('/leaves/admin/dashboard', [\App\Http\Controllers\Employee\EmployeeFrontendController::class, 'apiAdminLeaveDashboard'])->middleware('permission.box:attendance.admin_leaves.read');
    Route::get('/leaves/admin/requests', [\App\Http\Controllers\Employee\AttendaceController::class, 'getAdminRequestdata'])->middleware('permission.box:attendance.admin_leaves.read');
    Route::post('/leaves/admin/update-status', [\App\Http\Controllers\Employee\LeaveController::class, 'UpdateAdminRequest'])->middleware('permission.box:attendance.admin_leaves.update');
    
    // Admin Leave Type Management APIs
    Route::get('/leaves/types/data', [\App\Http\Controllers\Employee\LeaveTypeController::class, 'getDataLeaveType'])->middleware('permission.box:attendance.leave_type.read');
    Route::post('/leaves/types', [\App\Http\Controllers\Employee\LeaveTypeController::class, 'leave_type_store'])->middleware('permission.box:attendance.leave_type.create');
    Route::put('/leaves/types', [\App\Http\Controllers\Employee\LeaveTypeController::class, 'leave_type_update'])->middleware('permission.box:attendance.leave_type.update');
    Route::delete('/leaves/types', [\App\Http\Controllers\Employee\LeaveTypeController::class, 'leave_destroy'])->middleware('permission.box:attendance.leave_type.delete');
    Route::post('/leaves/types/toggle-status', [\App\Http\Controllers\Employee\LeaveTypeController::class, 'ToggleLeaveStatus'])->middleware('permission.box:attendance.leave_type.update');
    Route::post('/leaves/credit', [\App\Http\Controllers\Employee\LeaveController::class, 'CreditLeaves'])->middleware('permission.box:attendance.leave_type.create');


    // Leave Transaction APIs
    Route::get('/leave-transaction-all', [\App\Http\Controllers\Employee\LeaveTransactionController::class, 'Leave_dataTransaction'])->middleware('permission.box:attendance.leave_transaction.read');
    Route::get('/calculate-leave-transactions-all', [\App\Http\Controllers\Employee\LeaveTransactionController::class, 'calculate_leave_transactions_all'])->middleware('permission.box:attendance.leave_transaction.update');
    
    // Existing structured ones
    Route::get('/leaves/transactions', [\App\Http\Controllers\Employee\LeaveTransactionController::class, 'Leave_dataTransaction'])->middleware('permission.box:attendance.leave_transaction.read');
    Route::post('/leaves/transactions', [\App\Http\Controllers\Employee\LeaveTransactionController::class, 'store_leave_transaction'])->middleware('permission.box:attendance.leave_transaction.create');
    Route::get('/leaves/transactions/calculate-all', [\App\Http\Controllers\Employee\LeaveTransactionController::class, 'calculate_leave_transactions_all'])->middleware('permission.box:attendance.leave_transaction.update');
    Route::get('/leaves/transactions/calculate-team-all', [\App\Http\Controllers\Employee\LeaveTransactionController::class, 'calculate_team_leave_transactions_all'])->middleware('permission.box:attendance.leave_transaction.update');

    // Employee List APIs (Dropdown helpers)
    Route::get('/employees/list', [\App\Http\Controllers\Employee\EmployeManageController::class, 'getBoxUsers'])->middleware('permission.box:user_management.employee_list.read');

    // Holiday APIs
    Route::get('/holidays', [\App\Http\Controllers\Employee\HolidayController::class, 'getHolidayData'])->middleware('permission.box:attendance.holiday.read');
    Route::post('/holidays', [\App\Http\Controllers\Employee\HolidayController::class, 'holiday_post'])->middleware('permission.box:attendance.holiday.create');
    Route::put('/holidays', [\App\Http\Controllers\Employee\HolidayController::class, 'holiday_update'])->middleware('permission.box:attendance.holiday.update');
    Route::delete('/holidays', [\App\Http\Controllers\Employee\HolidayController::class, 'holiday_delete'])->middleware('permission.box:attendance.holiday.delete');
    Route::get('/holidays/regions', [\App\Http\Controllers\Employee\HolidayController::class, 'GetAllRegional'])->middleware('permission.box:attendance.holiday.read');


    
    Route::get('employee-dashboard-team-members', [\App\Http\Controllers\Employee\EmployeeFrontendController::class, 'recentTeamMembers'])->middleware('permission.box:attendance.employee.read');
    Route::get('employee-leave-states', [\App\Http\Controllers\Employee\LeaveController::class, 'employeeLeaveStats'])->middleware('permission.box:attendance.leaves.read');
    Route::get('employee-dashboard-stats', [\App\Http\Controllers\Employee\AttendaceController::class, 'employeeDashboardStats'])->middleware('permission.box:attendance.employee.read');
    Route::get('check-employee-attendace', [\App\Http\Controllers\Employee\AttendaceController::class, 'check_attendance'])->middleware('permission.box:attendance.employee.read');
    Route::get('check-attendance-summary', [\App\Http\Controllers\Employee\AttendaceController::class, 'checkattendace_summary'])->middleware('permission.box:attendance.employee.read');
    Route::post('mark-attendace', [\App\Http\Controllers\Employee\AttendaceController::class, 'mark_attendace'])->middleware('permission.box:attendance.employee.create');

    // Holiday Type APIs
    Route::get('/holidays/types', [\App\Http\Controllers\Employee\HolidayTypeController::class, 'getHolidayTypes'])->middleware('permission.box:attendance.holiday_type.read');
    Route::post('/holidays/types', [\App\Http\Controllers\Employee\HolidayTypeController::class, 'holidayStore'])->middleware('permission.box:attendance.holiday_type.create');
    Route::put('/holidays/types', [\App\Http\Controllers\Employee\HolidayTypeController::class, 'holidayUpdate'])->middleware('permission.box:attendance.holiday_type.update');
    Route::delete('/holidays/types', [\App\Http\Controllers\Employee\HolidayTypeController::class, 'deleteHoliday'])->middleware('permission.box:attendance.holiday_type.delete');
    Route::get('/holidays/types/prefill', [\App\Http\Controllers\Employee\HolidayTypeController::class, 'HolidayPrefill'])->middleware('permission.box:attendance.holiday_type.read');

    // Shift APIs
    Route::get('/shifts/details', [\App\Http\Controllers\Employee\ShiftController::class, 'ShiftDetails'])->middleware('permission.box:attendance.shift.read');
    Route::post('/shifts', [\App\Http\Controllers\Employee\ShiftController::class, 'store_shift'])->middleware('permission.box:attendance.shift.create');
    Route::put('/shifts', [\App\Http\Controllers\Employee\ShiftController::class, 'updateShift'])->middleware('permission.box:attendance.shift.update');
    Route::delete('/shifts', [\App\Http\Controllers\Employee\ShiftController::class, 'deleteShift'])->middleware('permission.box:attendance.shift.delete');
    Route::post('/shifts/generate', [\App\Http\Controllers\Employee\ShiftController::class, 'generate_shift_for_all_employees'])->middleware('permission.box:attendance.shift.generate');
    // Legacy aliases requested by user
    Route::get('employee-dashboard-team-members', [\App\Http\Controllers\Employee\EmployeeFrontendController::class, 'recentTeamMembers'])->middleware('permission.box:attendance.employee.read');
    Route::get('employee-leave-states', [\App\Http\Controllers\Employee\LeaveController::class, 'employeeLeaveStats'])->middleware('permission.box:attendance.leaves.read');
    Route::get('employee-dashboard-stats', [\App\Http\Controllers\Employee\AttendaceController::class, 'employeeDashboardStats'])->middleware('permission.box:attendance.employee.read');
    Route::get('check-employee-attendace', [\App\Http\Controllers\Employee\AttendaceController::class, 'check_attendance'])->middleware('permission.box:attendance.employee.read');
    Route::get('check-attendance-summary', [\App\Http\Controllers\Employee\AttendaceController::class, 'checkattendace_summary'])->middleware('permission.box:attendance.employee.read');
    Route::post('mark-attendace', [\App\Http\Controllers\Employee\AttendaceController::class, 'mark_attendace'])->middleware('permission.box:attendance.employee.create');

    // Account Deletion User Flow APIs (Only Admin can request)
    Route::prefix('account/delete')->middleware(['check.roles.box:admin'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\AccountDeletionController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\AccountDeletionController::class, 'submit']);
        Route::post('/otp/verify', [\App\Http\Controllers\Api\AccountDeletionController::class, 'verifyOtp']);
        Route::post('/otp/resend', [\App\Http\Controllers\Api\AccountDeletionController::class, 'resendOtp']);
        Route::post('/cancel', [\App\Http\Controllers\Api\AccountDeletionController::class, 'cancel']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::get('/finance/dashboard', [\App\Http\Controllers\Api\FinanceApiController::class, 'dashboard']);
    // Finance Settings APIs
    Route::prefix('finance/settings')->group(function () {
        // Allowance Types
        Route::get('allowance-types', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'getAllowanceTypes']);
        Route::post('allowance-types', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'storeAllowanceType']);
        Route::put('allowance-types/{id}', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'updateAllowanceType']);
        Route::delete('allowance-types/{id}', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'deleteAllowanceType']);
        Route::post('allowance-types/{id}/status', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'toggleAllowanceTypeStatus']);

        // Expense Categories
        Route::get('expense-categories', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'getExpenseCategories']);
        Route::post('expense-categories', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'storeExpenseCategory']);
        Route::put('expense-categories/{id}', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'updateExpenseCategory']);
        Route::delete('expense-categories/{id}', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'deleteExpenseCategory']);
        Route::post('expense-categories/{id}/status', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'toggleExpenseCategoryStatus']);

        // Approval Rules
        Route::get('approval-rules', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'getApprovalRules']);
        Route::post('approval-rules', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'storeApprovalRule']);
        Route::put('approval-rules/{id}', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'updateApprovalRule']);
        Route::delete('approval-rules/{id}', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'deleteApprovalRule']);
        Route::post('approval-rules/{id}/status', [\App\Http\Controllers\Api\FinanceSettingsApiController::class, 'toggleApprovalRuleStatus']);
    });

    // Finance Expenses APIs
    Route::prefix('finance/expenses')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\FinanceExpenseApiController::class, 'getExpenses']);
        Route::post('/', [\App\Http\Controllers\Api\FinanceExpenseApiController::class, 'storeExpense']);
        Route::put('/{id}', [\App\Http\Controllers\Api\FinanceExpenseApiController::class, 'updateExpense']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\FinanceExpenseApiController::class, 'deleteExpense']);
    });

    // Finance Approvals APIs
    Route::prefix('finance/approvals')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\FinanceApprovalApiController::class, 'getApprovals']);
        Route::post('/{id}', [\App\Http\Controllers\Api\FinanceApprovalApiController::class, 'storeApproval']);
    });

    // Finance Reimbursements APIs
    Route::prefix('finance/reimbursements')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\FinanceReimbursementApiController::class, 'getReimbursements']);
        Route::post('/', [\App\Http\Controllers\Api\FinanceReimbursementApiController::class, 'storeReimbursement']);
    });

});
