"use client";

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { toast } from 'sonner';
import { Calendar, Download, Settings, ChevronDown, Check, Clock, AlertCircle, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/components/ui/dialog';
import AdminCorrectionModal from '@/components/employee-attendance/AdminCorrectionModal';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination";

export default function AttendanceAllPage() {
  const params = useParams();
  const boxSlug = params.box_slug as string;

  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<any[]>([]);
  const [stats, setStats] = useState<any>(null);
  
  const [filters, setFilters] = useState({
    status: '',
    status_correction: '',
    department: '',
    from_date: '',
    to_date: '',
    search: '',
  });

  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    per_page: 25,
    total: 0
  });

  const [processModalOpen, setProcessModalOpen] = useState(false);
  const [processMonth, setProcessMonth] = useState('');
  
  // Modal state
  const [selectedAttendanceId, setSelectedAttendanceId] = useState<number | null>(null);
  const [summaryData, setSummaryData] = useState<any>(null);
  const [summaryLoading, setSummaryLoading] = useState(false);
  const [modalOpen, setModalOpen] = useState(false);

  // Correction Modal state
  const [correctionModalOpen, setCorrectionModalOpen] = useState(false);
  const [selectedCorrectionRecord, setSelectedCorrectionRecord] = useState<any>(null);

  const fetchStats = async () => {
    try {
      const res = await apiClient.get(`/${boxSlug}/attendance/dashboard/stats`);
      if (res.status === 200) {
        setStats(res.data);
      }
    } catch (error) {
      console.error('Error fetching stats', error);
    }
  };

  const handleCorrectionActionClick = (item: any) => {
    setSelectedCorrectionRecord(item);
    setCorrectionModalOpen(true);
  };

  const fetchData = async (page = 1) => {
    setLoading(true);
    try {
      const query = new URLSearchParams({
        page: String(page),
        per_page: String(pagination.per_page),
        ...(filters.status && { status: filters.status }),
        ...(filters.status_correction && { status_correction: filters.status_correction }),
        ...(filters.department && { department: filters.department }),
        ...(filters.from_date && { from_date: filters.from_date }),
        ...(filters.to_date && { to_date: filters.to_date }),
        ...(filters.search && { search: filters.search }),
      }).toString();

      const res = await apiClient.get(`/${boxSlug}/attendance/admin?${query}`);
      if (res.success) {
        setData(res.data || []);
        if (res.meta) setPagination(res.meta);
      } else {
        toast.error(res.message || 'Failed to load attendance');
      }
    } catch (error) {
      toast.error('Failed to load attendance');
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!boxSlug) return;
    fetchStats();
    fetchData();
  }, [boxSlug]);

  useEffect(() => {
    if (!boxSlug) return;
    fetchData(1);
  }, [filters, boxSlug]);

  const handleProcessAttendance = async () => {
    if (!processMonth) {
      toast.error('Please select month and year');
      return;
    }
    const [year, month] = processMonth.split('-');
    try {
      const res = await apiClient.post(`/${boxSlug}/attendance/dashboard/process`, { year, month });
      if (res.success) {
        toast.success(res.message || 'Processed successfully');
        setProcessModalOpen(false);
        fetchData(pagination.current_page);
      } else {
        toast.error(res.message || 'Failed to process');
      }
    } catch (err) {
      toast.error('An error occurred');
    }
  };
  const handleProductionClick = async (item: any) => {
    setSelectedAttendanceId(item.id);
    setSummaryData({
      ...item,
      // fallback values
    });
    setModalOpen(true);
    setSummaryLoading(true);
    
    try {
      const res = await apiClient.get(`/${boxSlug}/attendance/dashboard/individual-summary?attendance_id=${item.id}`);
      if (res.status) {
        setSummaryData(res.data || res); // Depending on response wrapping
      }
    } catch (e) {
      toast.error('Failed to load detailed summary');
    } finally {
      setSummaryLoading(false);
    }
  };

  const getStatusBadge = (status: string) => {
    const s = String(status).toLowerCase();
    switch (s) {
      case 'present': return <Badge className="bg-emerald-500 hover:bg-emerald-600">Present</Badge>;
      case 'absent': return <Badge variant="destructive">Absent</Badge>;
      case 'half day': return <Badge className="bg-amber-500 hover:bg-amber-600">Half Day</Badge>;
      case 'short leave': return <Badge className="bg-blue-500 hover:bg-blue-600">Short Leave</Badge>;
      case 'comp off': return <Badge className="bg-purple-500 hover:bg-purple-600">Comp Off</Badge>;
      default: return <Badge variant="secondary">{status}</Badge>;
    }
  };

  const renderPaginationItems = () => {
    if (!pagination.last_page || pagination.last_page <= 1) return null;
    
    const current = pagination.current_page;
    const last = pagination.last_page;
    const pages = [];
    
    pages.push(1);
    if (current > 3) pages.push('ellipsis-start');
    
    for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) {
      pages.push(i);
    }
    
    if (current < last - 2) pages.push('ellipsis-end');
    if (last > 1) pages.push(last);
    
    return pages.map((page, idx) => {
      if (typeof page === 'string') {
        return (
          <PaginationItem key={`${page}-${idx}`}>
            <PaginationEllipsis />
          </PaginationItem>
        );
      }
      return (
        <PaginationItem key={page}>
          <PaginationLink 
            isActive={page === current} 
            onClick={() => fetchData(page)}
            className="cursor-pointer"
          >
            {page}
          </PaginationLink>
        </PaginationItem>
      );
    });
  };

  return (
    <div className="p-6 space-y-6 max-w-[1600px] mx-auto">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">Attendance Admin</h1>
          <p className="text-sm text-muted-foreground">Manage and view all employee attendance records.</p>
        </div>
        <div className="flex flex-wrap items-center gap-2">
          <Button variant="outline" size="sm" onClick={() => window.location.href = `/${boxSlug}/attendance-calendar`}>
            <Calendar className="w-4 h-4 me-2" /> Calendar
          </Button>
          <Button variant="outline" size="sm">
            <Download className="w-4 h-4 me-2" /> Export
          </Button>
          <Button size="sm" onClick={() => setProcessModalOpen(true)}>
            <Settings className="w-4 h-4 me-2" /> Process
          </Button>
        </div>
      </div>

      {/* Stats Cards */}
      <Card>
        <CardHeader className="pb-3 border-b border-border/50">
          <div className="flex flex-col md:flex-row md:items-center justify-between">
            <div>
              <CardTitle className="text-lg">Attendance Details Today</CardTitle>
              <p className="text-sm text-muted-foreground mt-1">
                Data from the <b>{stats?.total_users || 0}</b> total employees
              </p>
            </div>
            <div className="flex items-center gap-3 mt-4 md:mt-0">
              <span className="text-sm font-medium">Total Absentees today:</span>
              <div className="flex -space-x-2">
                <Avatar className="w-8 h-8 border-2 border-background bg-primary">
                  <AvatarFallback className="text-xs text-primary-foreground">
                    +{stats?.today?.absent || 0}
                  </AvatarFallback>
                </Avatar>
              </div>
            </div>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 p-4 bg-muted/30">
            <div className="p-4 bg-card border rounded-xl shadow-sm">
              <div className="text-sm font-medium text-muted-foreground mb-2">Present</div>
              <div className="flex items-end justify-between">
                <div className="text-2xl font-bold">{stats?.today?.present || 0}</div>
                <Badge variant={stats?.diff_percent?.present >= 0 ? "secondary" : "destructive"} className={stats?.diff_percent?.present >= 0 ? "bg-emerald-100 text-emerald-700" : ""}>
                  {stats?.diff_percent?.present > 0 ? '+' : ''}{stats?.diff_percent?.present || 0}%
                </Badge>
              </div>
            </div>
            <div className="p-4 bg-card border rounded-xl shadow-sm">
              <div className="text-sm font-medium text-muted-foreground mb-2">Late Login</div>
              <div className="flex items-end justify-between">
                <div className="text-2xl font-bold">{stats?.today?.late_logins || 0}</div>
                <Badge variant={stats?.diff_percent?.late_logins >= 0 ? "secondary" : "destructive"} className={stats?.diff_percent?.late_logins <= 0 ? "bg-emerald-100 text-emerald-700" : "bg-red-100 text-red-700"}>
                  {stats?.diff_percent?.late_logins > 0 ? '+' : ''}{stats?.diff_percent?.late_logins || 0}%
                </Badge>
              </div>
            </div>
            <div className="p-4 bg-card border rounded-xl shadow-sm">
              <div className="text-sm font-medium text-muted-foreground mb-2">Uninformed</div>
              <div className="flex items-end justify-between">
                <div className="text-2xl font-bold">{stats?.today?.unapproved_leaves || 0}</div>
                <Badge variant="secondary" className="bg-red-100 text-red-700">
                  {stats?.diff_percent?.unapproved_leaves > 0 ? '+' : ''}{stats?.diff_percent?.unapproved_leaves || 0}%
                </Badge>
              </div>
            </div>
            <div className="p-4 bg-card border rounded-xl shadow-sm">
              <div className="text-sm font-medium text-muted-foreground mb-2">Permission</div>
              <div className="flex items-end justify-between">
                <div className="text-2xl font-bold">{stats?.today?.approved_leaves || 0}</div>
                <Badge variant="secondary" className="bg-emerald-100 text-emerald-700">
                  {stats?.diff_percent?.approved_leaves > 0 ? '+' : ''}{stats?.diff_percent?.approved_leaves || 0}%
                </Badge>
              </div>
            </div>
            <div className="p-4 bg-card border rounded-xl shadow-sm col-span-2 sm:col-span-1">
              <div className="text-sm font-medium text-muted-foreground mb-2">Absent</div>
              <div className="flex items-end justify-between">
                <div className="text-2xl font-bold">{stats?.today?.absent || 0}</div>
                <Badge variant="secondary" className="bg-red-100 text-red-700">
                  {stats?.diff_percent?.absent > 0 ? '+' : ''}{stats?.diff_percent?.absent || 0}%
                </Badge>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Table Section */}
      <Card>
        <CardHeader className="p-4">
          <div className="flex flex-col md:flex-row gap-4 justify-between">
            <h3 className="font-semibold text-lg flex-shrink-0">Admin Attendance</h3>
            <div className="flex flex-wrap items-center gap-3 w-full md:w-auto">
              <Input 
                type="date"
                value={filters.from_date}
                onChange={e => setFilters({...filters, from_date: e.target.value})}
                className="w-[140px]"
              />
              <span className="text-muted-foreground">-</span>
              <Input 
                type="date"
                value={filters.to_date}
                onChange={e => setFilters({...filters, to_date: e.target.value})}
                className="w-[140px]"
              />
              <Select value={filters.status || "all"} onValueChange={(v: any) => setFilters({...filters, status: v === 'all' ? '' : v})}>
                <SelectTrigger className="w-[140px]">
                  <SelectValue placeholder="Status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Status</SelectItem>
                  <SelectItem value="present">Present</SelectItem>
                  <SelectItem value="absent">Absent</SelectItem>
                  <SelectItem value="half day">Half Day</SelectItem>
                  <SelectItem value="short leave">Short Leave</SelectItem>
                  <SelectItem value="punch out miss">Punch Out Miss</SelectItem>
                </SelectContent>
              </Select>
              <Select value={filters.status_correction || "all"} onValueChange={(v: any) => setFilters({...filters, status_correction: v === 'all' ? '' : v})}>
                <SelectTrigger className="w-[160px]">
                  <SelectValue placeholder="Correction" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Corrections</SelectItem>
                  <SelectItem value="approved">Approved</SelectItem>
                  <SelectItem value="pending">Pending</SelectItem>
                  <SelectItem value="rejected">Rejected</SelectItem>
                </SelectContent>
              </Select>
              <Input 
                placeholder="Search employee..."
                value={filters.search}
                onChange={e => setFilters({...filters, search: e.target.value})}
                className="w-full md:w-[200px]"
              />
            </div>
          </div>
        </CardHeader>
        <div className="border-t overflow-auto w-full max-h-[60vh]">
          <Table>
            <TableHeader className="sticky top-0 z-10 shadow-sm">
              <TableRow className="bg-muted hover:bg-muted">
                <TableHead className="w-12 text-center">#</TableHead>
                <TableHead>Employee</TableHead>
                <TableHead>Department</TableHead>
                <TableHead>Shift</TableHead>
                <TableHead>Date</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Check In</TableHead>
                <TableHead>Check Out</TableHead>
                <TableHead>Overtime</TableHead>
                <TableHead>Working Hrs</TableHead>
                <TableHead>Production Hours</TableHead>
                <TableHead>Machine</TableHead>
                <TableHead>Correction</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {loading ? (
                <TableRow>
                  <TableCell colSpan={13} className="text-center py-10">
                    <div className="flex flex-col items-center justify-center text-muted-foreground">
                      <div className="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-4" />
                      Loading attendance data...
                    </div>
                  </TableCell>
                </TableRow>
              ) : data.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={13} className="text-center py-10 text-muted-foreground">
                    No attendance records found matching your filters.
                  </TableCell>
                </TableRow>
              ) : (
                data.map((item, index) => {
                  const serial = (pagination.current_page - 1) * pagination.per_page + index + 1;
                  return (
                    <TableRow key={item.id}>
                      <TableCell className="text-center">{serial}</TableCell>
                      <TableCell>
                        <div className="flex items-center gap-3">
                          <Avatar className="w-8 h-8">
                            <AvatarImage src={item.profile_image} />
                            <AvatarFallback>{item.user_name?.charAt(0) || 'U'}</AvatarFallback>
                          </Avatar>
                          <div>
                            <p className="font-medium text-sm leading-none">{item.user_name}</p>
                            <p className="text-xs text-muted-foreground mt-1">{item.email}</p>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>{item.box_user?.department_relation?.name || '-'}</TableCell>
                      <TableCell>
                        {item.box_user?.shift ? (
                           <Badge variant="outline">{item.box_user?.shift.code || 'Shift'}</Badge>
                        ) : '-'}
                      </TableCell>
                      <TableCell className="whitespace-nowrap">
                        {item.attendance_date !== 'N/A' ? new Date(item.attendance_date).toLocaleDateString() : '-'}
                      </TableCell>
                      <TableCell>
                        {getStatusBadge(item.status)}
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5 font-medium">
                          <Clock className="w-3.5 h-3.5 text-muted-foreground" />
                          {item.check_in || '-'}
                        </div>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1.5 font-medium">
                          <Clock className="w-3.5 h-3.5 text-muted-foreground" />
                          {item.check_out || '-'}
                        </div>
                      </TableCell>
                      <TableCell>
                        {item.over_time ? (
                           <span className="font-mono text-muted-foreground">{item.over_time}</span>
                        ) : '-'}
                      </TableCell>
                      <TableCell>
                        {item.working_hours ? (
                           <span className="font-mono">{item.working_hours}</span>
                        ) : '-'}
                      </TableCell>
                      <TableCell>
                        <div 
                           onClick={() => handleProductionClick(item)}
                           className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-100 text-red-700 font-medium text-xs cursor-pointer hover:bg-red-200 transition-colors"
                        >
                           <Clock className="w-3.5 h-3.5" />
                           {item.production_hours || '0.00 Hrs'}
                        </div>
                      </TableCell>
                      <TableCell>{item.machine || '-'}</TableCell>
                      <TableCell>
                        {item.corrections_statuses && item.corrections_statuses.length > 0 ? (
                          <Badge variant="outline" className="bg-orange-50 text-orange-600 border-orange-200 cursor-pointer hover:bg-orange-100" onClick={() => handleCorrectionActionClick(item)}>Yes</Badge>
                        ) : '-'}
                      </TableCell>
                    </TableRow>
                  );
                })
              )}
            </TableBody>
          </Table>
        </div>
        <div className="p-4 border-t flex flex-col md:flex-row items-center justify-between gap-4">
          <div className="text-sm text-muted-foreground whitespace-nowrap">
            Showing {data.length} of {pagination.total} records
          </div>
          <div className="flex-1 w-full">
            <Pagination className="justify-end">
              <PaginationContent>
                <PaginationItem>
                  <PaginationPrevious 
                    onClick={() => pagination.current_page > 1 && fetchData(pagination.current_page - 1)}
                    className={pagination.current_page <= 1 ? "pointer-events-none opacity-50" : "cursor-pointer"} 
                  />
                </PaginationItem>
                {renderPaginationItems()}
                <PaginationItem>
                  <PaginationNext 
                    onClick={() => pagination.current_page < pagination.last_page && fetchData(pagination.current_page + 1)}
                    className={pagination.current_page >= pagination.last_page ? "pointer-events-none opacity-50" : "cursor-pointer"}
                  />
                </PaginationItem>
              </PaginationContent>
            </Pagination>
          </div>
        </div>
      </Card>

      {/* Process Modal */}
      <Dialog open={processModalOpen} onOpenChange={setProcessModalOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Process Attendance</DialogTitle>
          </DialogHeader>
          <div className="py-4 space-y-4">
            <p className="text-sm text-muted-foreground">Select the month and year to process attendance records. This may take a few moments.</p>
            <div className="space-y-2">
              <label className="text-sm font-medium">Month & Year</label>
              <Input 
                type="month" 
                value={processMonth}
                onChange={e => setProcessMonth(e.target.value)}
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setProcessModalOpen(false)}>Cancel</Button>
            <Button onClick={handleProcessAttendance}>Proceed</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Attendance Summary Modal */}
      <Dialog open={modalOpen} onOpenChange={setModalOpen}>
        <DialogContent className="max-w-4xl p-0 overflow-hidden">
          <div className="bg-white">
            <div className="flex justify-between items-center p-4 border-b">
              <div className="bg-[#E22812] text-white px-3 py-1.5 font-bold text-lg">
                Attendance
              </div>
              <DialogClose className="text-muted-foreground hover:text-foreground">
                <X className="w-5 h-5" />
              </DialogClose>
            </div>
            
            <div className="p-6">
              {summaryLoading && !summaryData?.total_working_hours ? (
                <div className="flex items-center justify-center py-20">
                  <div className="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin" />
                </div>
              ) : (
                <>
                  <div className="grid grid-cols-1 md:grid-cols-5 gap-6 items-center mb-8">
                    <div className="md:col-span-2 flex items-center gap-4">
                      <Avatar className="w-12 h-12 border border-border">
                        <AvatarImage src={summaryData?.profile_block?.profile_image || summaryData?.profile_image} />
                        <AvatarFallback className="bg-purple-600 text-white font-medium text-lg">
                          {(summaryData?.profile_block?.user_name || summaryData?.user_name || 'U').charAt(0)}
                        </AvatarFallback>
                      </Avatar>
                      <div>
                        <h4 className="font-medium text-foreground">{summaryData?.profile_block?.user_name || summaryData?.user_name}</h4>
                        <p className="text-sm text-muted-foreground">{summaryData?.profile_block?.department_name || summaryData?.box_user?.department_relation?.name} / {summaryData?.profile_block?.designation_name || summaryData?.box_user?.designation_relation?.name || 'Manager'}</p>
                      </div>
                    </div>
                    
                    <div className="text-center">
                      <p className="text-sm text-muted-foreground mb-1">Date</p>
                      <p className="font-medium">{summaryData?.attendance_date && summaryData?.attendance_date !== 'N/A' ? new Date(summaryData?.attendance_date).toISOString().split('T')[0] : (summaryData?.date || '-')}</p>
                    </div>
                    
                    <div className="text-center">
                      <p className="text-sm text-muted-foreground mb-1">Punch in at</p>
                      <p className="font-medium">{summaryData?.punchInTime || summaryData?.intimeFormatted || summaryData?.check_in || '-'}</p>
                    </div>
                    
                    <div className="text-center">
                      <p className="text-sm text-muted-foreground mb-1">Punch out at</p>
                      <p className="font-medium">{summaryData?.punchOutTime || summaryData?.outTime || summaryData?.check_out || '-'}</p>
                    </div>
                    
                    <div className="text-center">
                      <p className="text-sm text-muted-foreground mb-1">Status</p>
                      <p className="font-medium capitalize">{summaryData?.status || '-'}</p>
                    </div>
                  </div>
                  
                  <div className="border rounded-xl p-6 shadow-sm">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                      <div>
                        <div className="flex items-center gap-2 mb-2">
                          <div className="w-2 h-2 rounded-full bg-slate-800" />
                          <span className="text-sm font-medium text-slate-700">Total Working Hours</span>
                        </div>
                        <p className="text-3xl font-bold tracking-tight">{summaryData?.total_working_hours || '00h 00m 00s'}</p>
                      </div>
                      
                      <div>
                        <div className="flex items-center gap-2 mb-2">
                          <div className="w-2 h-2 rounded-full bg-emerald-500" />
                          <span className="text-sm font-medium text-slate-700">Productive Hours</span>
                        </div>
                        <p className="text-3xl font-bold tracking-tight">{summaryData?.productive_hours || '00h 00m 00s'}</p>
                      </div>
                      
                      <div>
                        <div className="flex items-center gap-2 mb-2">
                          <div className="w-2 h-2 rounded-full bg-amber-400" />
                          <span className="text-sm font-medium text-slate-700">Break Hours</span>
                        </div>
                        <p className="text-3xl font-bold tracking-tight">{summaryData?.break_hours || '00h 00m 00s'}</p>
                      </div>
                      
                      <div>
                        <div className="flex items-center gap-2 mb-2">
                          <div className="w-2 h-2 rounded-full bg-sky-400" />
                          <span className="text-sm font-medium text-slate-700">Overtime</span>
                        </div>
                        <p className="text-3xl font-bold tracking-tight">{summaryData?.overtime || '00h 00m 00s'}</p>
                      </div>
                    </div>
                    
                    {/* Timeline representation */}
                    <div className="relative pt-6 pb-2">
                      <div className="h-4 bg-slate-50 rounded-full w-full relative flex items-center px-4 overflow-hidden">
                        {summaryData?.time_segments?.map((segment: any, idx: number) => {
                          const width = Math.max(2, (segment.width || 0));
                          const colorClass = segment.type === 'productive' ? 'bg-emerald-500' : (segment.type === 'break' ? 'bg-amber-400' : 'bg-slate-300');
                          return (
                            <div key={idx} className={`h-full ${colorClass} opacity-80 border-r border-white/50`} style={{ width: `${width}%` }} />
                          );
                        })}
                        {(!summaryData?.time_segments || summaryData.time_segments.length === 0) && summaryData?.productive_hours !== '00h 00m 00s' && (
                          <div className="h-full bg-emerald-500 opacity-80 w-full" />
                        )}
                      </div>
                      <div className="flex justify-between mt-3 text-xs font-medium text-muted-foreground px-2">
                        {summaryData?.hour_labels?.map((label: string, i: number) => (
                          <span key={i}>{label}</span>
                        )) || (
                          <>
                            <span>08:00</span>
                            <span>10:00</span>
                            <span>12:00</span>
                            <span>14:00</span>
                            <span>16:00</span>
                            <span>18:00</span>
                          </>
                        )}
                      </div>
                    </div>
                  </div>
                </>
              )}
            </div>
          </div>
        </DialogContent>
      </Dialog>

      <AdminCorrectionModal 
        open={correctionModalOpen} 
        onOpenChange={setCorrectionModalOpen} 
        record={selectedCorrectionRecord} 
        boxSlug={boxSlug}
        onSuccess={() => fetchData(pagination.current_page)}
      />
    </div>
  );
}
