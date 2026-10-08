'use client'

import React, { useState } from 'react'
import { Button } from "@/components/ui/button"
import { ChevronDown, ChevronUp, Edit, MapPin, Trash2 } from "lucide-react"
import { toast } from 'sonner'
import { apiClient } from '@/lib/api-client'
import { useParams } from 'next/navigation'
import { EmployeeData } from './types'
import { EditEducationModal, EditExperienceModal, EditFamilyModal, EditBankModal, EditBasicInfoModal, AssignShiftModal, DeleteConfirmModal } from './EmployeeForms'

interface EmployeeAccordionsProps {
  employee: EmployeeData;
  onRefresh: () => void;
}

function SimpleAccordion({ title, children, onEdit, defaultOpen = true }: { title: string, children: React.ReactNode, onEdit?: () => void, defaultOpen?: boolean }) {
  const [isOpen, setIsOpen] = useState(defaultOpen)
  
  return (
    <div className="border rounded-lg bg-card mb-4 overflow-hidden shadow-sm">
      <div 
        className="px-4 py-3 cursor-pointer flex justify-between items-center hover:bg-slate-50 transition-colors"
        onClick={() => setIsOpen(!isOpen)}
      >
        <h5 className="text-lg font-semibold m-0">{title}</h5>
        <div className="flex items-center gap-2">
          {onEdit && (
            <Button variant="ghost" size="icon" className="h-8 w-8" onClick={(e) => { e.stopPropagation(); onEdit(); }}>
              <Edit className="w-4 h-4" />
            </Button>
          )}
          {isOpen ? <ChevronUp className="w-5 h-5 text-muted-foreground" /> : <ChevronDown className="w-5 h-5 text-muted-foreground" />}
        </div>
      </div>
      {isOpen && (
        <div className="px-4 pb-4 pt-2 border-t">
          {children}
        </div>
      )}
    </div>
  )
}

