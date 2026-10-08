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

const formSchema = z.object({
  id: z.number().optional(),
  name: z.string().min(1, 'Work mode name is required'),
  status: z.enum(['active', 'inactive']),
});

type FormValues = z.infer<typeof formSchema>;

export default function WorkModeSettingsPage() {
  const params = useParams();

  const [workModes, setWorkModes] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [modeToDelete, setModeToDelete] = useState<number | null>(null);

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: '',
      status: 'active',
    },
  });

  const { control } = form;

  const routeBoxSlug = (params?.box_slug as string) || '';

  useEffect(() => {
    if (!routeBoxSlug || routeBoxSlug === '[box_slug]') return;
    fetchWorkModes();
  }, [routeBoxSlug]);

  const fetchWorkModes = async () => {
    setIsLoading(true);
    try {
      const boxSlug = routeBoxSlug;
      const res = await apiClient.get(`/${boxSlug}/settings/work-modes`);
      setWorkModes(res?.data || []);
    } catch (err: any) {
      toast.error('Failed to load work modes');
    } finally {
      setIsLoading(false);
    }
  };

  const handleOpenModal = (mode?: any) => {
    if (mode) {
      form.reset({
        id: mode.id,
        name: mode.name || '',
        status: mode.status ? 'active' : 'inactive',
      });
    } else {
      form.reset({
        name: '',
        status: 'active',
      });
    }
    setIsModalOpen(true);
  };

  const onSubmit = async (data: FormValues) => {
    try {
      const boxSlug = routeBoxSlug;
      const payload = {
        ...data,
        status: data.status === 'active' ? 1 : 0
      };

      if (payload.id) {
        await apiClient.put(`/${boxSlug}/settings/work-modes`, payload);
        toast.success('Work mode updated successfully');
      } else {
        await apiClient.post(`/${boxSlug}/settings/work-modes`, payload);
        toast.success('Work mode added successfully');
      }
      setIsModalOpen(false);
      fetchWorkModes();
    } catch (err: any) {
      toast.error(err.message || 'An error occurred');
    }
  };

  const handleDelete = async () => {
    if (!modeToDelete) return;
    try {
      const boxSlug = routeBoxSlug;
      await apiClient.delete(`/${boxSlug}/settings/work-modes`, { params: { id: modeToDelete } });
      toast.success('Work mode deleted successfully');
      setIsDeleteModalOpen(false);
      setModeToDelete(null);
      fetchWorkModes();
    } catch (err: any) {
      toast.error(err.message || 'Failed to delete work mode');
    }
  };

  return (
    <div className="p-6 max-w-[1000px] mx-auto">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-bold">Work Mode Settings</h1>
          <p className="text-muted-foreground text-sm">Manage work modes for your organization.</p>
        </div>
        <Button onClick={() => handleOpenModal()} className="flex items-center gap-2">
          <Plus className="w-4 h-4" /> Add Mode
        </Button>
      </div>

      <div className="bg-card text-card-foreground rounded-xl shadow-sm border p-4 overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow className="bg-muted/50 hover:bg-muted/50">
              <TableHead>Work Mode</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {isLoading ? (
              <TableRow>
                <TableCell colSpan={3} className="text-center py-10">
                  Loading work modes...
                </TableCell>
              </TableRow>
            ) : workModes.length === 0 ? (
              <TableRow>
                <TableCell colSpan={3} className="text-center py-10 text-muted-foreground">
                  No work modes found.
                </TableCell>
              </TableRow>
            ) : (
              workModes.map((m) => (
                <TableRow key={m.id}>
                  <TableCell className="font-medium">{m.name}</TableCell>
                  <TableCell>
                    <span className={`px-2 py-1 rounded text-xs ${m.status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                      {m.status ? 'Active' : 'Inactive'}
                    </span>
                  </TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-2">
                      <Button variant="ghost" size="icon" onClick={() => handleOpenModal(m)}>
                        <Pencil className="w-4 h-4 text-primary" />
                      </Button>
                      <Button 
                        variant="ghost" 
                        size="icon" 
                        onClick={() => {
                          setModeToDelete(m.id);
                          setIsDeleteModalOpen(true);
                        }}
                      >
                        <Trash2 className="w-4 h-4 text-destructive" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>

      {/* Add / Edit Modal */}
      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>{form.getValues('id') ? 'Edit Work Mode' : 'Add Work Mode'}</DialogTitle>
          </DialogHeader>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4 py-4">
            <div className="space-y-4">
              
              <div className="space-y-2">
                <Label>Work Mode Name <span className="text-red-500">*</span></Label>
                <Input {...form.register('name')} placeholder="Enter Work Mode Name" />
                {form.formState.errors.name && <p className="text-xs text-red-500">{form.formState.errors.name.message}</p>}
              </div>

              <div className="space-y-2">
                <Label>Status</Label>
                <Controller
                  name="status"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || 'active'}>
                      <ComboboxInput placeholder="Select Status" showClear={false} showTrigger />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxItem value="active">Active</ComboboxItem>
                          <ComboboxItem value="inactive">Inactive</ComboboxItem>
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
                {form.formState.errors.status && <p className="text-xs text-red-500">{form.formState.errors.status.message}</p>}
              </div>

            </div>
            
            <DialogFooter className="mt-6">
              <Button type="button" variant="outline" onClick={() => setIsModalOpen(false)}>Cancel</Button>
              <Button type="submit">Save</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Modal */}
      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent className="max-w-sm text-center p-6">
          <div className="mx-auto w-12 h-12 bg-destructive/10 rounded-full flex items-center justify-center mb-4">
            <Trash2 className="w-6 h-6 text-destructive" />
          </div>
          <DialogTitle className="text-center text-xl mb-2">Confirm Delete</DialogTitle>
          <p className="text-muted-foreground mb-6">
            Are you sure you want to delete this work mode? This action cannot be undone.
          </p>
          <DialogFooter className="flex gap-2 sm:justify-center">
            <Button variant="outline" onClick={() => setIsDeleteModalOpen(false)}>Cancel</Button>
            <Button variant="destructive" onClick={handleDelete}>Yes, Delete</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

    </div>
  );
}
