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
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { toast } from 'sonner';
import { apiClient } from '@/lib/api-client';
import { Pencil, Trash2, Plus } from 'lucide-react';
import { useParams } from 'next/navigation';

const formSchema = z.object({
  branch_id: z.number().optional(),
  branch_name: z.string().min(1, 'Branch name is required'),
  country_id: z.string().min(1, 'Country is required'),
  state_id: z.string().min(1, 'State is required'),
  city_id: z.string().min(1, 'City is required'),
  pincode: z.string().min(5, 'Pin code must be at least 5 digits').max(10, 'Pin code must not exceed 10 digits'),
  address: z.string().min(1, 'Address is required'),
});

type FormValues = z.infer<typeof formSchema>;

export default function BranchSettingsPage() {
  const params = useParams();

  const [branches, setBranches] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [branchToDelete, setBranchToDelete] = useState<number | null>(null);

  // Dropdown states
  const [countries, setCountries] = useState<any[]>([]);
  const [states, setStates] = useState<any[]>([]);
  const [cities, setCities] = useState<any[]>([]);

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      branch_name: '',
      country_id: '',
      state_id: '',
      city_id: '',
      pincode: '',
      address: '',
    },
  });

  const { watch, setValue, control } = form;
  const watchCountryId = watch('country_id');
  const watchStateId = watch('state_id');

  const routeBoxSlug = (params?.box_slug as string) || '';

  useEffect(() => {
    if (!routeBoxSlug || routeBoxSlug === '[box_slug]') return;
    fetchBranches();
    fetchCountries();
  }, [routeBoxSlug]);

  useEffect(() => {
    if (watchCountryId) {
      fetchStates(watchCountryId);
    } else {
      setStates([]);
      setValue('state_id', '');
    }
  }, [watchCountryId, setValue]);

  useEffect(() => {
    if (watchStateId) {
      fetchCities(watchStateId);
    } else {
      setCities([]);
      setValue('city_id', '');
    }
  }, [watchStateId, setValue]);

  const fetchBranches = async () => {
    setIsLoading(true);
    try {
      const boxSlug = (params?.box_slug as string) || '';
      const res = await apiClient.get(`/${boxSlug}/company-branches`);
      setBranches(res?.data || []);
    } catch (err: any) {
      toast.error('Failed to load branches');
    } finally {
      setIsLoading(false);
    }
  };

  const fetchCountries = async () => {
    try {
      const res = await apiClient.get('/search-country?type=country');
      setCountries(res?.data || []);
    } catch (err) {
      console.error('Failed to load countries', err);
    }
  };

  const fetchStates = async (countryId: string) => {
    try {
      const res = await apiClient.get(`/search-country?type=state&country_id=${countryId}`);
      setStates(res?.data || []);
    } catch (err) {
      console.error('Failed to load states', err);
    }
  };

  const fetchCities = async (stateId: string) => {
    try {
      const res = await apiClient.get(`/search-country?type=city&state_id=${stateId}`);
      setCities(res?.data || []);
    } catch (err) {
      console.error('Failed to load cities', err);
    }
  };

  const handleOpenModal = (branch?: any) => {
    if (branch) {
      form.reset({
        branch_id: branch.id,
        branch_name: branch.name || '',
        country_id: String(branch.country_id) || '',
        state_id: String(branch.state_id) || '',
        city_id: String(branch.city_id) || '',
        pincode: branch.pincode || '',
        address: branch.address || '',
      });
      // Fetch dependent dropdowns manually so they populate correctly on edit
      fetchStates(String(branch.country_id)).then(() => {
        fetchCities(String(branch.state_id));
      });
    } else {
      form.reset({
        branch_name: '',
        country_id: '',
        state_id: '',
        city_id: '',
        pincode: '',
        address: '',
      });
    }
    setIsModalOpen(true);
  };

  const onSubmit = async (data: FormValues) => {
    try {
      const boxSlug = (params?.box_slug as string) || '';
      if (data.branch_id) {
        await apiClient.put(`/${boxSlug}/company-branches`, data);
        toast.success('Branch updated successfully');
      } else {
        await apiClient.post(`/${boxSlug}/company-branches`, data);
        toast.success('Branch added successfully');
      }
      setIsModalOpen(false);
      fetchBranches();
    } catch (err: any) {
      toast.error(err.message || 'An error occurred');
    }
  };

  const handleDelete = async () => {
    if (!branchToDelete) return;
    try {
      const boxSlug = (params?.box_slug as string) || '';
      await apiClient.delete(`/${boxSlug}/company-branches`, { params: { branch_id: branchToDelete } });
      toast.success('Branch deleted successfully');
      setIsDeleteModalOpen(false);
      setBranchToDelete(null);
      fetchBranches();
    } catch (err: any) {
      toast.error(err.message || 'Failed to delete branch');
    }
  };

  return (
    <div className="p-6 max-w-[1400px] mx-auto">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-bold">Branch Settings</h1>
          <p className="text-muted-foreground text-sm">Manage company branches and their details.</p>
        </div>
        <Button onClick={() => handleOpenModal()} className="flex items-center gap-2">
          <Plus className="w-4 h-4" /> Add Branch
        </Button>
      </div>

      <div className="bg-card text-card-foreground rounded-xl shadow-sm border p-4 overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow className="bg-muted/50 hover:bg-muted/50">
              <TableHead>Branch Name</TableHead>
              <TableHead>Country</TableHead>
              <TableHead>State</TableHead>
              <TableHead>City</TableHead>
              <TableHead>Pin Code</TableHead>
              <TableHead>Address</TableHead>
              <TableHead className="text-right">Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {isLoading ? (
              <TableRow>
                <TableCell colSpan={7} className="text-center py-10">
                  Loading branches...
                </TableCell>
              </TableRow>
            ) : branches.length === 0 ? (
              <TableRow>
                <TableCell colSpan={7} className="text-center py-10 text-muted-foreground">
                  No branches found.
                </TableCell>
              </TableRow>
            ) : (
              branches.map((b) => (
                <TableRow key={b.id}>
                  <TableCell className="font-medium">{b.name}</TableCell>
                  <TableCell>{b.country?.name || '-'}</TableCell>
                  <TableCell>{b.state?.name || '-'}</TableCell>
                  <TableCell>{b.city?.name || '-'}</TableCell>
                  <TableCell>{b.pincode}</TableCell>
                  <TableCell>{b.address}</TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-2">
                      <Button variant="ghost" size="icon" onClick={() => handleOpenModal(b)}>
                        <Pencil className="w-4 h-4 text-primary" />
                      </Button>
                      <Button 
                        variant="ghost" 
                        size="icon" 
                        onClick={() => {
                          setBranchToDelete(b.id);
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
        <DialogContent className="max-w-3xl">
          <DialogHeader>
            <DialogTitle>{form.getValues('branch_id') ? 'Edit Branch' : 'Add Branch'}</DialogTitle>
          </DialogHeader>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4 py-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              
              {/* Branch Name */}
              <div className="space-y-2">
                <Label>Branch Name</Label>
                <Input {...form.register('branch_name')} placeholder="Enter Branch Name" />
                {form.formState.errors.branch_name && <p className="text-xs text-red-500">{form.formState.errors.branch_name.message}</p>}
              </div>

              {/* Country */}
              <div className="space-y-2">
                <Label>Country</Label>
                <Controller
                  name="country_id"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || ''} key={countries.length ? 'loaded' : 'loading'}>
                      <ComboboxInput placeholder="Select Country" showClear showTrigger />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxEmpty>No results found</ComboboxEmpty>
                          {countries.map(c => (
                            <ComboboxItem key={c.id} value={String(c.id)}>{c.name}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
                {form.formState.errors.country_id && <p className="text-xs text-red-500">{form.formState.errors.country_id.message}</p>}
              </div>

              {/* State */}
              <div className="space-y-2">
                <Label>State</Label>
                <Controller
                  name="state_id"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || ''} disabled={!watchCountryId} key={states.length ? 'loaded' : 'loading'}>
                      <ComboboxInput placeholder={watchCountryId ? "Select State" : "Select Country First"} showClear showTrigger disabled={!watchCountryId} />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxEmpty>No results found</ComboboxEmpty>
                          {states.map(s => (
                            <ComboboxItem key={s.id} value={String(s.id)}>{s.name}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
                {form.formState.errors.state_id && <p className="text-xs text-red-500">{form.formState.errors.state_id.message}</p>}
              </div>

              {/* City */}
              <div className="space-y-2">
                <Label>City</Label>
                <Controller
                  name="city_id"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || ''} disabled={!watchStateId} key={cities.length ? 'loaded' : 'loading'}>
                      <ComboboxInput placeholder={watchStateId ? "Select City" : "Select State First"} showClear showTrigger disabled={!watchStateId} />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxEmpty>No results found</ComboboxEmpty>
                          {cities.map(c => (
                            <ComboboxItem key={c.id} value={String(c.id)}>{c.name}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
                {form.formState.errors.city_id && <p className="text-xs text-red-500">{form.formState.errors.city_id.message}</p>}
              </div>

              {/* Pin Code */}
              <div className="space-y-2">
                <Label>Pin Code</Label>
                <Input {...form.register('pincode')} placeholder="Enter Pin Code" />
                {form.formState.errors.pincode && <p className="text-xs text-red-500">{form.formState.errors.pincode.message}</p>}
              </div>

              {/* Address */}
              <div className="space-y-2 col-span-1 md:col-span-2">
                <Label>Address</Label>
                <Textarea {...form.register('address')} placeholder="Enter Full Address" rows={3} />
                {form.formState.errors.address && <p className="text-xs text-red-500">{form.formState.errors.address.message}</p>}
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
            Are you sure you want to delete this branch? This action cannot be undone.
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