export function EmployeeAccordions({ employee, onRefresh }: EmployeeAccordionsProps) {
  const [basicOpen, setBasicOpen] = useState(false)
  const [shiftOpen, setShiftOpen] = useState(false)
  const [bankModalState, setBankModalState] = useState<{isOpen: boolean, data?: any}>({isOpen: false})
  const [familyModalState, setFamilyModalState] = useState<{isOpen: boolean, data?: any}>({isOpen: false})
  const [educationModalState, setEducationModalState] = useState<{isOpen: boolean, data?: any}>({isOpen: false})
  const [experienceModalState, setExperienceModalState] = useState<{isOpen: boolean, data?: any}>({isOpen: false})
  const [deleteModalState, setDeleteModalState] = useState<{isOpen: boolean, type: string, id: string | number | null}>({isOpen: false, type: '', id: null})
  const [isDeleting, setIsDeleting] = useState(false)

  const params = useParams()
  const boxSlug = params?.box_slug as string
  
  const triggerDelete = (type: string, id: string | number) => {
    setDeleteModalState({ isOpen: true, type, id })
  }

  const handleDeleteConfirm = async () => {
    const { type, id } = deleteModalState;
    if (!type || id === null) return;
    
    setIsDeleting(true);
    try {
      let endpoint = '';
      let payload = {};
      
      if (type === 'bank') { endpoint = 'delete-bank'; payload = { bank_id: id }; }
      else if (type === 'family') { endpoint = 'delete-family'; payload = { family_id: id }; }
      else if (type === 'education') { endpoint = 'delete-education'; payload = { education_id: id }; }
      else if (type === 'experience') { endpoint = 'delete-experience'; payload = { experience_id: id }; }
      
      await apiClient.delete(`/${boxSlug}/employees/${endpoint}`, { 
        body: JSON.stringify(payload),
        headers: { 'Content-Type': 'application/json' }
      })
      toast.success('Deleted successfully')
      onRefresh()
      setDeleteModalState({ isOpen: false, type: '', id: null })
    } catch (e: any) {
      toast.error(e.message || 'Failed to delete')
    } finally {
      setIsDeleting(false);
    }
  }

  return (
    <div className="w-full space-y-4">
      
      <div className="border rounded-lg bg-card mb-4 overflow-hidden shadow-sm">
        <div className="px-4 py-3 flex justify-between items-center">
          <h5 className="text-lg font-semibold m-0">About Employee</h5>
          <Button variant="ghost" size="icon" className="h-8 w-8" onClick={() => setBasicOpen(true)}>
            <Edit className="w-4 h-4" />
          </Button>
        </div>
        <div className="px-4 pb-4 pt-2 border-t">
          <p className="text-muted-foreground whitespace-pre-wrap">{employee.about || 'No description provided.'}</p>
        </div>
      </div>

      <div className="border rounded-lg bg-card mb-4 overflow-hidden shadow-sm">
        <div className="px-4 py-3 flex justify-between items-center">
          <h5 className="text-lg font-semibold m-0">Assign Employee Shift</h5>
          <Button variant="ghost" size="icon" className="h-8 w-8" onClick={() => setShiftOpen(true)}>
            <Edit className="w-4 h-4" />
          </Button>
        </div>
        <div className="px-4 pb-4 pt-2 border-t">
          <p className="font-medium">Assigned Shift: <span className="font-normal text-muted-foreground">{employee.shift?.code || 'Not Assigned'}</span></p>
        </div>
      </div>

      <SimpleAccordion title="Bank Information">
        <div className="flex justify-end mb-2">
           <Button size="sm" onClick={() => setBankModalState({isOpen: true, data: null})}>Add Bank</Button>
        </div>
        {employee.bank_details && employee.bank_details.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            {employee.bank_details.map((bank, index) => (
              <div key={index} className="p-4 border rounded-md relative">
                <div className="absolute top-2 right-2 flex gap-1">
                  <Button variant="ghost" size="icon" className="h-6 w-6 text-muted-foreground hover:text-red-500" onClick={() => triggerDelete('bank', bank.id)}><Trash2 className="w-3 h-3" /></Button>
                  <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setBankModalState({isOpen: true, data: bank})}><Edit className="w-3 h-3" /></Button>
                </div>
                <h6 className="font-semibold mb-2">{bank.bank_name}</h6>
                <div className="grid grid-cols-2 gap-2 text-sm">
                  <span className="text-muted-foreground">Account No:</span>
                  <span className="font-medium text-right">{bank.account_number}</span>
                  <span className="text-muted-foreground">IFSC Code:</span>
                  <span className="font-medium text-right">{bank.ifsc_code}</span>
                  <span className="text-muted-foreground">Branch:</span>
                  <span className="font-medium text-right">{bank.branch}</span>
                  <span className="text-muted-foreground">Type:</span>
                  <span className="font-medium text-right capitalize">{bank.bank_type}</span>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <p className="text-muted-foreground">No bank details found.</p>
        )}
      </SimpleAccordion>

      <SimpleAccordion title="Family Information">
        <div className="flex justify-end mb-2">
           <Button size="sm" onClick={() => setFamilyModalState({isOpen: true, data: null})}>Add Family</Button>
        </div>
        {employee.families && employee.families.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            {employee.families.map((family: any, index: number) => (
              <div key={index} className="p-4 border rounded-md text-sm relative">
                <div className="absolute top-2 right-2 flex gap-1">
                  <Button variant="ghost" size="icon" className="h-6 w-6 text-muted-foreground hover:text-red-500" onClick={() => triggerDelete('family', family.id)}><Trash2 className="w-3 h-3" /></Button>
                  <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setFamilyModalState({isOpen: true, data: family})}><Edit className="w-3 h-3" /></Button>
                </div>
                <p className="font-semibold text-base mb-1">{family.name}</p>
                <p className="text-muted-foreground mb-1">{family.relationship}</p>
                <p>Date of Birth: {family.dob}</p>
                <p>Phone: {family.contact}</p>
              </div>
            ))}
          </div>
        ) : (
          <p className="text-muted-foreground">No family information found.</p>
        )}
      </SimpleAccordion>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <SimpleAccordion title="Education Details">
          <div className="flex justify-end mb-2">
             <Button size="sm" onClick={() => setEducationModalState({isOpen: true, data: null})}>Add Education</Button>
          </div>
          <div className="space-y-3">
            {employee.educations && employee.educations.length > 0 ? (
              employee.educations.map((edu, index) => (
                <div key={index} className="border-b pb-3 last:border-0 last:pb-0 relative">
                  <div className="absolute top-0 right-0 flex gap-1">
                    <Button variant="ghost" size="icon" className="h-6 w-6 text-muted-foreground hover:text-red-500" onClick={() => triggerDelete('education', edu.id)}><Trash2 className="w-3 h-3" /></Button>
                    <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setEducationModalState({isOpen: true, data: edu})}><Edit className="w-3 h-3" /></Button>
                  </div>
                  <p className="font-semibold">{edu.institute}</p>
                  <p className="text-sm text-muted-foreground">{edu.degree}</p>
                  <p className="text-xs text-muted-foreground mt-1">{edu.start_year} - {edu.end_year}</p>
                </div>
              ))
            ) : (
              <p className="text-muted-foreground">No education details found.</p>
            )}
          </div>
        </SimpleAccordion>

        <SimpleAccordion title="Experience">
          <div className="flex justify-end mb-2">
             <Button size="sm" onClick={() => setExperienceModalState({isOpen: true, data: null})}>Add Experience</Button>
          </div>
          <div className="space-y-3">
            {employee.experiences && employee.experiences.length > 0 ? (
              employee.experiences.map((exp, index) => (
                <div key={index} className="border-b pb-3 last:border-0 last:pb-0 relative">
                  <div className="absolute top-0 right-0 flex gap-1">
                    <Button variant="ghost" size="icon" className="h-6 w-6 text-muted-foreground hover:text-red-500" onClick={() => handleDelete('experience', exp.id)}><Trash2 className="w-3 h-3" /></Button>
                    <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setExperienceModalState({isOpen: true, data: exp})}><Edit className="w-3 h-3" /></Button>
                  </div>
                  <p className="font-semibold">{exp.company_name}</p>
                  <p className="text-sm text-muted-foreground">{exp.position}</p>
                  <p className="text-xs text-muted-foreground mt-1">
                    {exp.start_date} - {exp.current_working ? 'Present' : exp.end_date}
                  </p>
                </div>
              ))
            ) : (
              <p className="text-muted-foreground">No experience details found.</p>
            )}
          </div>
        </SimpleAccordion>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <SimpleAccordion title="Location Tracking">
          <div className="flex items-center justify-between p-3 border rounded bg-slate-50 mb-4">
            <span className="font-medium text-sm">Enable Tracking</span>
            <input type="checkbox" checked={employee.is_location_tracking} readOnly className="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" />
          </div>
          <div className="w-full h-48 bg-slate-100 rounded flex items-center justify-center border text-muted-foreground flex-col gap-2">
            <MapPin className="w-8 h-8 opacity-50" />
            <span>Map integration pending</span>
          </div>
        </SimpleAccordion>

        <SimpleAccordion title="Biometric Mapping">
          {employee.machine_mappings && employee.machine_mappings.length > 0 ? (
            <div className="space-y-3 mt-2">
              {employee.machine_mappings.map((mapping: any, index: number) => (
                <div key={index} className="flex justify-between items-center p-3 border rounded-md">
                  <div>
                    <p className="font-medium">{mapping.machine?.machine_name || 'Unknown Machine'}</p>
                    <p className="text-xs text-muted-foreground">SN: {mapping.machine?.serial_no || '-'}</p>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-muted-foreground">No biometric mappings found.</p>
          )}
        </SimpleAccordion>
      </div>

      {familyModalState.isOpen && <EditFamilyModal isOpen={familyModalState.isOpen} onClose={() => setFamilyModalState({isOpen: false})} employee={employee} onSuccess={onRefresh} initialData={familyModalState.data} />}
      {educationModalState.isOpen && <EditEducationModal isOpen={educationModalState.isOpen} onClose={() => setEducationModalState({isOpen: false})} employee={employee} onSuccess={onRefresh} initialData={educationModalState.data} />}
      {experienceModalState.isOpen && <EditExperienceModal isOpen={experienceModalState.isOpen} onClose={() => setExperienceModalState({isOpen: false})} employee={employee} onSuccess={onRefresh} initialData={experienceModalState.data} />}
      {bankModalState.isOpen && <EditBankModal isOpen={bankModalState.isOpen} onClose={() => setBankModalState({isOpen: false})} employee={employee} onSuccess={onRefresh} initialData={bankModalState.data} />}
      {basicOpen && <EditBasicInfoModal isOpen={basicOpen} onClose={() => setBasicOpen(false)} employee={employee} onSuccess={onRefresh} />}
      {shiftOpen && <AssignShiftModal isOpen={shiftOpen} onClose={() => setShiftOpen(false)} employee={employee} onSuccess={onRefresh} />}

      <DeleteConfirmModal 
        isOpen={deleteModalState.isOpen} 
        onClose={() => setDeleteModalState({ isOpen: false, type: '', id: null })}
        onConfirm={handleDeleteConfirm}
        loading={isDeleting}
      />
    </div>
  )
}
