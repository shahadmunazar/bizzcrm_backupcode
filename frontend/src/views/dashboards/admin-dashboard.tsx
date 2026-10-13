'use client'

import React, { useEffect, useState } from 'react'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbSeparator } from '@/components/ui/breadcrumb'
import { CalendarIcon, BellRing, Plus, FileText, LayoutDashboard, Settings } from 'lucide-react'
import { apiClient } from '@/lib/api-client'
import { useAuth } from '@/contexts/AuthContext'
import { format } from 'date-fns'
import { useParams } from 'next/navigation'

// Simple Circular Progress / Semi-Circle component for dashboard using current theme colors via currentColor
const SemiCircleProgress = ({ value, total, colorClass, label }: { value: number, total: number, colorClass: string, label: string }) => {
  const percentage = total > 0 ? (value / total) * 100 : 0
  return (
    <div className="relative w-full h-32 flex flex-col items-center justify-end overflow-hidden">
      <div className="w-48 h-48 rounded-full border-[20px] border-muted absolute top-4"></div>
      <div
        className={`w-48 h-48 rounded-full border-[20px] border-transparent absolute top-4 ${colorClass}`}
        style={{
          borderTopColor: 'currentColor',
          borderRightColor: 'currentColor',
          transform: `rotate(${percentage * 1.8 - 45}deg)`
        }}
      ></div>
      <div className="flex flex-col items-center z-10 bg-card px-8 pb-2">
        <span className="text-xs text-muted-foreground">{label}</span>
        <span className="text-2xl font-bold text-foreground">{value}</span>
      </div>
    </div>
  )
}

