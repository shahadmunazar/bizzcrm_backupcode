'use client';

import React, { useState, useEffect, useMemo } from 'react';

import { useForm, Controller } from 'react-hook-form';

import { zodResolver } from '@hookform/resolvers/zod';

import * as z from 'zod';

import { toast } from 'sonner';

import { Pencil, Trash2, Plus, ArrowLeft } from 'lucide-react';

import { format } from 'date-fns';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { DesignationTreeModal } from '@/components/ui/designation-tree-modal';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ImageCropper } from '@/components/ui/image-cropper';
import {
  Combobox,
  ComboboxInput,
  ComboboxContent,
  ComboboxItem,
  ComboboxEmpty,
  ComboboxList
} from '@/components/ui/combobox';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog';

import { apiClient } from '@/lib/api-client';


import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { PermissionsTable } from './permissions-table';

export function SearchableSelect({ items, value, onChange, placeholder, emptyText = 'No items found' }: any) {
  const [inputValue, setInputValue] = useState('');
  
  const filteredItems = items.filter((item: any) => {
    const name = item?.name || `${item?.first_name || ''} ${item?.last_name || ''}`.trim() || '';
    return name.toLowerCase().includes(inputValue.toLowerCase());
  });

  const selectedItem = items.find((item: any) => String(item.id) === String(value));
  const selectedName = selectedItem ? (selectedItem.name || `${selectedItem.first_name || ''} ${selectedItem.last_name || ''}`.trim() || '') : '';
  const comboboxValue = selectedItem ? { value: String(selectedItem.id), label: selectedName } : null;

  return (
    <div className="relative">
      <Combobox 
        value={comboboxValue} 
        onValueChange={(val: any) => onChange(val ? val.value : null)}
        onInputValueChange={(val: string) => setInputValue(val)}
      >
        <div className="relative w-full flex items-center">
          <ComboboxInput placeholder={placeholder} className="w-full" showClear />
        </div>
        
        <ComboboxContent>
          <ComboboxEmpty>{emptyText}</ComboboxEmpty>
          <ComboboxList>
            {filteredItems.map((item: any) => {
              const name = item?.name || `${item?.first_name || ''} ${item?.last_name || ''}`.trim();
              return (
                <ComboboxItem key={item.id} value={{ value: String(item.id), label: name }}>
                  {name}
                </ComboboxItem>
              );
            })}
          </ComboboxList>
        </ComboboxContent>
      </Combobox>
    </div>
  );
}

const formSchema = z.object({
  id: z.number().optional(),
  employee_id: z.string().optional().nullable(),
  name: z.string().min(1, 'First Name is required'),
  last_name: z.string().optional().nullable(),
  gender: z.string().min(1, 'Gender is required'),
  email: z.string().email('Invalid email address').min(1, 'Email is required'),
  phone: z.string().min(1, 'Mobile number is required'),
  joining_date: z.string().min(1, 'Joining date is required'),
  emp_type: z.string().optional().nullable(),
  department: z.string().optional().nullable(),
  designation: z.string().optional().nullable(),
  reporting_manager_id: z.string().optional().nullable(),
  can_login_anytime: z.string().optional().nullable(),
  call_logs: z.string().optional().nullable(),
  exlude_ip_restriction: z.boolean().optional(),
  exclude_round_robin: z.boolean().optional(),
  user_box_timing: z.boolean().optional(),
  day_start_time: z.string().optional().nullable(),
  day_end_time: z.string().optional().nullable(),
  allowed_ips: z.string().optional().nullable(),
  working_web_days: z.array(z.string()).optional(),
  permissions: z.array(z.number()).optional(),
});

type FormValues = z.infer<typeof formSchema>;

