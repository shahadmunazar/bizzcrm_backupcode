import React from 'react';
import AttendanceCalendarClientWrapper from '@/components/employee-attendance/AttendanceCalendarClientWrapper';

export default async function AttendanceCalendarPage({ params }: { params: { box_slug: string } }) {
  const { box_slug } = await params;

  return (
    <div className="page-wrapper h-full flex flex-col p-6 space-y-6 bg-slate-50 min-h-screen">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900">Attendance Calendar</h1>
      </div>
      
      <div className="flex-1 rounded-xl border bg-white shadow-sm overflow-hidden relative">
        <AttendanceCalendarClientWrapper boxSlug={box_slug} />
      </div>
    </div>
  );
}