const AdminDashboard = () => {
  const { user, boxUser } = useAuth()
  const [data, setData] = useState<any>(null)
  const [loading, setLoading] = useState(true)
  const [dateRange, setDateRange] = useState({
    startDate: new Date(),
    endDate: new Date()
  })

  const params = useParams()
  const routeBoxSlug = (params?.box_slug as string) || ''

  useEffect(() => {
    const fetchDashboardData = async () => {
      setLoading(true)
      try {
        let slug = routeBoxSlug;
        if (!slug || slug === 'undefined') {
          slug = boxUser?.box?.slug || localStorage.getItem('box_slug') || '';
        }

        if (!slug || slug === 'undefined') {
           setLoading(false);
           return;
        }

        const token = typeof window !== 'undefined' ? localStorage.getItem('access_token') : null;
        if (!token) {
           setLoading(false);
           return;
        }

        const start = format(dateRange.startDate, 'yyyy-MM-dd')
        const end = format(dateRange.endDate, 'yyyy-MM-dd')
        const url = `/${slug}/dashboard/admin-attendance-list`

        const response = await apiClient.get(`${url}?start_date=${start}&end_date=${end}`)
        if (response?.success) {
          setData(response)
        }
      } catch (error) {
        console.error('Failed to fetch dashboard data', error)
      } finally {
        setLoading(false)
      }
    }
    fetchDashboardData()
  }, [dateRange, routeBoxSlug, boxUser])

  if (loading || !data) {
    return <div className="p-8 flex justify-center items-center h-64 text-muted-foreground">Loading dashboard...</div>
  }

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold tracking-tight text-foreground">Admin Dashboard</h1>
          <Breadcrumb className="mt-1">
            <BreadcrumbList>
              <BreadcrumbItem>
                <BreadcrumbLink href="/" className="flex items-center gap-1 text-muted-foreground">
                  <LayoutDashboard className="h-3 w-3" /> Dashboard
                </BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <span className="text-primary font-medium">Admin Dashboard</span>
              </BreadcrumbItem>
            </BreadcrumbList>
          </Breadcrumb>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="outline" className="gap-2">
            {format(dateRange.startDate, 'MM/dd/yyyy')} - {format(dateRange.endDate, 'MM/dd/yyyy')}
            <CalendarIcon className="h-4 w-4" />
          </Button>
        </div>
      </div>

      {/* Welcome Banner */}
      <Card className="border-none shadow-sm overflow-hidden relative">
        <CardContent className="p-6">
          <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div className="flex items-center gap-4">
              <div className="h-16 w-16 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-2xl font-bold">
                {user?.name?.charAt(0) || 'U'}
              </div>
              <div>
                <h2 className="text-2xl font-bold text-foreground">Welcome Back, {user?.name || 'Admin'}</h2>
                <p className="text-muted-foreground mt-1 text-sm">
                  You have <span className="text-destructive font-semibold underline">{data.pending_requests?.attendance_corrections || 0}</span> Pending Approvals & <span className="text-destructive font-semibold underline">{data.pending_requests?.leave_requests || 0}</span> Leave Requests
                </p>
              </div>
            </div>
            <div className="flex flex-wrap items-center gap-3 pr-10">
              <Button className="gap-2" variant="default">
                <BellRing className="h-4 w-4" /> Send Notification
              </Button>
              <Button className="gap-2" variant="secondary">
                <Plus className="h-4 w-4" /> Add Project
              </Button>
              <Button className="gap-2" variant="destructive">
                <FileText className="h-4 w-4" /> Add Requests
              </Button>
            </div>
          </div>
        </CardContent>
        {/* settings cog on the right */}
        <div className="absolute top-1/2 right-0 transform -translate-y-1/2 bg-destructive p-2 text-destructive-foreground cursor-pointer rounded-l-md hover:bg-destructive/90 transition-colors">
          <Settings className="h-5 w-5" />
        </div>
      </Card>

      {/* Quick Stats & Chart Row */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <Card className="shadow-sm">
          <CardContent className="p-5">
            <div className="h-10 w-10 rounded-full bg-destructive/10 text-destructive flex items-center justify-center mb-4">
              <CalendarIcon className="h-5 w-5" />
            </div>
            <p className="text-sm text-muted-foreground font-medium">Attendance Overview</p>
            <div className="flex items-end gap-2 mt-2">
              <h3 className="text-2xl font-bold text-foreground">{data.attendance_summary?.present || 0}/{data.attendance_summary?.total || data.total_active_employees || 0}</h3>
              <span className={`text-xs font-medium mb-1 ${data.attendance_summary?.is_increase ? 'text-primary' : 'text-destructive'}`}>
                {data.attendance_summary?.is_increase ? '+' : '-'}{data.attendance_summary?.percentage_diff || 0}%
              </span>
            </div>
            <div className="mt-4 text-sm font-medium hover:underline cursor-pointer text-foreground">View Details</div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardContent className="p-5">
            <div className="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-4">
              <FileText className="h-5 w-5" />
            </div>
            <p className="text-sm text-muted-foreground font-medium">Task Overview</p>
            <div className="flex items-end gap-2 mt-2">
              <h3 className="text-2xl font-bold text-primary">{data.task_stats?.open_count || 0} <span className="text-muted-foreground font-normal">/ {data.task_stats?.completed || 0}</span></h3>
              <span className="text-xs text-primary font-medium mb-1">+0%</span>
            </div>
            <div className="mt-4 text-sm font-medium hover:underline cursor-pointer text-foreground">View All</div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardContent className="p-5 relative">
            <div className="h-10 w-10 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center mb-4">
              <FileText className="h-5 w-5" />
            </div>
            <p className="absolute top-5 right-5 text-sm text-muted-foreground font-medium">Open Task</p>
            <h3 className="text-3xl font-bold mt-2 text-foreground">{data.task_stats?.open_count || 0}</h3>
            <div className="w-full h-1 bg-muted mt-4 rounded-full overflow-hidden">
              <div className="h-full bg-primary transition-all duration-500" style={{ width: `${Math.min(((data.task_stats?.open_count || 0) / (data.task_stats?.total || 1)) * 100, 100)}%` }}></div>
            </div>
            <div className="mt-4 text-sm font-medium text-primary hover:underline cursor-pointer">View Open</div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardContent className="p-5">
            <div className="h-10 w-10 rounded-full bg-green-500/10 text-green-500 flex items-center justify-center mb-4">
              <FileText className="h-5 w-5" />
            </div>
            <p className="text-sm text-muted-foreground font-medium">Job Applicants</p>
            <div className="flex items-end gap-2 mt-2">
              <h3 className="text-2xl font-bold text-foreground">{0}</h3>
              <span className="text-xs text-primary font-medium mb-1">+0.0%</span>
            </div>
            <div className="mt-4 text-sm font-medium hover:underline cursor-pointer text-foreground">View All</div>
          </CardContent>
        </Card>

        <Card className="shadow-sm lg:col-span-1">
          <CardHeader className="p-4 pb-0 flex flex-row items-center justify-between">
            <CardTitle className="text-sm font-bold text-foreground">Employees By Department</CardTitle>
            <Button variant="outline" size="sm" className="h-7 text-xs">This Week</Button>
          </CardHeader>
          <CardContent className="p-4 pt-2">
            <div className="space-y-2 mt-2">
              {Object.entries(data.department_counts || {}).slice(0, 5).map(([dept, count]: any) => (
                <div key={dept} className="flex items-center text-xs">
                  <div className="w-16 truncate text-muted-foreground">{dept}</div>
                  <div className="flex-1 flex items-center gap-2">
                    <div className="h-2 bg-primary rounded-full" style={{ width: `${Math.max((count / (data.total_active_employees || 1)) * 100, 5)}%` }}></div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Row 3 */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Employee Status */}
        <Card className="shadow-sm">
          <CardHeader className="p-5 pb-2 flex flex-row items-center justify-between border-b">
            <CardTitle className="text-base font-bold text-foreground">Employee Status</CardTitle>
            <Button variant="outline" size="sm" className="h-7 text-xs">This Week</Button>
          </CardHeader>
          <CardContent className="p-5 space-y-6">
            <div>
              <div className="flex justify-between items-center mb-2">
                <span className="text-sm text-muted-foreground">Total Employee</span>
                <span className="text-xl font-bold text-foreground">{data.total_active_employees || 0}</span>
              </div>
              <div className="w-full h-3 bg-muted rounded-full overflow-hidden"></div>
              <div className="border border-border bg-muted/50 rounded-md p-4 text-center mt-4 text-sm text-muted-foreground">
                No employee data available.
              </div>
            </div>

            <div>
              <h4 className="font-bold text-sm mb-3 text-foreground">Top Performer</h4>
              <div className="border border-border rounded-md p-3 flex justify-between items-center relative overflow-hidden">
                <div className="absolute left-0 top-0 bottom-0 w-1 bg-destructive"></div>
                <div className="flex items-center gap-3 pl-2">
                  <div className="text-destructive"><Settings className="h-5 w-5" /></div>
                  <div className="h-8 w-8 rounded-full bg-muted flex items-center justify-center text-xs overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=Daniel+Esbella" alt="Avatar" />
                  </div>
                  <div>
                    <p className="text-sm font-bold text-foreground">Daniel Esbella</p>
                    <p className="text-xs text-muted-foreground">IOS Developer</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-xs text-muted-foreground">Performance</p>
                  <p className="text-destructive font-bold">99%</p>
                </div>
              </div>
              <Button variant="secondary" className="w-full mt-4 font-semibold">
                View All Employees
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Attendance Overview */}
        <Card className="shadow-sm">
          <CardHeader className="p-5 pb-2 flex flex-row items-center justify-between border-b">
            <CardTitle className="text-base font-bold text-foreground">Attendance Overview</CardTitle>
            <Button variant="outline" size="sm" className="h-7 text-xs">Today</Button>
          </CardHeader>
          <CardContent className="p-5 flex flex-col items-center">
            <SemiCircleProgress value={data.attendance_summary?.present || 0} total={data.attendance_summary?.total || data.total_active_employees || 0} colorClass="text-destructive" label="Total Attendance" />

            <div className="w-full mt-4 space-y-3">
              <h4 className="font-bold text-sm text-foreground">Status</h4>
              <div className="space-y-2 text-sm text-muted-foreground">
                <div className="flex justify-between items-center">
                  <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-green-500"></div> Present</div>
                  <div>{data.attendance_breakdown?.present?.percentage || 0}%</div>
                </div>
                <div className="flex justify-between items-center">
                  <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-gray-500"></div> Late</div>
                  <div>{data.attendance_breakdown?.late?.percentage || 0}%</div>
                </div>
                <div className="flex justify-between items-center">
                  <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-secondary"></div> Permission</div>
                  <div>{data.attendance_breakdown?.permission?.percentage || 0}%</div>
                </div>
                <div className="flex justify-between items-center">
                  <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-destructive"></div> Absent</div>
                  <div>{data.attendance_breakdown?.absent?.percentage || 0}%</div>
                </div>
                <div className="flex justify-between items-center">
                  <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-primary"></div> Weekly Off</div>
                  <div>0%</div>
                </div>
                <div className="flex justify-between items-center">
                  <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-accent"></div> Holiday</div>
                  <div>0%</div>
                </div>
              </div>
            </div>

            <div className="w-full flex justify-between items-center mt-6 pt-4 border-t">
              <span className="text-sm text-muted-foreground">Total Absentees</span>
              <span className="text-sm text-destructive font-medium hover:underline cursor-pointer">View Details</span>
            </div>
          </CardContent>
        </Card>

        {/* Clock-In/Out */}
        <Card className="shadow-sm relative overflow-hidden">
          <CardHeader className="p-5 pb-2 flex flex-row items-center justify-between border-b">
            <CardTitle className="text-base font-bold text-foreground">Clock-In/Out</CardTitle>
            <div className="flex gap-2">
              <Button variant="outline" size="sm" className="h-7 text-xs">All Departments</Button>
              <Button variant="outline" size="sm" className="h-7 text-xs">Today</Button>
            </div>
          </CardHeader>
          <CardContent className="p-5 space-y-4">
            <h4 className="font-bold text-sm text-foreground">Late</h4>
            {data.latest_late_employee ? (
              <div className="text-sm text-muted-foreground">Late employee data</div>
            ) : (
              <p className="text-sm text-muted-foreground mb-2">No late employee today.</p>
            )}
            <Button variant="secondary" className="w-full font-semibold">
              View All Attendance
            </Button>
          </CardContent>
          <div className="absolute top-1/2 right-0 transform -translate-y-1/2 bg-destructive p-1 text-destructive-foreground cursor-pointer rounded-l-sm z-10 hover:bg-destructive/90 transition-colors">
            <Settings className="h-4 w-4" />
          </div>
        </Card>
      </div>

      {/* Row 4 */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card className="shadow-sm">
          <CardHeader className="p-5 pb-2 flex flex-row items-center justify-between border-b">
            <CardTitle className="text-base font-bold text-foreground">Tasks Statistics</CardTitle>
            <Button variant="outline" size="sm" className="h-7 text-xs">This Week</Button>
          </CardHeader>
          <CardContent className="p-5">
            <SemiCircleProgress value={data.task_stats?.failed_task || 0} total={data.task_stats?.total || 100} colorClass="text-secondary" label="Failed Tasks" />

            <div className="grid grid-cols-4 gap-2 text-center text-xs mt-6 mb-6 text-muted-foreground">
              <div>
                <div className="flex items-center justify-center gap-1"><div className="w-2 h-2 rounded-full bg-secondary"></div> Ongoing</div>
                <div className="font-bold mt-1 text-foreground">{Math.round(((data.task_stats?.ongoing || 0) / (data.task_stats?.total || 1)) * 100)}%</div>
              </div>
              <div>
                <div className="flex items-center justify-center gap-1"><div className="w-2 h-2 rounded-full bg-primary"></div> On Hold</div>
                <div className="font-bold mt-1 text-foreground">{Math.round(((data.task_stats?.on_hold || 0) / (data.task_stats?.total || 1)) * 100)}%</div>
              </div>
              <div>
                <div className="flex items-center justify-center gap-1"><div className="w-2 h-2 rounded-full bg-destructive"></div> Overdue</div>
                <div className="font-bold mt-1 text-foreground">{Math.round(((data.task_stats?.overdue || 0) / (data.task_stats?.total || 1)) * 100)}%</div>
              </div>
              <div>
                <div className="flex items-center justify-center gap-1"><div className="w-2 h-2 rounded-full bg-green-500"></div> Completed</div>
                <div className="font-bold mt-1 text-foreground">{Math.round(((data.task_stats?.completed || 0) / (data.task_stats?.total || 1)) * 100)}%</div>
              </div>
              <div className="col-span-4 mt-2">
                <div className="flex items-center justify-center gap-1"><div className="w-2 h-2 rounded-full bg-secondary"></div> Late</div>
                <div className="font-bold mt-1 text-foreground">{Math.round(((data.task_stats?.late_task || 0) / (data.task_stats?.total || 1)) * 100)}%</div>
              </div>
            </div>

            <div className="bg-primary/10 rounded-md p-4 flex justify-between items-center border border-primary/20">
              <div>
                <div className="text-primary font-bold text-lg">389/689 hrs</div>
                <div className="text-xs text-muted-foreground">Spent on Overall Tasks <span className="text-muted-foreground/50 mx-1">___</span> Week</div>
              </div>
              <Button size="sm" variant="outline" className="bg-background">View All</Button>
            </div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardHeader className="p-5 pb-2 flex flex-row items-center justify-between border-b">
            <CardTitle className="text-base font-bold text-foreground">Recent Activities</CardTitle>
            <Button variant="secondary" size="sm" className="h-7 text-xs">View All</Button>
          </CardHeader>
          <CardContent className="p-5 flex items-center justify-center h-[300px]">
            {data.recent_activities?.length > 0 ? (
              <ul className="w-full space-y-4">
                {data.recent_activities.map((act: any) => (
                  <li key={act.id} className="text-sm border-b pb-2 text-foreground">{act.subject} - <span className="text-muted-foreground">{act.time}</span></li>
                ))}
              </ul>
            ) : (
              <p className="text-sm text-muted-foreground">No recent activities</p>
            )}
          </CardContent>
        </Card>

        <Card className="shadow-sm relative overflow-hidden">
          <CardHeader className="p-5 pb-2 flex flex-row items-center justify-between border-b">
            <CardTitle className="text-base font-bold text-foreground">Birthdays</CardTitle>
            <Button variant="secondary" size="sm" className="h-7 text-xs">View All</Button>
          </CardHeader>
          <CardContent className="p-5 flex items-center justify-center h-[300px]">
            {data.upcomingBirthdays?.length > 0 ? (
              <ul className="w-full space-y-4">
                {data.upcomingBirthdays.map((b: any, i: number) => (
                  <li key={i} className="text-sm border-b pb-2 text-foreground">{b.name}</li>
                ))}
              </ul>
            ) : (
              <p className="text-sm text-muted-foreground">No birthdays found</p>
            )}
          </CardContent>
          <div className="absolute top-10 right-0 bg-destructive p-1 text-destructive-foreground cursor-pointer rounded-l-sm z-10 hover:bg-destructive/90 transition-colors">
            <Settings className="h-4 w-4" />
          </div>
        </Card>
      </div>

      {/* Row 5 */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card className="shadow-sm">
          <CardHeader className="p-5 pb-2 flex flex-row items-center justify-between border-b">
            <CardTitle className="text-base font-bold text-foreground">Employees</CardTitle>
            <Button variant="secondary" size="sm" className="h-7 text-xs">View All</Button>
          </CardHeader>
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead className="border-b bg-muted/50 text-muted-foreground">
                <tr>
                  <th className="px-5 py-3 font-bold">Name</th>
                  <th className="px-5 py-3 font-bold">Department</th>
                  <th className="px-5 py-3 font-bold">Date Time</th>
                </tr>
              </thead>
              <tbody>
                {data.all_employess?.slice(0, 5).map((emp: any, i: number) => (
                  <tr key={emp.id || i} className="border-b hover:bg-muted/30 transition-colors">
                    <td className="px-5 py-3">
                      <div className="flex items-center gap-3">
                        <div className="relative">
                          <div className={`h-10 w-10 rounded-full text-white flex items-center justify-center bg-primary`}>
                            {emp.name?.charAt(0) || 'E'}
                          </div>
                          <div className="absolute top-0 -left-1 w-2.5 h-2.5 bg-green-500 border-2 border-background rounded-full"></div>
                        </div>
                        <div>
                          <div className="font-medium text-foreground">{emp.name || 'Employee Name'}</div>
                          <div className="text-xs text-muted-foreground">{emp.department || 'N/A'}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-5 py-3 text-muted-foreground">
                      {emp.department || 'N/A'}
                    </td>
                    <td className="px-5 py-3 text-muted-foreground">
                      <div>{emp.date || 'N/A'}</div>
                      <div className="text-xs">{emp.time || ''}</div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </Card>

        <Card className="shadow-sm relative overflow-hidden">
          <CardHeader className="p-5 pb-2 flex flex-row items-center justify-between border-b">
            <CardTitle className="text-base font-bold text-foreground">Pending Attendance Corrections</CardTitle>
            <Button variant="secondary" size="sm" className="h-7 text-xs">View All</Button>
          </CardHeader>
          <CardContent className="p-5 flex items-center justify-center h-[200px]">
            {data.pending_corrections_list?.length > 0 ? (
              <ul className="w-full space-y-4">
                {data.pending_corrections_list.map((c: any) => (
                  <li key={c.id} className="text-sm border-b pb-2 text-foreground">{c.user_name} - <span className="text-muted-foreground">{c.date}</span></li>
                ))}
              </ul>
            ) : (
              <p className="text-sm text-muted-foreground">No pending corrections found.</p>
            )}
          </CardContent>
          <div className="absolute top-10 right-0 bg-destructive p-1 text-destructive-foreground cursor-pointer rounded-l-sm z-10 hover:bg-destructive/90 transition-colors">
            <Settings className="h-4 w-4" />
          </div>
        </Card>
      </div>

    </div>
  )
}

export default AdminDashboard
