'use client';

import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Loader2, Trash2, X } from 'lucide-react';
import { toast } from 'sonner';

interface Fingerprint {
  id: number;
  user_name: string;
  device_name: string;
  device_id: string;
  status: string;
  created_at: string;
}

export default function EnrolledFingerprints() {
  const { box_slug } = useParams();
  const boxSlug = box_slug as string;

  const [fingerprints, setFingerprints] = useState<Fingerprint[]>([]);
  const [loading, setLoading] = useState(true);
  const [confirmAction, setConfirmAction] = useState<{type: 'revoke' | 'delete' | null, id: number | null}>({ type: null, id: null });
  const [isSubmitting, setIsSubmitting] = useState(false);

  const fetchFingerprints = async () => {
    setLoading(true);
    try {
      const res = await apiClient.get(`/${boxSlug}/mobile-attendance/fingerprints`);
      setFingerprints(res.data?.data || res.data || []);
    } catch (error) {
      console.error('Error fetching fingerprints:', error);
      toast.error('Failed to fetch enrolled fingerprints.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchFingerprints();
    }
  }, [boxSlug]);

  const executeConfirmAction = async () => {
    if (!confirmAction.id || !confirmAction.type) return;
    setIsSubmitting(true);
    const { id, type } = confirmAction;
    
    try {
      if (type === 'delete') {
        await apiClient.delete(`/${boxSlug}/mobile-attendance/fingerprints/${id}`);
        toast.success('Fingerprint deleted successfully.');
      } else {
        await apiClient.post(`/${boxSlug}/mobile-attendance/fingerprints/revoke/${id}`, {});
        toast.success('Fingerprint revoked successfully.');
      }
      fetchFingerprints();
      setConfirmAction({ type: null, id: null });
    } catch (error) {
      toast.error(`Failed to ${type} fingerprint.`);
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
          <h1 className="text-2xl font-bold tracking-tight">Enrolled Fingerprints</h1>
          <p className="text-sm text-muted-foreground">Manage employee fingerprints for mobile attendance tracking.</p>
        </div>

        <Dialog open={!!confirmAction.type} onOpenChange={(open) => !open && setConfirmAction({ type: null, id: null })}>
          <DialogContent className="sm:max-w-[425px]">
            <DialogHeader>
              <DialogTitle>
                {confirmAction.type === 'revoke' && 'Revoke Fingerprint'}
                {confirmAction.type === 'delete' && 'Delete Fingerprint'}
              </DialogTitle>
            </DialogHeader>
            <div className="py-4 text-muted-foreground">
              {confirmAction.type === 'revoke' && 'Are you sure you want to revoke this fingerprint? The user will no longer be able to punch attendance from it.'}
              {confirmAction.type === 'delete' && 'Are you sure you want to permanently delete this fingerprint? This action cannot be undone.'}
            </div>
            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => setConfirmAction({ type: null, id: null })}>Cancel</Button>
              <Button 
                type="button" 
                variant={confirmAction.type === 'delete' ? 'destructive' : 'default'}
                className={confirmAction.type === 'revoke' ? 'bg-orange-500 hover:bg-orange-600 text-white' : ''}
                onClick={executeConfirmAction} 
                disabled={isSubmitting}
              >
                {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                {confirmAction.type === 'revoke' && 'Yes, Revoke'}
                {confirmAction.type === 'delete' && 'Yes, Delete'}
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>

      <Card>
        <CardHeader className="p-4 border-b">
          <CardTitle className="text-lg">Registered Fingerprints</CardTitle>
        </CardHeader>
        <div className="overflow-auto w-full max-h-[70vh]">
          <Table>
            <TableHeader className="bg-muted/50 sticky top-0 z-10 shadow-sm">
              <TableRow className="bg-muted hover:bg-muted">
                <TableHead className="w-12 text-center">No</TableHead>
                <TableHead>Employee Name</TableHead>
                <TableHead>Device Name</TableHead>
                <TableHead>Device ID</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Enrolled At</TableHead>
                <TableHead className="text-right pr-6">Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={7} className="text-center py-10">
                    <div className="flex justify-center items-center">
                      <Loader2 className="h-6 w-6 animate-spin text-muted-foreground mr-2" />
                      <span className="text-muted-foreground">Loading fingerprints...</span>
                    </div>
                  </TableCell>
                </TableRow>
              ) : fingerprints.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={7} className="text-center py-10 text-muted-foreground">
                    No fingerprints registered yet.
                  </TableCell>
                </TableRow>
              ) : (
                fingerprints.map((fp, index) => {
                  const s = (fp.status || '').replace(/<[^>]+>/g, '').trim().toLowerCase();
                  return (
                    <TableRow key={fp.id}>
                      <TableCell className="text-center">{index + 1}</TableCell>
                      <TableCell className="font-medium" dangerouslySetInnerHTML={{ __html: fp.user_name || '-' }} />
                      <TableCell dangerouslySetInnerHTML={{ __html: fp.device_name || '-' }} />
                      <TableCell>{fp.device_id || '-'}</TableCell>
                      <TableCell>{getStatusBadge(fp.status)}</TableCell>
                      <TableCell>{fp.created_at ? new Date(fp.created_at).toLocaleString() : '-'}</TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          {s === 'active' && (
                            <Button size="sm" onClick={() => setConfirmAction({ type: 'revoke', id: fp.id })} className="bg-orange-500 hover:bg-orange-600 text-white">
                              Revoke
                            </Button>
                          )}
                          <Button size="sm" variant="destructive" onClick={() => setConfirmAction({ type: 'delete', id: fp.id })}>
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
