/**
 * Sidebar Permission Filter
 * This script filters sidebar menu items based on user permissions
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Function to check if user has permission for a route
        function canAccessRoute(routeName) {
            if (typeof window.canAccessRoute === 'function') {
                return window.canAccessRoute(routeName);
            }
            return true; // Default to true if function doesn't exist
        }

        // Function to check if user has module permission
        function hasModulePermission(module, action) {
            if (typeof window.hasModulePermission === 'function') {
                return window.hasModulePermission(module, action);
            }
            return true; // Default to true if function doesn't exist
        }

        // Extract route name from href
        function getRouteFromHref(href) {
            if (!href || href === 'javascript:void(0)' || href === '#') {
                return null;
            }
            
            // Try to extract route name from data attribute first
            var $link = $(href);
            if ($link.length && $link.data('route')) {
                return $link.data('route');
            }
            
            // Extract from href - look for route name patterns
            // This is a fallback - ideally routes should have data-route attributes
            var match = href.match(/route[=:]([^&]+)/);
            if (match) {
                return match[1];
            }
            
            return null;
        }

        // Hide menu items based on permissions
        // NOTE: This function is currently disabled to trust Blade's rendering
        // Blade template handles all permission-based visibility from backend
        function filterSidebarMenu() {
            // If user is admin, don't filter anything
            if (window.isAdmin === true) {
                return; // Admins see everything, no filtering needed
            }
            
            // IMPORTANT: Blade template already handles ALL permission-based visibility
            // JavaScript should NOT override Blade's decisions
            // Don't run any filtering - trust Blade's rendering completely
            return;
            
            // Code below is disabled - Blade handles all permission checks
            /*
            // Map of route names to their permission requirements
            var routePermissionMap = window.routePermissionMap || {};
            
            // Process all sidebar links
            $('#sidebar-menu a[href]').each(function() {
                var $link = $(this);
                var href = $link.attr('href');
                
                // Skip if it's a javascript link or dropdown toggle
                if (!href || href === 'javascript:void(0)' || href === '#' || $link.hasClass('subdrop')) {
                    return;
                }
                
                // Try to get route name from data attribute
                var routeName = $link.data('route');
                
                // If no data-route, try to extract from href
                if (!routeName) {
                    // Extract route name from box_route helper output
                    // Pattern: /box-slug/route-name or similar
                    var routeMatch = href.match(/\/([a-z0-9\-]+)\/([a-z0-9\.\-]+)/);
                    if (routeMatch && routeMatch.length > 2) {
                        // Try to match common route patterns
                        var possibleRoute = routeMatch[2].replace(/\//g, '.');
                        if (routePermissionMap[possibleRoute]) {
                            routeName = possibleRoute;
                        }
                    }
                }
                
                // Check permission if route is found
                if (routeName && routePermissionMap[routeName]) {
                    if (!canAccessRoute(routeName)) {
                        $link.closest('li').hide();
                    } else {
                        $link.closest('li').show();
                    }
                }
            });
            
            // Hide parent menu items if all children are hidden
            $('#sidebar-menu .submenu').each(function() {
                var $submenu = $(this);
                var $children = $submenu.find('ul > li');
                var visibleChildren = $children.filter(':visible').length;
                
                if (visibleChildren === 0 && $children.length > 0) {
                    $submenu.closest('li').hide();
                } else {
                    $submenu.closest('li').show();
                }
            });
            
            // Hide empty submenu headers
            $('#sidebar-menu .submenu-hdr').each(function() {
                var $header = $(this);
                var $nextSibling = $header.next('ul');
                if ($nextSibling.length > 0) {
                    var visibleItems = $nextSibling.find('li:visible').length;
                    if (visibleItems === 0) {
                        $header.hide();
                    } else {
                        $header.show();
                    }
                }
            });
        }

        // Check permissions dynamically based on actual user permissions
        function filterByModulePermissions() {
            // If user is admin, show all items - don't filter anything
            if (window.isAdmin === true) {
                $('#sidebar-menu a[data-route]').closest('li').show();
                $('#sidebar-menu .submenu').closest('li').show();
                return; // Admins see everything, no filtering needed
            }
            
            // IMPORTANT: Blade template already handles showing/hiding main menu sections
            // JavaScript should NOT hide items that Blade has already shown
            // Only filter individual sub-items if needed, but respect Blade's decisions
            
            if (!window.userPermissions || window.userPermissions.length === 0) {
                // If no permissions and not admin, but Blade might have shown items
                // Don't hide items that Blade template has already rendered as visible
                // Only hide items that are definitely not permitted
                return;
            }
            
            // Map of route patterns to check permissions for
            var routePermissionChecks = {
                // Attendance routes
                'employee.attendace.admin': { module: 'attendance', subModule: 'admin', action: 'read' },
                'employee.biometric.machine': { module: 'attendance', subModule: 'biometric', action: 'read' },
                'employee.attendace': { module: 'attendance', subModule: 'employee', action: 'read' },
                'employee.dashboard': { module: 'attendance', subModule: 'employee', action: 'read' },
                'employee.attendance.calendar': { module: 'attendance', subModule: 'calendar', action: 'read' },
                'employee.list': { module: 'attendance', subModule: 'admin', action: 'read' },
                'employee.grid': { module: 'attendance', subModule: 'admin', action: 'read' },
                'employee.details': { module: 'attendance', subModule: 'admin', action: 'read' },
                'employee.leaves': { module: 'attendance', subModule: 'leave', action: 'read' },
                'employee.leave.admin': { module: 'attendance', subModule: 'leave', action: 'read' },
                'employee.leave_settings': { module: 'attendance', subModule: 'leave', action: 'update' },
                'employee.leads_type': { module: 'attendance', subModule: 'leave', action: 'read' },
                'employee.leave_transaction': { module: 'attendance', subModule: 'leave', action: 'read' },
                'employee.shift': { module: 'attendance', subModule: 'shift', action: 'read' },
                'employee.holiday_type': { module: 'attendance', subModule: 'holiday', action: 'read' },
                'employee.holiday.page': { module: 'attendance', subModule: 'holiday', action: 'read' },
                
                // HR Tools routes
                'hr.employess.assets': { module: 'hr_tools', subModule: 'assets', action: 'read' },
                'hr.employess.hr.documents': { module: 'hr_tools', subModule: 'documents', action: 'read' },
                'hr.employeess.letter.generation': { module: 'hr_tools', subModule: 'letter_generation', action: 'read' },
                
                // Payroll routes
                'payroll.employee.salary': { module: 'payroll', subModule: 'salary', action: 'read' },
                'payroll.employee.report': { module: 'payroll', subModule: 'report', action: 'read' },
                'payroll.salary.slip': { module: 'payroll', subModule: 'slip', action: 'read' },
                'payroll.payroll.items': { module: 'payroll', subModule: 'items', action: 'read' },
                'payroll.setting': { module: 'payroll', subModule: 'salary', action: 'update' },
                'payroll.loan.settings': { module: 'payroll', subModule: 'loan_settings', action: 'update' },
                'payroll.loan.tracker': { module: 'payroll', subModule: 'loan_tracker', action: 'read' },
                
                // Leads routes
                'leads.details': { module: 'leads', subModule: 'details', action: 'read' },
                
                // Integrations routes
                'settings.integrations.integrations': { module: 'integrations', subModule: 'main', action: 'read' },
                'settings.integrations.facebook.form': { module: 'integrations', subModule: 'facebook_forms', action: 'read' },
                'settings.integrations.compaign.settings': { module: 'integrations', subModule: 'campaign_settings', action: 'update' },
                'settings.integrations.compaign.details': { module: 'integrations', subModule: 'campaign_details', action: 'read' },
                'settings.integrations.create.project': { module: 'integrations', subModule: 'project_create', action: 'create' },
                'settings.integrations.labels': { module: 'integrations', subModule: 'labels', action: 'read' },
                'settings.integrations.bulk.uploads': { module: 'integrations', subModule: 'bulk_upload', action: 'create' },
                
                // General Settings routes
                'settings.general.companies': { module: 'general_settings', subModule: 'companies', action: 'read' },
                'settings.general.packages': { module: 'general_settings', subModule: 'packages', action: 'read' },
                'settings.general.sources': { module: 'general_settings', subModule: 'sources', action: 'read' },
                'settings.general.lead_status': { module: 'general_settings', subModule: 'lead_status', action: 'read' },
                
                // Website Settings routes
                'website.settings.company': { module: 'website_settings', subModule: 'company', action: 'update' },
                'website.settings.branch': { module: 'website_settings', subModule: 'branch', action: 'read' },
                'website.settings.attendance.module': { module: 'website_settings', subModule: 'attendance_module', action: 'update' },
                'website.settings.work_mode': { module: 'website_settings', subModule: 'work_mode', action: 'read' },
                'website.settings.localization': { module: 'website_settings', subModule: 'localization', action: 'update' },
                
                // App Settings routes
                'app.settings.invoice': { module: 'app_settings', subModule: 'invoice', action: 'update' },
                'app.settings.printers': { module: 'app_settings', subModule: 'printers', action: 'read' },
                'app.settings.custom_fields.index': { module: 'app_settings', subModule: 'custom_fields', action: 'read' },
                'app.settings.manage.form': { module: 'app_settings', subModule: 'manage_form', action: 'read' },
                'app.settings.calls': { module: 'app_settings', subModule: 'calls', action: 'read' },
                
                // System Settings routes
                'system.settings.email': { module: 'system_settings', subModule: 'email', action: 'update' },
                'system.settings.sms_gateways': { module: 'system_settings', subModule: 'sms_gateways', action: 'update' },
                'system.settings.gdpr': { module: 'system_settings', subModule: 'gdpr', action: 'update' },
                
                // Financial Settings routes
                'financial.settings.payment_gateways': { module: 'financial_settings', subModule: 'payment_gateways', action: 'read' },
                'financial.settings.bank_accounts': { module: 'financial_settings', subModule: 'bank_accounts', action: 'read' },
                'financial.settings.tax_rates': { module: 'financial_settings', subModule: 'tax_rates', action: 'read' },
                'financial.settings.currencies': { module: 'financial_settings', subModule: 'currencies', action: 'read' },
                
                // Other Settings routes
                'other.settings.box_settings': { module: 'other_settings', subModule: 'box_settings', action: 'update' },
                'other.settings.storage': { module: 'other_settings', subModule: 'storage', action: 'read' },
                'other.settings.ban_ip': { module: 'other_settings', subModule: 'ban_ip', action: 'read' },
            };
            
            // Check each link with data-route attribute
            // Only hide items if user definitely doesn't have permission
            // Don't hide items that Blade template already showed
            $('#sidebar-menu a[data-route]').each(function() {
                var $link = $(this);
                var $parentLi = $link.closest('li');
                var routeName = $link.data('route');
                var check = routePermissionChecks[routeName];
                
                // IMPORTANT: Trust Blade template's decision
                // If Blade showed it, it means backend permissions allow it
                // Don't hide items that Blade has already rendered as visible
                
                // If item is already hidden by Blade template, respect that decision
                if ($parentLi.css('display') === 'none' || !$parentLi.is(':visible')) {
                    return; // Blade already hid it - respect that decision
                }
                
                // If Blade showed it, trust Blade's decision - don't hide it
                // Blade template uses @if conditions based on actual backend permissions
                // JavaScript should only do additional filtering if absolutely necessary
                
                // For now, trust Blade's rendering - if Blade showed it, keep it visible
                $parentLi.show();
            });
            
            // Hide parent menu items if all children are hidden
            // But respect Blade template's decision to show/hide main sections
            $('#sidebar-menu .submenu').each(function() {
                var $submenu = $(this);
                var $parentLi = $submenu.closest('li');
                var $children = $submenu.find('ul > li');
                var visibleChildren = $children.filter(':visible').length;
                
                // Only hide if all children are hidden AND parent was initially visible
                // Don't hide if Blade template already hid it
                if (visibleChildren === 0 && $children.length > 0) {
                    // Check if parent was shown by Blade (it should be visible if Blade showed it)
                    // Only hide if it's currently visible (meaning Blade showed it)
                    if ($parentLi.is(':visible')) {
                        $parentLi.hide();
                    }
                } else if (visibleChildren > 0) {
                    // If any children are visible, make sure parent is visible
                    $parentLi.show();
                }
            });
            
            // Hide empty submenu headers
            $('#sidebar-menu .submenu-hdr').each(function() {
                var $header = $(this);
                var $nextSibling = $header.next('ul');
                if ($nextSibling.length > 0) {
                    var visibleItems = $nextSibling.find('li:visible').length;
                    if (visibleItems === 0) {
                        $header.hide();
                    } else {
                        $header.show();
                    }
                }
            });
        }

        // Run filter on page load
        // IMPORTANT: Blade template handles ALL permission-based visibility from backend
        // JavaScript should NOT override Blade's decisions - trust Blade completely
        setTimeout(function() {
            // If user is admin, don't filter anything - show all items
            if (window.isAdmin === true) {
                console.log('Admin user detected - showing all sidebar items');
                // Force show all sidebar items for admins
                $('#sidebar-menu li').show();
                $('#sidebar-menu a').closest('li').show();
                $('#sidebar-menu .submenu').closest('li').show();
                $('#sidebar-menu .submenu-hdr').show();
                $('#sidebar-menu ul').show();
                // Remove any inline display:none styles
                $('#sidebar-menu li').css('display', '');
                $('#sidebar-menu ul').css('display', '');
                return; // Admins see everything, no filtering needed
            }
            
            // For non-admin users: Blade template has already shown/hidden items based on backend permissions
            // JavaScript should NOT run any filtering - trust Blade's rendering completely
            // Blade uses @if conditions that check actual permissions from database via PermissionHelper
            
            // Ensure all items that Blade rendered remain visible (don't hide anything)
            // Blade has already determined visibility based on user's actual permissions
            $('#sidebar-menu li:visible').show();
            $('#sidebar-menu .submenu:visible').closest('li').show();
            $('#sidebar-menu .submenu-hdr:visible').show();
            
            // DO NOT run filterByModulePermissions() or filterSidebarMenu()
            // These functions would override Blade's permission-based rendering
            // Blade template is the single source of truth for permission-based visibility
        }, 200);
    });

})(jQuery);

