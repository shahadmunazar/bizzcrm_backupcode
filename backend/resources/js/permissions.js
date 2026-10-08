/**
 * Frontend Permission Helper
 * Check user permissions on the client side
 */

class PermissionHelper {
    constructor() {
        this.userPermissions = [];
        this.isAdmin = false; // Added isAdmin support
        this.loadPermissions();
    }

    /**
     * Load user permissions from the server
     */
    async loadPermissions() {
        try {
            // Updated to use the consistent /user-permissions endpoint
            const response = await fetch('/user-permissions', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            if (response.ok) {
                const data = await response.json();
                this.userPermissions = data.permissions || [];
                this.isAdmin = data.is_admin || false; // Set isAdmin from server response
            }
        } catch (error) {
            console.error('Failed to load permissions:', error);
        }
    }

    /**
     * Check if user has a specific permission
     * @param {string} permission - Permission name (e.g., 'holidays.read')
     * @returns {boolean}
     */
    hasPermission(permission) {
        // Admins have all permissions
        if (this.isAdmin) {
            return true;
        }

        // Check for "all.permissions" master permission
        if (this.userPermissions.includes('all.permissions')) {
            return true;
        }

        // Check for module-level "all" permission (e.g., "holidays.all")
        const parts = permission.split('.');
        if (parts.length >= 2) {
            const moduleAllPermission = parts[0] + '.all';
            if (this.userPermissions.includes(moduleAllPermission)) {
                return true;
            }
        }

        // Check specific permission
        return this.userPermissions.includes(permission);
    }

    /**
     * Check if user has any of the given permissions
     * @param {string[]} permissions - Array of permission names
     * @returns {boolean}
     */
    hasAnyPermission(permissions) {
        return permissions.some(perm => this.hasPermission(perm));
    }

    /**
     * Check if user has all of the given permissions
     * @param {string[]} permissions - Array of permission names
     * @returns {boolean}
     */
    hasAllPermissions(permissions) {
        return permissions.every(perm => this.hasPermission(perm));
    }

    /**
     * Show/hide element based on permission
     * @param {string} permission - Permission name
     * @param {string} selector - CSS selector
     * @param {boolean} show - Whether to show (true) or hide (false)
     */
    toggleElement(permission, selector, show = true) {
        const hasPerm = this.hasPermission(permission);
        const elements = document.querySelectorAll(selector);

        elements.forEach(el => {
            if (hasPerm) {
                if (show) {
                    el.style.display = '';
                    el.classList.remove('d-none');
                } else {
                    el.style.display = 'none';
                    el.classList.add('d-none');
                }
            } else {
                if (show) {
                    el.style.display = 'none';
                    el.classList.add('d-none');
                } else {
                    el.style.display = '';
                    el.classList.remove('d-none');
                }
            }
        });
    }

    /**
     * Enable/disable element based on permission
     * @param {string} permission - Permission name
     * @param {string} selector - CSS selector
     */
    toggleDisabled(permission, selector) {
        const hasPerm = this.hasPermission(permission);
        const elements = document.querySelectorAll(selector);

        elements.forEach(el => {
            el.disabled = !hasPerm;
            if (!hasPerm) {
                el.classList.add('disabled');
                el.setAttribute('title', 'You do not have permission for this action');
            } else {
                el.classList.remove('disabled');
                el.removeAttribute('title');
            }
        });
    }
}

// Initialize global permission helper
window.PermissionHelper = new PermissionHelper();

// jQuery plugin for easy permission checks
if (typeof $ !== 'undefined') {
    $.fn.hasPermission = function(permission) {
        return window.PermissionHelper.hasPermission(permission);
    };

    $.fn.toggleByPermission = function(permission, show = true) {
        const hasPerm = window.PermissionHelper.hasPermission(permission);
        if (hasPerm && show) {
            this.show();
        } else {
            this.hide();
        }
        return this;
    };

    $.fn.enableByPermission = function(permission) {
        const hasPerm = window.PermissionHelper.hasPermission(permission);
        this.prop('disabled', !hasPerm);
        if (!hasPerm) {
            this.addClass('disabled');
        } else {
            this.removeClass('disabled');
        }
        return this;
    };
}

