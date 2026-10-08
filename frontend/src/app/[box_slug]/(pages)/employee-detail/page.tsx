'use client'

import React, { useEffect, useState } from 'react'
import { useParams, useSearchParams, useRouter } from 'next/navigation'
import { apiClient } from '@/lib/api-client'
import { EmployeeProfileCard } from './components/EmployeeProfileCard'
import { EmployeeAccordions } from './components/EmployeeAccordions'
import { EmployeeData } from './components/types'
import { Button } from '@/components/ui/button'
import { ArrowLeft, KeySquare, PlusCircle, ChevronUp } from 'lucide-react'
import Link from 'next/link'
import { toast } from 'sonner'

export default function EmployeeDetailPage() {
  const params = useParams()
  const searchParams = useSearchParams()
  const router = useRouter()
  
  const boxSlug = params?.box_slug as string
  const empId = searchParams?.get('emp_id')

  const [employee, setEmployee] = useState<EmployeeData | null>(null)
  const [loading, setLoading] = useState(true)
  const [refreshKey, setRefreshKey] = useState(0)

  useEffect(() => {
    if (!boxSlug || boxSlug === '[box_slug]') return;
    if (!empId) {
      toast.error('Employee ID is missing.')
      setLoading(false)
      return
    }

    const fetchEmployeeDetails = async () => {
      setLoading(true)
      try {
        const url = `/${boxSlug}/employees/details?req_emp_id=${empId}`
        const res = await apiClient.get(url)
        
        if (res && res.data) {
          setEmployee(res.data)
        } else {
          toast.error('Failed to load employee details')
        }
      } catch (error: any) {
        console.error('Error fetching employee details:', error)
        toast.error(error?.message || 'Error fetching employee details')
      } finally {
        setLoading(false)
      }
    }

    fetchEmployeeDetails()
  }, [boxSlug, empId, refreshKey])

  const handleRefresh = () => {
    setRefreshKey(prev => prev + 1)
  }

  if (loading) {
    return (
      <div className="p-8 flex justify-center items-center h-[50vh]">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>
    )
  }

  if (!employee) {
    return (
      <div className="p-8 text-center text-muted-foreground">
        <p>Employee not found or could not be loaded.</p>
        <Button variant="link" onClick={() => router.push(`/${boxSlug}/employee-list`)}>
          Return to Employee List
        </Button>
      </div>
    )
  }

  return (
    <div className="p-4 md:p-6 space-y-6 max-w-7xl mx-auto">
      
      {/* Breadcrumb Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="flex items-center">
          <Link href={`/${boxSlug}/employee-list`} className="text-muted-foreground hover:text-foreground transition-colors mr-3">
            <ArrowLeft className="w-5 h-5" />
          </Link>
          <h1 className="text-xl md:text-2xl font-bold flex items-center gap-2">
            Employee Details
            <span className="text-sm font-normal text-muted-foreground bg-slate-100 px-3 py-1 rounded-full ml-2">
              ID: {employee.employee_id}
            </span>
          </h1>
        </div>

        <div className="flex flex-wrap items-center gap-2">
          <Button variant="outline" className="flex items-center gap-2">
            <PlusCircle className="w-4 h-4" /> Bank & Statutory
          </Button>
          <Button variant="outline" className="flex items-center gap-2">
            <KeySquare className="w-4 h-4" /> Change Password
          </Button>
          <Button variant="ghost" size="icon" className="hidden md:flex text-muted-foreground">
            <ChevronUp className="w-5 h-5" />
          </Button>
        </div>
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-12 gap-6">
        {/* Left Sidebar - Profile Card */}
        <div className="xl:col-span-4">
          <div className="sticky top-6">
            <EmployeeProfileCard employee={employee} onRefresh={handleRefresh} />
          </div>
        </div>

        {/* Right Content - Accordions */}
        <div className="xl:col-span-8">
          <EmployeeAccordions employee={employee} onRefresh={handleRefresh} />
        </div>
      </div>

    </div>
  )
}
