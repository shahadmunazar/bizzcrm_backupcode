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
import AttendanceCheckStatus from '@/components/employee-attendance/AttendanceCheckStatus';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/components/ui/dialog';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination";
import { Home, Upload, FileText, ChevronsUp } from 'lucide-react';

export default function EmployeeAttendancePage() {
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

  const fetchStats = async () => {
    try {
      const res = await apiClient.get(`/${boxSlug}/attendance/dashboard/stats?scope=team`);
      if (res.status === 200) {
        setStats(res.data);
      }
    } catch (error) {
      console.error('Error fetching stats', error);
    }
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

      const res = await apiClient.get(`/${boxSlug}/attendance/employee/records?${query}`);
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
    <div className="p-6 space-y-6 max-w-[1600px] mx-auto bg-[#F4F6F9] min-h-screen">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h2 className="text-[28px] font-bold tracking-tight mb-1 text-[#222222]">Employee Attendance</h2>
          <div className="flex items-center text-[13px] text-gray-500 gap-1.5 font-medium">
            <Home className="w-4 h-4 text-gray-400" />
            <span className="text-gray-300">/</span>
            <span className="text-gray-500">Employee</span>
            <span className="text-gray-300">/</span>
            <span className="text-[#EE201C]">Employee Attendance</span>
          </div>
        </div>
        <div className="flex items-center gap-2">
          <div className="flex items-center bg-white rounded-md border p-0.5 shadow-sm">
            <Button variant="ghost" size="icon" className="h-8 w-8 bg-[#EE201C] text-white rounded-[4px] hover:bg-red-700">
              <Clock className="h-[18px] w-[18px]" />
            </Button>
            <Button variant="ghost" size="icon" className="h-8 w-8 text-gray-500 rounded-[4px] hover:bg-gray-100">
              <Calendar className="h-[18px] w-[18px]" />
            </Button>
          </div>
          <Button variant="outline" className="h-10 px-4 bg-white border-gray-200 shadow-sm flex items-center gap-2 text-gray-700 font-medium">
            <Upload className="h-4 w-4" />
            Export
            <ChevronDown className="h-4 w-4 opacity-70" />
          </Button>
          <Button className="h-10 px-4 bg-[#EE201C] hover:bg-red-700 shadow-sm flex items-center gap-2 font-medium">
            <FileText className="h-4 w-4" />
            Report
          </Button>
          <Button variant="ghost" size="icon" className="h-10 w-8 text-gray-400 p-0 hover:bg-transparent">
            <ChevronsUp className="h-5 w-5" />
          </Button>
        </div>
      </div>

      <AttendanceCheckStatus />

      {/* Table Section */}
      <Card>
        <CardHeader className="p-4">
          <div className="flex flex-col md:flex-row gap-4 justify-between">
            <h3 className="font-semibold text-lg flex-shrink-0">Team Attendance</h3>
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
                          <Badge variant="outline" className="bg-orange-50 text-orange-600 border-orange-200">Yes</Badge>
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
    </div>
  );
}
