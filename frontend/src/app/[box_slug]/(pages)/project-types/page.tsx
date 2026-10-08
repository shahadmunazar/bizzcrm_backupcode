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
  name: z.string().min(1, 'Name is required'),
  status: z.string().min(1, 'Status is required'),
});

type FormValues = z.infer<typeof formSchema>;

export default function ProjectTypesPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [types, setTypes] = useState<any[]>([]);
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
      name: '',
      status: 'Active',
    },
  });

  const fetchTypes = async () => {
    try {
      setIsLoading(true);
      const response = await apiClient.get(`/${routeBoxSlug}/manage-users/project-types`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      // Handle DataTables format
      const data = response?.data?.data || response?.data || [];
      setTypes(data);
      setCurrentPage(1); // Reset page on new fetch
    } catch (error) {
      console.error('Failed to fetch project types:', error);
      toast.error('Failed to load project types');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (routeBoxSlug) {
      fetchTypes();
    }
  }, [routeBoxSlug]);

  const handleEdit = (item: any) => {
    form.reset({
      id: item.id,
      name: item.name,
      status: (item.status === 1 || item.status === true || item.status === '1' || item.status === 'Active') ? 'Active' : 'Inactive',
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
      await apiClient.delete(`/${routeBoxSlug}/manage-users/project-types`, {
        body: JSON.stringify({ id: itemToDelete }),
        headers: { 'Content-Type': 'application/json' }
      });
      toast.success('Project type deleted successfully');
      setIsDeleteModalOpen(false);
      setItemToDelete(null);
      fetchTypes();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to delete project type');
    }
  };

  const onSubmit = async (data: FormValues) => {
    try {
      const payload = {
        ...data,
        status: data.status === 'Active' ? 1 : 0
      };
      if (data.id) {
        await apiClient.put(`/${routeBoxSlug}/manage-users/project-types`, payload);
        toast.success('Project type updated successfully');
      } else {
        await apiClient.post(`/${routeBoxSlug}/manage-users/project-types`, payload);
        toast.success('Project type created successfully');
      }
      
      setIsModalOpen(false);
      form.reset();
      fetchTypes();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to save project type');
    }
  };

  const filteredTypes = types.filter(t => t.name?.toLowerCase().includes(filterTitle.toLowerCase()));
  const totalItems = filteredTypes.length;
  const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
  const currentTypes = filteredTypes.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

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
            Project Types
            <Badge variant="outline" className="rounded-full bg-slate-100 text-slate-500">{types.length}</Badge>
          </h4>
        </div>

        <div className="flex items-center gap-3 flex-wrap">
          <div className="icon-form">
            <div className="relative">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" />
              <Input 
                className="pl-9 bg-white" 
                placeholder="Search Types" 
                value={filterTitle}
                onChange={(e) => setFilterTitle(e.target.value)}
              />
            </div>
          </div>

          <Button onClick={() => {
            form.reset({ name: '', status: 'Active' });
            setIsModalOpen(true);
          }} className="bg-primary hover:bg-primary/90">
            <Plus className="mr-2 h-4 w-4" /> Add Type
          </Button>
        </div>
      </div>

      {/* Types List */}
      <div className="bg-white rounded-md border p-4 shadow-sm min-h-[500px]">
        {isLoading ? (
          <div className="text-center py-12 text-muted-foreground">Loading types...</div>
        ) : (
          <div className="table-responsive">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Sr. No.</TableHead>
                  <TableHead>Name</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Created By</TableHead>
                  <TableHead>Updated By</TableHead>
                  <TableHead className="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {currentTypes.map((item, index) => (
                  <TableRow key={item.id}>
                    <TableCell>{(currentPage - 1) * itemsPerPage + index + 1}</TableCell>
                    <TableCell className="font-medium text-primary cursor-pointer hover:underline" onClick={() => handleEdit(item)}>{item.name}</TableCell>
                    <TableCell>
                      {(item.status === 1 || item.status === true || item.status === '1' || item.status === 'Active') ? (
                        <Badge className="bg-green-100 text-green-700 hover:bg-green-100 border-green-200">Active</Badge>
                      ) : (
                        <Badge variant="secondary">Inactive</Badge>
                      )}
                    </TableCell>
                    <TableCell>{item.created_by || item.creator?.name || '-'}</TableCell>
                    <TableCell>{item.updated_by || item.updater?.name || '-'}</TableCell>
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
                {types.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={6} className="text-center py-6 text-muted-foreground">
                      No types found.
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

      {/* Add/Edit Modal */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{form.watch('id') ? 'Edit Type' : 'Add New Type'}</DialogTitle>
          </DialogHeader>
          
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <div className="space-y-2">
              <Label>Name <span className="text-destructive">*</span></Label>
              <Input {...form.register('name')} />
              {form.formState.errors.name && <p className="text-sm text-destructive">{form.formState.errors.name.message}</p>}
            </div>

            <div className="space-y-2">
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

      {/* Delete Confirmation Modal */}
      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Project Type</DialogTitle>
          </DialogHeader>
          <div className="py-4 text-center">
            <div className="mx-auto w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
              <Trash2 className="h-8 w-8" />
            </div>
            <h4 className="text-xl font-bold mb-2">Remove Project Type?</h4>
            <p className="text-muted-foreground">Are you sure you want to remove the selected project type?</p>
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
