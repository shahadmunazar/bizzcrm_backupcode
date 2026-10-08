'use client';

import React, { useState, useEffect, useRef } from 'react';
import { apiClient } from '@/lib/api-client';
import { toast } from 'sonner';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { ChevronDown } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';

export default function AttendanceCalendar({ boxSlug }: { boxSlug: string }) {
  const [loading, setLoading] = useState(false);
  const calendarRef = useRef<HTMLDivElement>(null);
  
  // Modal State
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedEvent, setSelectedEvent] = useState<any>(null);
  
  const [isCorrectionModalOpen, setIsCorrectionModalOpen] = useState(false);
  const [correctionForm, setCorrectionForm] = useState({ punch_in: false, punch_in_time: '', punch_out: false, punch_out_time: '', reason: '' });
  const [isLeaveModalOpen, setIsLeaveModalOpen] = useState(false);
  const [leaveForm, setLeaveForm] = useState({ leave_type: '', reason: '', from_date: '', to_date: '' });

  const handleCorrectionSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedEvent?.attendance_id) {
      toast.error('Attendance ID missing');
      return;
    }
    try {
      setLoading(true);
      await apiClient.post(`/${boxSlug}/attendance/employee/correction`, {
        ...correctionForm,
        attendace_id: selectedEvent.attendance_id
      });
      toast.success('Attendance correction submitted successfully');
      setIsCorrectionModalOpen(false);
    } catch (error: any) {
      toast.error(error.message || 'Failed to submit correction');
    } finally {
      setLoading(false);
    }
  };

  const handleLeaveSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setLoading(true);
      await apiClient.post(`/${boxSlug}/leaves`, leaveForm);
      toast.success('Leave applied successfully');
      setIsLeaveModalOpen(false);
    } catch (error: any) {
      toast.error(error.message || 'Failed to apply leave');
    } finally {
      setLoading(false);
    }
  };

  const handleEventClick = (clickInfo: any) => {
    const props = clickInfo.event.extendedProps;
    setSelectedEvent({
      date: clickInfo.event.startStr,
      title: clickInfo.event.title,
      className: clickInfo.event.classNames.join(' '),
      attendance_id: props.attendance_id,
      checkIn: props.check_in,
      checkOut: props.check_out,
      breakDuration: props.break_duration,
      productionHours: props.production_hours,
      workingHours: props.working_hours,
      overTime: props.over_time
    });
    setIsModalOpen(true);
  };

  const renderEventContent = (eventInfo: any) => {
    const { title, classNames } = eventInfo.event;
    const checkIn = eventInfo.event.extendedProps?.check_in;
    
    let bgColorClass = "bg-blue-500 text-white border-blue-600";
    const classes = classNames.join(' ');
    
    if (classes.includes('new-bg-green') || classes.includes('bg-success')) {
      bgColorClass = "bg-[#22c55e] text-white border-[#16a34a]";
    } else if (classes.includes('bg-danger')) {
      bgColorClass = "bg-[#ef4444] text-white border-[#dc2626]";
    } else if (classes.includes('new-bg-orange') || classes.includes('bg-warning')) {
      bgColorClass = "bg-[#f97316] text-white border-[#ea580c]";
    } else if (classes.includes('new-bg-yellow')) {
      bgColorClass = "bg-[#eab308] text-white border-[#ca8a04]";
    } else if (classes.includes('new-bg-purple') || classes.includes('bg-purple')) {
      bgColorClass = "bg-[#a855f7] text-white border-[#9333ea]";
    } else if (classes.includes('new-bg-light-yellow')) {
      bgColorClass = "bg-[#fef9c3] text-yellow-800 border-[#fde047]";
    } else if (classes.includes('new-bg-blue') || classes.includes('new-bg-skyblue')) {
      bgColorClass = "bg-[#3b82f6] text-white border-[#2563eb]";
    } else if (classes.includes('bg-dark')) {
      bgColorClass = "bg-slate-700 text-white border-slate-800";
    } else if (classes.includes('bg-light')) {
      bgColorClass = "bg-transparent text-slate-400 border-transparent font-normal";
    }

    const checkInHtml = checkIn ? `<span class="text-[10px] opacity-90 truncate block leading-tight">In: ${checkIn}</span>` : '';

    return {
      html: `
        <div class="w-full overflow-hidden text-xs px-1.5 py-1 rounded-sm border flex flex-col justify-center text-center shadow-sm ${bgColorClass}" style="min-height: 24px;">
          <span class="font-bold truncate tracking-wide leading-tight">${title}</span>
          ${checkInHtml}
        </div>
      `
    };
  };

  useEffect(() => {
    if (!calendarRef.current) return;

    import('@fullcalendar/core').then(({ Calendar }) => {
      import('@fullcalendar/daygrid').then(({ default: dayGridPlugin }) => {
        import('@fullcalendar/interaction').then(({ default: interactionPlugin }) => {
          
          const calendar = new Calendar(calendarRef.current!, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth'
            },
            events: function(info, successCallback, failureCallback) {
              setLoading(true);
              const start = info.startStr.split('T')[0];
              const end = info.endStr.split('T')[0];
              
              apiClient.get(`/${boxSlug}/attendance/employee/calendar`, {
                params: { start, end }
              }).then(res => {
                successCallback(res.data || []);
              }).catch(err => {
                console.error('Failed to fetch calendar data:', err);
                toast.error('Failed to load attendance calendar.');
                failureCallback(err);
              }).finally(() => {
                setLoading(false);
              });
            },
            eventClick: handleEventClick,
            eventContent: renderEventContent,
            height: 'auto',
            contentHeight: 700,
            dayMaxEvents: true,
          });

          calendar.render();

          // Cleanup
          return () => {
            calendar.destroy();
          };
        });
      });
    });
  }, [boxSlug]);

  return (
    <div className="w-full h-full p-4 relative">
      {loading && (
        <div className="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex items-center justify-center">
          <div className="w-6 h-6 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
        </div>
      )}
      
      <style dangerouslySetInnerHTML={{__html: `
        .fc-event { border: none !important; background: transparent !important; }
        .fc-daygrid-day-frame { min-height: 100px; }
        .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 600; color: #0f172a; }
        .fc .fc-button-primary { background-color: #f8fafc; border-color: #e2e8f0; color: #0f172a; }
        .fc .fc-button-primary:hover { background-color: #f1f5f9; border-color: #cbd5e1; color: #0f172a; }
        .fc .fc-button-primary:not(:disabled).fc-button-active, .fc .fc-button-primary:not(:disabled):active { background-color: #e2e8f0; border-color: #cbd5e1; color: #0f172a; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #e2e8f0; }
        .fc-day-today { background-color: #f8fafc !important; }
      `}} />

      <div ref={calendarRef} />

      <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
        <DialogContent className="sm:max-w-4xl bg-[#f5f6f8] p-0 overflow-hidden border-0">
          <div className="bg-white border-b px-6 py-4 flex items-center justify-between">
            <div className="flex items-center gap-4">
              <h2 className="text-xl font-bold text-slate-800">Attendance</h2>
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="outline" size="sm" className="h-8 border-red-500 text-red-500 hover:bg-red-50 font-medium">
                    Raise a Requests <ChevronDown className="ml-1 w-4 h-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start" className="w-48 p-2">
                  <DropdownMenuItem className="cursor-pointer text-slate-600 font-medium py-2" onClick={() => setIsCorrectionModalOpen(true)}>
                    Attendance Request
                  </DropdownMenuItem>
                  <DropdownMenuItem className="cursor-pointer text-slate-600 font-medium py-2" onClick={() => setIsLeaveModalOpen(true)}>
                    Apply Leave
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </div>
          
          {selectedEvent && (
            <div className="p-6 space-y-6">
              <div className="flex items-center justify-between">
                <h3 className="text-lg font-bold text-slate-800">Attendance for {selectedEvent.date}</h3>
                <span className="font-bold text-slate-800">{selectedEvent.title}</span>
              </div>
              
              {/* Detailed metrics box */}
              <div className="bg-white rounded border overflow-hidden">
                <div className="grid grid-cols-2 divide-x divide-slate-100">
                  <div className="p-4 space-y-4">
                    <div className="flex items-center">
                      <span className="w-1/3 text-sm font-bold text-slate-600">Punch In:</span>
                      <span className={`w-2/3 text-sm px-2 py-1 rounded ${!selectedEvent.checkIn || selectedEvent.checkIn === '-' ? 'bg-red-300 text-red-900 font-bold' : 'text-slate-800'}`}>
                        {selectedEvent.checkIn || '-'}
                      </span>
                    </div>
                    <div className="flex items-center">
                      <span className="w-1/3 text-sm font-bold text-slate-600">Break Duration:</span>
                      <span className={`w-2/3 text-sm px-2 py-1 rounded font-bold ${!selectedEvent.breakDuration || selectedEvent.breakDuration === '00h 00m 00s' ? 'bg-red-300 text-red-900' : 'text-slate-800'}`}>
                        {selectedEvent.breakDuration || '00h 00m 00s'}
                      </span>
                    </div>
                    <div className="flex items-center border-t border-slate-100 pt-4 mt-2">
                      <span className="w-1/3 text-sm font-bold text-slate-600">Status:</span>
                      <span className="w-2/3 text-sm font-bold text-slate-800">
                        {selectedEvent.title}
                      </span>
                    </div>
                  </div>
                  <div className="p-4 space-y-4">
                    <div className="flex items-center">
                      <span className="w-1/3 text-sm font-bold text-slate-600">Punch Out:</span>
                      <span className={`w-2/3 text-sm px-2 py-1 rounded ${!selectedEvent.checkOut || selectedEvent.checkOut === '-' ? 'bg-red-300 text-red-900 font-bold' : 'text-slate-800'}`}>
                        {selectedEvent.checkOut || '-'}
                      </span>
                    </div>
                    <div className="flex items-center">
                      <span className="w-1/3 text-sm font-bold text-slate-600">Late Duration:</span>
                      <span className="w-2/3 text-sm px-2 py-1 rounded font-bold bg-red-300 text-red-900">
                        00:00:00
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              {/* Summary totals box */}
              <div className="bg-white rounded border p-6">
                <div className="grid grid-cols-4 gap-4 mb-10">
                  <div>
                    <div className="flex items-center gap-2 mb-1">
                      <div className="w-2 h-2 rounded-full bg-slate-800"></div>
                      <span className="text-sm text-slate-600">Total Working hours</span>
                    </div>
                    <div className="text-2xl font-bold text-slate-800">{selectedEvent.workingHours || '00h 00m 00s'}</div>
                  </div>
                  <div>
                    <div className="flex items-center gap-2 mb-1">
                      <div className="w-2 h-2 rounded-full bg-green-500"></div>
                      <span className="text-sm text-slate-600">Productive Hours</span>
                    </div>
                    <div className="text-2xl font-bold text-slate-800">{selectedEvent.productionHours || '00h 00m 00s'}</div>
                  </div>
                  <div>
                    <div className="flex items-center gap-2 mb-1">
                      <div className="w-2 h-2 rounded-full bg-orange-400"></div>
                      <span className="text-sm text-slate-600">Break Hours</span>
                    </div>
                    <div className="text-2xl font-bold text-slate-800">{selectedEvent.breakDuration || '00h 00m 00s'}</div>
                  </div>
                  <div>
                    <div className="flex items-center gap-2 mb-1">
                      <div className="w-2 h-2 rounded-full bg-sky-400"></div>
                      <span className="text-sm text-slate-600">Overtime</span>
                    </div>
                    <div className="text-2xl font-bold text-slate-800">{selectedEvent.overTime || '00h 00m 00s'}</div>
                  </div>
                </div>

                {/* Timeline Placeholder */}
                <div className="relative pt-4 border-t border-slate-100 h-16 w-full flex items-end justify-between px-2 text-[10px] text-slate-500">
                  <span>02:00</span>
                  <span>03:00</span>
                  <span>04:00</span>
                  <span>05:00</span>
                  <span>06:00</span>
                  <span>07:00</span>
                  <span>08:00</span>
                  <span>09:00</span>
                  <span>10:00</span>
                  <span>11:00</span>
                  <span>12:00</span>
                  <span>13:00</span>
                  <span>14:00</span>
                  <span>15:00</span>
                </div>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>
      <Dialog open={isCorrectionModalOpen} onOpenChange={setIsCorrectionModalOpen}>
        <DialogContent className="sm:max-w-xl bg-white">
          <DialogHeader>
            <DialogTitle>Attendance Correction</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleCorrectionSubmit} className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="flex items-center space-x-2">
                <Checkbox id="punch_in" checked={correctionForm.punch_in} onCheckedChange={(c) => setCorrectionForm({...correctionForm, punch_in: !!c})} />
                <Label htmlFor="punch_in">Punch In</Label>
              </div>
              <Input type="time" disabled={!correctionForm.punch_in} value={correctionForm.punch_in_time} onChange={(e) => setCorrectionForm({...correctionForm, punch_in_time: e.target.value})} />
              
              <div className="flex items-center space-x-2">
                <Checkbox id="punch_out" checked={correctionForm.punch_out} onCheckedChange={(c) => setCorrectionForm({...correctionForm, punch_out: !!c})} />
                <Label htmlFor="punch_out">Punch Out</Label>
              </div>
              <Input type="time" disabled={!correctionForm.punch_out} value={correctionForm.punch_out_time} onChange={(e) => setCorrectionForm({...correctionForm, punch_out_time: e.target.value})} />
            </div>
            
            <div className="space-y-2">
              <Label>Reason</Label>
              <Input value={correctionForm.reason} onChange={(e) => setCorrectionForm({...correctionForm, reason: e.target.value})} />
            </div>

            <Button type="submit" className="w-full" disabled={loading || (!correctionForm.punch_in && !correctionForm.punch_out)}>
              {loading ? 'Submitting...' : 'Submit'}
            </Button>
          </form>
        </DialogContent>
      </Dialog>

      <Dialog open={isLeaveModalOpen} onOpenChange={setIsLeaveModalOpen}>
        <DialogContent className="sm:max-w-xl bg-white">
          <DialogHeader>
            <DialogTitle>Apply Leave</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleLeaveSubmit} className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label>From Date</Label>
                <Input type="date" value={leaveForm.from_date} onChange={(e) => setLeaveForm({...leaveForm, from_date: e.target.value})} />
              </div>
              <div className="space-y-2">
                <Label>To Date</Label>
                <Input type="date" value={leaveForm.to_date} onChange={(e) => setLeaveForm({...leaveForm, to_date: e.target.value})} />
              </div>
            </div>
            
            <div className="space-y-2">
              <Label>Reason</Label>
              <Input value={leaveForm.reason} onChange={(e) => setLeaveForm({...leaveForm, reason: e.target.value})} />
            </div>

            <Button type="submit" className="w-full" disabled={loading}>
              {loading ? 'Submitting...' : 'Submit'}
            </Button>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
