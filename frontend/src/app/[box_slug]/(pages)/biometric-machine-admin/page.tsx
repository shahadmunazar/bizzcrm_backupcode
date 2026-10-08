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
  id: z.number().optional(),
  machine_no: z.string().min(1, 'Machine number is required'),
  machine_type: z.string().min(1, 'Machine type is required'),
  serial_no: z.string().optional().nullable(),
  server_port_no: z.string().optional().nullable(),
  machine_name: z.string().optional().nullable(),
  branch_id: z.string().min(1, 'Branch is required'),
  country_id: z.string().min(1, 'Country is required'),
  state_id: z.string().min(1, 'State is required'),
  city_id: z.string().min(1, 'City is required'),
  timezone: z.string().min(1, 'Timezone is required'),
  access_control: z.string().min(1, 'Access control is required'),
  status: z.string().min(1, 'Status is required'),
});

type FormValues = z.infer<typeof formSchema>;

export default function BiometricMachineAdminPage() {
  const params = useParams();
  const routeBoxSlug = (params?.box_slug as string) || '';

  const [machines, setMachines] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [machineToDelete, setMachineToDelete] = useState<number | null>(null);

  // Dropdown states
  const [branches, setBranches] = useState<any[]>([]);
  const [countries, setCountries] = useState<any[]>([]);
  const [states, setStates] = useState<any[]>([]);
  const [cities, setCities] = useState<any[]>([]);
  const [timezones, setTimezones] = useState<string[]>([]);

  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      machine_no: '',
      machine_type: '',
      serial_no: '',
      server_port_no: '',
      machine_name: '',
      branch_id: '',
      country_id: '',
      state_id: '',
      city_id: '',
      timezone: '',
      access_control: '',
      status: 'Active',
    },
  });

  const { watch, setValue, control } = form;
  const watchCountryId = watch('country_id');
  const watchStateId = watch('state_id');

  useEffect(() => {
    if (!routeBoxSlug || routeBoxSlug === '[box_slug]') return;
    fetchMachines();
    fetchBranches();
    fetchCountries();
    fetchTimezones();
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

  const fetchMachines = async () => {
    setIsLoading(true);
    try {
      const boxSlug = routeBoxSlug;
      const res = await apiClient.get(`/${boxSlug}/biometric-machines`);
      setMachines(res?.data || []);
    } catch (err: any) {
      toast.error('Failed to load biometric machines');
    } finally {
      setIsLoading(false);
    }
  };

  const fetchBranches = async () => {
    try {
      const boxSlug = routeBoxSlug;
      const res = await apiClient.get(`/${boxSlug}/biometric-machines/branches`);
      setBranches(res?.data || []);
    } catch (err) {
      console.error('Failed to load branches', err);
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

  const fetchTimezones = async () => {
    try {
      const boxSlug = routeBoxSlug;
      const res = await apiClient.get(`/${boxSlug}/biometric-machines/timezones`);
      setTimezones(res?.data || []);
    } catch (err) {
      console.error('Failed to load timezones', err);
    }
  };

  const handleOpenModal = (machine?: any) => {
    if (machine) {
      form.reset({
        id: machine.id,
        machine_no: machine.machine_no || '',
        machine_type: machine.machine_type || '',
        serial_no: machine.serial_no || '',
        server_port_no: machine.server_port_no || '',
        machine_name: machine.machine_name || '',
        branch_id: String(machine.branch_id) || '',
        country_id: String(machine.country_id) || '',
        state_id: String(machine.state_id) || '',
        city_id: String(machine.city_id) || '',
        timezone: machine.timezone || '',
        access_control: machine.access_control || '',
        status: machine.status || 'Active',
      });
      // Need to fetch states and cities for edit manually since useEffect might lag
      fetchStates(String(machine.country_id)).then(() => {
        fetchCities(String(machine.state_id));
      });
    } else {
      form.reset({
        machine_no: '',
        machine_type: '',
        serial_no: '',
        server_port_no: '',
        machine_name: '',
        branch_id: '',
        country_id: '',
        state_id: '',
        city_id: '',
        timezone: '',
        access_control: '',
        status: 'Active',
      });
    }
    setIsModalOpen(true);
  };

  const onSubmit = async (data: FormValues) => {
    try {
      const boxSlug = routeBoxSlug;
      if (data.id) {
        await apiClient.put(`/${boxSlug}/biometric-machines`, data);
        toast.success('Machine updated successfully');
      } else {
        await apiClient.post(`/${boxSlug}/biometric-machines`, data);
        toast.success('Machine added successfully');
      }
      setIsModalOpen(false);
      fetchMachines();
    } catch (err: any) {
      toast.error(err.message || 'An error occurred');
    }
  };

  const handleDelete = async () => {
    if (!machineToDelete) return;
    try {
      const boxSlug = routeBoxSlug;
      await apiClient.delete(`/${boxSlug}/biometric-machines`, { params: { id: machineToDelete } });
      toast.success('Machine deleted successfully');
      setIsDeleteModalOpen(false);
      setMachineToDelete(null);
      fetchMachines();
    } catch (err: any) {
      toast.error(err.message || 'Failed to delete machine');
    }
  };

  return (
    <div className="p-6 max-w-[1400px] mx-auto">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-bold">Biometric Machine Setup</h1>
          <p className="text-muted-foreground text-sm">Manage your biometric machines across branches.</p>
        </div>
        <Button onClick={() => handleOpenModal()} className="flex items-center gap-2">
          <Plus className="w-4 h-4" /> Add Machine
        </Button>
      </div>

      <div className="bg-card text-card-foreground rounded-xl shadow-sm border p-4 overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow className="bg-muted/50 hover:bg-muted/50">
              <TableHead>Machine No.</TableHead>
              <TableHead>Machine Name</TableHead>
              <TableHead>Type</TableHead>
              <TableHead>Serial No.</TableHead>
              <TableHead>Branch</TableHead>
              <TableHead>Timezone</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {isLoading ? (
              <TableRow>
                <TableCell colSpan={8} className="text-center py-10">
                  Loading machines...
                </TableCell>
              </TableRow>
            ) : machines.length === 0 ? (
              <TableRow>
                <TableCell colSpan={8} className="text-center py-10 text-muted-foreground">
                  No biometric machines found.
                </TableCell>
              </TableRow>
            ) : (
              machines.map((m) => (
                <TableRow key={m.id}>
                  <TableCell className="font-medium">{m.machine_no}</TableCell>
                  <TableCell>{m.machine_name}</TableCell>
                  <TableCell>{m.machine_type}</TableCell>
                  <TableCell>{m.serial_no}</TableCell>
                  <TableCell>{m.branch?.branch_name || m.branch_name}</TableCell>
                  <TableCell>{m.timezone}</TableCell>
                  <TableCell>
                    <span className={"px-2 py-1 rounded-full text-xs " + (m.status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')}>
                      {m.status}
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
                          setMachineToDelete(m.id);
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

      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent className="max-w-4xl">
          <DialogHeader>
            <DialogTitle>{form.getValues('id') ? 'Edit Biometric Machine' : 'Add Biometric Machine'}</DialogTitle>
          </DialogHeader>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4 py-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              
              {/* Machine No */}
              <div className="space-y-2">
                <Label>Machine No.</Label>
                <Input {...form.register('machine_no')} placeholder="Enter Machine No." />
                {form.formState.errors.machine_no && <p className="text-xs text-red-500">{form.formState.errors.machine_no.message}</p>}
              </div>

              {/* Machine Type */}
              <div className="space-y-2">
                <Label>Machine Type</Label>
                <Controller
                  name="machine_type"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || ''}>
                      <ComboboxInput placeholder="Select Machine Type" showClear showTrigger />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxEmpty>No results found</ComboboxEmpty>
                          {['T52', 'T16', 'T28-OLD', 'T62', 'T61', 'RS20-OLD', 'RS70-OLD', 'RS10-OLD', 'T61N', 'C121', 'C101W', 'T60', 'S02 CONTROLER(T)', 'S04 CONTROLER(T)', 'T302', 'T304', 'T304F MINI'].map(type => (
                            <ComboboxItem key={type} value={type}>{type}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
                {form.formState.errors.machine_type && <p className="text-xs text-red-500">{form.formState.errors.machine_type.message}</p>}
              </div>

              {/* Serial No */}
              <div className="space-y-2">
                <Label>Serial No.</Label>
                <Input {...form.register('serial_no')} placeholder="Enter Serial No." />
              </div>

              {/* Server Port No */}
              <div className="space-y-2">
                <Label>Server Port No.</Label>
                <Input {...form.register('server_port_no')} placeholder="Enter Server Port No." />
              </div>

              {/* Machine Name */}
              <div className="space-y-2">
                <Label>Machine Name</Label>
                <Input {...form.register('machine_name')} placeholder="Enter Machine Name" />
              </div>

              {/* Branch */}
              <div className="space-y-2">
                <Label>Select Branch</Label>
                <Controller
                  name="branch_id"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || ''} key={branches.length ? 'loaded' : 'loading'}>
                      <ComboboxInput placeholder="Select Branch" showClear showTrigger />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxEmpty>No results found</ComboboxEmpty>
                          {branches.map(b => (
                            <ComboboxItem key={b.id} value={String(b.id)}>{b.branch_name || b.name}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
                {form.formState.errors.branch_id && <p className="text-xs text-red-500">{form.formState.errors.branch_id.message}</p>}
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

              {/* Timezone */}
              <div className="space-y-2">
                <Label>Time Zone</Label>
                <Controller
                  name="timezone"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || ''}>
                      <ComboboxInput placeholder="Select Time Zone" showClear showTrigger />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxEmpty>No results found</ComboboxEmpty>
                          {timezones.map(tz => (
                            <ComboboxItem key={tz} value={tz}>{tz}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
                {form.formState.errors.timezone && <p className="text-xs text-red-500">{form.formState.errors.timezone.message}</p>}
              </div>

              {/* Access Control */}
              <div className="space-y-2">
                <Label>Access Control</Label>
                <Controller
                  name="access_control"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || ''}>
                      <ComboboxInput placeholder="Select Access Control" showClear showTrigger />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxEmpty>No results found</ComboboxEmpty>
                          {['In', 'Out', 'In/Out'].map(ac => (
                            <ComboboxItem key={ac} value={ac}>{ac}</ComboboxItem>
                          ))}
                        </ComboboxList>
                      </ComboboxContent>
                    </Combobox>
                  )}
                />
                {form.formState.errors.access_control && <p className="text-xs text-red-500">{form.formState.errors.access_control.message}</p>}
              </div>

              {/* Status */}
              <div className="space-y-2">
                <Label>Status</Label>
                <Controller
                  name="status"
                  control={control}
                  render={({ field }) => (
                    <Combobox onValueChange={field.onChange} value={field.value || ''}>
                      <ComboboxInput placeholder="Select Status" showClear showTrigger />
                      <ComboboxContent>
                        <ComboboxList>
                          <ComboboxEmpty>No results found</ComboboxEmpty>
                          <ComboboxItem value="Active">Active</ComboboxItem>
                          <ComboboxItem value="Inactive">Inactive</ComboboxItem>
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

      <Dialog open={isDeleteModalOpen} onOpenChange={setIsDeleteModalOpen}>
        <DialogContent className="max-w-sm text-center p-6">
          <div className="mx-auto w-12 h-12 bg-destructive/10 rounded-full flex items-center justify-center mb-4">
            <Trash2 className="w-6 h-6 text-destructive" />
          </div>
          <DialogTitle className="text-center text-xl mb-2">Confirm Delete</DialogTitle>
          <p className="text-muted-foreground mb-6">
            Are you sure you want to delete this machine? This action cannot be undone.
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
