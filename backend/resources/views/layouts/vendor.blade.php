<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Streamline your business with our advanced CRM template. Easily integrate and customize to manage sales, support, and customer interactions efficiently. Perfect for any business size">
    <meta name="keywords" content="CRM, sales, customer management">
    <meta name="author" content="{{ config("app.name") }}">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $appName = config('app.name', 'ZipZapGo');
        $pageTitle = trim($__env->yieldContent('title'));
        $fullTitle = $pageTitle ? $pageTitle . ' | ' . $appName : $appName;
    @endphp
    <title>{{ $fullTitle }}</title>
    <!-- Expose Auth User ID for WebSockets -->
    <script>
        window.authUserId = {{ auth()->id() ?? 'null' }};
    </script>
    <!-- Load Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.orgchart.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-lite.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">
    <!-- my custom scroll -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.mCustomScrollbar.css') }}">
    <!-- Optional Head Script -->
    <script src="{{ asset('assets/js/theme-script.js') }}"></script>
    <style>
        .footer {
            margin-left: 276px;
            transition: all 0.5s ease;
        }

        .mini-sidebar .footer {
            margin-left: 80px;
        }

        @media (max-width: 991.98px) {
            .footer {
                margin-left: 0;
            }
        }
    </style>
    @stack('css')
</head>

<body>
    <!-- Synchronous sidebar layout check to eliminate FOUC (sidebar jump) -->
    <script>
        (function() {
            try {
                var layout = localStorage.getItem('layout') || 'default';
                var size = localStorage.getItem('size') || 'default';
                var width = localStorage.getItem('width') || 'fluid';
                
                var layout_mini = 0;
                if (layout === 'mini') {
                    document.body.classList.add("mini-sidebar");
                    layout_mini = 1;
                } else if (layout === 'horizontal' || layout === 'horizontal-single' || layout === 'horizontal-overlay') {
                    document.body.classList.add("menu-horizontal");
                }
                
                if (size === 'compact') {
                    document.body.classList.add("mini-sidebar");
                    layout_mini = 1;
                } else if (size === 'hoverview') {
                    document.body.classList.add("expand-menu");
                }
                
                if (width === 'box') {
                    document.body.classList.add("layout-box-mode");
                    if (layout !== 'horizontal' && layout !== 'horizontal-overlay' && layout !== 'horizontal-single' && layout !== 'without-header') {
                        document.body.classList.add("mini-sidebar");
                    }
                }
            } catch (e) {
                console.error("FOUC prevention error:", e);
            }
        })();
    </script>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        {{-- <div class="preloader">
            <span class="loader"></span>
        </div> --}}

        @include('layouts.vendor_header')
        @include('layouts.vendor_sidebar')

        <!-- Page Content -->
        @yield('content')
        @include('layouts.footer')
    </div>



    <!-- Scripts -->


    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script> -->
    
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.ui.touch-punch.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/plugins/summernote/summernote-bs4.min.js') }}"></script> --}}
    <script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
    <script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote-lite.min.js') }}"></script>


    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>

    <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
	<script src="{{ asset('assets/plugins/chartjs/chart-data.js') }}"></script>


    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/jsonscript.js') }}"></script>
    <script src="{{ asset('assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.orgchart.js') }}"></script>

    
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <!--<script>window.jQuery || document.write('<script src="{{ asset('assets/js/jquery-1.11.0.min.js') }}"><\/script>')</script>
	
	<script src="{{ asset('assets/js/jquery.mCustomScrollbar.js') }}"></script>

    <script>
		(function($){
			$(window).on("load",function(){
				
				$(".bizzscroll").mCustomScrollbar({
				    autoHideScrollbar:true,
					theme:"minimal"
					
				});
				
			});
		})(jQuery);
	</script> -->


 <script>

        flatpickr(".MyDatePicker", {
            dateFormat: "m/d/Y",
            allowInput: false,
        });


       
        flatpickr(".MyDateRangePicker", {
            dateFormat: "m/d/Y",
			mode: "range",
            allowInput: false,    
        });


        flatpickr(".MyTimePicker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false,
            allowInput: false,
            minuteIncrement: 1,
        });

        flatpickr(".MyDurationPicker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            allowInput: false,
            minuteIncrement: 1,
        });

        flatpickr(".MyDateTimePicker", {
            enableTime: true,
            dateFormat: "m/d/Y h:i K",
            time_24hr: false,
            allowInput: false,
            minuteIncrement: 1,
        });


		flatpickr(".MyMonthYearPicker", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "m/Y",
                    altFormat: "F Y",
					theme: "dark"
                })
            ],
            allowInput: false,
            clickOpens: true,
            onOpen: function (selectedDates, dateStr, instance) {
                instance.input.blur();
            }
        });

    </script>
    

