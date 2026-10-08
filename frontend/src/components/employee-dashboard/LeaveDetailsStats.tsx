import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogFooter,
} from "@/components/ui/dialog";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Calendar } from "lucide-react";
import { apiClient } from '@/lib/api-client';
import { toast } from 'sonner';

export default function LeaveDetailsStats({ stats, boxSlug }: { stats: any, boxSlug: string }) {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [leaveTypes, setLeaveTypes] = useState<any[]>([]);
  const [selectedLeaveType, setSelectedLeaveType] = useState("");
  const [leaveBalance, setLeaveBalance] = useState("0");
  const [loading, setLoading] = useState(false);

  // Form State
  const [formData, setFormData] = useState({
    applied_for: 'full_day',
    from_date: '',
    to_date: '',
    total_days: '',
    contact_during_leave: '',
    reason: ''
  });

  const fetchLeaveTypes = async () => {
    try {
      const res = await apiClient.get(`/${boxSlug}/leaves/types`);
      if (res && res.data) {
        setLeaveTypes(res.data);
      }
    } catch (error) {
      console.error('Error fetching leave types:', error);
    }
  };

  const fetchLeaveBalance = async (typeId: string) => {
    try {
      const res = await apiClient.get(`/${boxSlug}/leaves/balance`, { params: { leave_type_id: typeId } });
      if (res && res.data) {
        setLeaveBalance(res.data.balance_leave?.toString() || '0');
      } else {
        setLeaveBalance('0');
      }
    } catch (error) {
      console.error('Error fetching leave balance:', error);
      setLeaveBalance('0');
    }
  };

  useEffect(() => {
    if (isModalOpen) {
      fetchLeaveTypes();
    }
  }, [isModalOpen]);

  useEffect(() => {
    if (selectedLeaveType) {
      fetchLeaveBalance(selectedLeaveType);
    } else {
      setLeaveBalance("0");
    }
  }, [selectedLeaveType]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      const payload = {
        leave_type: selectedLeaveType,
        ...formData
      };
      // Mocking submission for now until POST API is fully wired
      await apiClient.post(`/${boxSlug}/leaves/request`, payload);
      toast.success("Leave request submitted successfully!");
      setIsModalOpen(false);
      
      // Reset form
      setFormData({
        applied_for: 'full_day',
        from_date: '',
        to_date: '',
        total_days: '',
        contact_during_leave: '',
        reason: ''
      });
      setSelectedLeaveType("");
    } catch (error: any) {
      toast.error(error.message || "Failed to submit leave request.");
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  return (
    <Card className="flex-fill border-0 shadow-sm h-full w-full flex flex-col">
      <CardHeader className="pb-3 border-b flex flex-row items-center justify-between">
        <CardTitle className="text-base font-bold text-slate-800">Leave Details</CardTitle>
        <button className="flex items-center gap-1 border rounded px-2 py-1 text-xs font-medium bg-white hover:bg-slate-50 transition-colors">
          <Calendar className="w-3.5 h-3.5" />
          <span className="text-slate-700">2026</span>
        </button>
      </CardHeader>
      <CardContent className="p-5 flex-1 flex flex-col justify-between h-[280px]">
        <div className="grid grid-cols-2 gap-y-5 gap-x-2 mb-4">
          <div>
            <span className="block text-[11px] text-slate-500 mb-1">Total Leaves</span>
            <h4 className="text-xl font-bold text-slate-900">{stats?.applied_leaves || 0}</h4>
          </div>
          <div>
            <span className="block text-[11px] text-slate-500 mb-1">Taken</span>
            <h4 className="text-xl font-bold text-slate-900">{stats?.approved_leaves || 0}</h4>
          </div>
          <div>
            <span className="block text-[11px] text-slate-500 mb-1">Absent</span>
            <h4 className="text-xl font-bold text-slate-900">{stats?.absent || 0}</h4>
          </div>
          <div>
            <span className="block text-[11px] text-slate-500 mb-1">Request</span>
            <h4 className="text-xl font-bold text-slate-900">{stats?.pending_leaves || 0}</h4>
          </div>
          <div>
            <span className="block text-[11px] text-slate-500 mb-1">Worked Days</span>
            <h4 className="text-xl font-bold text-slate-900">{stats?.present || 0}</h4>
          </div>
          <div>
            <span className="block text-[11px] text-slate-500 mb-1">Loss of Pay</span>
            <h4 className="text-xl font-bold text-slate-900">0</h4>
          </div>
        </div>

        <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
          <DialogTrigger render={<Button className="w-full bg-[#1e1e2d] hover:bg-[#2b2b40] text-white font-medium py-5 text-sm rounded-md mt-auto" />}>
            Apply New Leave
          </DialogTrigger>
          <DialogContent className="sm:max-w-[700px] bg-white">
            <DialogHeader>
              <DialogTitle className="text-xl font-bold">Add Leave</DialogTitle>
            </DialogHeader>
            <form onSubmit={handleSubmit} className="mt-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label>Leave Type</Label>
                  <Select value={selectedLeaveType} onValueChange={(val) => setSelectedLeaveType(val || '')}>
                    <SelectTrigger>
                      <SelectValue placeholder="Select Option" />
                    </SelectTrigger>
                    <SelectContent>
                      {leaveTypes.map((lt: any) => (
                        <SelectItem key={lt.id} value={lt.id.toString()}>{lt.leave_type}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>Leave Balance</Label>
                  <Input readOnly value={leaveBalance} className="bg-slate-50" />
                </div>
                <div className="col-span-2 space-y-2">
                  <Label>Applied For</Label>
                  <Select value={formData.applied_for} onValueChange={(v) => handleChange('applied_for', v || '')}>
                    <SelectTrigger>
                      <SelectValue placeholder="Applied For" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="full_day">Full Day</SelectItem>
                      <SelectItem value="half_day">Half Day</SelectItem>
                      <SelectItem value="short_leave">Short Leave</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>From Date</Label>
                  <div className="relative">
                    <Input type="date" value={formData.from_date} onChange={(e) => handleChange('from_date', e.target.value)} />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label>To Date</Label>
                  <div className="relative">
                    <Input type="date" value={formData.to_date} onChange={(e) => handleChange('to_date', e.target.value)} />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label>Total Days</Label>
                  <Input type="text" value={formData.total_days} onChange={(e) => handleChange('total_days', e.target.value)} />
                </div>
                <div className="space-y-2">
                  <Label>Contact During Leave</Label>
                  <Input type="text" value={formData.contact_during_leave} onChange={(e) => handleChange('contact_during_leave', e.target.value)} />
                </div>
                <div className="col-span-2 space-y-2">
                  <Label>Reason for Leave</Label>
                  <Textarea rows={3} value={formData.reason} onChange={(e) => handleChange('reason', e.target.value)} />
                </div>
                <div className="col-span-2 space-y-2">
                  <Label>Attachment (PDF, DOC, DOCX, JPG, PNG)</Label>
                  <Input type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                </div>
              </div>
              <DialogFooter className="mt-6">
                <Button type="button" variant="outline" onClick={() => setIsModalOpen(false)}>
                  Cancel
                </Button>
                <Button type="submit" disabled={loading} className="bg-primary text-primary-foreground">
                  {loading ? 'Applying...' : 'Apply'}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </CardContent>
    </Card>
  );
}
