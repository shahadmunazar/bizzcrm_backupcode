import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { PieChart, Pie, Cell, ResponsiveContainer } from 'recharts';

export default function LeaveDetailsDonut({ stats }: { stats: any }) {
  // Use real stats if available, otherwise fallback to sample 0s
  const data = [
    { name: 'on time', value: stats?.on_time || 0, color: '#4b5563' }, // gray-600
    { name: 'Late Attendance', value: stats?.late || 0, color: '#22c55e' }, // green-500
    { name: 'Work From Home', value: 0, color: '#ef4444' }, // red-500 (No API equivalent found)
    { name: 'Absent', value: stats?.absent || 0, color: '#ef4444' }, // red-500
    { name: 'Sick Leave', value: 0, color: '#f97316' }, // orange-500 (No API equivalent found)
    { name: 'Applied', value: stats?.applied_leaves || 0, color: '#06b6d4' }, // cyan-500
    { name: 'Approved', value: stats?.approved_leaves || 0, color: '#22c55e' }, // green-500
    { name: 'Rejected', value: stats?.rejected_leaves || 0, color: '#ef4444' }, // red-500
    { name: 'Pending', value: stats?.pending_leaves || 0, color: '#f59e0b' }, // amber-500
  ];

  // Filter out zeroes for the chart itself so it renders correctly, but if all zero, provide a default gray ring
  const activeData = data.filter(d => d.value > 0);
  const chartData = activeData.length > 0 ? activeData : [{ name: 'Empty', value: 1, color: '#e5e7eb' }];

  return (
    <Card className="flex-fill border-0 shadow-sm h-full w-full">
      <CardHeader className="pb-3 flex flex-row items-center justify-between border-b">
        <CardTitle className="text-base font-bold text-slate-800">Leave Details</CardTitle>
        <div className="flex items-center gap-2">
          <button className="flex items-center gap-1 border rounded px-2 py-1 text-xs font-medium bg-white hover:bg-slate-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <span className="font-medium text-slate-700">2026</span>
          </button>
        </div>
      </CardHeader>
      <CardContent className="p-4">
        <div className="flex flex-row items-center justify-between h-[280px]">
          {/* Legend side */}
          <div className="w-[45%] flex flex-col justify-center space-y-2">
            {data.map((item, index) => (
              <div key={index} className="flex items-center text-[12px] leading-tight">
                <span className="w-1.5 h-1.5 rounded-full mr-1.5 shrink-0" style={{ backgroundColor: item.color }}></span>
                <span className="font-bold text-slate-800 mr-1">{item.value}</span>
                <span className="text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">{item.name}</span>
              </div>
            ))}
            <div className="mt-3 pt-2 border-t flex items-start text-[11px] text-slate-600">
              <input type="checkbox" className="mr-1.5 mt-0.5 rounded border-slate-300" />
              <span>Better than <strong className="text-slate-800">85%</strong> of Employees</span>
            </div>
          </div>
          
          {/* Chart side */}
          <div className="w-[55%] h-full flex items-center justify-center">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={chartData}
                  cx="50%"
                  cy="50%"
                  innerRadius={50}
                  outerRadius={80}
                  paddingAngle={2}
                  dataKey="value"
                  stroke="none"
                >
                  {chartData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.color} />
                  ))}
                </Pie>
              </PieChart>
            </ResponsiveContainer>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
