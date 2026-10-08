import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

export default function TeamMembersList({ members }: { members: any[] }) {
  // Use provided members or fallback to an empty array
  const teamMembers = members || [];

  return (
    <Card className="flex-fill border-0 shadow-sm h-full w-full flex flex-col">
      <CardHeader className="p-5 border-b flex flex-row items-center justify-between">
        <CardTitle className="text-lg font-bold text-slate-800">Team Members</CardTitle>
        <button className="px-3 py-1 text-sm font-medium border rounded bg-slate-50 hover:bg-slate-100 transition-colors">
          View All
        </button>
      </CardHeader>
      <CardContent className="p-0 flex-1">
        {teamMembers.length > 0 ? (
          <div className="divide-y h-[400px] overflow-y-auto">
            {teamMembers.map((member: any) => {
              const name = member.user?.name || member.name || 'Unknown';
              const initial = name.charAt(0).toUpperCase();
              const empId = member.employee_id || `MLB-${member.id || 'XX'}`;
              
              // Colors for avatars if no image
              const colors = ['bg-red-500', 'bg-purple-600', 'bg-blue-500', 'bg-cyan-500', 'bg-teal-600'];
              const bgColor = colors[name.length % colors.length];

              return (
                <div key={member.id || Math.random()} className="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                  <div className="flex items-center gap-3">
                    <Avatar className={`h-10 w-10 ${!member.profile_image_path ? bgColor : ''}`}>
                      <AvatarImage src={member.profile_image_path || undefined} />
                      <AvatarFallback className="text-white font-medium text-sm bg-transparent">
                        {initial}
                      </AvatarFallback>
                    </Avatar>
                    <div>
                      <div className="text-base font-medium text-slate-900">{name}</div>
                      <div className="text-sm text-slate-500">
                        {member.designation || 'Member'} {member.department ? `• ${member.department}` : ''}
                      </div>
                    </div>
                  </div>
                  <div className="font-bold text-xs bg-slate-50 border rounded px-2 py-1 text-slate-900">
                    {empId}
                  </div>
                </div>
              );
            })}
          </div>
        ) : (
          <div className="p-8 text-center text-sm text-slate-500">
            No team members found.
          </div>
        )}
      </CardContent>
    </Card>
  );
}
