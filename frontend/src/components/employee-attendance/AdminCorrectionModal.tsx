import { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { apiClient } from '@/lib/api-client';
import { toast } from 'sonner';

interface AdminCorrectionModalProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  record: any;
  boxSlug: string;
  onSuccess: () => void;
}

export default function AdminCorrectionModal({ open, onOpenChange, record, boxSlug, onSuccess }: AdminCorrectionModalProps) {
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({ reason: '', status: '' });

  useEffect(() => {
    if (open && record?.corrections_statuses?.[0]) {
      const correction = record.corrections_statuses[0];
      setForm({
        reason: correction.reason || '',
        status: correction.status || 'pending'
      });
    }
  }, [open, record]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!record?.corrections_statuses?.[0]) return;
    
    setLoading(true);
    try {
      await apiClient.put(`/${boxSlug}/attendance/dashboard/update-correction`, {
        correctionId: record.corrections_statuses[0].id,
        reason: form.reason,
        status: form.status
      });
      toast.success('Correction request updated successfully');
      onOpenChange(false);
      onSuccess();
    } catch (error: any) {
      toast.error(error.message || 'Failed to update correction request');
    } finally {
      setLoading(false);
    }
  };

  if (!record || !record.corrections_statuses || !record.corrections_statuses[0]) return null;

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-xl bg-white">
        <DialogHeader>
          <DialogTitle>Attendance Corrections</DialogTitle>
        </DialogHeader>
        
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="bg-slate-50 p-4 rounded border space-y-3">
            <h4 className="font-bold text-slate-800 border-b pb-2 mb-3">Attendance Correction Request</h4>
            
            <div className="grid grid-cols-[150px_1fr] gap-1">
              <span className="font-semibold text-slate-700">Employee Name:</span>
              <span>{record.user_name}</span>
            </div>
            <div className="grid grid-cols-[150px_1fr] gap-1">
              <span className="font-semibold text-slate-700">Employee ID:</span>
              <span>{record.box_user?.employee_id || 'N/A'}</span>
            </div>
            <div className="grid grid-cols-[150px_1fr] gap-1">
              <span className="font-semibold text-slate-700">Attendance Date:</span>
              <span>{record.attendance_date !== 'N/A' ? new Date(record.attendance_date).toLocaleDateString('en-GB').replace(/\//g, '-') : '-'}</span>
            </div>
            <div className="grid grid-cols-[150px_1fr] gap-1">
              <span className="font-semibold text-slate-700">Requested Date & Time:</span>
              <span>{new Date(record.corrections_statuses[0].created_at).toLocaleString('en-GB').replace(/\//g, '-')}</span>
            </div>
            
            <div className="mt-4 pt-4 border-t space-y-3">
              <div className="grid grid-cols-[150px_1fr] gap-1">
                <span className="font-semibold text-slate-700">Check-in Status:</span>
                <span>
                  {record.check_in || 'Missed'} 
                  {record.corrections_statuses[0].punch_in_time ? ` / ${record.corrections_statuses[0].punch_in_time}` : ''}
                </span>
              </div>
              <div className="grid grid-cols-[150px_1fr] gap-1">
                <span className="font-semibold text-slate-700">Check-out Status:</span>
                <span>
                  {record.check_out || 'Missed'} 
                  {record.corrections_statuses[0].punch_out_time ? ` / ${record.corrections_statuses[0].punch_out_time}` : ''}
                </span>
              </div>
            </div>
          </div>

          <div className="space-y-2">
            <label className="font-semibold text-slate-700">Reason: <span className="text-red-500">*</span></label>
            <Input 
              value={form.reason} 
              onChange={(e) => setForm({...form, reason: e.target.value})} 
              placeholder="Enter reason..."
              required
            />
          </div>

          <div className="space-y-2">
            <label className="font-semibold text-slate-700">Status:</label>
            <Select 
              value={form.status || ''} 
              onValueChange={(val) => setForm({...form, status: val || ''})}
            >
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Select approval status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="rejected">Reject</SelectItem>
                <SelectItem value="pending">Pending</SelectItem>
                <SelectItem value="approved">Approve</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <DialogFooter className="mt-6 flex gap-2 sm:justify-end">
            <Button type="submit" className="bg-red-600 hover:bg-red-700 text-white" disabled={loading}>
              {loading ? 'Submitting...' : 'Submit'}
            </Button>
            <Button type="button" variant="outline" className="bg-orange-500 hover:bg-orange-600 text-white border-orange-500 hover:border-orange-600" onClick={() => onOpenChange(false)}>
              Close
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