<script>
$(document).on('click', 'li.user-list-item', function () {
  $('#chat-window-main').addClass('active-cht');
});

$(document).on('click', 'a.back-chat-list', function () {
  $('#chat-window-main').removeClass('active-cht');
});
 </script>

<!--<script>
if (window.location.href.includes('whatsapp')) {
    document.body.classList.add('w-chat-page');
}
</script> -->



<script>
const path = window.location.pathname.replace(/\/$/, '');

if (path.endsWith('/whatsapp')) {
    document.body.classList.add('w-chat-page');
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const path = window.location.pathname
        .split('/')
        .filter(Boolean);

    const lastSlug = path[path.length - 1];

    if (lastSlug) {
        document.body.classList.add(`page-${lastSlug}`);
    }
});
</script>


<!-- <script>
$(document).ready(function () {
    
    var $menu = $('.sidebar-inner'); 

    $menu.on('click', 'a', function () {
        // Find the actual scroll position of the slimScroll container
        var currentScroll = $menu.scrollTop();
        localStorage.setItem('slimScrollPos', currentScroll);
    });
});
</script> -->

<!--<script>
$(document).ready(function () {
    
    var savedPos = localStorage.getItem('slimScrollPos') || 0;

    
    $('.sidebar-inner').slimScroll({
        height: '100%', 
        railVisible: true,
        alwaysVisible: true,
       
        scrollTo: savedPos + 'px' 
    });
});
</script> -->



<script>
$(document).ready(function () {
    var $menu = $('.sidebar-inner'); // Your menu selector
    var $activeItem = $menu.find('.active'); // The active/clicked item
    var scrollToPosition = '0px'; // Default to top if no active item

    if ($activeItem.length) {
        // Calculate the position of the active item relative to the top of the menu container
        var itemOffset = $activeItem.position().top;
        var menuScrollTop = $menu.scrollTop();
        
        // This gives you the exact pixel position of the item inside the scrollable area
        scrollToPosition = (itemOffset + menuScrollTop) + 'px';
    }

    // Initialize SlimScroll and jump straight to the active item
    $menu.slimScroll({
        height: '100%', // Your existing configurations
        alwaysVisible: true,
        scrollTo: scrollToPosition // Forces the active item into view
    });
});
</script>






