"use client";

import { useState, useEffect, useMemo } from "react";
import { useParams } from "next/navigation";
import { apiClient } from "@/lib/api-client";
import { toast } from "sonner";
import {
  Calendar as CalendarIcon,
  CheckCircle2,
  Clock,
  XCircle,
  Search,
  RefreshCw,
  TrendingUp,
  User,
  Check,
  X,
  Building,
  Briefcase,
  AlertCircle,
  FileSpreadsheet,
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
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from "@/components/ui/dialog";
import { Progress } from "@/components/ui/progress";

export default function AdminLeavesPage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const [loading, setLoading] = useState(true);
  const [requests, setRequests] = useState<any[]>([]);
  const [dashboardStats, setDashboardStats] = useState<any>(null);
  const [leaveTypes, setLeaveTypes] = useState<any[]>([]);

  // Filters
  const [searchTerm, setSearchTerm] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");
  const [typeFilter, setTypeFilter] = useState("all");

  // Rejection Dialog State
  const [rejectModalOpen, setRejectModalOpen] = useState(false);
  const [selectedLeaveId, setSelectedLeaveId] = useState<number | null>(null);
  const [rejectReason, setRejectReason] = useState("");
  const [actionLoading, setActionLoading] = useState(false);

  const fetchDashboardStats = async () => {
    try {
      const res: any = await apiClient.get(`/${boxSlug}/leaves/admin/dashboard`);
      if (res?.stats) {
        setDashboardStats(res.stats);
      }
    } catch (err: any) {
      console.error("Failed to load admin leave stats", err);
    }
  };

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

  const fetchRequests = async () => {
    setLoading(true);
    try {
      const res: any = await apiClient.get(`/${boxSlug}/leaves/admin/requests`);
      if (res?.data && Array.isArray(res.data)) {
        setRequests(res.data);
      } else if (Array.isArray(res)) {
        setRequests(res);
      } else {
        setRequests([]);
      }
    } catch (err: any) {
      console.error("Failed to fetch leave requests", err);
      toast.error(err.message || "Failed to fetch leave requests");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchDashboardStats();
      fetchLeaveTypes();
      fetchRequests();
    }
  }, [boxSlug]);

  const handleUpdateStatus = async (
    leaveId: number,
    status: "approved" | "rejected",
    reason?: string
  ) => {
    setActionLoading(true);
    try {
      await apiClient.post(`/${boxSlug}/leaves/admin/update-status`, {
        leave_id: leaveId,
        status,
        reason: reason || null,
      });
      toast.success(
        status === "approved"
          ? "Leave request approved successfully"
          : "Leave request declined"
      );
      if (rejectModalOpen) {
        setRejectModalOpen(false);
        setRejectReason("");
      }
      fetchRequests();
      fetchDashboardStats();
    } catch (err: any) {
      toast.error(err.message || "Failed to update leave request");
    } finally {
      setActionLoading(false);
    }
  };

  const filteredRequests = useMemo(() => {
    return requests.filter((item) => {
      const status = (item.status || "").toLowerCase();
      const statusMatch =
        statusFilter === "all" ||
        status === statusFilter ||
        (statusFilter === "declined" && (status === "rejected" || status === "declined"));

      const typeMatch =
        typeFilter === "all" ||
        String(item.leave_type_id || item.leave_type) === typeFilter ||
        (item.leave_type &&
          String(item.leave_type).toLowerCase().includes(typeFilter.toLowerCase()));

      const empName = item.employee?.name || "";
      const empId = item.employee?.employee_id || "";
      const reason = item.reason || "";
      const searchMatch =
        !searchTerm ||
        empName.toLowerCase().includes(searchTerm.toLowerCase()) ||
        empId.toLowerCase().includes(searchTerm.toLowerCase()) ||
        reason.toLowerCase().includes(searchTerm.toLowerCase());

      return statusMatch && typeMatch && searchMatch;
    });
  }, [requests, statusFilter, typeFilter, searchTerm]);

  return (
    <div className="space-y-6 p-4 md:p-8 max-w-7xl mx-auto">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/40 pb-5">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold tracking-tight text-foreground">
            Admin Leave Management
          </h1>
          <p className="text-sm text-muted-foreground mt-1">
            Review, approve, and manage company-wide employee leave applications.
          </p>
        </div>
        <div className="flex items-center gap-3">
          <Button
            variant="outline"
            size="sm"
            onClick={() => {
              fetchRequests();
              fetchDashboardStats();
            }}
            className="gap-2"
          >
            <RefreshCw className="h-4 w-4" />
            Refresh
          </Button>
        </div>
      </div>

      {/* Admin KPI Stat Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card className="border-border/50 shadow-sm">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Total Applications
                </p>
                <h3 className="text-2xl font-bold mt-1 text-foreground">
                  {dashboardStats?.total ?? 0}
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center">
                <CalendarIcon className="h-5 w-5" />
              </div>
            </div>
            <div className="mt-3">
              <div className="flex items-center justify-between text-xs text-muted-foreground mb-1">
                <span>Month trend</span>
                <span className="font-semibold text-foreground">
                  {dashboardStats?.total_trend ?? 0}%
                </span>
              </div>
              <Progress value={100} className="h-1.5 bg-blue-100 dark:bg-blue-950" />
            </div>
          </CardContent>
        </Card>

        <Card className="border-border/50 shadow-sm">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Approved Leaves
                </p>
                <h3 className="text-2xl font-bold mt-1 text-emerald-600">
                  {dashboardStats?.approved ?? 0}
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                <CheckCircle2 className="h-5 w-5" />
              </div>
            </div>
            <div className="mt-3">
              <div className="flex items-center justify-between text-xs text-muted-foreground mb-1">
                <span>Approval rate</span>
                <span className="font-semibold text-emerald-600">
                  {dashboardStats?.total
                    ? Math.round((dashboardStats.approved / dashboardStats.total) * 100)
                    : 0}
                  %
                </span>
              </div>
              <Progress
                value={
                  dashboardStats?.total
                    ? (dashboardStats.approved / dashboardStats.total) * 100
                    : 0
                }
                className="h-1.5 bg-emerald-100 dark:bg-emerald-950"
              />
            </div>
          </CardContent>
        </Card>

        <Card className="border-border/50 shadow-sm">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Pending Review
                </p>
                <h3 className="text-2xl font-bold mt-1 text-amber-600">
                  {dashboardStats?.pending ?? 0}
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center">
                <Clock className="h-5 w-5" />
              </div>
            </div>
            <div className="mt-3">
              <div className="flex items-center justify-between text-xs text-muted-foreground mb-1">
                <span>Requires action</span>
                <span className="font-semibold text-amber-600">
                  {dashboardStats?.pending ?? 0} requests
                </span>
              </div>
              <Progress
                value={
                  dashboardStats?.total
                    ? (dashboardStats.pending / dashboardStats.total) * 100
                    : 0
                }
                className="h-1.5 bg-amber-100 dark:bg-amber-950"
              />
            </div>
          </CardContent>
        </Card>

        <Card className="border-border/50 shadow-sm">
          <CardContent className="p-5">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                  Declined Leaves
                </p>
                <h3 className="text-2xl font-bold mt-1 text-rose-600">
                  {dashboardStats?.declined ?? 0}
                </h3>
              </div>
              <div className="h-10 w-10 rounded-full bg-rose-500/10 text-rose-600 flex items-center justify-center">
                <XCircle className="h-5 w-5" />
              </div>
            </div>
            <div className="mt-3">
              <div className="flex items-center justify-between text-xs text-muted-foreground mb-1">
                <span>Declined rate</span>
                <span className="font-semibold text-rose-600">
                  {dashboardStats?.total
                    ? Math.round((dashboardStats.declined / dashboardStats.total) * 100)
                    : 0}
                  %
                </span>
              </div>
              <Progress
                value={
                  dashboardStats?.total
                    ? (dashboardStats.declined / dashboardStats.total) * 100
                    : 0
                }
                className="h-1.5 bg-rose-100 dark:bg-rose-950"
              />
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Filter Toolbar */}
      <Card className="border-border/60 shadow-sm">
        <CardContent className="p-4 flex flex-col sm:flex-row items-center gap-3">
          <div className="relative flex-1 w-full">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by employee name, ID, or reason..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-9 h-9"
            />
          </div>
          <div className="flex items-center gap-3 w-full sm:w-auto">
            <Select value={statusFilter} onValueChange={(val) => setStatusFilter(val || 'all')}>
              <SelectTrigger className="w-full sm:w-[140px] h-9">
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Status</SelectItem>
                <SelectItem value="pending">Pending</SelectItem>
                <SelectItem value="approved">Approved</SelectItem>
                <SelectItem value="declined">Declined</SelectItem>
              </SelectContent>
            </Select>

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
          </div>
        </CardContent>
      </Card>

      {/* Requests Table */}
      <Card className="border-border/60 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader className="bg-muted/40">
              <TableRow>
                <TableHead className="w-[240px]">Employee</TableHead>
                <TableHead>Leave Type</TableHead>
                <TableHead>From</TableHead>
                <TableHead>To</TableHead>
                <TableHead>Duration</TableHead>
                <TableHead>Reason</TableHead>
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
                      Loading leave requests...
                    </div>
                  </TableCell>
                </TableRow>
              ) : filteredRequests.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={8} className="text-center py-12 text-muted-foreground">
                    No leave requests found.
                  </TableCell>
                </TableRow>
              ) : (
                filteredRequests.map((item, index) => {
                  const statusStr = (item.status || "Pending").toLowerCase();
                  const emp = item.employee || {};
                  return (
                    <TableRow key={item.id || index} className="hover:bg-muted/30">
                      <TableCell>
                        <div className="flex items-center gap-3">
                          <Avatar className="h-9 w-9">
                            <AvatarImage src={emp.image} alt={emp.name} />
                            <AvatarFallback className="bg-primary/10 text-primary text-xs font-semibold">
                              {(emp.name || "U").substring(0, 2).toUpperCase()}
                            </AvatarFallback>
                          </Avatar>
                          <div className="min-w-0">
                            <div className="font-semibold text-sm truncate">{emp.name || "N/A"}</div>
                            <div className="text-xs text-muted-foreground flex items-center gap-1.5">
                              <span>{emp.employee_id || "ID"}</span>
                              {emp.department && (
                                <>
                                  <span>•</span>
                                  <span className="truncate">{emp.department}</span>
                                </>
                              )}
                            </div>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell className="font-medium text-sm">
                        {item.leave_type || "Leave"}
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
                      <TableCell className="max-w-[200px] truncate text-sm text-muted-foreground" title={item.reason}>
                        {item.reason || "-"}
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
                        {statusStr === "pending" ? (
                          <div className="flex items-center justify-end gap-1.5">
                            <Button
                              size="sm"
                              variant="outline"
                              onClick={() => handleUpdateStatus(item.id, "approved")}
                              disabled={actionLoading}
                              className="h-8 text-xs gap-1 border-emerald-300 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-950/40"
                            >
                              <Check className="h-3.5 w-3.5" />
                              Approve
                            </Button>
                            <Button
                              size="sm"
                              variant="outline"
                              onClick={() => {
                                setSelectedLeaveId(item.id);
                                setRejectModalOpen(true);
                              }}
                              disabled={actionLoading}
                              className="h-8 text-xs gap-1 border-rose-300 text-rose-600 hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-950/40"
                            >
                              <X className="h-3.5 w-3.5" />
                              Decline
                            </Button>
                          </div>
                        ) : (
                          <span className="text-xs text-muted-foreground italic">Processed</span>
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

      {/* Reject Reason Modal */}
      <Dialog open={rejectModalOpen} onOpenChange={setRejectModalOpen}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle className="text-lg font-semibold flex items-center gap-2 text-destructive">
              <XCircle className="h-5 w-5" />
              Decline Leave Request
            </DialogTitle>
          </DialogHeader>

          <div className="space-y-4 pt-2">
            <div className="space-y-1.5">
              <Label htmlFor="reject_reason" className="text-sm font-medium">
                Reason for Rejection <span className="text-destructive">*</span>
              </Label>
              <Textarea
                id="reject_reason"
                placeholder="Explain why this request is being declined..."
                rows={3}
                value={rejectReason}
                onChange={(e) => setRejectReason(e.target.value)}
                className="w-full resize-none"
              />
            </div>

            <DialogFooter className="pt-2">
              <Button
                type="button"
                variant="outline"
                onClick={() => setRejectModalOpen(false)}
                disabled={actionLoading}
              >
                Cancel
              </Button>
              <Button
                type="button"
                variant="destructive"
                disabled={actionLoading || !rejectReason.trim()}
                onClick={() => {
                  if (selectedLeaveId) {
                    handleUpdateStatus(selectedLeaveId, "rejected", rejectReason);
                  }
                }}
              >
                {actionLoading ? "Declining..." : "Confirm Decline"}
              </Button>
            </DialogFooter>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
