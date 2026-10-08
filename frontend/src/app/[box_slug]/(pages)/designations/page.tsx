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
  name: z.string().min(1, 'Designation name is required'),
  department_id: z.string().min(1, 'Department is required'),
  status: z.enum(['active', 'inactive']),
});

type FormValues = z.infer<typeof formSchema>;

export default function DesignationsSettingsPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [designations, setDesignations] = useState<any[]>([]);
  const [departments, setDepartments] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [typeToDelete, setTypeToDelete] = useState<number | null>(null);

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: '',
      department_id: '',
      status: 'active',
    },
  });

  const fetchData = async () => {
    try {
      setIsLoading(true);
      // Fetch designations
      const res = await apiClient.get(`/${routeBoxSlug}/settings/designations`);
      setDesignations(res?.data || []);
      
      // Fetch departments (reusing the same endpoint)
      const depRes = await apiClient.get(`/${routeBoxSlug}/settings/departments`);
      setDepartments(depRes?.data || []);
    } catch (error) {
      console.error('Failed to fetch data:', error);
      toast.error('Failed to load designations');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (routeBoxSlug) {
      fetchData();
    }
  }, [routeBoxSlug]);

  const handleEdit = (designation: any) => {
    form.reset({
      id: designation.id,
      name: designation.name,
      department_id: designation.department_id ? designation.department_id.toString() : '',
      status: designation.status == 1 || designation.status === 'active' || designation.status === '1' ? 'active' : 'inactive',
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
      await apiClient.delete(`/${routeBoxSlug}/settings/designations/${typeToDelete}`);
      toast.success('Designation deleted successfully');
      setIsDeleteModalOpen(false);
      setTypeToDelete(null);
      fetchData();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to delete designation');
    }
  };

  const onSubmit = async (data: FormValues) => {
    try {
      const payload = {
        name: data.name,
        department_id: data.department_id,
        status: data.status === 'active' ? '1' : '0',
      };

      if (data.id) {
        await apiClient.put(`/${routeBoxSlug}/settings/designations/${data.id}`, payload);
        toast.success('Designation updated successfully');
      } else {
        await apiClient.post(`/${routeBoxSlug}/settings/designations`, payload);
        toast.success('Designation created successfully');
      }
      
      setIsModalOpen(false);
      form.reset();
      fetchData();
    } catch (error: any) {
      toast.error(error?.message || 'Failed to save designation');
    }
  };

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-bold">Designations</h1>
          <p className="text-muted-foreground">Manage your organization's designations</p>
        </div>
        <Button onClick={() => {
          form.reset({ name: '', department_id: '', status: 'active' });
          setIsModalOpen(true);
        }}>
          <Plus className="mr-2 h-4 w-4" /> Add Designation
        </Button>
      </div>

      <div className="bg-card rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>ID</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Department</TableHead>
              <TableHead>Left (_lft)</TableHead>
              <TableHead>Right (_rgt)</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {isLoading ? (
              <TableRow>
                <TableCell colSpan={7} className="text-center py-8">
                  Loading designations...
                </TableCell>
              </TableRow>
            ) : designations.length === 0 ? (
              <TableRow>
                <TableCell colSpan={7} className="text-center py-8">
                  No designations found. Click "Add Designation" to create one.
                </TableCell>
              </TableRow>
            ) : (
              designations.map((desig) => (
                <TableRow key={desig.id}>
                  <TableCell className="font-medium">{desig.id}</TableCell>
                  <TableCell>{desig.name}</TableCell>
                  <TableCell>{desig.department || '-'}</TableCell>
                  <TableCell>{desig._lft}</TableCell>
                  <TableCell>{desig._rgt}</TableCell>
                  <TableCell>
                    {desig.status == 1 || desig.status === 'active' || desig.status === '1' ? (
                      <Badge className="bg-green-100 text-green-700 hover:bg-green-100 border-green-200">Active</Badge>
                    ) : (
                      <Badge variant="secondary" className="bg-red-100 text-red-700 hover:bg-red-100 border-red-200">Inactive</Badge>
                    )}
                  </TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-2">
                      <Button variant="ghost" size="icon" onClick={() => handleEdit(desig)}>
                        <Pencil className="h-4 w-4" />
                      </Button>
                      <Button variant="ghost" size="icon" onClick={() => confirmDelete(desig.id)}>
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
            <DialogTitle>{form.watch('id') ? 'Edit Designation' : 'Create New Designation'}</DialogTitle>
          </DialogHeader>
          
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="name">Designation Name <span className="text-destructive">*</span></Label>
              <Input 
                id="name" 
                placeholder="e.g. Software Engineer, Sales Manager" 
                {...form.register('name')} 
              />
              {form.formState.errors.name && (
                <p className="text-sm text-destructive">{form.formState.errors.name.message}</p>
              )}
            </div>

            <div className="space-y-2">
              <Label>Department <span className="text-destructive">*</span></Label>
              <Controller
                control={form.control}
                name="department_id"
                render={({ field }) => (
                  <Combobox 
                    value={field.value} 
                    onValueChange={field.onChange}
                  >
                    <ComboboxInput placeholder="Select department" />
                    <ComboboxContent>
                      <ComboboxList>
                        {departments.map((dept) => (
                          <ComboboxItem key={dept.id} value={dept.id.toString()}>
                            {dept.name}
                          </ComboboxItem>
                        ))}
                      </ComboboxList>
                    </ComboboxContent>
                  </Combobox>
                )}
              />
              {form.formState.errors.department_id && (
                <p className="text-sm text-destructive">{form.formState.errors.department_id.message}</p>
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
                {form.watch('id') ? 'Update Designation' : 'Create Designation'}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Modal */}
      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Designation</DialogTitle>
          </DialogHeader>
          <div className="py-4">
            <p>Are you sure you want to delete this designation? This action cannot be undone.</p>
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