export default function EmployeeListPage() {
  const params = useParams();
  const router = useRouter();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [employees, setEmployees] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [employeeToDelete, setEmployeeToDelete] = useState<number | null>(null);

  // Dropdown states
  const [departments, setDepartments] = useState<any[]>([]);
  const [employeeTypes, setEmployeeTypes] = useState<any[]>([]);
  const [managers, setManagers] = useState<any[]>([]);

  const [selectedImage, setSelectedImage] = useState<string | null>(null);
  const [croppedImageBase64, setCroppedImageBase64] = useState<string | null>(null);
  const [isCropperOpen, setIsCropperOpen] = useState(false);

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: '',
      employee_id: '',
      last_name: '',
      gender: '',
      email: '',
      phone: '',
      joining_date: format(new Date(), 'yyyy-MM-dd'),
      emp_type: '',
      department: '',
      designation: '',
      reporting_manager_id: '',
      can_login_anytime: '1',
      call_logs: '1',
      exlude_ip_restriction: false,
      exclude_round_robin: false,
      user_box_timing: true,
      day_start_time: '',
      day_end_time: '',
      allowed_ips: '',
      working_web_days: [],
    },
  });

  const [isDesignationModalOpen, setIsDesignationModalOpen] = useState(false);

  const { control, handleSubmit, reset, watch, setValue } = form;
  const watchUserBoxTiming = watch('user_box_timing');
  const watchDepartment = watch('department');

  // designations logic kept just to find selectedName for the input box
  const designations = React.useMemo(() => {
    if (!watchDepartment) return [];
    const dept = departments.find((d: any) => String(d.id) === String(watchDepartment));
    return dept?.all_teams || dept?.allTeams || [];
  }, [watchDepartment, departments]);

  useEffect(() => {
    if (!routeBoxSlug || routeBoxSlug === '[box_slug]') return;
    fetchEmployees();
    fetchDepartments();
    fetchEmployeeTypes();
  }, [routeBoxSlug]);

  const fetchEmployees = async () => {
    setIsLoading(true);

    try {
      const boxSlug = routeBoxSlug;
      const res = await apiClient.get(`/${boxSlug}/employees/list`);

      if (Array.isArray(res?.data)) {
        setEmployees(res?.data);
      } else if (res?.data?.data) {
        setEmployees(res?.data?.data);
      } else if (Array.isArray(res)) {
        setEmployees(res);
      } else {
        setEmployees([]);
      }
    } catch (err: any) {
      console.error(err);
      toast.error('Failed to load employees');
    } finally {
      setIsLoading(false);
    }
  };

  const fetchDepartments = async () => {
    try {
      const boxSlug = routeBoxSlug;

      const res = await apiClient.get(`/${boxSlug}/departments`);

      if (res?.data) setDepartments(res.data);
    } catch (error) {
      console.error('Failed to fetch departments:', error);
    }
  };

  const fetchEmployeeTypes = async () => {
    try {
      const boxSlug = routeBoxSlug;
      const res = await apiClient.get(`/${boxSlug}/employee-types`);

      if (res?.data) setEmployeeTypes(res.data);
    } catch (error) {
      console.error('Failed to fetch employee types:', error);
    }
  };

  useEffect(() => {
    setManagers(employees);
  }, [employees]);

  const onSubmit = async (data: FormValues) => {
    try {
      const boxSlug = routeBoxSlug;
      const isEdit = !!data.id;
      const url = isEdit ? `/${boxSlug}/employees/${data.id}` : `/${boxSlug}/create-employee`;
      const method = isEdit ? 'put' : 'post';
      
      let parsed_allowed_ips: string[] = [];
      if (typeof data.allowed_ips === 'string') {
        parsed_allowed_ips = data.allowed_ips
          .split(',')
          .map(ip => ip.trim())
          .filter(ip => ip !== '');
      } else if (Array.isArray(data.allowed_ips)) {
        parsed_allowed_ips = data.allowed_ips;
      }
      
      const payloadObj = { 
        ...data,
        allowed_ips: parsed_allowed_ips.length > 0 ? parsed_allowed_ips : []
      };
      
      let finalPayload: any = payloadObj;
      let finalMethod: 'post' | 'put' | 'get' | 'delete' = method;
      
      if (croppedImageBase64) {
        const formData = new FormData();
        Object.entries(payloadObj).forEach(([key, value]) => {
          if (value !== null && value !== undefined) {
             if (Array.isArray(value)) {
               value.forEach((v, i) => formData.append(`${key}[${i}]`, String(v)));
             } else {
               formData.append(key, String(value));
             }
          }
        });
        
        const arr = croppedImageBase64.split(',');
        const mimeMatch = arr[0].match(/:(.*?);/);
        const mime = mimeMatch ? mimeMatch[1] : 'image/jpeg';
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        while (n--) {
          u8arr[n] = bstr.charCodeAt(n);
        }
        const file = new File([u8arr], 'profile.jpg', { type: mime });
        formData.append('profile_image', file);
        
        finalPayload = formData;
        
        if (isEdit) {
          finalMethod = 'post';
          formData.append('_method', 'PUT');
        }
      }
      
      const res = await apiClient[finalMethod](url, finalPayload);
      
      toast.success(isEdit ? 'Employee updated successfully' : 'Employee created successfully');
      setIsModalOpen(false);
      reset();
      setCroppedImageBase64(null);
      
      const newEmpId = res?.data?.data?.id || res?.data?.id;
      if (newEmpId) {
        router.push(`/${boxSlug}/employee-detail?emp_id=${newEmpId}`);
      } else {
        fetchEmployees();
      }
    } catch (err: any) {
      toast.error(err.response?.data?.message || err.message || 'An error occurred');
    }
  };

  const handleDelete = async () => {
    if (!employeeToDelete) return;

    try {
      const boxSlug = routeBoxSlug;

      await apiClient.delete(`/${boxSlug}/employees/${employeeToDelete}`, {
        body: JSON.stringify({ employee_id: employeeToDelete }),
        headers: { 'Content-Type': 'application/json' },
      });
      toast.success('Employee deleted successfully');
      setIsDeleteModalOpen(false);
      fetchEmployees();
    } catch (err: any) {
      toast.error(err.message || 'Failed to delete employee');
    }
  };

  const openAddModal = async () => {
    let nextEmployeeId = '';
    try {
      const boxSlug = routeBoxSlug;
      const res = await apiClient.get(`/${boxSlug}/get-employee-id`);
      if (res?.data?.employee_id) {
        nextEmployeeId = res.data.employee_id;
      }
    } catch (error) {
      console.error('Failed to fetch next employee ID:', error);
    }

    setCroppedImageBase64(null);
    reset({
      name: '',
      employee_id: nextEmployeeId,
      last_name: '',
      gender: '',
      email: '',
      phone: '',
      joining_date: format(new Date(), 'yyyy-MM-dd'),
      emp_type: '',
      department: '',
      designation: '',
      reporting_manager_id: '',
      can_login_anytime: '1',
      call_logs: '1',
      exlude_ip_restriction: false,
      exclude_round_robin: false,
      user_box_timing: true,
      day_start_time: '',
      day_end_time: '',
      allowed_ips: '',
      working_web_days: [],
    });
    setIsModalOpen(true);
  };

  const openEditModal = (employee: any) => {
    const parseBool = (val: any) => val === true || val === 'true' || val === 1 || val === '1';

    if (employee.profile_image_path) {
      const baseUrl = 'http://127.0.0.1:8000/storage/';
      setCroppedImageBase64(baseUrl + employee.profile_image_path);
    } else {
      setCroppedImageBase64(null);
    }
    
    reset({
      id: employee.id,
      employee_id: employee.employee_id || '',
      name: employee.name || employee.first_name || '',
      last_name: employee.last_name || '',
      gender: employee.gender || '',
      email: employee.email || '',
      phone: employee.phone || '',
      joining_date: employee.joining_date || format(new Date(), 'yyyy-MM-dd'),
      emp_type: employee.employee_type_id?.toString() || employee.emp_type?.toString() || '',
      department: employee.department?.toString() || '',
      designation: employee.designation?.toString() || '',
      reporting_manager_id: employee.reporting_manager_id?.toString() || '',
      can_login_anytime: parseBool(employee.can_login_anytime) ? '1' : '0',
      call_logs: parseBool(employee.call_logs) ? '1' : '0',
      exlude_ip_restriction: parseBool(employee.exlude_ip_restriction),
      exclude_round_robin: parseBool(employee.exclude_round_robin),
      user_box_timing: parseBool(employee.user_box_timing),
      day_start_time: employee.day_start_time || '',
      day_end_time: employee.day_end_time || '',
      allowed_ips: employee.allowed_ips ? (Array.isArray(employee.allowed_ips) ? employee.allowed_ips.join(', ') : employee.allowed_ips) : '',
      working_web_days: employee.working_web_days ? (typeof employee.working_web_days === 'string' ? JSON.parse(employee.working_web_days) : employee.working_web_days) : [],
    });
    setIsModalOpen(true);
  };

  return (
    <div className="p-6 max-w-[1400px] mx-auto">
      {!isModalOpen ? (
        <>
          <div className="flex justify-between items-center mb-6">
            <div>
              <h1 className="text-2xl font-bold">Employee List</h1>
              <p className="text-muted-foreground">Manage your employees</p>
            </div>
            <Button onClick={openAddModal} className="gap-2">
              <Plus className="w-4 h-4" /> Add Employee
            </Button>
          </div>

          <div className="border rounded-lg bg-card">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Employee ID</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Phone</TableHead>
              <TableHead>Department</TableHead>
              <TableHead>Designation</TableHead>
              <TableHead>Role</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {isLoading ? (
              <TableRow>
                <TableCell colSpan={8} className="text-center py-8">
                  Loading employees...
                </TableCell>
              </TableRow>
            ) : employees.length === 0 ? (
              <TableRow>
                <TableCell colSpan={8} className="text-center py-8">
                  No employees found.
                </TableCell>
              </TableRow>
            ) : (
              employees.map((emp) => (
                <TableRow key={emp.id}>
                  <TableCell>
                    <Link href={`/${routeBoxSlug}/employee-detail?emp_id=${emp.id}`} className="text-blue-500 hover:underline">
                      {emp.employee_id || '-'}
                    </Link>
                  </TableCell>
                  <TableCell>
                    <Link href={`/${routeBoxSlug}/employee-detail?emp_id=${emp.id}`} className="text-blue-500 hover:underline">
                      {emp.name}
                    </Link>
                  </TableCell>
                  <TableCell>
                    <Link href={`/${routeBoxSlug}/employee-detail?emp_id=${emp.id}`} className="text-blue-500 hover:underline">
                      {emp.email}
                    </Link>
                  </TableCell>
                  <TableCell>
                    <Link href={`/${routeBoxSlug}/employee-detail?emp_id=${emp.id}`} className="text-blue-500 hover:underline">
                      {emp.phone || '-'}
                    </Link>
                  </TableCell>
                  <TableCell>{emp.department_name || '-'}</TableCell>
                  <TableCell>{emp.designation_name || '-'}</TableCell>
                  <TableCell>
                    <span className="capitalize">{emp.staff_role || '-'}</span>
                  </TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-2">
                      <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => openEditModal(emp)}
                      >
                        <Pencil className="w-4 h-4 text-blue-500" />
                      </Button>
                      <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => {
                          setEmployeeToDelete(emp.id);
                          setIsDeleteModalOpen(true);
                        }}
                      >
                        <Trash2 className="w-4 h-4 text-red-500" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>
        </>
      ) : (
        <>
          <div className="flex justify-between items-center mb-6 border-b pb-4">
            <div>
              <h1 className="text-2xl font-bold">
                {form.watch('id') 
                  ? `Edit Employee Employee ID : ${form.watch('employee_id')}` 
                  : form.watch('employee_id') 
                    ? `Add New Employee Employee ID : ${form.watch('employee_id')}`
                    : 'Add New Employee'
                }
              </h1>
              <p className="text-muted-foreground">Fill out the employee information below.</p>
            </div>
            <Button variant="outline" onClick={() => setIsModalOpen(false)} className="gap-2">
              <ArrowLeft className="w-4 h-4" /> Back to List
            </Button>
          </div>

          <div className="bg-card p-6 rounded-lg border shadow-sm">
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
              <div className="mb-6 flex flex-col gap-2">
                <Label>Profile Image</Label>
                <div className="flex items-center gap-4">
                  <div className="relative w-24 h-24 rounded overflow-hidden bg-muted flex items-center justify-center border">
                    {croppedImageBase64 ? (
                      <img src={croppedImageBase64} alt="Profile" className="w-full h-full object-cover" />
                    ) : (
                      <span className="text-muted-foreground text-xs">No image</span>
                    )}
                  </div>
                  <div>
                    <Input 
                      type="file" 
                      accept="image/*" 
                      className="max-w-xs cursor-pointer" 
                      onChange={(e) => {
                        if (e.target.files && e.target.files.length > 0) {
                          const file = e.target.files[0];
                          const reader = new FileReader();
                          reader.onload = () => {
                            setSelectedImage(reader.result as string);
                            setIsCropperOpen(true);
                          };
                          reader.readAsDataURL(file);
                        }
                        e.target.value = '';
                      }} 
                    />
                    <p className="text-xs text-muted-foreground mt-1">Select an image to crop and upload.</p>
                  </div>
                </div>
              </div>

              <Tabs defaultValue="basic" className="w-full">
              <TabsList className="mb-4">
                <TabsTrigger value="basic">Basic Information</TabsTrigger>
                <TabsTrigger value="permissions">Permissions</TabsTrigger>
              </TabsList>
              
              <TabsContent value="basic" className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label>First Name <span className="text-red-500">*</span></Label>
                    <Controller
                      name="name"
                      control={control}
                      render={({ field, fieldState }) => (
                        <div>
                          <Input {...field} placeholder="Enter First Name" />
                          {fieldState.error && <span className="text-xs text-red-500">{fieldState.error.message}</span>}
                        </div>
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Last Name</Label>
                    <Controller
                      name="last_name"
                      control={control}
                      render={({ field }) => (
                        <Input {...field} value={field.value || ''} placeholder="Enter Last Name" />
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Gender <span className="text-red-500">*</span></Label>
                    <Controller
                      name="gender"
                      control={control}
                      render={({ field, fieldState }) => (
                        <div>
                          <Combobox value={field.value} onValueChange={field.onChange}>
                            <ComboboxInput placeholder="Select Gender" className="w-full" />
                            <ComboboxContent>
                              <ComboboxList>
                                <ComboboxItem value="male">Male</ComboboxItem>
                                <ComboboxItem value="female">Female</ComboboxItem>
                                <ComboboxItem value="other">Other</ComboboxItem>
                              </ComboboxList>
                            </ComboboxContent>
                          </Combobox>
                          {fieldState.error && <span className="text-xs text-red-500">{fieldState.error.message}</span>}
                        </div>
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Email <span className="text-red-500">*</span></Label>
                    <Controller
                      name="email"
                      control={control}
                      render={({ field, fieldState }) => (
                        <div>
                          <Input {...field} type="email" placeholder="Email" />
                          {fieldState.error && <span className="text-xs text-red-500">{fieldState.error.message}</span>}
                        </div>
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Mobile Number <span className="text-red-500">*</span></Label>
                    <Controller
                      name="phone"
                      control={control}
                      render={({ field, fieldState }) => (
                        <div>
                          <Input {...field} placeholder="Mobile Number" />
                          {fieldState.error && <span className="text-xs text-red-500">{fieldState.error.message}</span>}
                        </div>
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Joining Date <span className="text-red-500">*</span></Label>
                    <Controller
                      name="joining_date"
                      control={control}
                      render={({ field, fieldState }) => (
                        <div>
                          <Input {...field} type="date" />
                          {fieldState.error && <span className="text-xs text-red-500">{fieldState.error.message}</span>}
                        </div>
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Employee Type</Label>
                    <Controller
                      name="emp_type"
                      control={control}
                      render={({ field }) => (
                        <SearchableSelect 
                          items={employeeTypes} 
                          value={field.value} 
                          onChange={field.onChange} 
                          placeholder="Select Type" 
                          emptyText="No types found" 
                        />
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Department</Label>
                    <Controller
                      name="department"
                      control={control}
                      render={({ field }) => (
                        <SearchableSelect 
                          items={departments} 
                          value={field.value} 
                          onChange={field.onChange} 
                          placeholder="Select Department" 
                          emptyText="No departments found" 
                        />
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Designation</Label>
                    <Controller
                      name="designation"
                      control={control}
                      render={({ field }) => {
                        const selectedTeam = designations.find((t: any) => String(t.id) === String(field.value));
                        return (
                          <div className="flex gap-2">
                            <Input 
                              readOnly 
                              placeholder="Select Designation" 
                              value={selectedTeam ? `${selectedTeam.name} (Team)` : ''} 
                              className="bg-gray-50 cursor-not-allowed flex-1"
                            />
                            <Button 
                              type="button" 
                              variant="outline" 
                              onClick={() => {
                                if (!watchDepartment) {
                                  toast.error('Please select a department first');
                                  return;
                                }
                                setIsDesignationModalOpen(true);
                              }}
                            >
                              <i className="ti ti-sitemap mr-2"></i> Select
                            </Button>
                            <DesignationTreeModal 
                              isOpen={isDesignationModalOpen}
                              onClose={() => setIsDesignationModalOpen(false)}
                              departmentId={watchDepartment}
                              departments={departments}
                              currentValue={field.value}
                              onSelect={(id: number) => {
                                setValue('designation', String(id), { shouldValidate: true, shouldDirty: true });
                              }}
                            />
                          </div>
                        );
                      }}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Reporting Manager</Label>
                    <Controller
                      name="reporting_manager_id"
                      control={control}
                      render={({ field }) => (
                        <SearchableSelect 
                          items={managers} 
                          value={field.value} 
                          onChange={field.onChange} 
                          placeholder="Select Manager" 
                          emptyText="No managers found" 
                        />
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Can Login Anytime</Label>
                    <Controller
                      name="can_login_anytime"
                      control={control}
                      render={({ field }) => (
                        <div className="flex items-center gap-4 mt-2">
                          <label className="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" checked={field.value === '1'} onChange={() => field.onChange('1')} className="accent-primary" /> Yes
                          </label>
                          <label className="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" checked={field.value === '0'} onChange={() => field.onChange('0')} className="accent-primary" /> No
                          </label>
                        </div>
                      )}
                    />
                  </div>

                  <div className="space-y-2">
                    <Label>Enable Call Logs</Label>
                    <Controller
                      name="call_logs"
                      control={control}
                      render={({ field }) => (
                        <div className="flex items-center gap-4 mt-2">
                          <label className="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" checked={field.value === '1'} onChange={() => field.onChange('1')} className="accent-primary" /> Yes
                          </label>
                          <label className="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" checked={field.value === '0'} onChange={() => field.onChange('0')} className="accent-primary" /> No
                          </label>
                        </div>
                      )}
                    />
                  </div>
                </div>

                <div className="space-y-2 mt-4">
                  <Label>Web Punch-In/Punch Out Allowed Days</Label>
                  <Controller
                    name="working_web_days"
                    control={control}
                    render={({ field }) => (
                      <div className="flex flex-wrap gap-4 mt-2">
                        {['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'].map((day) => (
                          <label key={day} className="flex items-center gap-2 text-sm cursor-pointer">
                            <input 
                              type="checkbox" 
                              checked={(field.value || []).includes(day)} 
                              onChange={(e) => {
                                const checked = e.target.checked;
                                const val = field.value || [];
                                field.onChange(checked ? [...val, day] : val.filter(d => d !== day));
                              }} 
                              className="rounded border-gray-300 accent-primary" 
                            />
                            {day}
                          </label>
                        ))}
                      </div>
                    )}
                  />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 border rounded p-4 mt-4 bg-muted/20">
                  <Controller
                    name="exlude_ip_restriction"
                    control={control}
                    render={({ field }) => (
                      <label className="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" checked={field.value} onChange={(e) => field.onChange(e.target.checked)} className="rounded border-gray-300 accent-primary" />
                        Exclude from IP Restriction
                      </label>
                    )}
                  />
                  <Controller
                    name="exclude_round_robin"
                    control={control}
                    render={({ field }) => (
                      <label className="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" checked={field.value} onChange={(e) => field.onChange(e.target.checked)} className="rounded border-gray-300 accent-primary" />
                        Exclude from Round Robin
                      </label>
                    )}
                  />
                  <Controller
                    name="user_box_timing"
                    control={control}
                    render={({ field }) => (
                      <label className="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" checked={field.value} onChange={(e) => field.onChange(e.target.checked)} className="rounded border-gray-300 accent-primary" />
                        Use Box Login Timing
                      </label>
                    )}
                  />
                </div>

                {!watchUserBoxTiming && (
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 p-4 border rounded bg-muted/10">
                    <div className="space-y-2">
                      <Label>Day Start Time</Label>
                      <Controller
                        name="day_start_time"
                        control={control}
                        render={({ field }) => (
                          <Input {...field} value={field.value || ''} type="time" />
                        )}
                      />
                    </div>
                    <div className="space-y-2">
                      <Label>Day End Time</Label>
                      <Controller
                        name="day_end_time"
                        control={control}
                        render={({ field }) => (
                          <Input {...field} value={field.value || ''} type="time" />
                        )}
                      />
                    </div>
                    <div className="space-y-2">
                      <Label>Allowed IPs</Label>
                      <Controller
                        name="allowed_ips"
                        control={control}
                        render={({ field }) => (
                          <Input {...field} value={field.value || ''} placeholder="e.g. 192.168.1.1" />
                        )}
                      />
                    </div>
                  </div>
                )}
              </TabsContent>

              <TabsContent value="permissions">
                <Controller
                  control={form.control}
                  name="permissions"
                  render={({ field }) => (
                    <PermissionsTable 
                      value={field.value || []} 
                      onChange={field.onChange} 
                    />
                  )}
                />
              </TabsContent>
            </Tabs>

            <div className="flex justify-end gap-2 mt-6 border-t pt-4">
              <Button type="button" variant="outline" onClick={() => setIsModalOpen(false)}>
                Cancel
              </Button>
              <Button type="submit">
                Save
              </Button>
            </div>
          </form>
          </div>
        </>
      )}

      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Confirm Deletion</DialogTitle>
          </DialogHeader>
          <div className="py-4">
            Are you sure you want to delete this employee? This action cannot be undone.
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsDeleteModalOpen(false)}>
              Cancel
            </Button>
            <Button variant="destructive" onClick={handleDelete}>
              Delete
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {isCropperOpen && selectedImage && (
        <ImageCropper
          imageSrc={selectedImage as string}
          onClose={() => setIsCropperOpen(false)}
          onCropComplete={(croppedBase64) => {
            setCroppedImageBase64(croppedBase64);
            setIsCropperOpen(false);
          }}
        />
      )}
    </div>
  );
}