<!-- old script -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr(".datePicker", {
                dateFormat: "m/d/Y",
                allowInput: false
            });
            flatpickr("#timePicker", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                allowInput: false
            });

            flatpickr("#dateTimePicker", {
                enableTime: true,
                dateFormat: "m/d/Y h:i K",
                allowInput: false
            });

            flatpickr("#monthYearPicker", {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "m/Y",
                        altFormat: "F Y",
                        theme: "light"
                    })
                ],
                allowInput: false,
                clickOpens: true,
                onOpen: function (selectedDates, dateStr, instance) {
                    instance.input.blur();
                }
            });
        });
    </script>








    @php
        use App\Helpers\PermissionHelper;
        $userPermissions = PermissionHelper::getUserPermissionsForJs();
        $routePermissionMap = PermissionHelper::getRoutePermissionMap();
        // Check if user is privileged (superadmin, user id == 1, or admin)
        $user = Auth::user();
        $isAdmin = $user && ($user->hasRole('superadmin') || $user->id == 1 || $user->hasRole('admin', 'web'));

        // Debug: Log admin status
        \Log::info('Admin check', ['isAdmin' => $isAdmin, 'user_id' => $user?->id, 'roles' => $user?->getRoleNames()]);
    @endphp

    <script>
        // Make permissions available globally
        window.userPermissions = @json($userPermissions);
        window.routePermissionMap = @json($routePermissionMap);
        window.isAdmin = @json($isAdmin);

        // Helper function to check if user has permission
        window.hasPermission = function(permission) {
            // Admin check
            if (window.isAdmin === true) {
                return true;
            }

            if (!window.userPermissions || window.userPermissions.length === 0) {
                return false;
            }

            // Check for exact permission match
            if (window.userPermissions.includes(permission)) {
                return true;
            }

            // Check for "all.permissions" master permission
            if (window.userPermissions.includes('all.permissions')) {
                return true;
            }

            return false;
        };

        // Helper function to check if user can access a route
        window.canAccessRoute = function(routeName) {
            if (!window.routePermissionMap || !window.routePermissionMap[routeName]) {
                return true; // If no mapping, allow access for backward compatibility
            }
            var permission = window.routePermissionMap[routeName];

            // Check if it's a sub-module permission (module.sub_module.action)
            var parts = permission.split('.');
            if (parts.length === 3) {
                // Sub-module permission
                var module = parts[0];
                var subModule = parts[1];
                var action = parts[2];
                return window.hasSubModulePermission(module, subModule, action) || window.hasModulePermission(module, action);
            } else if (parts.length === 2) {
                // Module-level permission
                return window.hasPermission(permission);
            }

            return window.hasPermission(permission);
        };

        // Helper function to check module permission (checks both module-level and sub-module permissions)
        window.hasModulePermission = function(module, action) {
            if (!window.userPermissions || window.userPermissions.length === 0) {
                return false;
            }

            // Check for module-level permission (e.g., "attendance.read")
            var modulePermission = module + '.' + action;
            if (window.hasPermission(modulePermission)) {
                return true;
            }

            // Check for any sub-module permission with this action (e.g., "attendance.admin.read", "attendance.employee.read", etc.)
            for (var i = 0; i < window.userPermissions.length; i++) {
                var perm = window.userPermissions[i];
                // Check if permission starts with module and ends with action
                if (perm.startsWith(module + '.') && perm.endsWith('.' + action)) {
                    return true;
                }
            }

            return false;
        };

        // Helper function to check sub-module permission
        window.hasSubModulePermission = function(module, subModule, action) {
            if (!window.userPermissions || window.userPermissions.length === 0) {
                return false;
            }

            var subModulePermission = module + '.' + subModule + '.' + action;
            return window.hasPermission(subModulePermission);
        };
    </script>

    {{-- <script src="{{ asset('assets/js/sidebar-permissions.js') }}"></script> --}}

    <!-- Global Session Expiration Handler -->
    <script>
        (function() {
            'use strict';

            const loginRoute = "{{ route('login') }}";
            const currentPath = window.location.pathname;

            // Don't run on login page
            if (currentPath === loginRoute || currentPath.includes('/login')) {
                return;
            }

            // Toast container for session expiration messages
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 start-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }

            function showSessionExpiredToast(message) {
                try {
                    const toastId = 'session-expired-toast-' + Date.now();
                    const toastHtml = `
                        <div id="${toastId}" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">${message}</div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    `;
                    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                    const toastElement = document.getElementById(toastId);
                    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                        const toast = new bootstrap.Toast(toastElement);
                        toast.show();
                    }
                } catch (error) {
                    console.error('Error showing toast:', error);
                }
            }

            function handleSessionExpired() {
                try {
                    console.warn('Session expired detected. Redirecting to login...');
                    showSessionExpiredToast('Your session has expired. Redirecting to login page...');

                    setTimeout(function() {
                        // Clear any cached data
                        if (typeof sessionStorage !== 'undefined') {
                            sessionStorage.clear();
                        }
                        if (typeof localStorage !== 'undefined') {
                            localStorage.removeItem('auth_token');
                            localStorage.removeItem('user_data');
                        }

                        // Redirect to login
                        window.location.href = loginRoute;
                    }, 2000);
                } catch (error) {
                    console.error('Error handling session expiration:', error);
                    window.location.href = loginRoute;
                }
            }

            function handleCsrfMismatch() {
                try {
                    console.warn('CSRF token mismatch detected. Reloading page...');
                    showSessionExpiredToast('Session expired. Reloading page...');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } catch (error) {
                    console.error('Error handling CSRF mismatch:', error);
                    window.location.reload();
                }
            }

            function showSoftLogoutModal(message) {
                if (message) {
                    $('#softLogoutModalText').text(message);
                }
                
                // Unbind previous click events to prevent multiple triggers
                $('#btnSoftLogoutKeep').off('click').on('click', function() {
                    // Disable buttons while processing
                    $(this).prop('disabled', true);
                    $('#btnSoftLogout').prop('disabled', true);
                    
                    $.ajax({
                        url: '/cancel-api-logout',
                        method: 'POST',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        global: false,
                        complete: function() {
                            $('#softLogoutModal').modal('hide');
                            // Re-enable
                            $('#btnSoftLogoutKeep').prop('disabled', false);
                            $('#btnSoftLogout').prop('disabled', false);
                        }
                    });
                });

                $('#btnSoftLogout').off('click').on('click', function() {
                    $(this).prop('disabled', true);
                    $('#btnSoftLogoutKeep').prop('disabled', true);
                    $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging out...');
                    
                    $.ajax({
                        url: '/logout',
                        method: 'POST',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        global: false,
                        complete: function() {
                            window.location.href = loginRoute || '/login';
                        }
                    });
                });

                $('#softLogoutModal').modal('show');
            }

            // Intercept all AJAX requests
            if (typeof jQuery !== 'undefined') {
                // Setup AJAX defaults
                $.ajaxSetup({
                    statusCode: {
                        401: function(xhr) {
                            try {
                                const response = xhr.responseJSON;
                                const reason = (response && response.reason) ? response.reason : xhr.getResponseHeader('X-Logout-Reason');
                                if (response && response.redirect) {
                                    window.location.href = response.redirect;
                                } else if (reason === 'confirm_api_logout') {
                                    showSoftLogoutModal((response && response.message) ? response.message : null);
                                } else {
                                    handleSessionExpired();
                                }
                            } catch (error) {
                                handleSessionExpired();
                            }
                        },
                        403: function(xhr) {
                            try {
                                const response = xhr.responseJSON;
                                // Ignore redirect for 403, just show message
                                const msg = (response && response.message) ? response.message : 'Access Denied';
                                showSessionExpiredToast(msg);
                            } catch (error) {
                                showSessionExpiredToast('Access Denied');
                            }
                        },
                        404: function(xhr) {
                            try {
                                const response = xhr.responseJSON;
                                if (response && response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    const msg = (response && response.message) ? response.message : 'Resource Not Found';
                                    showSessionExpiredToast(msg);
                                }
                            } catch (error) {
                                showSessionExpiredToast('Resource Not Found');
                            }
                        },
                        419: function() {
                            handleCsrfMismatch();
                        }
                    }
                });

                // Intercept AJAX errors globally
                $(document).ajaxError(function(event, xhr, settings, thrownError) {
                    try {
                        if (xhr.status === 401) {
                            const response = xhr.responseJSON;
                            const reason = (response && response.reason) ? response.reason : xhr.getResponseHeader('X-Logout-Reason');
                            
                            if (response && response.redirect) {
                                window.location.href = response.redirect;
                            } else if (reason === 'confirm_api_logout') {
                                showSoftLogoutModal((response && response.message) ? response.message : null);
                            } else {
                                handleSessionExpired();
                            }
                        } else if (xhr.status === 403) {
                             // Handle 403: Show message ONLY, do not redirect
                            const response = xhr.responseJSON;
                            const msg = (response && response.message) ? response.message : 'Access Denied';
                            showSessionExpiredToast(msg);
                        } else if (xhr.status === 404) {
                             // Handle 404: Redirect if provided, else show message
                            const response = xhr.responseJSON;
                            if (response && response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                const msg = (response && response.message) ? response.message : 'Resource Not Found';
                                showSessionExpiredToast(msg);
                            }
                        } else if (xhr.status === 419) {
                            handleCsrfMismatch();
                        }
                    } catch (error) {
                        console.error('Error in global AJAX error handler:', error);
                        if (xhr.status === 401) {
                            handleSessionExpired();
                        }
                    }
                });
            }

            // Handle browser back/forward button navigation
            window.addEventListener('pageshow', function(event) {
                try {
                    // Check if page was loaded from cache (back/forward button)
                    if (event.persisted) {
                        // Try to verify session is still valid
                        if (typeof jQuery !== 'undefined') {
                            $.ajax({
                                url: "{{ route('login') }}",
                                method: 'GET',
                                cache: false,
                                success: function(response) {
                                    // If we can access login page, session might be expired
                                    // Check if we're on a protected page
                                    const protectedPaths = ['/dashboard', '/admin', '/superadmin', '/employee'];
                                    const isProtectedPage = protectedPaths.some(path => currentPath.includes(path));

                                    if (isProtectedPage) {
                                        // Try to make a test request to verify session
                                        $.ajax({
                                            url: window.location.href,
                                            method: 'GET',
                                            cache: false,
                                            error: function(xhr) {
                                                if (xhr.status === 401 || xhr.status === 403) {
                                                    handleSessionExpired();
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error in pageshow handler:', error);
                }
            });

            // Check session on page load
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    // Verify session is still valid by checking if we can access current page
                    if (typeof jQuery !== 'undefined' && typeof fetch !== 'undefined') {
                        // Use fetch to check session without triggering AJAX handlers
                        fetch(window.location.href, {
                            method: 'HEAD',
                            cache: 'no-cache',
                            credentials: 'same-origin'
                        }).catch(function(error) {
                            console.warn('Session check failed:', error);
                        });
                    }
                } catch (error) {
                    console.error('Error in DOMContentLoaded session check:', error);
                }
            });

            // Track last session check time to avoid excessive requests
            let lastSessionCheckTime = Date.now();
            const SESSION_CHECK_INTERVAL = 10 * 60 * 1000; // 10 minutes

            // Handle visibility change (user switches tabs and comes back)
            document.addEventListener('visibilitychange', function() {
                try {
                    const currentTime = Date.now();
                    
                    if (!document.hidden && (currentTime - lastSessionCheckTime >= SESSION_CHECK_INTERVAL)) {
                        // Page became visible and interval passed, check session
                        if (typeof jQuery !== 'undefined') {
                            $.ajax({
                                url: window.location.href,
                                method: 'HEAD',
                                cache: false,
                                success: function() {
                                    // Update timestamp on successful check
                                    lastSessionCheckTime = Date.now();
                                },
                                error: function(xhr) {
                                    // Update timestamp even on error to prevent spamming
                                    lastSessionCheckTime = Date.now();
                                    
                                    const reason = xhr.getResponseHeader('X-Logout-Reason');
                                    if (reason === 'confirm_api_logout') {
                                        showSoftLogoutModal('You have logged out from another device. What would you like to do?');
                                    } else if (xhr.status === 401 || xhr.status === 403) {
                                        handleSessionExpired();
                                    }
                                }
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error in visibility change handler:', error);
                }
            });

            // Check session on user interaction (clicks), max once per minute
            let lastClickCheckTime = Date.now();
            document.addEventListener('click', function() {
                const currentTime = Date.now();
                if (currentTime - lastClickCheckTime >= 60000) {
                    if (typeof jQuery !== 'undefined' && !document.hidden) {
                        $.ajax({
                            url: window.location.href,
                            method: 'HEAD',
                            cache: false,
                            global: true, // this ensures it triggers the global ajaxError handler
                            success: function() {
                                lastClickCheckTime = Date.now();
                            },
                            error: function() {
                                lastClickCheckTime = Date.now();
                            }
                        });
                    }
                }
            });

        })();
    </script>

    @stack('modals')
    @stack('scripts')

    @if(session('confirm_api_logout'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof showSoftLogoutModal !== 'undefined') {
                showSoftLogoutModal('You have logged out from another device. What would you like to do?');
            }
        });
    </script>
    @endif

    <!-- Soft Logout Confirmation Modal -->
    <div class="modal fade" id="softLogoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="softLogoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title" id="softLogoutModalLabel">
                        <i class="mdi mdi-alert-circle-outline me-1"></i> Session Update
                    </h5>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="fs-5 mb-0" id="softLogoutModalText">You have logged out from another device. What would you like to do?</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-secondary px-4" id="btnSoftLogout">Log Out</button>
                    <button type="button" class="btn btn-primary px-4" id="btnSoftLogoutKeep">Keep Me Logged In</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="dynamicToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="dynamicToastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        window.showToast = function(message, type = 'success') {
            const toastEl = document.getElementById('dynamicToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                const bgClass = type === 'success' ? 'bg-success' : (type === 'warning' ? 'bg-warning' : 'bg-danger');
                toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
                toastEl.classList.add(bgClass);
                document.getElementById('dynamicToastMessage').innerText = message;
                toast.show();
            }
            
            // Call original toastr if it exists and is NOT our shim
            if (typeof toastr !== 'undefined' && !toastr.isShim) {
                try {
                    if (type === 'success') toastr.success(message);
                    else if (type === 'warning') toastr.warning(message);
                    else if (type === 'danger' || type === 'error') toastr.error(message);
                    else toastr.info(message);
                } catch(e) { console.error('Toastr error:', e); }
            }
        };

        // Global shim to prevent "toastr is not defined" errors
        // Use a flag to avoid infinite loops with showToast
        if (typeof toastr === 'undefined') {
            window.toastr = {
                isShim: true,
                success: (m) => showToast(m, 'success'),
                error: (m) => showToast(m, 'danger'),
                warning: (m) => showToast(m, 'warning'),
                info: (m) => showToast(m, 'info'),
                options: {}
            };
        }
    </script>
    @yield('scripts')
</body>

</html>
