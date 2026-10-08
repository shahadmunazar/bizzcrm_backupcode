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
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { toast } from 'sonner';
import { apiClient } from '@/lib/api-client';
import { Pencil, Trash2, Plus, Search } from 'lucide-react';
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

const formSchema = z.object({
  id: z.number().optional(),
  project_id: z.string().min(1, 'Project is required'),
  name: z.string().min(1, 'Name is required'),
  email: z.string().email('Valid email is required'),
  phone: z.string().min(1, 'Phone is required'),
  designation: z.string().optional(),
});

type FormValues = z.infer<typeof formSchema>;

export default function ProjectContactsPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [contacts, setContacts] = useState<any[]>([]);
  const [projects, setProjects] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [itemToDelete, setItemToDelete] = useState<number | null>(null);

  const [filterTitle, setFilterTitle] = useState('');

  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 10;

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      project_id: '',
      name: '',
      email: '',
      phone: '',
      designation: '',
    },
  });

  const fetchData = async () => {
    try {
      setIsLoading(true);
      
      const [contactsRes, projectsRes] = await Promise.all([
        apiClient.get(`/${routeBoxSlug}/manage-users/projects/contacts`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }),
        apiClient.get(`/${routeBoxSlug}/manage-users/projects-json`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
      ]);

      const contactsData = contactsRes?.data?.data || contactsRes?.data || [];
      const projectsData = projectsRes?.data?.data || projectsRes?.data || [];
      
      setContacts(contactsData);
      setProjects(projectsData);
      setCurrentPage(1); // Reset page
    } catch (error) {
      console.error('Failed to fetch data:', error);
      toast.error('Failed to load data');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (routeBoxSlug) {
      fetchData();
    }
  }, [routeBoxSlug]);

  const handleEdit = (item: any) => {
    form.reset({
      id: item.id,
      project_id: String(item.project_id || ''),
      name: item.name,
      email: item.email,
      phone: item.phone,
      designation: item.designation || '',
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
      await apiClient.delete(`/${routeBoxSlug}/manage-users/projects/contacts/${itemToDelete}`);
      toast.success('Project contact deleted successfully');
      setIsDeleteModalOpen(false);
      setItemToDelete(null);
      fetchData();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to delete project contact');
    }
  };

  const onSubmit = async (data: FormValues) => {
    try {
      if (data.id) {
        await apiClient.post(`/${routeBoxSlug}/manage-users/projects/contacts/${data.id}`, data);
        toast.success('Project contact updated successfully');
      } else {
        await apiClient.post(`/${routeBoxSlug}/manage-users/projects/contacts`, data);
        toast.success('Project contact created successfully');
      }
      
      setIsModalOpen(false);
      form.reset();
      fetchData();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to save project contact');
    }
  };

  const filteredContacts = contacts.filter(t => t.name?.toLowerCase().includes(filterTitle.toLowerCase()));
  const totalItems = filteredContacts.length;
  const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
  const currentContacts = filteredContacts.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

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
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
        <div className="flex items-center">
          <h4 className="text-xl font-bold flex items-center gap-2">
            Project Contacts
            <Badge variant="outline" className="rounded-full bg-slate-100 text-slate-500">{contacts.length}</Badge>
          </h4>
        </div>

        <div className="flex items-center gap-3 flex-wrap">
          <div className="icon-form">
            <div className="relative">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" />
              <Input 
                className="pl-9 bg-white" 
                placeholder="Search Contacts" 
                value={filterTitle}
                onChange={(e) => setFilterTitle(e.target.value)}
              />
            </div>
          </div>

          <Button onClick={() => {
            form.reset({ project_id: '', name: '', email: '', phone: '', designation: '' });
            setIsModalOpen(true);
          }} className="bg-primary hover:bg-primary/90">
            <Plus className="mr-2 h-4 w-4" /> Add Contact
          </Button>
        </div>
      </div>

      <div className="bg-white rounded-md border p-4 shadow-sm min-h-[500px]">
        {isLoading ? (
          <div className="text-center py-12 text-muted-foreground">Loading contacts...</div>
        ) : (
          <div className="table-responsive">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Sr. No.</TableHead>
                  <TableHead>Project Name</TableHead>
                  <TableHead>Name</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Phone</TableHead>
                  <TableHead>Designation</TableHead>
                  <TableHead className="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {currentContacts.map((item, index) => (
                  <TableRow key={item.id}>
                    <TableCell>{(currentPage - 1) * itemsPerPage + index + 1}</TableCell>
                    <TableCell className="font-medium">{item.project?.name || item.project_name || '-'}</TableCell>
                    <TableCell className="font-medium text-primary cursor-pointer hover:underline" onClick={() => handleEdit(item)}>{item.name}</TableCell>
                    <TableCell>{item.email}</TableCell>
                    <TableCell>{item.phone}</TableCell>
                    <TableCell>{item.designation}</TableCell>
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
                {contacts.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={7} className="text-center py-6 text-muted-foreground">
                      No contacts found.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </div>
        )}
        
        {!isLoading && totalItems > 0 && (
          <div className="p-4 border-t flex flex-col md:flex-row items-center justify-between gap-4 mt-4">
            <div className="text-sm text-muted-foreground whitespace-nowrap">
              Showing {(currentPage - 1) * itemsPerPage + 1} to {Math.min(currentPage * itemsPerPage, totalItems)} of {totalItems} records
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

      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{form.watch('id') ? 'Edit Contact' : 'Add New Contact'}</DialogTitle>
          </DialogHeader>
          
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <div className="space-y-2">
              <Label>Project <span className="text-destructive">*</span></Label>
              <Controller
                control={form.control}
                name="project_id"
                render={({ field }) => (
                  <Combobox value={field.value} onValueChange={field.onChange}>
                    <ComboboxInput placeholder="Select Project" />
                    <ComboboxContent>
                      <ComboboxList>
                        {projects.map(proj => (
                          <ComboboxItem key={proj.id} value={String(proj.id)}>
                            {proj.name}
                          </ComboboxItem>
                        ))}
                      </ComboboxList>
                    </ComboboxContent>
                  </Combobox>
                )}
              />
              {form.formState.errors.project_id && <p className="text-sm text-destructive">{form.formState.errors.project_id.message}</p>}
            </div>

            <div className="space-y-2">
              <Label>Name <span className="text-destructive">*</span></Label>
              <Input {...form.register('name')} />
              {form.formState.errors.name && <p className="text-sm text-destructive">{form.formState.errors.name.message}</p>}
            </div>

            <div className="space-y-2">
              <Label>Email <span className="text-destructive">*</span></Label>
              <Input type="email" {...form.register('email')} />
              {form.formState.errors.email && <p className="text-sm text-destructive">{form.formState.errors.email.message}</p>}
            </div>
            
            <div className="space-y-2">
              <Label>Phone <span className="text-destructive">*</span></Label>
              <Input type="tel" {...form.register('phone')} />
              {form.formState.errors.phone && <p className="text-sm text-destructive">{form.formState.errors.phone.message}</p>}
            </div>
            
            <div className="space-y-2">
              <Label>Designation</Label>
              <Input {...form.register('designation')} />
            </div>

            <DialogFooter className="pt-4 mt-4">
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

      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Project Contact</DialogTitle>
          </DialogHeader>
          <div className="py-4 text-center">
            <div className="mx-auto w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
              <Trash2 className="h-8 w-8" />
            </div>
            <h4 className="text-xl font-bold mb-2">Remove Project Contact?</h4>
            <p className="text-muted-foreground">Are you sure you want to remove the selected project contact?</p>
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
