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
import { Pencil, Trash2, Plus } from 'lucide-react';
import { useParams } from 'next/navigation';
import { Badge } from '@/components/ui/badge';

const formSchema = z.object({
  id: z.number().optional(),
  name: z.string().min(1, 'Department name is required'),
  status: z.enum(['active', 'inactive']),
});

type FormValues = z.infer<typeof formSchema>;

export default function DepartmentsSettingsPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [departments, setDepartments] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [typeToDelete, setTypeToDelete] = useState<number | null>(null);

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: '',
      status: 'active',
    },
  });

  const fetchDepartments = async () => {
    try {
      setIsLoading(true);
      const res = await apiClient.get(`/${routeBoxSlug}/settings/departments`);
      // The API returns a Yajra Datatable response, so the array is in res.data
      setDepartments(res?.data || []);
    } catch (error) {
      console.error('Failed to fetch departments:', error);
      toast.error('Failed to load departments');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (routeBoxSlug) {
      fetchDepartments();
    }
  }, [routeBoxSlug]);

  const handleEdit = (dept: any) => {
    form.reset({
      id: dept.id,
      name: dept.name,
      status: dept.status == 1 || dept.status === 'active' || dept.status === '1' ? 'active' : 'inactive',
    });
    setIsModalOpen(true);
  };

  const confirmDelete = (id: number) => {
    setTypeToDelete(id);
    setIsDeleteModalOpen(true);
  };

  const handleDelete = async () => {
    if (!typeToDelete) return;
    
    try {
      await apiClient.delete(`/${routeBoxSlug}/settings/departments/${typeToDelete}`);
      toast.success('Department deleted successfully');
      setIsDeleteModalOpen(false);
      setTypeToDelete(null);
      fetchDepartments();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to delete department');
    }
  };

  const onSubmit = async (data: FormValues) => {
    try {
      const payload = {
        name: data.name,
        status: data.status === 'active' ? '1' : '0',
      };

      if (data.id) {
        await apiClient.put(`/${routeBoxSlug}/settings/departments/${data.id}`, payload);
        toast.success('Department updated successfully');
      } else {
        await apiClient.post(`/${routeBoxSlug}/settings/departments`, payload);
        toast.success('Department created successfully');
      }
      
      setIsModalOpen(false);
      form.reset();
      fetchDepartments();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to save department');
    }
  };

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-bold">Departments</h1>
          <p className="text-muted-foreground">Manage your organization's departments</p>
        </div>
        <Button onClick={() => {
          form.reset({ name: '', status: 'active' });
          setIsModalOpen(true);
        }}>
          <Plus className="mr-2 h-4 w-4" /> Add Department
        </Button>
      </div>

      <div className="bg-card rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>ID</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Left (_lft)</TableHead>
              <TableHead>Right (_rgt)</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {isLoading ? (
              <TableRow>
                <TableCell colSpan={6} className="text-center py-8">
                  Loading departments...
                </TableCell>
              </TableRow>
            ) : departments.length === 0 ? (
              <TableRow>
                <TableCell colSpan={6} className="text-center py-8">
                  No departments found. Click "Add Department" to create one.
                </TableCell>
              </TableRow>
            ) : (
              departments.map((dept) => (
                <TableRow key={dept.id}>
                  <TableCell className="font-medium">{dept.id}</TableCell>
                  <TableCell>{dept.name}</TableCell>
                  <TableCell>{dept._lft}</TableCell>
                  <TableCell>{dept._rgt}</TableCell>
                  <TableCell>
                    {dept.status == 1 || dept.status === 'active' || dept.status === '1' ? (
                      <Badge className="bg-green-100 text-green-700 hover:bg-green-100 border-green-200">Active</Badge>
                    ) : (
                      <Badge variant="secondary" className="bg-red-100 text-red-700 hover:bg-red-100 border-red-200">Inactive</Badge>
                    )}
                  </TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-2">
                      <Button variant="ghost" size="icon" onClick={() => handleEdit(dept)}>
                        <Pencil className="h-4 w-4" />
                      </Button>
                      <Button variant="ghost" size="icon" onClick={() => confirmDelete(dept.id)}>
                        <Trash2 className="h-4 w-4 text-destructive" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>

      {/* Add/Edit Modal */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{form.watch('id') ? 'Edit Department' : 'Create New Department'}</DialogTitle>
          </DialogHeader>
          
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="name">Department Name <span className="text-destructive">*</span></Label>
              <Input 
                id="name" 
                placeholder="e.g. Sales, Marketing, IT" 
                {...form.register('name')} 
              />
              {form.formState.errors.name && (
                <p className="text-sm text-destructive">{form.formState.errors.name.message}</p>
              )}
            </div>

            <div className="space-y-2">
              <Label>Status <span className="text-destructive">*</span></Label>
              <Controller
                control={form.control}
                name="status"
                render={({ field }) => (
                  <Combobox 
                    value={field.value} 
                    onValueChange={field.onChange}
                  >
                    <ComboboxInput placeholder="Select status" />
                    <ComboboxContent>
                      <ComboboxList>
                        <ComboboxItem value="active">Active</ComboboxItem>
                        <ComboboxItem value="inactive">Inactive</ComboboxItem>
                      </ComboboxList>
                    </ComboboxContent>
                  </Combobox>
                )}
              />
            </div>

            <DialogFooter className="pt-4">
              <Button type="button" variant="outline" onClick={() => setIsModalOpen(false)}>
                Cancel
              </Button>
              <Button type="submit">
                {form.watch('id') ? 'Update Department' : 'Create Department'}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Modal */}
      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Department</DialogTitle>
          </DialogHeader>
          <div className="py-4">
            <p>Are you sure you want to delete this department? This action cannot be undone.</p>
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
    </div>
  );
}
