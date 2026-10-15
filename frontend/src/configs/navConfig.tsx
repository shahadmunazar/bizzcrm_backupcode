// Third-party Imports
import type * as Icon from 'lucide-react'

type IconName = keyof typeof Icon

export type MenuLeafSubItem = {
  label: string
  href: string
  activePath?: string
  badge?: string
  badgeClassName?: string
  target?: '_blank' | '_self' | '_parent' | '_top'
}

export type MenuGroupSubItem = {
  label: string
  childItems: MenuLeafSubItem[]
}

export type MenuSubItem = MenuLeafSubItem | MenuGroupSubItem

export type MenuItem = {
  icon: IconName
  label: string
} & (
  | {
      href: string
      badge?: string
      badgeClassName?: string
      childItems?: never
      target?: '_blank' | '_self' | '_parent' | '_top'
    }
  | {
      href?: never
      badge?: string
      badgeClassName?: string
      childItems: MenuSubItem[]
    }
)

export type NavItem = {
  groupLabel?: string
  items: MenuItem[]
}

export const navItems: NavItem[] = [
  {
    groupLabel: 'MAIN MENU',
    items: [
      {
        icon: 'LayoutGrid',
        label: 'Dashboard',
        childItems: [
          {
            label: 'Admin Dashboard',
            href: '/admin-dashboard'
          },
          {
            label: 'Tracking Dashboard',
            href: '/tracking-dashboard'
          },
          {
            label: 'Deals Dashboard',
            href: '/deals-dashboard'
          },
          {
            label: 'Leads Dashboard',
            href: '/leads-dashboard'
          },
          {
            label: 'Project Dashboard',
            href: '/project-dashboard'
          },
          {
            label: 'Call Dashboard',
            href: '/call-dashboard'
          },
          {
            label: 'Call Logs',
            href: '/call-logs'
          },
          {
            label: 'WhatsApp Dashboard',
            href: '/whatsapp-dashboard'
          },
          {
            label: 'Facebook Dashboard',
            href: '/facebook-dashboard'
          },
          {
            label: 'WhatsApp Chat',
            href: '/whatsapp-chat'
          }
        ]
      },
      {
        icon: 'UserStar',
        label: 'Super Admin',
        childItems: [
          {
            label: 'Dashboard',
            href: '/dashboard'
          },
          {
            label: 'Companies',
            href: '/companies'
          },
          {
            label: 'Subscriptions',
            href: '/subscriptions'
          },
          {
            label: 'Packages',
            href: '/packages'
          },
          {
            label: 'Coupons',
            href: '/coupons'
          },
          {
            label: 'Package Features',
            href: '/package-features'
          },
          {
            label: 'FAQs',
            href: '/faqs'
          },
          {
            label: 'Industry Register',
            href: '/industry-register'
          },
          {
            label: 'Domain',
            href: '/domain'
          },
          {
            label: 'Purchase Transaction',
            href: '/purchase-transaction'
          },
          {
            label: 'Realtime Logs',
            href: '/realtime-logs'
          },
          {
            label: 'Organizational Structure',
            href: '/organizational-structure'
          },
          {
            label: 'Delete Requests',
            href: '/delete-requests'
          }
        ]
      },
      {
        icon: 'Store',
        label: 'Vendors',
        childItems: [
          {
            label: 'Vendors',
            href: '/vendors'
          },
          {
            label: 'Vendor Percentages',
            href: '/vendor-percentages'
          },
          {
            label: 'Vendor Dashboard',
            href: '/vendor-dashboard'
          },
          {
            label: 'Vendor Transactions',
            href: '/vendor-transactions'
          }
        ]
      },
      {
        icon: 'Users',
        label: 'Attendance',
        childItems: [
          {
            label: 'Attendance All',
            href: '/attendance-all'
          },
          {
            label: 'Team Attendance',
            href: '/team-attendance'
          },
          {
            label: 'Biometric Machine (Admin)',
            href: '/biometric-machine-admin'
          },
          {
            label: 'Mobile Device Register',
            href: '/mobile-device-register'
          },
          {
            label: 'Enrolled Fingerprints',
            href: '/enrolled-fingerprints'
          },
          {
            label: 'Mobile Punches',
            href: '/mobile-punches'
          },
          {
            label: 'Attendance',
            href: '/attendance'
          },
          {
            label: 'Employee Dashboard',
            href: '/employee-dashboard'
          },
          {
            label: 'Attendance Calendar',
            href: '/attendance-calendar'
          },
          {
            label: 'Leaves',
            childItems: [
              {
                label: 'Leaves',
                href: '/leaves'
              },
              {
                label: 'Admin Leaves',
                href: '/admin-leaves'
              },
              {
                label: 'Leave Type',
                href: '/leave-type'
              },
              {
                label: 'Leave Transaction',
                href: '/leave-transaction'
              },
              {
                label: 'Leave Transaction All',
                href: '/leave-transaction-all'
              },
              {
                label: 'Team Leave Transaction',
                href: '/team-leave-transaction'
              }
            ]
          },
          {
            label: 'Holiday',
            childItems: [
              {
                label: 'Holiday',
                href: '/holiday'
              },
              {
                label: 'Holiday Type',
                href: '/holiday-type'
              }
            ]
          },
          {
            label: 'Shift',
            href: '/shift'
          },
          {
            label: 'Attendance Module Settings',
            href: '/attendace-module'
          }
        ]
      },
      {
        icon: 'Briefcase',
        label: 'HR Tools',
        childItems: [
          {
            label: 'Assets',
            href: '/assets'
          },
          {
            label: 'Hr Documents',
            href: '/hr-documents'
          },
          {
            label: 'Letter Generation',
            href: '/letter-generation'
          },
          {
            label: 'Job Applicants',
            href: '/job-applicants'
          }
        ]
      },
      {
        icon: 'Briefcase',
        label: 'Payroll',
        childItems: [
          {
            label: 'Employee Salary',
            href: '/employee-salary'
          },
          {
            label: 'Salary Report',
            href: '/salary-report'
          },
          {
            label: 'Pay Slip',
            href: '/pay-slip'
          },
          {
            label: 'Payroll Items',
            href: '/payroll-items'
          },
          {
            label: 'Payroll Setting',
            href: '/payroll-setting'
          },
          {
            label: 'Loan Settings',
            href: '/loan-settings'
          },
          {
            label: 'Loan Tracker',
            href: '/loan-tracker'
          }
        ]
      },
      {
        icon: 'Phone',
        label: 'Leads',
        childItems: [
          {
            label: 'Leads',
            href: '/leads'
          },
          {
            label: 'Leads All',
            href: '/leads-all'
          },
          {
            label: 'Leads Starred',
            href: '/leads-starred'
          },
          {
            label: 'Leads Trash',
            href: '/leads-trash'
          },
          {
            label: 'Leads Duplicate',
            href: '/leads-duplicate'
          },
          {
            label: 'Lead Approvals',
            href: '/lead-approvals'
          },
          {
            label: 'Working Location',
            href: '/working-location'
          },
          {
            label: 'Manage Form',
            href: '/manage-form'
          },
          {
            label: 'Facebook Leads',
            href: '/facebook-leads'
          }
        ]
      },
      {
        icon: 'FileBarChart',
        label: 'Activities Records',
        childItems: [
          {
            label: 'Todays Follow Up',
            href: '/todays-follow-up'
          },
          {
            label: 'Pending Follow Ups',
            href: '/pending-follow-ups'
          },
          {
            label: 'Upcoming Follow Ups',
            href: '/upcoming-follow-ups'
          },
          {
            label: 'Today DSR',
            href: '/today-dsr'
          },
          {
            label: 'This Month DSR',
            href: '/this-month-dsr'
          },
          {
            label: 'All DSR',
            href: '/all-dsr'
          },
          {
            label: 'All Activity',
            href: '/all-activity'
          }
        ]
      },
      {
        icon: 'FileBarChart',
        label: 'My Reports',
        childItems: [
          {
            label: 'Dynamic Label',
            href: '/dynamic-label'
          }
        ]
      },
      {
        icon: 'Phone',
        label: 'Integration',
        childItems: [
          {
            label: 'Integrations',
            href: '/integrations'
          },
          {
            label: 'Facebook Dashboard',
            href: '/facebook-dashboard'
          },
          {
            label: 'Facebook Forms',
            href: '/facebook-forms'
          },
          {
            label: 'Compaign Settings',
            href: '/compaign-settings'
          },
          {
            label: 'Compaign Details',
            href: '/compaign-details'
          },
          {
            label: 'Create Project',
            href: '/create-project'
          },
          {
            label: 'Bulk Upload',
            href: '/bulk-upload'
          },
          {
            label: 'Labels',
            href: '/labels'
          }
        ]
      },
      {
        icon: 'MessageCircle',
        label: 'WhatsApp Integration',
        childItems: [
          {
            label: 'WhatsApp Dashboard',
            href: '/whatsapp-dashboard'
          },
          {
            label: 'WhatsApp Chat',
            href: '/whatsapp-chat'
          },
          {
            label: 'Projects',
            href: '/projects'
          },
          {
            label: 'Templates',
            href: '/templates'
          },
          {
            label: 'Chatbots',
            href: '/chatbots'
          },
          {
            label: 'Quick Replies',
            href: '/quick-replies'
          },
          {
            label: 'Campaign',
            href: '/campaign'
          }
        ]
      },
      {
        icon: 'Receipt',
        label: 'Accounts',
        childItems: [
          {
            label: 'Dynamic Label',
            href: '/dynamic-label'
          }
        ]
      },
      {
        icon: 'PieChart',
        label: 'Stats',
        childItems: [
          {
            label: 'Source Stats',
            href: '/source-stats'
          },
          {
            label: 'Status Stats',
            href: '/status-stats'
          },
          {
            label: 'Task Stats',
            href: '/task-stats'
          },
          {
            label: 'Date Task Stats',
            href: '/date-task-stats'
          },
          {
            label: 'User Stats',
            href: '/user-stats'
          }
        ]
      }
    ]
  },
  {
    groupLabel: 'SETTINGS',
    items: [
      {
        icon: 'Settings',
        label: 'General Settings',
        childItems: [
          {
            label: 'Companies',
            href: '/companies'
          },
          {
            label: 'Companies Grid',
            href: '/companies-grid'
          },
          {
            label: 'Companies Details',
            href: '/companies-details'
          },
          {
            label: 'HSNs',
            href: '/hsns'
          },
          {
            label: 'Items',
            href: '/items'
          },
          {
            label: 'Setting',
            href: '/setting'
          },
          {
            label: 'Activity Type',
            href: '/activity-type'
          },
          {
            label: 'Lead Source',
            href: '/lead-source'
          },
          {
            label: 'Lead Status',
            href: '/lead-status'
          },
          {
            label: 'Custom Fields',
            href: '/custom-fields'
          },
          {
            label: 'Email Inboxes',
            href: '/email-inboxes'
          },
          {
            label: 'Email Messages',
            href: '/email-messages'
          },
          {
            label: 'Lead APIs',
            href: '/lead-apis'
          },
          {
            label: 'User Roles',
            href: '/user-roles'
          }
        ]
      },
      {
        icon: 'Globe',
        label: 'Website Settings',
        childItems: [
          {
            label: 'Company Settings',
            href: '/company-settings'
          },
          {
            label: 'Roles & Permissions',
            href: '/roles-permissions'
          },
          {
            label: 'Company Branches',
            href: '/settings/branchs-settings'
          },
          {
            label: 'Employee Types',
            href: '/employee-type-settings'
          },
          {
            label: 'Work Modes',
            href: '/work-mode-add'
          },
          {
            label: 'Attendace Module',
            href: '/attendace-module'
          },
          {
            label: 'Company Currency',
            href: '/company-currency'
          },
          {
            label: 'Localization',
            href: '/localization'
          },
          {
            label: 'Prefixes',
            href: '/prefixes'
          },
          {
            label: 'Preference',
            href: '/preference'
          },
          {
            label: 'Appearance',
            href: '/appearance'
          },
          {
            label: 'Language',
            href: '/language'
          }
        ]
      },
      {
        icon: 'AppWindow',
        label: 'App Settings',
        childItems: [
          {
            label: 'Invoice Settings',
            href: '/invoice-settings'
          },
          {
            label: 'Printers',
            href: '/printers'
          },
          {
            label: 'Custom Fields',
            href: '/custom-fields'
          },
          {
            label: 'Manage Form',
            href: '/manage-form'
          },
          {
            label: 'Lead Source',
            href: '/lead-source'
          },
          {
            label: 'Lost Reason',
            href: '/lost-reason'
          },
          {
            label: 'Lead Status',
            href: '/lead-status'
          },
          {
            label: 'Lead Sub Status',
            href: '/lead-sub-status'
          },
          {
            label: 'Industry',
            href: '/industry'
          },
          {
            label: 'Activity Setting',
            href: '/activity-setting'
          }
        ]
      },
      {
        icon: 'Laptop',
        label: 'System Settings',
        childItems: [
          {
            label: 'Email Settings',
            href: '/email-settings'
          },
          {
            label: 'SMS Gateways',
            href: '/sms-gateways'
          },
          {
            label: 'GDPR Cookies',
            href: '/gdpr-cookies'
          }
        ]
      },
      {
        icon: 'CircleDollarSign',
        label: 'Financial Settings',
        childItems: [
          {
            label: 'Payment Gateways',
            href: '/payment-gateways'
          },
          {
            label: 'Bank Accounts',
            href: '/bank-accounts'
          },
          {
            label: 'Payment Modes',
            href: '/payment-modes'
          },
          {
            label: 'Divisions',
            href: '/divisions'
          },
          {
            label: 'Tax Rates',
            href: '/tax-rates'
          },
          {
            label: 'Currencies',
            href: '/currencies'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Other Settings',
        childItems: [
          {
            label: 'Storage',
            href: '/storage'
          },
          {
            label: 'Ban IP Address',
            href: '/ban-ip-address'
          },
          {
            label: 'Box Settings',
            href: '/box-settings'
          }
        ]
      }
    ]
  },
  {
    groupLabel: 'CRM SETTINGS',
    items: [
      {
        icon: 'Circle',
        label: 'Sources',
        href: '/sources'
      },
      {
        icon: 'Circle',
        label: 'Lost Reason',
        href: '/lost-reason'
      },
      {
        icon: 'Circle',
        label: 'Lead Status',
        href: '/lead-status'
      },
      {
        icon: 'Circle',
        label: 'Industry',
        href: '/industry'
      },
      {
        icon: 'Circle',
        label: 'Activity Setting',
        href: '/activity-setting'
      },
      {
        icon: 'MessageCircle',
        label: 'WhatsApp',
        childItems: [
          {
            label: 'WhatsApp Chat',
            href: '/whatsapp-chat'
          },
          {
            label: 'Campaign',
            href: '/campaign'
          },
          {
            label: 'WhatsApp Templates',
            href: '/whatsapp-templates'
          }
        ]
      },
      {
        icon: 'Phone',
        label: 'Auto Dialer',
        childItems: [
          {
            label: 'Call Dashboard',
            href: '/call-dashboard'
          },
          {
            label: 'calls',
            href: '/calls'
          },
          {
            label: 'call-history',
            href: '/call-history'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Expense',
        childItems: [
          {
            label: 'Designation Wise Expense',
            href: '/designation-wise-expense'
          },
          {
            label: 'Expenses',
            href: '/expenses'
          },
          {
            label: 'Day wise Expenses',
            href: '/day-wise-expenses'
          },
          {
            label: 'Standard Fare Chart',
            href: '/standard-fare-chart'
          }
        ]
      },
      {
        icon: 'UserStar',
        label: 'Sales & Purchase',
        childItems: [
          {
            label: 'Sales Transactions',
            href: '/sales-transactions'
          },
          {
            label: 'Purchase Transaction',
            href: '/purchase-transaction'
          },
          {
            label: 'Quotations',
            href: '/quotations'
          },
          {
            label: 'All Documents',
            href: '/all-documents'
          },
          {
            label: 'Reminders',
            href: '/reminders'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Inventory',
        childItems: [
          {
            label: 'Item Master',
            href: '/item-master'
          },
          {
            label: 'Inventory Approvals',
            href: '/inventory-approvals'
          },
          {
            label: 'Stock Movement',
            href: '/stock-movement'
          },
          {
            label: 'Barcode',
            href: '/barcode'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Buyers & Suppliers',
        childItems: [
          {
            label: 'Buyers',
            href: '/buyers'
          },
          {
            label: 'Suplier',
            href: '/suplier'
          },
          {
            label: 'Account Details',
            href: '/account-details'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Payments',
        childItems: [
          {
            label: 'Receivables',
            href: '/receivables'
          },
          {
            label: 'Payables',
            href: '/payables'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Task Dashboard',
        childItems: [
          {
            label: 'Tasks',
            href: '/tasks'
          },
          {
            label: 'Inportant Tasks',
            href: '/inportant-tasks'
          },
          {
            label: 'Completed Tasks',
            href: '/completed-tasks'
          },
          {
            label: 'Task Reports',
            href: '/task-reports'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Production',
        childItems: [
          {
            label: 'Bill of Materials',
            href: '/bill-of-materials'
          },
          {
            label: 'Work Orders',
            href: '/work-orders'
          },
          {
            label: 'Production Process',
            href: '/production-process'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Reports & Intelligence',
        childItems: [
          {
            label: 'Reports',
            href: '/reports'
          },
          {
            label: 'Sales Dashboard',
            href: '/sales-dashboard'
          }
        ]
      }
    ]
  },
  {
    groupLabel: 'FIELD TRACKING',
    items: [
      {
        icon: 'Circle',
        label: 'Employees',
        childItems: [
          {
            label: 'Employees',
            href: '/employees'
          },
          {
            label: 'Employee Detail',
            href: '/employee-detail'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Tour',
        childItems: [
          {
            label: 'Tour List',
            href: '/tour-list'
          },
          {
            label: 'Tour Plan',
            href: '/tour-plan'
          },
          {
            label: 'Tour bulk upload',
            href: '/tour-bulk-upload'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Target',
        childItems: [
          {
            label: 'My Target',
            href: '/my-target'
          },
          {
            label: 'Target Template',
            href: '/target-template'
          },
          {
            label: 'Team Targets',
            href: '/team-targets'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Activities',
        childItems: [
          {
            label: 'Team Activity',
            href: '/team-activity'
          },
          {
            label: 'Team Activity Details',
            href: '/team-activity-details'
          },
          {
            label: 'Tracking History',
            href: '/tracking-history'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Customers',
        childItems: [
          {
            label: 'Customer List',
            href: '/customer-list'
          }
        ]
      }
    ]
  },
  {
    groupLabel: 'FIELD TRACKING',
    items: [
      {
        icon: 'Circle',
        label: 'Customers',
        childItems: [
          {
            label: 'Customer List',
            href: '/customer-list'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Field Tracking',
        childItems: [
          {
            label: 'Tracking Dashboard',
            href: '/tracking-dashboard'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Activities',
        childItems: [
          {
            label: 'Team Activity',
            href: '/team-activity'
          },
          {
            label: 'Team Activity Details',
            href: '/team-activity-details'
          },
          {
            label: 'Tracking History',
            href: '/tracking-history'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Payments',
        childItems: [
          {
            label: 'Payments List',
            href: '/payments-list'
          },
          {
            label: 'Payment',
            href: '/payment'
          },
          {
            label: 'Payment Admin',
            href: '/payment-admin'
          },
          {
            label: 'Payment Gateways',
            href: '/payment-gateways'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Tour',
        childItems: [
          {
            label: 'Tour List',
            href: '/tour-list'
          },
          {
            label: 'Tour Plan',
            href: '/tour-plan'
          },
          {
            label: 'Tour bulk upload',
            href: '/tour-bulk-upload'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Target',
        childItems: [
          {
            label: 'My Target',
            href: '/my-target'
          },
          {
            label: 'Target Template',
            href: '/target-template'
          },
          {
            label: 'Team Targets',
            href: '/team-targets'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Finance & Expenses',
        childItems: [
          {
            label: 'Dashboard',
            href: '/dashboard'
          },
          {
            label: 'Settings',
            href: '/settings'
          },
          {
            label: 'My Expenses',
            href: '/my-expenses'
          },
          {
            label: 'Approvals',
            href: '/approvals'
          },
          {
            label: 'Reimbursements',
            href: '/reimbursements'
          }
        ]
      }
    ]
  },
  {
    groupLabel: 'USER MANAGEMENT',
    items: [
      {
        icon: 'Circle',
        label: 'Manage Users',
        childItems: [
          {
            label: 'Organization Structure',
            href: '/organization-structure'
          },
          {
            label: 'Manage Users',
            href: '/manage-users'
          },
          {
            label: 'Employee List',
            href: '/employee-list'
          },
          {
            label: 'Employee Grid',
            href: '/employee-grid'
          },
          {
            label: 'Employee Type Settings',
            href: '/employee-type-settings'
          },
          {
            label: 'Work Mode Add',
            href: '/work-mode-add'
          },
          {
            label: 'Roles & Permissions',
            href: '/roles-permissions'
          },
          {
            label: 'Permissions',
            href: '/permissions'
          },
          {
            label: 'Departments',
            href: '/departments'
          },
          {
            label: 'Designations',
            href: '/designations'
          },
          {
            label: 'To-Do',
            href: '/to-do'
          },
          {
            label: 'Projects',
            href: '/projects'
          },
          {
            label: 'Project Contacts',
            href: '/project-contacts'
          },
          {
            label: 'Project Types',
            href: '/project-types'
          },
          {
            label: 'Project Categories',
            href: '/project-categories'
          },
          {
            label: 'Deals',
            href: '/deals'
          },
          {
            label: 'Deal Settings',
            href: '/deal-settings'
          }
        ]
      }
    ]
  },
  {
    groupLabel: 'USER MANAGEMENT',
    items: [
      {
        icon: 'Circle',
        label: 'Manage Users',
        href: '/manage-users'
      },
      {
        icon: 'Circle',
        label: 'Roles & Permissions',
        href: '/roles-permissions'
      },
      {
        icon: 'Circle',
        label: 'Delete Request',
        href: '/delete-request'
      },
      {
        icon: 'Circle',
        label: 'Departments',
        href: '/departments'
      },
      {
        icon: 'Circle',
        label: 'Designations',
        href: '/designations'
      }
    ]
  },
  {
    groupLabel: 'MEMBERSHIP',
    items: [
      {
        icon: 'Circle',
        label: 'Membership',
        childItems: [
          {
            label: 'Membership Plans',
            href: '/membership-plans'
          },
          {
            label: 'Membership Addons',
            href: '/membership-addons'
          },
          {
            label: 'Transactions',
            href: '/transactions'
          }
        ]
      }
    ]
  },
  {
    groupLabel: 'CONTENT',
    items: [
      {
        icon: 'Circle',
        label: 'Pages',
        href: '/pages'
      },
      {
        icon: 'Circle',
        label: 'Location',
        childItems: [
          {
            label: 'Countries',
            href: '/countries'
          },
          {
            label: 'States',
            href: '/states'
          },
          {
            label: 'Cities',
            href: '/cities'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Testimonials',
        href: '/testimonials'
      },
      {
        icon: 'Circle',
        label: 'FAQ',
        href: '/faq'
      }
    ]
  },
  {
    groupLabel: 'SUPPORT',
    items: [
      {
        icon: 'Circle',
        label: 'Contact Messages',
        href: '/contact-messages'
      },
      {
        icon: 'Circle',
        label: 'Tickets',
        href: '/tickets'
      }
    ]
  },
  {
    groupLabel: 'SETTINGS',
    items: [
      {
        icon: 'Settings',
        label: 'General Settings',
        childItems: [
          {
            label: 'Profile',
            href: '/profile'
          },
          {
            label: 'Security',
            href: '/security'
          },
          {
            label: 'Notifications',
            href: '/notifications'
          },
          {
            label: 'Connected Apps',
            href: '/connected-apps'
          }
        ]
      },
      {
        icon: 'Globe',
        label: 'Website Settings',
        childItems: [
          {
            label: 'Company Settings',
            href: '/company-settings'
          },
          {
            label: 'Localization',
            href: '/localization'
          },
          {
            label: 'Prefixes',
            href: '/prefixes'
          },
          {
            label: 'Preference',
            href: '/preference'
          },
          {
            label: 'Appearance',
            href: '/appearance'
          },
          {
            label: 'Language',
            href: '/language'
          }
        ]
      },
      {
        icon: 'AppWindow',
        label: 'App Settings',
        childItems: [
          {
            label: 'Invoice Settings',
            href: '/invoice-settings'
          },
          {
            label: 'Printers',
            href: '/printers'
          },
          {
            label: 'Custom Fields',
            href: '/custom-fields'
          },
          {
            label: 'Leads Sub Status',
            href: '/leads-sub-status'
          }
        ]
      },
      {
        icon: 'Laptop',
        label: 'System Settings',
        childItems: [
          {
            label: 'Email Settings',
            href: '/email-settings'
          },
          {
            label: 'SMS Gateways',
            href: '/sms-gateways'
          },
          {
            label: 'GDPR Cookies',
            href: '/gdpr-cookies'
          }
        ]
      },
      {
        icon: 'CircleDollarSign',
        label: 'Financial Settings',
        childItems: [
          {
            label: 'Payment Gateways',
            href: '/payment-gateways'
          },
          {
            label: 'Bank Accounts',
            href: '/bank-accounts'
          },
          {
            label: 'Tax Rates',
            href: '/tax-rates'
          },
          {
            label: 'Currencies',
            href: '/currencies'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Other Settings',
        childItems: [
          {
            label: 'Storage',
            href: '/storage'
          },
          {
            label: 'Ban IP Address',
            href: '/ban-ip-address'
          }
        ]
      }
    ]
  },
  {
    groupLabel: 'PAGES',
    items: [
      {
        icon: 'Circle',
        label: 'Authentication',
        childItems: [
          {
            label: 'Login',
            href: '/login'
          },
          {
            label: 'Register',
            href: '/register'
          },
          {
            label: 'Forgot Password',
            href: '/forgot-password'
          },
          {
            label: 'Reset Password',
            href: '/reset-password'
          },
          {
            label: 'Email Verification',
            href: '/email-verification'
          },
          {
            label: '2 Step Verification',
            href: '/2-step-verification'
          },
          {
            label: 'Lock Screen',
            href: '/lock-screen'
          }
        ]
      },
      {
        icon: 'Circle',
        label: 'Error Pages',
        childItems: [
          {
            label: '404 Error',
            href: '/404-error'
          },
          {
            label: '500 Error',
            href: '/500-error'
          }
        ]
      },
      {
        icon: 'AppWindow',
        label: 'Blank Page',
        href: '/blank-page'
      },
      {
        icon: 'Laptop',
        label: 'Coming Soon',
        href: '/coming-soon'
      },
      {
        icon: 'CircleDollarSign',
        label: 'Under Maintenance',
        href: '/under-maintenance'
      }
    ]
  }
]
