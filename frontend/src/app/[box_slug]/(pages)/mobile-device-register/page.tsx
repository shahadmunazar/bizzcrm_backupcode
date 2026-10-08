'use client';

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Loader2, Check, X, Trash2, Plus } from 'lucide-react';
import { toast } from 'sonner';

interface Device {
  id: number;
  device_name: string;
  platform: string;
  os_version: string;
  brand: string;
  model: string;
  status: string;
}

export default function MobileDeviceRegister() {
  const { box_slug } = useParams();
  const boxSlug = box_slug as string;

  const [devices, setDevices] = useState<Device[]>([]);
  const [loading, setLoading] = useState(true);
  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [confirmAction, setConfirmAction] = useState<{type: 'approve' | 'revoke' | 'delete' | 'reject' | null, id: number | null}>({ type: null, id: null });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [newDevice, setNewDevice] = useState({
    device_name: '',
    platform: '',
    os_version: '',
    app_version: '',
    brand: '',
    model: '',
    status: 'Active',
    device_id: '',
    public_key: '',
    key_algorithm: '',
    challenge_id: '',
    signed_payload: '',
    signature: ''
  });

  const fetchDevices = async () => {
    setLoading(true);
    try {
      const res = await apiClient.get(`/${boxSlug}/device-register`);
      // DataTables returns data in 'data' array
      if (res && typeof res === 'object' && res.data && Array.isArray(res.data)) {
        setDevices(res.data);
      } else if (Array.isArray(res)) {
        setDevices(res);
      } else {
        setDevices([]);
      }
    } catch (error) {
      console.error('Error fetching devices:', error);
      toast.error('Failed to fetch registered devices.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchDevices();
    }
  }, [boxSlug]);

  const executeConfirmAction = async () => {
    if (!confirmAction.id || !confirmAction.type) return;
    setIsSubmitting(true);
    const { id, type } = confirmAction;
    
    try {
      if (type === 'delete') {
        await apiClient.delete(`/${boxSlug}/device-register/${id}`);
        toast.success('Device deleted successfully.');
      } else {
        const endpointType = type === 'reject' ? 'revoke' : type;
        await apiClient.post(`/${boxSlug}/device-register/${endpointType}/${id}`, {});
        toast.success(`Device marked as ${type === 'approve' ? 'Active' : 'Inactive'}.`);
      }
      fetchDevices();
      setConfirmAction({ type: null, id: null });
    } catch (error) {
      toast.error(`Failed to ${type} device.`);
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleAddDevice = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      await apiClient.post(`/${boxSlug}/device-register`, newDevice);
      toast.success('Device added successfully.');
      setIsAddModalOpen(false);
      setNewDevice({
        device_name: '', platform: '', os_version: '', app_version: '', 
        brand: '', model: '', status: 'Active', device_id: '',
        public_key: '', key_algorithm: '', challenge_id: '', signed_payload: '', signature: ''
      });
      fetchDevices();
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Failed to add device.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const getStatusBadge = (status: string) => {
    const s = (status || '').replace(/<[^>]+>/g, '').trim().toLowerCase();
    if (s === 'active') return <Badge className="bg-emerald-100 text-emerald-700 hover:bg-emerald-100">Active</Badge>;
    if (s === 'inactive' || s === 'revoked') return <Badge variant="destructive">Inactive</Badge>;
    return <Badge className="bg-orange-100 text-orange-700 hover:bg-orange-100">Pending</Badge>;
  };

  return (
    <div className="p-6 space-y-6 max-w-[1600px] mx-auto">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">Mobile Device Registration</h1>
          <p className="text-sm text-muted-foreground">Manage and approve employee mobile devices for attendance tracking.</p>
        </div>
        <Button onClick={() => setIsAddModalOpen(true)}>
          <Plus className="h-4 w-4 mr-2" /> Add Mobile Device
        </Button>
        <Dialog open={isAddModalOpen} onOpenChange={setIsAddModalOpen}>
          <DialogContent className="sm:max-w-[600px]">
            <form onSubmit={handleAddDevice}>
              <DialogHeader>
                <DialogTitle>Add Mobile Device</DialogTitle>
              </DialogHeader>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 py-4">
                <div className="space-y-2">
                  <Label>Device Name *</Label>
                  <Input required value={newDevice.device_name} onChange={e => setNewDevice({...newDevice, device_name: e.target.value})} />
                </div>
                <div className="space-y-2">
                  <Label>Platform *</Label>
                  <Input required value={newDevice.platform} onChange={e => setNewDevice({...newDevice, platform: e.target.value})} />
                </div>
                <div className="space-y-2">
                  <Label>OS Version</Label>
                  <Input value={newDevice.os_version} onChange={e => setNewDevice({...newDevice, os_version: e.target.value})} />
                </div>
                <div className="space-y-2">
                  <Label>App Version</Label>
                  <Input value={newDevice.app_version} onChange={e => setNewDevice({...newDevice, app_version: e.target.value})} />
                </div>
                <div className="space-y-2">
                  <Label>Brand</Label>
                  <Input value={newDevice.brand} onChange={e => setNewDevice({...newDevice, brand: e.target.value})} />
                </div>
                <div className="space-y-2">
                  <Label>Model</Label>
                  <Input value={newDevice.model} onChange={e => setNewDevice({...newDevice, model: e.target.value})} />
                </div>
                <div className="space-y-2">
                  <Label>Device ID</Label>
                  <Input value={newDevice.device_id} onChange={e => setNewDevice({...newDevice, device_id: e.target.value})} />
                </div>
                <div className="space-y-2">
                  <Label>Status *</Label>
                  <Select value={newDevice.status} onValueChange={(v: any) => setNewDevice({...newDevice, status: v})}>
                    <SelectTrigger>
                      <SelectValue placeholder="Select Status" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Active">Active</SelectItem>
                      <SelectItem value="Inactive">Inactive</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2 col-span-1 md:col-span-2">
                  <Label>Public Key</Label>
                  <Textarea value={newDevice.public_key} onChange={e => setNewDevice({...newDevice, public_key: e.target.value})} className="resize-none" />
                </div>
                <div className="space-y-2">
                  <Label>Key Algorithm</Label>
                  <Input value={newDevice.key_algorithm} onChange={e => setNewDevice({...newDevice, key_algorithm: e.target.value})} />
                </div>
                <div className="space-y-2">
                  <Label>Challenge ID</Label>
                  <Input value={newDevice.challenge_id} onChange={e => setNewDevice({...newDevice, challenge_id: e.target.value})} />
                </div>
                <div className="space-y-2 col-span-1 md:col-span-2">
                  <Label>Signed Payload</Label>
                  <Textarea value={newDevice.signed_payload} onChange={e => setNewDevice({...newDevice, signed_payload: e.target.value})} className="resize-none" />
                </div>
                <div className="space-y-2 col-span-1 md:col-span-2">
                  <Label>Signature</Label>
                  <Textarea value={newDevice.signature} onChange={e => setNewDevice({...newDevice, signature: e.target.value})} className="resize-none" />
                </div>
              </div>
              <DialogFooter>
                <Button type="button" variant="outline" onClick={() => setIsAddModalOpen(false)}>Cancel</Button>
                <Button type="submit" disabled={isSubmitting}>
                  {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                  Save Device
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>

        <Dialog open={!!confirmAction.type} onOpenChange={(open) => !open && setConfirmAction({ type: null, id: null })}>
          <DialogContent className="sm:max-w-[425px]">
            <DialogHeader>
              <DialogTitle>
                {confirmAction.type === 'approve' && 'Activate Device'}
                {confirmAction.type === 'revoke' && 'Deactivate Device'}
                {confirmAction.type === 'reject' && 'Reject Device Request'}
                {confirmAction.type === 'delete' && 'Delete Device'}
              </DialogTitle>
            </DialogHeader>
            <div className="py-4 text-muted-foreground">
              {confirmAction.type === 'approve' && 'Are you sure you want to mark this device as Active?'}
              {confirmAction.type === 'revoke' && 'Are you sure you want to mark this device as Inactive? The user will no longer be able to punch attendance from it.'}
              {confirmAction.type === 'reject' && 'Are you sure you want to reject this device request? It will be marked as Inactive.'}
              {confirmAction.type === 'delete' && 'Are you sure you want to permanently delete this device? This action cannot be undone.'}
            </div>
            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => setConfirmAction({ type: null, id: null })}>Cancel</Button>
              <Button 
                type="button" 
                variant={confirmAction.type === 'delete' || confirmAction.type === 'reject' ? 'destructive' : 'default'}
                className={
                  confirmAction.type === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 
                  confirmAction.type === 'revoke' ? 'bg-orange-500 hover:bg-orange-600 text-white' : ''
                }
                onClick={executeConfirmAction} 
                disabled={isSubmitting}
              >
                {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                {confirmAction.type === 'approve' && 'Yes, Activate'}
                {confirmAction.type === 'revoke' && 'Yes, Deactivate'}
                {confirmAction.type === 'reject' && 'Yes, Reject'}
                {confirmAction.type === 'delete' && 'Yes, Delete'}
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>

      <Card>
        <CardHeader className="p-4 border-b">
          <CardTitle className="text-lg">Registered Devices</CardTitle>
        </CardHeader>
        <div className="overflow-auto w-full max-h-[70vh]">
          <Table>
            <TableHeader className="bg-muted/50 sticky top-0 z-10 shadow-sm">
              <TableRow className="bg-muted hover:bg-muted">
                <TableHead className="w-12 text-center">No</TableHead>
                <TableHead>Device Name</TableHead>
                <TableHead>Platform</TableHead>
                <TableHead>OS Version</TableHead>
                <TableHead>Brand</TableHead>
                <TableHead>Model</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right pr-6">Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={8} className="text-center py-10">
                    <div className="flex justify-center items-center">
                      <Loader2 className="h-6 w-6 animate-spin text-muted-foreground mr-2" />
                      <span className="text-muted-foreground">Loading devices...</span>
                    </div>
                  </TableCell>
                </TableRow>
              ) : devices.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={8} className="text-center py-10 text-muted-foreground">
                    No devices registered yet.
                  </TableCell>
                </TableRow>
              ) : (
                devices.map((device, index) => {
                  const rawStatus = (device.status || '').replace(/<[^>]+>/g, '').trim().toLowerCase();
                  const s = rawStatus;
                  return (
                    <TableRow key={device.id}>
                      <TableCell className="text-center">{index + 1}</TableCell>
                      <TableCell className="font-medium" dangerouslySetInnerHTML={{ __html: device.device_name || '-' }} />
                      <TableCell>{device.platform || '-'}</TableCell>
                      <TableCell>{device.os_version || '-'}</TableCell>
                      <TableCell>{device.brand || '-'}</TableCell>
                      <TableCell>{device.model || '-'}</TableCell>
                      <TableCell>{getStatusBadge(device.status)}</TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          {(s === 'pending approval' || s === 'pending_approval' || s === 'pending') && (
                            <>
                              <Button size="sm" onClick={() => setConfirmAction({ type: 'approve', id: device.id })} className="bg-blue-600 hover:bg-blue-700">
                                <Check className="h-4 w-4 mr-1" /> Approve
                              </Button>
                              <Button size="sm" onClick={() => setConfirmAction({ type: 'reject', id: device.id })} className="bg-red-600 hover:bg-red-700 text-white">
                                <X className="h-4 w-4 mr-1" /> Reject
                              </Button>
                            </>
                          )}
                          {(s === 'revoked' || s === 'inactive') && (
                            <Button size="sm" onClick={() => setConfirmAction({ type: 'approve', id: device.id })} className="bg-emerald-500 hover:bg-emerald-600 text-white">
                              <Check className="h-4 w-4 mr-1" /> Mark Active
                            </Button>
                          )}
                          {s === 'active' && (
                            <Button size="sm" onClick={() => setConfirmAction({ type: 'revoke', id: device.id })} className="bg-orange-500 hover:bg-orange-600 text-white">
                              Mark Inactive
                            </Button>
                          )}
                          <Button size="sm" variant="destructive" onClick={() => setConfirmAction({ type: 'delete', id: device.id })}>
                            <Trash2 className="h-4 w-4 mr-1" /> Delete
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  );
                })
              )}
            </TableBody>
          </Table>
        </div>
      </Card>
    </div>
  );
}
