'use client';

import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowUp, ArrowDown, Clock, Activity, Settings, Zap } from 'lucide-react';
import { toast } from 'sonner';

export default function AttendanceCheckStatus() {
  const { box_slug } = useParams();
  const boxSlug = box_slug as string;

  const [loading, setLoading] = useState(true);
  const [currentTime, setCurrentTime] = useState(new Date());
  const [data, setData] = useState<any>(null);

  const fetchStatus = async () => {
    try {
      const res = await apiClient.get(`/${boxSlug}/attendance/employee/check-status`);
      setData(res.data?.data || res.data);
    } catch (error) {
      console.error('Error fetching attendance status:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const timer = setInterval(() => setCurrentTime(new Date()), 1000);
    return () => clearInterval(timer);
  }, []);

  useEffect(() => {
    if (boxSlug) {
      fetchStatus();
    }
  }, [boxSlug]);

  const handlePunch = async (action: 'in' | 'out') => {
    try {
      const pos = await new Promise<GeolocationPosition>((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject);
      }).catch(() => null);

      const payload = {
        punch: action,
        latitude: pos?.coords?.latitude || null,
        longitude: pos?.coords?.longitude || null,
      };

      await apiClient.post(`/${boxSlug}/attendance/employee/mark`, payload);
      toast.success(`Punched ${action} successfully!`);
      fetchStatus();
    } catch (error: any) {
      toast.error(error.response?.data?.message || `Failed to punch ${action}`);
    }
  };

  const handleBreak = async (action: 'start' | 'end') => {
    if (!data?.attendance?.id) return;
    try {
      await apiClient.post(`/${boxSlug}/attendance/employee/break`, {
        attendance_id: data.attendance.id,
        action
      });
      toast.success(action === 'start' ? 'Break started!' : 'Break ended!');
      fetchStatus();
    } catch (error: any) {
      toast.error(error.response?.data?.message || `Failed to ${action} break`);
    }
  };

  if (loading) return <div className="h-40 animate-pulse bg-slate-100 rounded-xl" />;

  const attendance = data?.attendance;
  const breaks = data?.breaks || [];
  
  const hasPunchedIn = !!attendance?.raw_check_in;
  const hasPunchedOut = !!attendance?.raw_check_out;
  const isPunchInAllowed = data?.is_punch_in_allowed ?? !hasPunchedIn;
  const punchStatus = attendance?.status ? attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1) : 'Absent';
  const lowerStatus = (attendance?.status || '').toLowerCase();
  
  const isLeave = lowerStatus.startsWith('approved') || lowerStatus.includes('leave');
  const isPunchOutMiss = lowerStatus === 'punch out miss';

  const activeBreak = breaks.find((b: any) => b.break_start && !b.break_end);
  const isOnBreak = !!activeBreak;

  const parseTime = (timeStr: string) => {
    if (!timeStr || !attendance?.attendance_date) return null;
    return new Date(`${attendance.attendance_date}T${timeStr}`);
  };

  let productionSeconds = 0;
  let breakSeconds = 0;

  if (hasPunchedIn && !hasPunchedOut) {
     const punchInTime = parseTime(attendance.raw_check_in)?.getTime() || currentTime.getTime();
     
     let completedBreakSecs = 0;
     breaks.forEach((b: any) => {
        if (b.break_start && b.break_end) {
           const s = new Date(b.break_start).getTime();
           const e = new Date(b.break_end).getTime();
           completedBreakSecs += (e - s) / 1000;
        }
     });

     if (isOnBreak) {
         const activeBreakStart = new Date(activeBreak.break_start).getTime();
         breakSeconds = completedBreakSecs + ((currentTime.getTime() - activeBreakStart) / 1000);
         productionSeconds = (activeBreakStart - punchInTime) / 1000 - completedBreakSecs;
     } else {
         breakSeconds = completedBreakSecs;
         productionSeconds = (currentTime.getTime() - punchInTime) / 1000 - completedBreakSecs;
     }
  } else if (hasPunchedOut) {
     if (attendance.production_hours) {
         const [h, m, s] = attendance.production_hours.split(':').map(Number);
         productionSeconds = (h || 0) * 3600 + (m || 0) * 60 + (s || 0);
     }
     if (attendance.break_duration) {
         const [h, m, s] = attendance.break_duration.split(':').map(Number);
         breakSeconds = (h || 0) * 3600 + (m || 0) * 60 + (s || 0);
     }
  }

  const formatSecs = (totalSecs: number, showSeconds: boolean = true) => {
      const s = Math.max(0, Math.floor(totalSecs));
      const h = Math.floor(s / 3600);
      const m = Math.floor((s % 3600) / 60);
      const sc = s % 60;
      if (showSeconds) {
          return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${sc.toString().padStart(2, '0')}`;
      }
      return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
  };

  const productionTimeStr = formatSecs(productionSeconds, false);
  const breakTimeStr = formatSecs(breakSeconds);
  const fullProductionTimeStr = formatSecs(productionSeconds);

  let primaryButton = null;
  let showBreakBtn = false;
  let breakButton = null;
  
  if (isLeave) {
    primaryButton = <Button disabled className="w-full bg-blue-500 text-white font-bold h-[46px] rounded-[6px] disabled:opacity-100">On Leave Today</Button>;
  } else if (isPunchOutMiss) {
    primaryButton = <Button disabled className="w-full bg-slate-500 text-white font-bold h-[46px] rounded-[6px] disabled:opacity-100">Punch Out Miss</Button>;
  } else if (!hasPunchedIn && isPunchInAllowed) {
    primaryButton = <Button onClick={() => handlePunch('in')} className="w-full bg-[#1BC55E] hover:bg-emerald-600 text-white font-bold h-[46px] rounded-[6px]">Punch In</Button>;
  } else if (hasPunchedOut) {
    primaryButton = <Button disabled className="w-full bg-[#FF6B6B] text-white opacity-90 disabled:opacity-90 font-bold h-[46px] rounded-[6px]">Already Punched Out</Button>;
  } else {
    primaryButton = <Button onClick={() => handlePunch('out')} className="w-full bg-[#FF6B6B] hover:bg-red-500 text-white font-bold h-[46px] rounded-[6px]">Punch Out</Button>;
    showBreakBtn = true;
    if (isOnBreak) {
      breakButton = (
        <Button onClick={() => handleBreak('end')} className="w-full bg-[#FF001C] hover:bg-red-700 text-white font-bold h-[46px] rounded-[6px] border-none mt-2">
          End Break
        </Button>
      );
    } else {
      breakButton = (
        <Button onClick={() => handleBreak('start')} className="w-full bg-[#FCA311] hover:bg-[#E08D00] text-white font-bold h-[46px] rounded-[6px] border-none mt-2">
          Take Break
        </Button>
      );
    }
  }

  return (
    <div className="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
      
      {/* Action Panel */}
      <Card className="bg-white border-none shadow-[0_2px_10px_rgba(0,0,0,0.04)] rounded-xl">
        <CardContent className="p-6 flex flex-col items-center justify-center text-center h-full">
          <p className="text-sm font-medium text-gray-500 mb-1">Attendance</p>
          <h2 className="text-[22px] font-bold mb-6 text-[#222222]">
            {currentTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}, {currentTime.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}
          </h2>
          
          {isOnBreak ? (
            <div className="text-[15px] font-bold text-[#FCA311] mb-1 animate-pulse">Currently on Break</div>
          ) : (
            <div className="text-[15px] font-bold text-[#1BC55E] mb-1">No Upcoming Break</div>
          )}
          
          <div className="text-[#007BFF] font-bold text-[17px] mb-6">Total Break: {breakTimeStr}</div>
          
          <div className="bg-[#1E1E2D] text-white px-5 py-1.5 rounded-[6px] font-bold mb-6 text-[15px]">
            {productionTimeStr}
          </div>
          
          <p className="text-[15px] font-medium text-gray-800 flex items-center gap-1.5 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#EE201C" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 12m-5 0a5 5 0 1 0 10 0a5 5 0 1 0 -10 0"></path><path d="M12 12l.01 0"></path></svg>
            {hasPunchedIn ? `Punch In at ${attendance?.check_in || '--:--'}` : `Punch Out at ${attendance?.check_out || '--:--'}`}
          </p>
          
          <div className="text-[15px] font-medium text-gray-500 mb-6">
            Status: <span className="text-gray-800">{punchStatus}</span>
          </div>
          
          <div className="w-full flex flex-col">
            {primaryButton}
            {showBreakBtn && breakButton}
          </div>
        </CardContent>
      </Card>

      {/* Stats Panels */}
      <div className="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        
        {/* Today */}
        <Card className="border-none shadow-[0_2px_10px_rgba(0,0,0,0.04)] bg-white rounded-xl relative overflow-hidden group">
          <CardContent className="p-6 h-full flex flex-col justify-between">
            <div className="mb-4">
              <div className="w-9 h-9 rounded-[6px] bg-[#EE201C] flex items-center justify-center mb-5 text-white">
                <Clock className="w-[18px] h-[18px]" />
              </div>
              <h2 className="text-[28px] font-bold flex items-baseline gap-1.5 mb-1 text-[#222222]">
                <span>{productionTimeStr}</span>
                <span className="text-[19px] text-gray-500">/ {data?.daily_hours || '09:30'}</span>
              </h2>
              <p className="text-[15px] font-medium text-gray-500">Total Hours Today</p>
            </div>
            <div className="pt-4 border-t border-gray-100 flex items-center text-[13px] font-medium text-gray-500">
              <span className="w-5 h-5 rounded-full bg-[#1BC55E] text-white flex items-center justify-center mr-2">
                <ArrowUp className="w-3 h-3" />
              </span>
              5% This Week
            </div>
          </CardContent>
        </Card>

        {/* Weekly */}
        <Card className="border-none shadow-[0_2px_10px_rgba(0,0,0,0.04)] bg-white rounded-xl relative overflow-hidden">
          <CardContent className="p-6 h-full flex flex-col justify-between">
            <div className="mb-4">
              <div className="w-9 h-9 rounded-[6px] bg-[#1E1E2D] flex items-center justify-center mb-5 text-white">
                <Clock className="w-[18px] h-[18px]" />
              </div>
              <h2 className="text-[28px] font-bold flex items-baseline gap-1.5 mb-1 text-[#222222]">
                <span>{data?.weekly_production_hours || '07:53'}</span>
                <span className="text-[19px] text-gray-500">/ {data?.weekly_hours || '47:30'}</span>
              </h2>
              <p className="text-[15px] font-medium text-gray-500">Total Hours Week</p>
            </div>
            <div className="pt-4 border-t border-gray-100 flex items-center text-[13px] font-medium text-gray-500">
              <span className="w-5 h-5 rounded-full bg-[#1BC55E] text-white flex items-center justify-center mr-2">
                <ArrowUp className="w-3 h-3" />
              </span>
              7% Last Week
            </div>
          </CardContent>
        </Card>

        {/* Monthly */}
        <Card className="border-none shadow-[0_2px_10px_rgba(0,0,0,0.04)] bg-white rounded-xl relative overflow-hidden">
          <CardContent className="p-6 h-full flex flex-col justify-between">
            <div className="mb-4">
              <div className="w-9 h-9 rounded-[6px] bg-[#00C5FF] flex items-center justify-center mb-5 text-white">
                <Activity className="w-[18px] h-[18px]" />
              </div>
              <h2 className="text-[28px] font-bold flex items-baseline gap-1.5 mb-1 text-[#222222]">
                <span>{data?.monthly_production_hours || '07:53'}</span>
                <span className="text-[19px] text-gray-500">/ {data?.monthly_hours || '209:00'}</span>
              </h2>
              <p className="text-[15px] font-medium text-gray-500">Total Hours Month</p>
            </div>
            <div className="pt-4 border-t border-gray-100 flex items-center text-[13px] font-medium text-gray-500">
              <span className="w-5 h-5 rounded-full bg-[#FF001C] text-white flex items-center justify-center mr-2">
                <ArrowDown className="w-3 h-3" />
              </span>
              8% Last Month
            </div>
          </CardContent>
        </Card>

        {/* Overtime */}
        <Card className="border-none shadow-[0_2px_10px_rgba(0,0,0,0.04)] bg-white rounded-xl relative overflow-hidden">
          <CardContent className="p-6 h-full flex flex-col justify-between">
            <div className="mb-4">
              <div className="w-9 h-9 rounded-[6px] bg-[#DF00FF] flex items-center justify-center mb-5 text-white">
                <Zap className="w-[18px] h-[18px]" />
              </div>
              <h2 className="text-[28px] font-bold flex items-baseline gap-1.5 mb-1 text-[#222222]">
                <span>{data?.total_overtime_hours || '00:00'}</span>
                <span className="text-[19px] text-gray-500">/ 00:00</span>
              </h2>
              <p className="text-[15px] font-medium text-gray-500">Overtime this Month</p>
            </div>
            <div className="pt-4 border-t border-gray-100 flex items-center text-[13px] font-medium text-gray-500">
              <span className="w-5 h-5 rounded-full bg-[#FF001C] text-white flex items-center justify-center mr-2">
                <ArrowDown className="w-3 h-3" />
              </span>
              6% Last Month
            </div>
          </CardContent>
        </Card>

        {/* Timeline block (spans all 4 columns below the cards) */}
        <div className="col-span-1 sm:col-span-2 xl:col-span-4 mt-2">
          <Card className="border-none shadow-[0_2px_10px_rgba(0,0,0,0.04)] bg-white rounded-xl">
            <CardContent className="p-6">
              <div className="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                <div>
                  <div className="flex items-center gap-2 mb-2">
                    <div className="w-2 h-2 rounded-full bg-gray-400" />
                    <span className="text-[15px] font-medium text-gray-500">Total Working hours</span>
                  </div>
                  <p className="text-[26px] font-bold text-[#222222]">{data?.total_working_hours || '00h 00m 00s'}</p>
                </div>
                <div>
                  <div className="flex items-center gap-2 mb-2">
                    <div className="w-2 h-2 rounded-full bg-[#1BC55E]" />
                    <span className="text-[15px] font-medium text-gray-500">Productive Hours</span>
                  </div>
                  <p className="text-[26px] font-bold text-[#222222]">
                    {fullProductionTimeStr.split(':').map((v, i) => v + (i===0?'h ':(i===1?'m ':'s'))).join('')}
                  </p>
                </div>
                <div>
                  <div className="flex items-center gap-2 mb-2">
                    <div className="w-2 h-2 rounded-full bg-[#FCA311]" />
                    <span className="text-[15px] font-medium text-gray-500">Break hours</span>
                  </div>
                  <p className="text-[26px] font-bold text-[#222222]">
                    {breakTimeStr.split(':').map((v, i) => v + (i===0?'h ':(i===1?'m ':'s'))).join('')}
                  </p>
                </div>
                <div>
                  <div className="flex items-center gap-2 mb-2">
                    <div className="w-2 h-2 rounded-full bg-[#00C5FF]" />
                    <span className="text-[15px] font-medium text-gray-500">Overtime</span>
                  </div>
                  <p className="text-[26px] font-bold text-[#222222]">{data?.total_overtime_hours ? data.total_overtime_hours + 'h' : '00h 00m 00s'}</p>
                </div>
              </div>

              {/* Progress bar timeline */}
              <div className="relative pt-2">
                <div className="h-4 bg-transparent rounded-sm w-full relative flex items-center overflow-hidden gap-1.5">
                  <div className="h-full bg-[#62C279] rounded-[4px] w-[60%]" />
                  <div className="h-full bg-[#F5B53F] rounded-[4px] w-[5%]" />
                  <div className="h-full bg-[#62C279] rounded-[4px] w-[5%]" />
                  <div className="h-full bg-[#24BDD3] rounded-[4px] w-[30%]" />
                </div>
                <div className="flex justify-between mt-3 text-[12px] font-medium text-gray-500 px-1">
                  <span>02:00</span><span>03:00</span><span>04:00</span><span>05:00</span>
                  <span>06:00</span><span>07:00</span><span>08:00</span><span>09:00</span>
                  <span>10:00</span><span>11:00</span><span>12:00</span><span>13:00</span>
                  <span>14:00</span><span>15:00</span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

      </div>
    </div>
  );
}
