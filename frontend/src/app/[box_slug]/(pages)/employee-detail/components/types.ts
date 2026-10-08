export interface BankDetail {
  id: number;
  box_user_id: number;
  bank_type: string;
  bank_name: string;
  account_number: string;
  branch: string;
  ifsc_code: string;
}

export interface Family {
  id: number;
  box_user_id: number;
  name: string;
  relationship: string;
  dob: string;
  contact: string;
}

export interface Education {
  id: number;
  box_user_id: number;
  institute: string;
  degree: string;
  start_year: string;
  end_year: string;
}

export interface Experience {
  id: number;
  box_user_id: number;
  company_name: string;
  position: string;
  start_date: string;
  end_date: string;
  current_working: number;
}

export interface EmergencyContact {
  id: number;
  box_user_id: number;
  name: string;
  relation: string;
  phone: string;
  type: string;
  phone_alt: string;
}

export interface BankStatutory {
  id: number;
  box_user_id: number;
  salary_basis: string;
  salary_amount: string;
  payment_type: string;
  pf_contribution: string;
  pf_no: string;
  pf_rate: string;
  pf_additional: string;
  pf_total: string;
  esi_contribution: string;
  esi_no: string;
  esi_rate: string;
  esi_additional: string;
  esi_total: string;
}

export interface EmployeeData {
  id: number;
  user_id: number;
  pan_number: string;
  employee_id: string;
  esi_no: string;
  pf_no: string;
  resignation_date: string;
  phone: string;
  joining_date: string;
  status: string;
  first_name: string;
  last_name: string;
  company: string;
  username: string;
  assign_tour: string;
  staff_role: string;
  assign_customer: string;
  about: string;
  department: string;
  gender: string;
  birthday: string;
  designation: string;
  passport_no: string;
  passport_exp_date: string;
  nationality: string;
  religion: string;
  address: string;
  marital_status: string;
  work_mode_id: string;
  working_web_days: any;
  Employment_of_spouse: string;
  no_of_children: string;
  profile_image_path: string;
  report_office: string;
  shift_id: string;
  marriage_date: string;
  reporting_manager_id: string;
  emp_type: string;
  box_id: number;
  is_location_tracking: boolean;
  
  // Appended fields
  profile_image_url: string;
  name: string;
  email: string;
  department_name: string;
  department_id: number;
  designation_name: string;
  designation_id: number;
  employee_type_name: string;
  
  // Relations
  bank_details: BankDetail[];
  families: Family[];
  educations: Education[];
  experiences: Experience[];
  emergency_contacts: EmergencyContact[];
  bank_statutory: BankStatutory[];
  user: any;
  shift: any;
  work_mode: any;
  assigned_offices: any[];
  machine_mappings: any[];
  reporting_manager: any;
  employee_type: any;
  assigned_assets: any[];
  
  // Extra data
  all_employees: any[];
  departments: any[];
  permissions: {
    direct: number[];
    inherited: number[];
  };
  has_assigned_branches: boolean;
}
