'use client';

import { useState, useEffect } from 'react';
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
import { Textarea } from '@/components/ui/textarea';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog';
import {
  Combobox,
  ComboboxInput,
  ComboboxContent,
  ComboboxItem,
  ComboboxList
} from '@/components/ui/combobox';
import { Checkbox } from '@/components/ui/checkbox';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { useForm, Controller, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { toast } from 'sonner';
import { apiClient } from '@/lib/api-client';
import { Pencil, Trash2, Plus, Search, ChevronDown, Filter, ArrowUpDown, Calendar, Settings, FileType, FileText } from 'lucide-react';
import { useParams } from 'next/navigation';
import { Badge } from '@/components/ui/badge';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination";

const contactSchema = z.object({
  id: z.number().optional(),
  name: z.string().min(1, 'Name is required'),
  email: z.string().optional(),
  phone: z.string().optional(),
  designation: z.string().optional()
});

const formSchema = z.object({
  id: z.number().optional(),
  name: z.string().min(1, 'Name is required'),
  project_custom_id: z.string().min(1, 'Project ID is required'),
  project_type_id: z.string().min(1, 'Project Type is required'),
  client_name: z.string().min(1, 'Client is required'),
  project_category_id: z.string().min(1, 'Category is required'),
  project_timing: z.string().min(1, 'Project Timing is required'),
  price: z.string().or(z.number()),
  amount: z.string().or(z.number()),
  total: z.string().or(z.number()),
  responsible_persons: z.array(z.number()).min(1, 'Responsible persons are required'),
  team_leaders: z.array(z.number()).min(1, 'Team leaders are required'),
  start_date: z.string().min(1, 'Start Date is required'),
  due_date: z.string().min(1, 'Due Date is required'),
  priority: z.string().optional(),
  status: z.string().optional(),
  description: z.string().min(1, 'Description is required'),
  contacts: z.array(contactSchema).optional()
});

type FormValues = z.infer<typeof formSchema>;

export default function ProjectsPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [projects, setProjects] = useState<any[]>([]);
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 20;
  
  const [projectTypes, setProjectTypes] = useState<any[]>([]);
  const [projectCategories, setProjectCategories] = useState<any[]>([]);
  const [users, setUsers] = useState<any[]>([]);
  const [leads, setLeads] = useState<any[]>([]);
  const [leadSearchQuery, setLeadSearchQuery] = useState('');
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [itemToDelete, setItemToDelete] = useState<number | null>(null);

  const [filterTitle, setFilterTitle] = useState('');

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: '',
      project_custom_id: '',
      project_type_id: '',
      client_name: '',
      project_category_id: '',
      project_timing: '',
      price: '',
      amount: '',
      total: '',
      responsible_persons: [],
      team_leaders: [],
      start_date: '',
      due_date: '',
      priority: 'High',
      status: 'Active',
      description: '',
      contacts: [{ name: '', email: '', phone: '', designation: '' }]
    },
  });

  const { fields: contactFields, append: appendContact, remove: removeContact } = useFieldArray({
    control: form.control,
    name: "contacts"
  });

  const fetchOptions = async () => {
    try {
      const [typesRes, categoriesRes, usersRes] = await Promise.all([
        apiClient.get(`/${routeBoxSlug}/manage-users/projects/options/project-types`).catch(() => ({ data: { data: [] } })),
        apiClient.get(`/${routeBoxSlug}/manage-users/projects/options/project-categories`).catch(() => ({ data: { data: [] } })),
        apiClient.get(`/${routeBoxSlug}/manage-users/projects/options/users`).catch(() => ({ data: { data: [] } }))
      ]);
      setProjectTypes(Array.isArray(typesRes) ? typesRes : (typesRes.data?.data || typesRes.data || []));
      setProjectCategories(Array.isArray(categoriesRes) ? categoriesRes : (categoriesRes.data?.data || categoriesRes.data || []));
      setUsers(Array.isArray(usersRes) ? usersRes : (usersRes.data?.data || usersRes.data || []));
    } catch (error) {
      console.error('Failed to fetch options:', error);
    }
  };

  const fetchProjects = async () => {
    try {
      setIsLoading(true);
      // Adjust endpoint if necessary, using manage-users/projects-json as standard for fetching projects.
      const response = await apiClient.get(`/${routeBoxSlug}/manage-users/projects-json`);
      setProjects(Array.isArray(response) ? response : (response?.data?.data || response?.data || []));
    } catch (error) {
      console.error('Failed to fetch projects:', error);
      toast.error('Failed to load projects');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (routeBoxSlug) {
      fetchOptions();
      fetchProjects();
    }
  }, [routeBoxSlug]);

  useEffect(() => {
    if (!routeBoxSlug) return;
    const fetchLeads = async () => {
      try {
        const query = leadSearchQuery ? `?q=${encodeURIComponent(leadSearchQuery)}` : '';
        const res = await apiClient.get(`/${routeBoxSlug}/manage-users/projects/options/leads${query}`).catch(() => ({ data: { data: [] } }));
        setLeads(Array.isArray(res) ? res : (res.data?.data || res.data || []));
      } catch (e) {}
    };
    const timer = setTimeout(fetchLeads, 300);
    return () => clearTimeout(timer);
  }, [routeBoxSlug, leadSearchQuery]);

  const handleEdit = (item: any) => {
    let rp = [];
    let tl = [];
    try {
      rp = typeof item.responsible_persons === 'string' ? JSON.parse(item.responsible_persons) : (item.responsible_persons || []);
      tl = typeof item.team_leaders === 'string' ? JSON.parse(item.team_leaders) : (item.team_leaders || []);
    } catch(e) {}

    form.reset({
      id: item.id,
      name: item.name,
      project_custom_id: item.project_custom_id || '',
      project_type_id: String(item.project_type_id || ''),
      client_name: item.client_name || '',
      project_category_id: String(item.project_category_id || ''),
      project_timing: item.project_timing || '',
      price: item.price || '',
      amount: item.amount || '',
      total: item.total || '',
      responsible_persons: rp,
      team_leaders: tl,
      start_date: item.start_date ? item.start_date.split('T')[0] : '',
      due_date: item.due_date ? item.due_date.split('T')[0] : '',
      priority: item.priority || 'High',
      status: item.status || 'Active',
      description: item.description || '',
      contacts: item.contacts && item.contacts.length > 0 ? item.contacts : [{ name: '', email: '', phone: '', designation: '' }]
    });
    setIsModalOpen(true);
  };

  const confirmDelete = (id: number) => {
    setItemToDelete(id);
    setIsDeleteModalOpen(true);
  };

  const handleDelete = async () => {
    if (!itemToDelete) return;
    
    try {
      await apiClient.delete(`/${routeBoxSlug}/manage-users/projects/${itemToDelete}`);
      toast.success('Project deleted successfully');
      setIsDeleteModalOpen(false);
      setItemToDelete(null);
      fetchProjects();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to delete project');
    }
  };

  const onSubmit = async (data: FormValues) => {
    try {
      if (data.id) {
        await apiClient.put(`/${routeBoxSlug}/manage-users/projects/${data.id}`, data);
        toast.success('Project updated successfully');
      } else {
        await apiClient.post(`/${routeBoxSlug}/manage-users/projects`, data);
        toast.success('Project created successfully');
      }
      
      setIsModalOpen(false);
      form.reset();
      fetchProjects();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to save project');
    }
  };

  const filteredProjects = projects.filter(p => p.name?.toLowerCase().includes(filterTitle.toLowerCase()));
  const totalPages = Math.ceil(filteredProjects.length / itemsPerPage);
  const paginatedProjects = filteredProjects.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  const renderPaginationItems = () => {
    if (totalPages <= 1) return null;
    const pages = [];
    pages.push(1);
    if (currentPage > 3) pages.push('ellipsis-start');
    for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
      pages.push(i);
    }
    if (currentPage < totalPages - 2) pages.push('ellipsis-end');
    if (totalPages > 1) pages.push(totalPages);
    
    return pages.map((page, idx) => {
      if (typeof page === 'string') {
        return (
          <PaginationItem key={`${page}-${idx}`}>
            <PaginationEllipsis />
          </PaginationItem>
        );
      }
      return (
        <PaginationItem key={page}>
          <PaginationLink 
            isActive={page === currentPage} 
            onClick={() => setCurrentPage(page)}
            className="cursor-pointer"
          >
            {page}
          </PaginationLink>
        </PaginationItem>
      );
    });
  };

  return (
    <div className="p-6 bg-slate-50 min-h-screen">
      {/* Top Header */}
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
        <div className="flex items-center">
          <h4 className="text-xl font-bold flex items-center gap-2">
            Projects
            <Badge variant="outline" className="rounded-full bg-slate-100 text-slate-500">{projects.length}</Badge>
          </h4>
        </div>

        <div className="flex items-center gap-3 flex-wrap">
          <div className="icon-form">
            <div className="relative">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" />
              <Input 
                className="pl-9 bg-white" 
                placeholder="Search Projects" 
                value={filterTitle}
                onChange={(e) => {
                  setFilterTitle(e.target.value);
                  setCurrentPage(1);
                }}
              />
            </div>
          </div>

          <DropdownMenu>
            <DropdownMenuTrigger className="inline-flex items-center justify-center whitespace-nowrap rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-3 text-sm bg-white">
              <FileType className="mr-2 h-4 w-4" /> Export
            </DropdownMenuTrigger>
            <DropdownMenuContent>
              <DropdownMenuItem onClick={() => window.open(`/${routeBoxSlug}/manage-users/projects/export/pdf`, '_blank')}>
                <FileText className="mr-2 h-4 w-4 text-red-500" /> Export as PDF
              </DropdownMenuItem>
              <DropdownMenuItem onClick={() => window.open(`/${routeBoxSlug}/manage-users/projects/export/excel`, '_blank')}>
                <FileType className="mr-2 h-4 w-4 text-green-500" /> Export as Excel
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>

          <Button onClick={() => {
            form.reset({
              name: '',
              project_custom_id: '',
              project_type_id: '',
              client_name: '',
              project_category_id: '',
              project_timing: '',
              price: '',
              amount: '',
              total: '',
              responsible_persons: [],
              team_leaders: [],
              start_date: '',
              due_date: '',
              priority: 'High',
              status: 'Active',
              description: '',
              contacts: [{ name: '', email: '', phone: '', designation: '' }]
            });
            setIsModalOpen(true);
          }} className="bg-primary hover:bg-primary/90">
            <Plus className="mr-2 h-4 w-4" /> Add New Project
          </Button>
        </div>
      </div>

      {/* Projects List */}
      <div className="bg-white rounded-md border p-4 shadow-sm min-h-[500px]">
        {isLoading ? (
          <div className="text-center py-12 text-muted-foreground">Loading projects...</div>
        ) : (
          <div className="table-responsive">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Sr. No.</TableHead>
                  <TableHead>Project ID</TableHead>
                  <TableHead>Name</TableHead>
                  <TableHead>Client</TableHead>
                  <TableHead>Priority</TableHead>
                  <TableHead>Start Date</TableHead>
                  <TableHead>End Date</TableHead>
                  <TableHead>Type</TableHead>
                  <TableHead>Category</TableHead>
                  <TableHead>Price</TableHead>
                  <TableHead>Total</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Responsible Persons</TableHead>
                  <TableHead>Team Members</TableHead>
                  <TableHead className="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {paginatedProjects.map((item, index) => (
                  <TableRow key={item.id}>
                    <TableCell>{index + 1}</TableCell>
                    <TableCell>{item.project_custom_id || '-'}</TableCell>
                    <TableCell className="font-medium text-primary cursor-pointer hover:underline" onClick={() => handleEdit(item)}>{item.name}</TableCell>
                    <TableCell>{item.client_name || '-'}</TableCell>
                    <TableCell>
                      <Badge variant={item.priority === 'High' ? 'destructive' : item.priority === 'Medium' ? 'default' : 'secondary'}>
                        {item.priority || 'Medium'}
                      </Badge>
                    </TableCell>
                    <TableCell>{item.start_date ? new Date(item.start_date).toLocaleDateString() : '-'}</TableCell>
                    <TableCell>{item.due_date ? new Date(item.due_date).toLocaleDateString() : '-'}</TableCell>
                    <TableCell>
                      {projectTypes.find((t: any) => String(t.id) === String(item.project_type_id))?.name || item.project_type_id || '-'}
                    </TableCell>
                    <TableCell>
                      {projectCategories.find((c: any) => String(c.id) === String(item.project_category_id))?.name || item.project_category_id || '-'}
                    </TableCell>
                    <TableCell>{item.price ? `$${item.price}` : '-'}</TableCell>
                    <TableCell>{item.total ? `$${item.total}` : '-'}</TableCell>
                    <TableCell>
                      {item.status === 'Active' ? (
                        <Badge className="bg-green-100 text-green-700 hover:bg-green-100 border-green-200">Active</Badge>
                      ) : (
                        <Badge variant="secondary">{item.status || 'Inactive'}</Badge>
                      )}
                    </TableCell>
                    <TableCell>
                      {item.responsible_persons ? (
                        (() => {
                          try {
                            const ids = typeof item.responsible_persons === 'string' ? JSON.parse(item.responsible_persons) : item.responsible_persons;
                            const names = ids.map((id: number) => {
                              const user = users.find((u: any) => Number(u.id) === Number(id));
                              return user ? user.name : 'Unknown';
                            });
                            return names.length > 0 ? names.join(', ') : '-';
                          } catch(e) {
                            return '-';
                          }
                        })()
                      ) : '-'}
                    </TableCell>
                    <TableCell>
                      {item.team_leaders ? (
                        (() => {
                          try {
                            const ids = typeof item.team_leaders === 'string' ? JSON.parse(item.team_leaders) : item.team_leaders;
                            const names = ids.map((id: number) => {
                              const user = users.find((u: any) => Number(u.id) === Number(id));
                              return user ? user.name : 'Unknown';
                            });
                            return names.length > 0 ? names.join(', ') : '-';
                          } catch(e) {
                            return '-';
                          }
                        })()
                      ) : '-'}
                    </TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-2">
                        <Button variant="ghost" size="icon" onClick={() => handleEdit(item)}>
                          <Pencil className="h-4 w-4" />
                        </Button>
                        <Button variant="ghost" size="icon" onClick={() => confirmDelete(item.id)}>
                          <Trash2 className="h-4 w-4 text-destructive" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
                {paginatedProjects.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={15} className="text-center py-6 text-muted-foreground">
                      No projects found.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
            
            {totalPages > 0 && (
              <div className="p-4 border-t flex flex-col md:flex-row items-center justify-between gap-4 mt-4">
                <div className="text-sm text-muted-foreground whitespace-nowrap">
                  Showing {(currentPage - 1) * itemsPerPage + 1} to {Math.min(currentPage * itemsPerPage, filteredProjects.length)} of {filteredProjects.length} records
                </div>
                <div className="flex-1 w-full">
                  <Pagination className="justify-end">
                    <PaginationContent>
                      <PaginationItem>
                        <PaginationPrevious 
                          onClick={() => currentPage > 1 && setCurrentPage(currentPage - 1)}
                          className={currentPage <= 1 ? "pointer-events-none opacity-50" : "cursor-pointer"} 
                        />
                      </PaginationItem>
                      {renderPaginationItems()}
                      <PaginationItem>
                        <PaginationNext 
                          onClick={() => currentPage < totalPages && setCurrentPage(currentPage + 1)}
                          className={currentPage >= totalPages ? "pointer-events-none opacity-50" : "cursor-pointer"}
                        />
                      </PaginationItem>
                    </PaginationContent>
                  </Pagination>
                </div>
              </div>
            )}
          </div>
        )}
      </div>

      {/* Add/Edit Modal */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>{form.watch('id') ? 'Edit Project' : 'Add New Project'}</DialogTitle>
          </DialogHeader>
          
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
              
              <div className="col-span-12 space-y-2">
                <Label>Name <span className="text-destructive">*</span></Label>
                <Input {...form.register('name')} />
                {form.formState.errors.name && <p className="text-sm text-destructive">{form.formState.errors.name.message}</p>}
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Project ID <span className="text-destructive">*</span></Label>
                <Input {...form.register('project_custom_id')} />
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Project Type <span className="text-destructive">*</span></Label>
                <Controller
                  control={form.control}
                  name="project_type_id"
                  render={({ field }) => (
                    <Combobox value={field.value} onValueChange={field.onChange}>
                      <ComboboxInput placeholder="Choose" />
                      <ComboboxContent>
                        <ComboboxList>
                          {projectTypes.map(t => (
                            <ComboboxItem key={t.id} value={String(t.id)}>{t.name}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Client <span className="text-destructive">*</span></Label>
                <Controller
                  control={form.control}
                  name="client_name"
                  render={({ field }) => (
                    <Combobox value={field.value} onValueChange={field.onChange}>
                      <ComboboxInput 
                        placeholder="Search & Select" 
                        onChange={(e) => setLeadSearchQuery(e.target.value)}
                      />
                      <ComboboxContent>
                        <ComboboxList>
                          {field.value && !leads.some(l => ((l.name || '') + ' ' + (l.last_name || '')).trim() === field.value) && (
                            <ComboboxItem value={field.value}>{field.value}</ComboboxItem>
                          )}
                          {leads.map(l => {
                            const name = (l.name || '') + ' ' + (l.last_name || '');
                            return <ComboboxItem key={l.id || name.trim()} value={name.trim()}>{name.trim()}</ComboboxItem>;
                          })}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Category <span className="text-destructive">*</span></Label>
                <Controller
                  control={form.control}
                  name="project_category_id"
                  render={({ field }) => (
                    <Combobox value={field.value} onValueChange={field.onChange}>
                      <ComboboxInput placeholder="Select" />
                      <ComboboxContent>
                        <ComboboxList>
                          {projectCategories.map(c => (
                            <ComboboxItem key={c.id} value={String(c.id)}>{c.name}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
              </div>

              <div className="col-span-12 md:col-span-3 space-y-2">
                <Label>Project Timing <span className="text-destructive">*</span></Label>
                <Input {...form.register('project_timing')} placeholder="e.g. 1 Month" />
              </div>
              <div className="col-span-12 md:col-span-3 space-y-2">
                <Label>Price <span className="text-destructive">*</span></Label>
                <Input type="number" step="0.01" {...form.register('price')} />
              </div>
              <div className="col-span-12 md:col-span-3 space-y-2">
                <Label>Amount <span className="text-destructive">*</span></Label>
                <Input type="number" step="0.01" {...form.register('amount')} />
              </div>
              <div className="col-span-12 md:col-span-3 space-y-2">
                <Label>Total <span className="text-destructive">*</span></Label>
                <Input type="number" step="0.01" {...form.register('total')} />
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Responsible Persons <span className="text-destructive">*</span></Label>
                <Controller
                  control={form.control}
                  name="responsible_persons"
                  render={({ field }) => (
                    <Popover>
                      <PopoverTrigger className="w-full text-left justify-between inline-flex items-center rounded-md border border-input bg-background h-10 px-3 py-2 text-sm">
                        {field.value.length > 0 ? `${field.value.length} selected` : 'Select persons'}
                        <ChevronDown className="h-4 w-4 opacity-50" />
                      </PopoverTrigger>
                      <PopoverContent className="w-[400px] p-0" align="start">
                        <div className="max-h-60 overflow-y-auto p-2 space-y-1">
                          {users.map((user) => (
                            <div key={user.id} className="flex items-center space-x-2 p-2 hover:bg-slate-100 rounded-md cursor-pointer" onClick={() => {
                              const isSelected = field.value.includes(user.id);
                              field.onChange(isSelected ? field.value.filter(id => id !== user.id) : [...field.value, user.id]);
                            }}>
                              <Checkbox checked={field.value.includes(user.id)} />
                              <Label className="cursor-pointer font-normal">{user.name}</Label>
                            </div>
                          ))}
                        </div>
                      </PopoverContent>
                    </Popover>
                  )}
                />
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Team Leader <span className="text-destructive">*</span></Label>
                <Controller
                  control={form.control}
                  name="team_leaders"
                  render={({ field }) => (
                    <Popover>
                      <PopoverTrigger className="w-full text-left justify-between inline-flex items-center rounded-md border border-input bg-background h-10 px-3 py-2 text-sm">
                        {field.value.length > 0 ? `${field.value.length} selected` : 'Select leaders'}
                        <ChevronDown className="h-4 w-4 opacity-50" />
                      </PopoverTrigger>
                      <PopoverContent className="w-[400px] p-0" align="start">
                        <div className="max-h-60 overflow-y-auto p-2 space-y-1">
                          {users.map((user) => (
                            <div key={user.id} className="flex items-center space-x-2 p-2 hover:bg-slate-100 rounded-md cursor-pointer" onClick={() => {
                              const isSelected = field.value.includes(user.id);
                              field.onChange(isSelected ? field.value.filter(id => id !== user.id) : [...field.value, user.id]);
                            }}>
                              <Checkbox checked={field.value.includes(user.id)} />
                              <Label className="cursor-pointer font-normal">{user.name}</Label>
                            </div>
                          ))}
                        </div>
                      </PopoverContent>
                    </Popover>
                  )}
                />
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Start Date <span className="text-destructive">*</span></Label>
                <Input type="date" {...form.register('start_date')} />
              </div>
              
              <div className="col-span-6 space-y-2">
                <Label>Due Date <span className="text-destructive">*</span></Label>
                <Input type="date" {...form.register('due_date')} />
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Priority</Label>
                <Controller
                  control={form.control}
                  name="priority"
                  render={({ field }) => (
                    <Combobox value={field.value} onValueChange={field.onChange}>
                      <ComboboxInput placeholder="Select" />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxItem value="High">High</ComboboxItem>
                          <ComboboxItem value="Medium">Medium</ComboboxItem>
                          <ComboboxItem value="Low">Low</ComboboxItem>
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
              </div>

              <div className="col-span-6 space-y-2">
                <Label>Status</Label>
                <Controller
                  control={form.control}
                  name="status"
                  render={({ field }) => (
                    <Combobox value={field.value} onValueChange={field.onChange}>
                      <ComboboxInput placeholder="Select" />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxItem value="Active">Active</ComboboxItem>
                          <ComboboxItem value="Inactive">Inactive</ComboboxItem>
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
              </div>

              <div className="col-span-12 space-y-2">
                <Label>Description <span className="text-destructive">*</span></Label>
                <Textarea className="resize-none h-24" {...form.register('description')} />
              </div>

              <div className="col-span-12">
                <div className="flex justify-between items-center mb-4 mt-2">
                  <h5 className="font-semibold text-lg">Project Contacts</h5>
                  <Button type="button" size="sm" variant="outline" onClick={() => appendContact({ name: '', email: '', phone: '', designation: '' })}>
                    <Plus className="h-4 w-4 mr-2" /> Add
                  </Button>
                </div>
                <div className="border rounded-md overflow-hidden">
                  <Table>
                    <TableHeader className="bg-slate-50">
                      <TableRow>
                        <TableHead>Name <span className="text-destructive">*</span></TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead>Designation</TableHead>
                        <TableHead className="w-[80px]">Action</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {contactFields.map((field, index) => (
                        <TableRow key={field.id}>
                          <TableCell className="p-2">
                            <Input {...form.register(`contacts.${index}.name`)} />
                          </TableCell>
                          <TableCell className="p-2">
                            <Input type="email" {...form.register(`contacts.${index}.email`)} />
                          </TableCell>
                          <TableCell className="p-2">
                            <Input {...form.register(`contacts.${index}.phone`)} />
                          </TableCell>
                          <TableCell className="p-2">
                            <Input {...form.register(`contacts.${index}.designation`)} />
                          </TableCell>
                          <TableCell className="p-2">
                            <Button type="button" variant="destructive" size="icon" onClick={() => removeContact(index)}>
                              <Trash2 className="h-4 w-4" />
                            </Button>
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </div>
              </div>
            </div>

            <DialogFooter className="pt-4 border-t mt-4">
              <Button type="button" variant="outline" onClick={() => setIsModalOpen(false)}>
                Cancel
              </Button>
              <Button type="submit">
                {form.watch('id') ? 'Save Changes' : 'Create'}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Modal */}
      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Project</DialogTitle>
          </DialogHeader>
          <div className="py-4 text-center">
            <div className="mx-auto w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
              <Trash2 className="h-8 w-8" />
            </div>
            <h4 className="text-xl font-bold mb-2">Remove Project?</h4>
            <p className="text-muted-foreground">Are you sure you want to remove the project you selected?</p>
          </div>
          <DialogFooter className="flex justify-center sm:justify-center">
            <Button variant="outline" onClick={() => setIsDeleteModalOpen(false)}>
              Cancel
            </Button>
            <Button variant="destructive" onClick={handleDelete}>
              Yes, Delete it
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
