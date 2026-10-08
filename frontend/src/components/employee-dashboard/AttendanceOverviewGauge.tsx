import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { RadialBarChart, RadialBar, ResponsiveContainer, PolarAngleAxis } from 'recharts';

export default function AttendanceOverviewGauge({ overview }: { overview: any }) {
  const totalAttendance = overview?.total_attendance || 0;
  const counts = overview?.raw_counts || {};
  
  // Fake or calculated data for the gauge
  // We'll create a semi-circle (180 to 0 degrees)
  const data = [
    { name: 'Base', value: 100, fill: '#e5e7eb' }, // Background track
    { name: 'Attendance', value: totalAttendance > 0 ? (totalAttendance / 31) * 100 : 0, fill: '#ef4444' } // Red fill
  ];

  return (
    <Card className="flex-fill border-0 shadow-sm h-full w-full">
      <CardHeader className="pb-3 border-b flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <CardTitle className="text-base font-bold text-slate-800 shrink-0">Attendance Overview</CardTitle>
        <div className="flex gap-2">
          <select className="border rounded px-2 py-1 text-xs font-medium bg-white text-slate-700 outline-none">
            <option>2026</option>
          </select>
          <select className="border rounded px-2 py-1 text-xs font-medium bg-white text-slate-700 outline-none">
            <option>September</option>
          </select>
        </div>
      </CardHeader>
      <CardContent className="p-4 pt-6">
        <div className="relative h-[180px] w-full flex items-center justify-center overflow-hidden">
          <ResponsiveContainer width="100%" height="100%">
            <RadialBarChart 
              cx="50%" 
              cy="100%" 
              innerRadius="70%" 
              outerRadius="100%" 
              barSize={30} 
              data={data} 
              startAngle={180} 
              endAngle={0}
            >
              <PolarAngleAxis type="number" domain={[0, 100]} angleAxisId={0} tick={false} />
              <RadialBar
                // @ts-expect-error minAngle typing missing
                minAngle={15}
                background={{ fill: '#64748b' }} // Dark gray background as seen in the screenshot
                clockWise
                dataKey="value"
                cornerRadius={10}
              />
            </RadialBarChart>
          </ResponsiveContainer>
          <div className="absolute bottom-4 left-0 right-0 text-center">
            <p className="text-sm text-slate-500 mb-1">Total Attendance</p>
            <h3 className="text-3xl font-bold text-slate-900">{totalAttendance}</h3>
          </div>
        </div>

        <div className="mt-6 space-y-3">
          <h6 className="font-bold text-slate-800 mb-3 text-sm">Status</h6>
          
          <div className="flex justify-between items-center text-sm">
            <div className="flex items-center"><span className="w-2 h-2 rounded-full bg-green-500 mr-2"></span><span className="text-slate-600">Present</span></div>
            <span className="font-bold text-slate-800">{counts?.present || 0}</span>
          </div>
          
          <div className="flex justify-between items-center text-sm">
            <div className="flex items-center"><span className="w-2 h-2 rounded-full bg-amber-500 mr-2"></span><span className="text-slate-600">Punch Out Miss</span></div>
            <span className="font-bold text-slate-800">{counts?.punch_out_miss || 0}</span>
          </div>
          
          <div className="flex justify-between items-center text-sm">
            <div className="flex items-center"><span className="w-2 h-2 rounded-full bg-red-500 mr-2"></span><span className="text-slate-600">Absent</span></div>
            <span className="font-bold text-slate-800">{counts?.absent || 0}</span>
          </div>
          
          <div className="flex justify-between items-center text-sm">
            <div className="flex items-center"><span className="w-2 h-2 rounded-full bg-orange-400 mr-2"></span><span className="text-slate-600">Weekly Off</span></div>
            <span className="font-bold text-slate-800">{counts?.weekly_off || 0}</span>
          </div>
          
          <div className="flex justify-between items-center text-sm">
            <div className="flex items-center"><span className="w-2 h-2 rounded-full bg-cyan-400 mr-2"></span><span className="text-slate-600">Holiday</span></div>
            <span className="font-bold text-slate-800">{counts?.holiday || 0}</span>
          </div>
          
          <div className="flex justify-between items-center text-sm">
            <div className="flex items-center"><span className="w-2 h-2 rounded-full bg-red-600 mr-2"></span><span className="text-slate-600">Half Day</span></div>
            <span className="font-bold text-slate-800">{counts?.half_day || 0}</span>
          </div>
          
          <div className="flex justify-between items-center text-sm">
            <div className="flex items-center"><span className="w-2 h-2 rounded-full bg-fuchsia-500 mr-2"></span><span className="text-slate-600">Short Leave</span></div>
            <span className="font-bold text-slate-800">{counts?.short_leave || 0}</span>
          </div>
        </div>

        <div className="mt-4 pt-4 border-t flex justify-between items-center bg-slate-50 p-3 rounded">
          <span className="text-sm text-slate-600">Total Absentees</span>
          <a href="#" className="text-sm font-medium text-red-600 hover:underline">View Details</a>
        </div>
      </CardContent>
    </Card>
  );
}
