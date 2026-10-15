"use client";

import { useState, useEffect, useMemo } from "react";
import { useParams } from "next/navigation";
import { apiClient } from "@/lib/api-client";
import { toast } from "sonner";
import {
  Plus,
  RefreshCw,
  Search,
  Pencil,
  Trash2,
  Gift,
  ShieldCheck,
  CheckCircle2,
  XCircle,
  FileText,
  Layers,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Switch } from "@/components/ui/switch";
import { Checkbox } from "@/components/ui/checkbox";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from "@/components/ui/dialog";

export default function LeaveTypePage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const [loading, setLoading] = useState(true);
  const [leaveTypes, setLeaveTypes] = useState<any[]>([]);
  const [searchTerm, setSearchTerm] = useState("");

  // Modals state
  const [isFormModalOpen, setIsFormModalOpen] = useState(false);
  const [isCreditModalOpen, setIsCreditModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [submitting, setSubmitting] = useState(false);

  // Form State
  const initialForm = {
    leave_type: "",
    leave_code: "",
    credit_frequency: "Monthly",
    credit_days: "1",
    number_of_days: "12",
    holiday_off: false,
    holiday_off_details: "",
    weekly_off: false,
    weekly_off_details: "",
    overtime_equal_shift: false,
    overtime_equal_shift_details: "",
    carry_forward: "no",
    carry_forward_days: "0",
    status: "Active",
  };
  const [formData, setFormData] = useState(initialForm);

  // Credit Month State
  const [creditMonth, setCreditMonth] = useState(() => {
    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, "0");
    return `${y}-${m}`;
  });

  const fetchLeaveTypes = async () => {
    setLoading(true);
    try {
      const res: any = await apiClient.get(`/${boxSlug}/leaves/types/data`);
      if (res?.data && Array.isArray(res.data)) {
        setLeaveTypes(res.data);
      } else if (Array.isArray(res)) {
        setLeaveTypes(res);
      } else {
        setLeaveTypes([]);
      }
    } catch (err: any) {
      console.error("Failed to load leave types", err);
      toast.error(err.message || "Failed to load leave types");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchLeaveTypes();
    }
  }, [boxSlug]);

  // Auto-calculate number_of_days when frequency or credit_days changes
  const handleFrequencyOrCreditDaysChange = (freq: string, days: number) => {
    const frequencyMap: Record<string, number> = {
      Monthly: 12,
      "Alternate Month": 6,
      Quarterly: 4,
      "Half Yearly": 2,
      Yearly: 1,
    };
    const multiplier = frequencyMap[freq] ?? 1;
    const total = days * multiplier;
    setFormData((prev) => ({
      ...prev,
      credit_frequency: freq,
      credit_days: String(days),
      number_of_days: String(total),
    }));
  };

  const handleOpenAdd = () => {
    setEditingId(null);
    setFormData(initialForm);
    setIsFormModalOpen(true);
  };

  const handleOpenEdit = (item: any) => {
    setEditingId(item.id);
    setFormData({
      leave_type: item.leave_type || "",
      leave_code: item.leave_code || "",
      credit_frequency: item.credit_frequency || "Monthly",
      credit_days: String(item.credit_days || "1"),
      number_of_days: String(item.number_of_days || "12"),
      holiday_off: Boolean(item.holiday_off),
      holiday_off_details: item.holiday_off_details || "",
      weekly_off: Boolean(item.weekly_off),
      weekly_off_details: item.weekly_off_details || "",
      overtime_equal_shift: Boolean(item.overtime_equal_shift),
      overtime_equal_shift_details: item.overtime_equal_shift_details || "",
      carry_forward: item.carry_forward || "no",
      carry_forward_days: String(item.carry_forward_days || "0"),
      status: item.status || "Active",
    });
    setIsFormModalOpen(true);
  };

  const handleSaveLeaveType = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.leave_type.trim()) {
      toast.error("Please enter a leave type name");
      return;
    }

    setSubmitting(true);
    try {
      const payload: any = {
        ...formData,
        credit_days: Number(formData.credit_days),
        number_of_days: Number(formData.number_of_days),
        carry_forward_days: Number(formData.carry_forward_days),
      };

      if (editingId) {
        payload.leave_id = editingId;
        await apiClient.put(`/${boxSlug}/leaves/types`, payload);
        toast.success("Leave type updated successfully");
      } else {
        await apiClient.post(`/${boxSlug}/leaves/types`, payload);
        toast.success("Leave type created successfully");
      }
      setIsFormModalOpen(false);
      fetchLeaveTypes();
    } catch (err: any) {
      toast.error(err.message || "Failed to save leave type");
    } finally {
      setSubmitting(false);
    }
  };

  const handleToggleStatus = async (item: any) => {
    try {
      const currentStatus =
        item.status === "Active" || item.status === "1" || item.status === 1
          ? "Active"
          : "Inactive";
      const nextStatus = currentStatus === "Active" ? "Inactive" : "Active";

      await apiClient.post(`/${boxSlug}/leaves/types/toggle-status`, {
        id: item.id,
        leave_id: item.id,
        status: nextStatus,
      });
      toast.success(`Leave type marked as ${nextStatus}`);
      fetchLeaveTypes();
    } catch (err: any) {
      toast.error(err.message || "Failed to toggle status");
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this leave type?")) return;
    try {
      await apiClient.delete(`/${boxSlug}/leaves/types`, {
        params: { id, leave_id: id },
      });
      toast.success("Leave type deleted");
      fetchLeaveTypes();
    } catch (err: any) {
      toast.error(err.message || "Failed to delete leave type");
    }
  };

  const handleCreditLeaves = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!creditMonth) {
      toast.error("Please select a month to credit leaves");
      return;
    }
    setSubmitting(true);
    try {
      const res: any = await apiClient.post(`/${boxSlug}/leaves/credit`, {
        month: creditMonth,
      });
      toast.success(res?.message || `Leave credit job dispatched for ${creditMonth}!`);
      setIsCreditModalOpen(false);
      fetchLeaveTypes();
    } catch (err: any) {
      toast.error(err.message || "Failed to credit leaves");
    } finally {
      setSubmitting(false);
    }
  };

  const filteredLeaveTypes = useMemo(() => {
    return leaveTypes.filter((item) => {
      const matchSearch =
        !searchTerm ||
        item.leave_type?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        item.leave_code?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        item.credit_frequency?.toLowerCase().includes(searchTerm.toLowerCase());
      return matchSearch;
    });
  }, [leaveTypes, searchTerm]);

  return (
    <div className="space-y-6 p-4 md:p-8 max-w-7xl mx-auto">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/40 pb-5">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold tracking-tight text-foreground">
            Leave Types
          </h1>
          <p className="text-sm text-muted-foreground mt-1">
            Configure leave entitlements, credit frequencies, carry-forwards, and compensation rules.
          </p>
        </div>
        <div className="flex items-center gap-3">
          <Button
            variant="outline"
            size="sm"
            onClick={fetchLeaveTypes}
            className="gap-2"
          >
            <RefreshCw className="h-4 w-4" />
            Refresh
          </Button>
          <Button
            variant="outline"
            onClick={() => setIsCreditModalOpen(true)}
            className="gap-2 border-primary/40 text-primary hover:bg-primary/5"
          >
            <Gift className="h-4 w-4" />
            Credit Leave
          </Button>
          <Button onClick={handleOpenAdd} className="gap-2">
            <Plus className="h-4 w-4" />
            Add Leave Type
          </Button>
        </div>
      </div>

      {/* Search Toolbar */}
      <Card className="border-border/60 shadow-sm">
        <CardContent className="p-4 flex items-center justify-between gap-4">
          <div className="relative flex-1 max-w-md">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search leave types by name, code, frequency..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-9 h-9"
            />
          </div>
          <span className="text-xs text-muted-foreground">
            Total {filteredLeaveTypes.length} configured types
          </span>
        </CardContent>
      </Card>

      {/* Table */}
      <Card className="border-border/60 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader className="bg-muted/40">
              <TableRow>
                <TableHead className="w-[200px]">Leave Type</TableHead>
                <TableHead>Leave Code</TableHead>
                <TableHead>Total Days/Yr</TableHead>
                <TableHead>Frequency</TableHead>
                <TableHead>Credit / Cycle</TableHead>
                <TableHead>Comp Off Rules</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={8} className="text-center py-12 text-muted-foreground">
                    <div className="flex items-center justify-center gap-2">
                      <RefreshCw className="h-4 w-4 animate-spin" />
                      Loading leave configurations...
                    </div>
                  </TableCell>
                </TableRow>
              ) : filteredLeaveTypes.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={8} className="text-center py-12 text-muted-foreground">
                    No leave types found.
                  </TableCell>
                </TableRow>
              ) : (
                filteredLeaveTypes.map((item) => {
                  const isActive = item.status === "Active" || item.status === "1";
                  const compOffs = [];
                  if (item.holiday_off) compOffs.push("Holiday");
                  if (item.weekly_off) compOffs.push("Weekly Off");
                  if (item.overtime_equal_shift) compOffs.push("Overtime");

                  return (
                    <TableRow key={item.id} className="hover:bg-muted/30">
                      <TableCell className="font-semibold text-sm">
                        <div className="flex items-center gap-2">
                          <Layers className="h-4 w-4 text-primary" />
                          <span>{item.leave_type}</span>
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge variant="outline" className="font-mono text-xs">
                          {item.leave_code || "-"}
                        </Badge>
                      </TableCell>
                      <TableCell className="font-semibold text-sm">
                        {item.number_of_days ?? "-"} Days
                      </TableCell>
                      <TableCell className="text-sm text-muted-foreground">
                        {item.credit_frequency || "-"}
                      </TableCell>
                      <TableCell className="text-sm">
                        {item.credit_days ? `${item.credit_days} days` : "-"}
                      </TableCell>
                      <TableCell>
                        {compOffs.length > 0 ? (
                          <div className="flex flex-wrap gap-1">
                            {compOffs.map((c) => (
                              <Badge key={c} variant="secondary" className="text-[10px] py-0 px-1.5">
                                {c}
                              </Badge>
                            ))}
                          </div>
                        ) : (
                          <span className="text-xs text-muted-foreground">Standard</span>
                        )}
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-2">
                          <Switch
                            checked={isActive}
                            onCheckedChange={() => handleToggleStatus(item)}
                          />
                          <span className="text-xs text-muted-foreground">
                            {isActive ? "Active" : "Inactive"}
                          </span>
                        </div>
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex items-center justify-end gap-1">
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => handleOpenEdit(item)}
                            className="h-8 w-8 p-0"
                          >
                            <Pencil className="h-4 w-4 text-muted-foreground" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => handleDelete(item.id)}
                            className="h-8 w-8 p-0 text-destructive hover:bg-destructive/10"
                          >
                            <Trash2 className="h-4 w-4" />
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

      {/* Add / Edit Modal */}
      <Dialog open={isFormModalOpen} onOpenChange={setIsFormModalOpen}>
        <DialogContent className="sm:max-w-xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle className="text-lg font-semibold flex items-center gap-2">
              <Layers className="h-5 w-5 text-primary" />
              {editingId ? "Edit Leave Type" : "Add Leave Type"}
            </DialogTitle>
          </DialogHeader>

          <form onSubmit={handleSaveLeaveType} className="space-y-4 pt-2">
            {/* Row 1: Type Name & Code */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label htmlFor="leave_type_name">
                  Leave Type Name <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="leave_type_name"
                  placeholder="e.g. Annual Leave, Casual Leave"
                  value={formData.leave_type}
                  onChange={(e) => setFormData((p) => ({ ...p, leave_type: e.target.value }))}
                  required
                />
              </div>

              <div className="space-y-1.5">
                <Label htmlFor="leave_code_input">Leave Code</Label>
                <Input
                  id="leave_code_input"
                  placeholder="e.g. AL, CL, ML"
                  value={formData.leave_code}
                  onChange={(e) => setFormData((p) => ({ ...p, leave_code: e.target.value }))}
                />
              </div>
            </div>

            {/* Row 2: Credit Frequency & Credit per Cycle */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label>Credit Frequency</Label>
                <Select
                  value={formData.credit_frequency}
                  onValueChange={(val) =>
                    handleFrequencyOrCreditDaysChange(
                      val || "Monthly",
                      Number(formData.credit_days)
                    )
                  }
                >
                  <SelectTrigger className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="Monthly">Monthly</SelectItem>
                    <SelectItem value="Alternate Month">Alternate Month</SelectItem>
                    <SelectItem value="Quarterly">Quarterly</SelectItem>
                    <SelectItem value="Half Yearly">Half Yearly</SelectItem>
                    <SelectItem value="Yearly">Yearly</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-1.5">
                <Label htmlFor="credit_days_input">Credit / Cycle</Label>
                <Input
                  id="credit_days_input"
                  type="number"
                  step="0.5"
                  min="0"
                  value={formData.credit_days}
                  onChange={(e) =>
                    handleFrequencyOrCreditDaysChange(
                      formData.credit_frequency,
                      Number(e.target.value)
                    )
                  }
                  required
                  className="w-full"
                />
              </div>
            </div>

            {/* Row 3: Total Days / Year & Status */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label htmlFor="number_of_days_input">Total Days / Year</Label>
                <Input
                  id="number_of_days_input"
                  type="number"
                  step="0.5"
                  min="0"
                  value={formData.number_of_days}
                  onChange={(e) =>
                    setFormData((p) => ({ ...p, number_of_days: e.target.value }))
                  }
                  required
                  className="w-full"
                />
              </div>

              <div className="space-y-1.5">
                <Label>Status</Label>
                <Select
                  value={formData.status}
                  onValueChange={(val) =>
                    setFormData((p) => ({ ...p, status: val || "Active" }))
                  }
                >
                  <SelectTrigger className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="Active">Active</SelectItem>
                    <SelectItem value="Inactive">Inactive</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            {/* Row 4: Carry Forward Settings */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label>Carry Forward to Next Year</Label>
                <Select
                  value={formData.carry_forward}
                  onValueChange={(val) =>
                    setFormData((p) => ({ ...p, carry_forward: val || "no" }))
                  }
                >
                  <SelectTrigger className="w-full">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="no">No</SelectItem>
                    <SelectItem value="yes">Yes</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              {formData.carry_forward === "yes" ? (
                <div className="space-y-1.5">
                  <Label htmlFor="cf_days">Max Carry Forward Days</Label>
                  <Input
                    id="cf_days"
                    type="number"
                    min="1"
                    value={formData.carry_forward_days}
                    onChange={(e) =>
                      setFormData((p) => ({ ...p, carry_forward_days: e.target.value }))
                    }
                  />
                </div>
              ) : (
                <div className="space-y-1.5 opacity-40">
                  <Label htmlFor="cf_days_disabled">Max Carry Forward Days</Label>
                  <Input id="cf_days_disabled" disabled value="0" />
                </div>
              )}
            </div>

            {/* Row 5: Compensatory Off Rules */}
            <div className="border rounded-lg p-3.5 space-y-3 bg-muted/20">
              <Label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground block">
                Compensatory Off Rules
              </Label>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div className="flex items-center space-x-2">
                  <Checkbox
                    id="holiday_off"
                    checked={formData.holiday_off}
                    onCheckedChange={(checked) =>
                      setFormData((p) => ({ ...p, holiday_off: Boolean(checked) }))
                    }
                  />
                  <Label htmlFor="holiday_off" className="text-xs font-normal cursor-pointer leading-tight">
                    Holiday Off
                  </Label>
                </div>

                <div className="flex items-center space-x-2">
                  <Checkbox
                    id="weekly_off"
                    checked={formData.weekly_off}
                    onCheckedChange={(checked) =>
                      setFormData((p) => ({ ...p, weekly_off: Boolean(checked) }))
                    }
                  />
                  <Label htmlFor="weekly_off" className="text-xs font-normal cursor-pointer leading-tight">
                    Weekly Off
                  </Label>
                </div>

                <div className="flex items-center space-x-2">
                  <Checkbox
                    id="overtime_off"
                    checked={formData.overtime_equal_shift}
                    onCheckedChange={(checked) =>
                      setFormData((p) => ({ ...p, overtime_equal_shift: Boolean(checked) }))
                    }
                  />
                  <Label htmlFor="overtime_off" className="text-xs font-normal cursor-pointer leading-tight">
                    Overtime = Shift
                  </Label>
                </div>
              </div>
            </div>

            <DialogFooter className="pt-2">
              <Button
                type="button"
                variant="outline"
                onClick={() => setIsFormModalOpen(false)}
                disabled={submitting}
              >
                Cancel
              </Button>
              <Button type="submit" disabled={submitting}>
                {submitting ? "Saving..." : editingId ? "Update Leave Type" : "Create Leave Type"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Credit Leave Modal */}
      <Dialog open={isCreditModalOpen} onOpenChange={setIsCreditModalOpen}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle className="text-lg font-semibold flex items-center gap-2">
              <Gift className="h-5 w-5 text-primary" />
              Credit Monthly Leaves
            </DialogTitle>
          </DialogHeader>

          <form onSubmit={handleCreditLeaves} className="space-y-4 pt-2">
            <div className="space-y-1.5">
              <Label htmlFor="credit_month" className="text-sm font-medium">
                Select Cycle Month <span className="text-destructive">*</span>
              </Label>
              <Input
                id="credit_month"
                type="month"
                value={creditMonth}
                onChange={(e) => setCreditMonth(e.target.value)}
                required
                className="w-full"
              />
              <p className="text-xs text-muted-foreground mt-1 leading-relaxed">
                This will trigger the background calculation and credit leaves to all active employees for this cycle according to each leave type's credit frequency.
              </p>
            </div>

            <DialogFooter className="pt-2">
              <Button
                type="button"
                variant="outline"
                onClick={() => setIsCreditModalOpen(false)}
                disabled={submitting}
              >
                Cancel
              </Button>
              <Button type="submit" disabled={submitting}>
                {submitting ? "Processing..." : "Dispatch Credit Job"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
