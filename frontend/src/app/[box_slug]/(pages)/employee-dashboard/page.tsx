'use client';

import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Loader2, Briefcase, Clock, Bell } from 'lucide-react';
import { toast } from 'sonner';
import AttendanceCheckStatus from '@/components/employee-attendance/AttendanceCheckStatus';
import AttendanceOverviewGauge from '@/components/employee-dashboard/AttendanceOverviewGauge';
import LeaveDetailsDonut from '@/components/employee-dashboard/LeaveDetailsDonut';
import LeaveDetailsStats from '@/components/employee-dashboard/LeaveDetailsStats';
import TeamMembersList from '@/components/employee-dashboard/TeamMembersList';

export default function EmployeeDashboard() {
  const { box_slug } = useParams();
  const boxSlug = box_slug as string;

  const [loading, setLoading] = useState(true);
  const [dashboardData, setDashboardData] = useState<any>(null);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);

  const fetchDashboardData = async () => {
    setLoading(true);
    setErrorMsg(null);
    try {
      const res = await apiClient.get(`/${boxSlug}/dashboard`);
      setDashboardData(res.data?.data || res.data || null);
    } catch (error: any) {
      console.error('Error fetching dashboard data:', error);
      setErrorMsg(error.response?.data?.message || error.message || 'Unknown error');
      toast.error('Failed to load dashboard data.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (boxSlug) {
      fetchDashboardData();
    }
  }, [boxSlug]);

  if (loading) {
    return (
      <div className="flex h-[80vh] w-full items-center justify-center">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-lg font-medium text-muted-foreground">Loading dashboard...</span>
      </div>
    );
  }

  if (errorMsg || !dashboardData) {
    return (
      <div className="p-8 text-center text-muted-foreground text-red-500">
        Failed to load dashboard data. Please try again.
        <br />
        Error details: {errorMsg || 'Response was null'}
      </div>
    );
  }

  const { info, overview, stats, notifications, team_members } = dashboardData;

  return (
    <div className="p-6 space-y-6 max-w-[1600px] mx-auto bg-slate-50/50 min-h-screen">
      
      {/* Header Section */}
      <div className="flex flex-col md:flex-row gap-6 items-start md:items-center justify-between mb-2">
        <div className="flex items-center gap-4">
          <Avatar className="h-16 w-16 border-2 border-primary/10">
            <AvatarImage src={info?.employee?.user?.profile_image} />
            <AvatarFallback className="bg-primary text-primary-foreground text-xl">
              {(info?.employee?.user?.name || 'U').charAt(0)}
            </AvatarFallback>
          </Avatar>
          <div>
            <h1 className="text-2xl font-bold tracking-tight text-slate-900">
              Welcome back, {info?.employee?.user?.name || 'Employee'}!
            </h1>
            <p className="text-muted-foreground">
              {info?.employee?.designation_relation?.name || 'Employee'} at {info?.employee?.department_relation?.name || 'Department'}
            </p>
          </div>
        </div>
        
        <div className="flex gap-2">
          {info?.reportOffices?.map((office: any) => (
            <Badge key={office.id} variant="secondary" className="px-3 py-1.5 text-sm font-medium">
              <Briefcase className="w-4 h-4 mr-2 text-slate-500" />
              {office.name}
            </Badge>
          ))}
          <Badge variant="outline" className="px-3 py-1.5 text-sm font-medium border-emerald-200 bg-emerald-50 text-emerald-700">
            <Clock className="w-4 h-4 mr-2" />
            {info?.employee?.shift?.name || 'Standard Shift'}
          </Badge>
        </div>
      </div>

      {/* Top Row: Attendance Overview, Leave Donut, Leave Stats */}
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 xl:col-span-4 flex">
          <AttendanceOverviewGauge overview={overview} />
        </div>
        <div className="col-span-12 xl:col-span-5 flex">
          <LeaveDetailsDonut stats={stats} />
        </div>
        <div className="col-span-12 xl:col-span-3 flex">
          <LeaveDetailsStats stats={stats} boxSlug={boxSlug} />
        </div>
      </div>

      {/* Middle Row: Live Attendance & Total Hours */}
      <div>
        <AttendanceCheckStatus />
      </div>

      {/* Bottom Row: Team Members, Notifications, etc. */}
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 xl:col-span-4 flex">
          <TeamMembersList members={team_members} />
        </div>
        
        <div className="col-span-12 xl:col-span-5 flex">
          <Card className="flex-fill border-0 shadow-sm h-full w-full flex flex-col">
            <CardHeader className="p-5 border-b flex flex-row items-center justify-between">
              <CardTitle className="text-lg font-bold text-slate-800 flex items-center">
                Notifications
              </CardTitle>
              <button className="px-3 py-1 text-sm font-medium border rounded bg-slate-50 hover:bg-slate-100 transition-colors">
                View All
              </button>
            </CardHeader>
            <CardContent className="p-0 flex-1">
              {notifications?.length > 0 ? (
                <div className="divide-y h-[400px] overflow-y-auto">
                  {notifications.map((notif: any) => (
                    <div key={notif.id} className="p-4 hover:bg-slate-50 transition-colors flex gap-3">
                      <div className="flex-shrink-0 w-10 h-10 rounded bg-slate-100 flex items-center justify-center font-bold text-slate-600 border border-slate-200">
                        {notif.data?.title ? notif.data.title.charAt(0).toUpperCase() : 'N'}
                      </div>
                      <div>
                        <p className="text-sm font-medium text-slate-900">{notif.data?.message || 'New notification'}</p>
                        <p className="text-xs text-slate-500 mt-1">{new Date(notif.created_at).toLocaleString()}</p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="h-[400px] flex items-center justify-center text-sm text-slate-400">
                  No new notifications
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Third Column: Birthday, Policy, Holiday */}
        <div className="col-span-12 xl:col-span-3 flex flex-col gap-6">
          {/* Team Birthday */}
          <Card className="bg-[#1e1e2d] text-white border-0 shadow-sm flex-1 flex flex-col justify-center items-center p-6 text-center">
            <h5 className="font-bold text-lg mb-6">Team Birthday</h5>
            <div className="flex-1 flex flex-col items-center justify-center">
              <p className="text-sm text-slate-300 mb-6">No upcoming birthdays</p>
              <button className="bg-[#ef4444] hover:bg-red-600 text-white text-sm font-bold py-2 px-4 rounded mb-3 w-[140px] transition-colors">
                Send Wishes
              </button>
              <button className="bg-[#06b6d4] hover:bg-cyan-600 text-white text-sm font-bold py-2 px-4 rounded w-[160px] transition-colors">
                Upcoming Birthdays
              </button>
            </div>
          </Card>

          {/* Leave Policy */}
          <Card className="bg-[#f59e0b] text-white border-0 shadow-sm">
            <CardContent className="p-5 flex items-center justify-between">
              <div>
                <h5 className="font-bold text-lg mb-1">Leave Policy</h5>
                <p className="text-white/80 text-sm">Last Updated : Today</p>
              </div>
              <button className="bg-white text-slate-800 text-sm font-bold py-1.5 px-4 rounded hover:bg-slate-50 transition-colors">
                View All
              </button>
            </CardContent>
          </Card>

          {/* Next Holiday */}
          <Card className="bg-[#eab308] text-white border-0 shadow-sm">
            <CardContent className="p-5 flex items-center justify-between">
              <div>
                <h5 className="font-bold text-lg mb-1">Next Holiday</h5>
                <p className="text-white/80 text-sm">-, -</p>
              </div>
              <button className="bg-white text-slate-800 text-sm font-bold py-1.5 px-4 rounded hover:bg-slate-50 transition-colors">
                View All
              </button>
            </CardContent>
          </Card>
        </div>
      </div>
      
    </div>
  );
}
