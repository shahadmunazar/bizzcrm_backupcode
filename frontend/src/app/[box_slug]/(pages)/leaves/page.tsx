"use client";

import { useState, useEffect, useMemo } from "react";
import { useParams } from "next/navigation";
import { apiClient } from "@/lib/api-client";
import { toast } from "sonner";
import {
  Calendar as CalendarIcon,
  Plus,
  RefreshCw,
  Search,
  Filter,
  CheckCircle2,
  Clock,
  XCircle,
  FileText,
  CalendarDays,
  HeartPulse,
  Coffee,
  Sparkles,
  Trash2,
  ChevronRight,
  ExternalLink,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
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

export default function EmployeeLeavesPage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const [loading, setLoading] = useState(true);
  const [records, setRecords] = useState<any[]>([]);
  const [stats, setStats] = useState<any>(null);
  const [leaveTypes, setLeaveTypes] = useState<any[]>([]);

  // Filters
  const [searchTerm, setSearchTerm] = useState("");
  const [typeFilter, setTypeFilter] = useState("all");
  const [statusFilter, setStatusFilter] = useState("all");

  // Modal State
  const [isApplyModalOpen, setIsApplyModalOpen] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    leave_type: "",
    from_date: "",
    to_date: "",
    total_days: "1",
    contact_during_leave: "",
    reason: "",
  });

  const fetchLeaveTypes = async () => {
    try {
      const res: any = await apiClient.get(`/${boxSlug}/leaves/types`);
      if (res?.data && Array.isArray(res.data)) {
        setLeaveTypes(res.data);
      } else if (Array.isArray(res)) {
        setLeaveTypes(res);
      }
    } catch (err: any) {
      console.error("Failed to load leave types", err);
    }
  };

  const fetchStats = async () => {
    try {
      const res: any = await apiClient.get(`/${boxSlug}/leaves/stats`);
      if (res?.data) {
        setStats(res.data);
      }
    } catch (err: any) {
      console.error("Failed to load leave stats", err);
    }
  };

  const fetchRecords = async () => {
    setLoading(true);
    try {
      const res: any = await apiClient.get(`/${boxSlug}/leaves/records`);
      // DataTables JSON structure { data: [...] }
      if (res?.data && Array.isArray(res.data)) {
        setRecords(res.data);
      } else if (Array.isArray(res)) {
        setRecords(res);
      } else {
        setRecords([]);
      }
    } catch (err: any) {
      console.error("Failed to load leave records", err);
      toast.error(err.message || "Failed to fetch leave history");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchLeaveTypes();
      fetchStats();
      fetchRecords();
    }
  }, [boxSlug]);

  // Auto calculate total days when dates change
  useEffect(() => {
    if (formData.from_date && formData.to_date) {
      const start = new Date(formData.from_date);
      const end = new Date(formData.to_date);
      if (end >= start) {
        const diffTime = Math.abs(end.getTime() - start.getTime());
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        setFormData((prev) => ({ ...prev, total_days: String(diffDays) }));
      }
    }
  }, [formData.from_date, formData.to_date]);

  const handleApplyLeave = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.leave_type) {
      toast.error("Please select a leave type");
      return;
    }
    if (!formData.from_date || !formData.to_date) {
      toast.error("Please specify both from and to dates");
      return;
    }
    if (!formData.reason.trim()) {
      toast.error("Please provide a reason for the leave");
      return;
    }

    setSubmitting(true);
    try {
      await apiClient.post(`/${boxSlug}/leaves/request`, {
        ...formData,
        total_days: Number(formData.total_days),
      });
      toast.success("Leave application submitted successfully!");
      setIsApplyModalOpen(false);
      setFormData({
        leave_type: "",
        from_date: "",
        to_date: "",
        total_days: "1",
        contact_during_leave: "",
        reason: "",
      });
      fetchRecords();
      fetchStats();
    } catch (err: any) {
      toast.error(err.message || "Failed to submit leave request");
    } finally {
      setSubmitting(false);
    }
  };

  const handleDeleteRequest = async (id: number) => {
    if (!confirm("Are you sure you want to cancel this leave request?")) return;
    try {
      await apiClient.delete(`/${boxSlug}/leaves/request`, {
        params: { id, leave_request_id: id },
      });
      toast.success("Leave request cancelled");
      fetchRecords();
      fetchStats();
    } catch (err: any) {
      toast.error(err.message || "Failed to cancel leave request");
    }
  };

  // Filtered records
  const filteredRecords = useMemo(() => {
    return records.filter((item) => {
      const typeMatch =
        typeFilter === "all" ||
        String(item.leave_type_id || item.leave_type) === typeFilter ||
        (item.leave_type &&
          String(item.leave_type).toLowerCase().includes(typeFilter.toLowerCase()));

      const status = (item.status || "").toLowerCase();
      const statusMatch =
        statusFilter === "all" ||
        status === statusFilter ||
        (statusFilter === "declined" && (status === "rejected" || status === "declined"));

      const searchMatch =
        !searchTerm ||
        (item.leave_type &&
          String(item.leave_type).toLowerCase().includes(searchTerm.toLowerCase())) ||
        (item.reason &&
          String(item.reason).toLowerCase().includes(searchTerm.toLowerCase())) ||
        (item.approved_by &&
          String(item.approved_by).toLowerCase().includes(searchTerm.toLowerCase()));

      return typeMatch && statusMatch && searchMatch;
    });
  }, [records, typeFilter, statusFilter, searchTerm]);

  return (
    <div className="space-y-6 p-4 md:p-8 max-w-7xl mx-auto">
      {/* Page Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/40 pb-5">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold tracking-tight text-foreground">
            Employee Leaves
          </h1>
          <p className="text-sm text-muted-foreground mt-1">
            Track your leave balance, submit new applications, and view status history.
          </p>
        </div>
        <div className="flex items-center gap-3">
          <Button
            variant="outline"
            size="sm"
            onClick={() => {
              fetchRecords();
              fetchStats();
            }}
            className="gap-2"
          >
            <RefreshCw className="h-4 w-4" />
            Refresh
          </Button>
          <Button
            onClick={() => setIsApplyModalOpen(true)}
            className="gap-2 bg-primary text-primary-foreground shadow-sm"
          >
            <Plus className="h-4 w-4" />
            Apply Leave
          </Button>
        </div>
      </div>

      {/* Leave Stats Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card className="border-border/50 shadow-sm bg-gradient-to-br from-blue-50/50 via-background to-background dark:from-blue-950/20">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Available Leaves
                </p>
                <h3 className="text-2xl font-bold mt-1 text-foreground">
                  {stats?.total_prorated_leave_days ?? "0"}
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center">
                <CalendarDays className="h-5 w-5" />
              </div>
            </div>
            <div className="mt-3 flex items-center gap-1.5 text-xs text-muted-foreground">
              <span className="font-medium text-foreground">
                {stats?.days_active_in_year ?? 0}
              </span>{" "}
              days active this year
            </div>
          </CardContent>
        </Card>

        <Card className="border-border/50 shadow-sm bg-gradient-to-br from-emerald-50/50 via-background to-background dark:from-emerald-950/20">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Approved Leaves
                </p>
                <h3 className="text-2xl font-bold mt-1 text-emerald-600">
                  {stats?.leave_requests?.approved ?? "0"}
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                <CheckCircle2 className="h-5 w-5" />
              </div>
            </div>
            <div className="mt-3 flex items-center gap-1.5 text-xs text-emerald-600">
              <span>All approved applications</span>
            </div>
          </CardContent>
        </Card>

        <Card className="border-border/50 shadow-sm bg-gradient-to-br from-amber-50/50 via-background to-background dark:from-amber-950/20">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Pending Approval
                </p>
                <h3 className="text-2xl font-bold mt-1 text-amber-600">
                  {stats?.leave_requests?.pending ?? "0"}
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center">
                <Clock className="h-5 w-5" />
              </div>
            </div>
            <div className="mt-3 flex items-center gap-1.5 text-xs text-amber-600">
              <span>Awaiting manager response</span>
            </div>
          </CardContent>
        </Card>

        <Card className="border-border/50 shadow-sm bg-gradient-to-br from-purple-50/50 via-background to-background dark:from-purple-950/20">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Next Holiday
                </p>
                <h3 className="text-base font-bold mt-1 truncate max-w-[150px] text-purple-700 dark:text-purple-400">
                  {stats?.nextHoliday?.title || "None scheduled"}
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-purple-500/10 text-purple-600 flex items-center justify-center">
                <Sparkles className="h-5 w-5" />
              </div>
            </div>
            <div className="mt-3 flex items-center gap-1.5 text-xs text-muted-foreground">
              <span>
                {stats?.nextHoliday?.date
                  ? new Date(stats.nextHoliday.date).toLocaleDateString("en-US", {
                      day: "numeric",
                      month: "short",
                      year: "numeric",
                    })
                  : "Check holiday list"}
              </span>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Filters & Search */}
      <Card className="border-border/60 shadow-sm">
        <CardContent className="p-4 flex flex-col sm:flex-row items-center gap-3">
          <div className="relative flex-1 w-full">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by reason, type, or approver..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-9 h-9"
            />
          </div>
          <div className="flex items-center gap-3 w-full sm:w-auto">
            <Select value={typeFilter} onValueChange={(val) => setTypeFilter(val || 'all')}>
              <SelectTrigger className="w-full sm:w-[160px] h-9">
                <SelectValue placeholder="Leave Type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Types</SelectItem>
                {leaveTypes.map((t) => (
                  <SelectItem key={t.id} value={String(t.id)}>
                    {t.leave_type}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            <Select value={statusFilter} onValueChange={(val) => setStatusFilter(val || 'all')}>
              <SelectTrigger className="w-full sm:w-[140px] h-9">
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Status</SelectItem>
                <SelectItem value="approved">Approved</SelectItem>
                <SelectItem value="pending">Pending</SelectItem>
                <SelectItem value="declined">Declined</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* Records Table */}
      <Card className="border-border/60 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader className="bg-muted/40">
              <TableRow>
                <TableHead className="w-[180px]">Leave Type</TableHead>
                <TableHead>From</TableHead>
                <TableHead>To</TableHead>
                <TableHead>Duration</TableHead>
                <TableHead>Reason</TableHead>
                <TableHead>Approved By</TableHead>
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
                      Loading leave applications...
                    </div>
                  </TableCell>
                </TableRow>
              ) : filteredRecords.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={8} className="text-center py-12 text-muted-foreground">
                    No leave requests found matching your filters.
                  </TableCell>
                </TableRow>
              ) : (
                filteredRecords.map((item, index) => {
                  const statusStr = (item.status || "Pending").toLowerCase();
                  return (
                    <TableRow key={item.id || index} className="hover:bg-muted/30">
                      <TableCell className="font-medium">
                        <div className="flex items-center gap-2">
                          <span className="h-2 w-2 rounded-full bg-primary" />
                          <span>{item.leave_type || "Leave"}</span>
                        </div>
                      </TableCell>
                      <TableCell className="text-sm">
                        {item.from || item.from_date || "-"}
                      </TableCell>
                      <TableCell className="text-sm">
                        {item.to || item.to_date || "-"}
                      </TableCell>
                      <TableCell>
                        <Badge variant="secondary" className="font-normal text-xs">
                          {item.no_of_days || `${item.total_days} Days`}
                        </Badge>
                      </TableCell>
                      <TableCell className="max-w-[220px] truncate text-sm text-muted-foreground" title={item.reason}>
                        {item.reason || "-"}
                      </TableCell>
                      <TableCell className="text-sm text-muted-foreground">
                        {item.approved_by || "-"}
                      </TableCell>
                      <TableCell>
                        {statusStr === "approved" ? (
                          <Badge className="bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 border-emerald-200">
                            Approved
                          </Badge>
                        ) : statusStr === "pending" ? (
                          <Badge className="bg-amber-500/10 text-amber-600 hover:bg-amber-500/20 border-amber-200">
                            Pending
                          </Badge>
                        ) : (
                          <Badge className="bg-rose-500/10 text-rose-600 hover:bg-rose-500/20 border-rose-200">
                            Declined
                          </Badge>
                        )}
                      </TableCell>
                      <TableCell className="text-right">
                        {statusStr === "pending" && (
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => handleDeleteRequest(item.id || item.action?.delete_id)}
                            className="text-destructive hover:bg-destructive/10 h-8 px-2"
                          >
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        )}
                      </TableCell>
                    </TableRow>
                  );
                })
              )}
            </TableBody>
          </Table>
        </div>
      </Card>

      {/* Apply Leave Modal */}
      <Dialog open={isApplyModalOpen} onOpenChange={setIsApplyModalOpen}>
        <DialogContent className="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle className="text-lg font-semibold flex items-center gap-2">
              <CalendarIcon className="h-5 w-5 text-primary" />
              Apply for Leave
            </DialogTitle>
          </DialogHeader>

          <form onSubmit={handleApplyLeave} className="space-y-4 pt-2">
            <div className="space-y-1.5">
              <Label htmlFor="leave_type" className="text-sm font-medium">
                Leave Type <span className="text-destructive">*</span>
              </Label>
              <Select
                value={formData.leave_type}
                onValueChange={(val) => setFormData((p) => ({ ...p, leave_type: val || "" }))}
              >
                <SelectTrigger id="leave_type" className="w-full">
                  <SelectValue placeholder="Select Leave Type" />
                </SelectTrigger>
                <SelectContent>
                  {leaveTypes.map((t) => (
                    <SelectItem key={t.id} value={String(t.id)}>
                      {t.leave_type} ({t.number_of_days || 0} days/yr)
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label htmlFor="from_date" className="text-sm font-medium">
                  From Date <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="from_date"
                  type="date"
                  value={formData.from_date}
                  onChange={(e) => {
                    const from = e.target.value;
                    setFormData((p) => {
                      let days = p.total_days;
                      if (from && p.to_date) {
                        const d1 = new Date(from);
                        const d2 = new Date(p.to_date);
                        if (!isNaN(d1.getTime()) && !isNaN(d2.getTime()) && d2 >= d1) {
                          const diffTime = Math.abs(d2.getTime() - d1.getTime());
                          days = String(Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1);
                        }
                      }
                      return { ...p, from_date: from, total_days: days };
                    });
                  }}
                  required
                  className="w-full"
                />
              </div>

              <div className="space-y-1.5">
                <Label htmlFor="to_date" className="text-sm font-medium">
                  To Date <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="to_date"
                  type="date"
                  value={formData.to_date}
                  onChange={(e) => {
                    const to = e.target.value;
                    setFormData((p) => {
                      let days = p.total_days;
                      if (p.from_date && to) {
                        const d1 = new Date(p.from_date);
                        const d2 = new Date(to);
                        if (!isNaN(d1.getTime()) && !isNaN(d2.getTime()) && d2 >= d1) {
                          const diffTime = Math.abs(d2.getTime() - d1.getTime());
                          days = String(Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1);
                        }
                      }
                      return { ...p, to_date: to, total_days: days };
                    });
                  }}
                  required
                  className="w-full"
                />
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <Label htmlFor="total_days" className="text-sm font-medium">
                  Total Days <span className="text-destructive">*</span>
                </Label>
                <Input
                  id="total_days"
                  type="number"
                  step="0.5"
                  min="0.5"
                  value={formData.total_days}
                  onChange={(e) => setFormData((p) => ({ ...p, total_days: e.target.value }))}
                  required
                  className="w-full"
                />
              </div>

              <div className="space-y-1.5">
                <Label htmlFor="contact" className="text-sm font-medium">
                  Contact During Leave
                </Label>
                <Input
                  id="contact"
                  placeholder="e.g. Phone number"
                  value={formData.contact_during_leave}
                  onChange={(e) =>
                    setFormData((p) => ({ ...p, contact_during_leave: e.target.value }))
                  }
                  className="w-full"
                />
              </div>
            </div>

            <div className="space-y-1.5">
              <Label htmlFor="reason" className="text-sm font-medium">
                Reason <span className="text-destructive">*</span>
              </Label>
              <Textarea
                id="reason"
                placeholder="Describe your reason for leave..."
                rows={3}
                value={formData.reason}
                onChange={(e) => setFormData((p) => ({ ...p, reason: e.target.value }))}
                required
                className="w-full resize-none"
              />
            </div>

            <DialogFooter className="pt-2">
              <Button
                type="button"
                variant="outline"
                onClick={() => setIsApplyModalOpen(false)}
                disabled={submitting}
              >
                Cancel
              </Button>
              <Button type="submit" disabled={submitting}>
                {submitting ? "Submitting..." : "Submit Application"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
