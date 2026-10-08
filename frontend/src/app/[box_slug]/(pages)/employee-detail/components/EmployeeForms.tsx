'use client'

import React, { useState } from 'react'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { toast } from 'sonner'
import { apiClient } from '@/lib/api-client'
import { EmployeeData } from './types'
import { useParams } from 'next/navigation'

interface ModalProps {
  isOpen: boolean;
  onClose: () => void;
  employee: EmployeeData;
  onSuccess: () => void;
  initialData?: any;
}

export function EditBasicInfoModal({ isOpen, onClose, employee, onSuccess }: ModalProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string
  const [loading, setLoading] = useState(false)
  const [formData, setFormData] = useState({
    first_name: employee.first_name || '',
    last_name: employee.last_name || '',
    employee_code: employee.employee_id || '',
    email: employee.email || '',
    username: employee.username || '',
    phone: employee.phone || '',
    gender: employee.gender || '',
    birthday: employee.birthday || '',
    joining_date: employee.joining_date || '',
    address: employee.address || '',
    resignation_date: employee.resignation_date || '',
    esi_no: employee.esi_no || '',
    pf_no: employee.pf_no || '',
    about: employee.about || '',
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { ...formData, id: employee.id }
      await apiClient.put(`/${boxSlug}/employees/${employee.id}`, payload)
      toast.success('Basic info updated successfully')
      onSuccess()
      onClose()
    } catch (err: any) {
      toast.error(err.message || 'Failed to update')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Edit Basic Information</DialogTitle>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div><Label>First Name <span className="text-red-500">*</span></Label><Input value={formData.first_name} onChange={e => setFormData({...formData, first_name: e.target.value})} required /></div>
            <div><Label>Last Name</Label><Input value={formData.last_name} onChange={e => setFormData({...formData, last_name: e.target.value})} /></div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <Label>Gender</Label>
              <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background" value={formData.gender} onChange={e => setFormData({...formData, gender: e.target.value})}>
                  <option value="" disabled>Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
              </select>
            </div>
            <div><Label>Date Of Birth <span className="text-red-500">*</span></Label><Input type="date" value={formData.birthday} onChange={e => setFormData({...formData, birthday: e.target.value})} required /></div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>Employee ID <span className="text-red-500">*</span></Label><Input value={formData.employee_code} onChange={e => setFormData({...formData, employee_code: e.target.value})} required /></div>
            <div><Label>Joining Date <span className="text-red-500">*</span></Label><Input type="date" value={formData.joining_date} onChange={e => setFormData({...formData, joining_date: e.target.value})} required /></div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>Username <span className="text-red-500">*</span></Label><Input value={formData.username} onChange={e => setFormData({...formData, username: e.target.value})} required /></div>
            <div><Label>Email <span className="text-red-500">*</span></Label><Input type="email" value={formData.email} onChange={e => setFormData({...formData, email: e.target.value})} required /></div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>Phone Number <span className="text-red-500">*</span></Label><Input value={formData.phone} onChange={e => setFormData({...formData, phone: e.target.value})} required /></div>
            <div><Label>Date of Resignation</Label><Input type="date" value={formData.resignation_date} onChange={e => setFormData({...formData, resignation_date: e.target.value})} /></div>
          </div>
          <div><Label>Address <span className="text-red-500">*</span></Label><Textarea value={formData.address} onChange={e => setFormData({...formData, address: e.target.value})} required /></div>
          <div><Label>About <span className="text-red-500">*</span></Label><Textarea value={formData.about} onChange={e => setFormData({...formData, about: e.target.value})} required /></div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>PF No <span className="text-red-500">*</span></Label><Input value={formData.pf_no} onChange={e => setFormData({...formData, pf_no: e.target.value})} required /></div>
            <div><Label>ESI No <span className="text-red-500">*</span></Label><Input value={formData.esi_no} onChange={e => setFormData({...formData, esi_no: e.target.value})} required /></div>
          </div>
          <div className="flex justify-end gap-2 pt-4">
            <Button variant="outline" type="button" onClick={onClose}>Cancel</Button>
            <Button type="submit" disabled={loading}>Save</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}

export function EditPersonalInfoModal({ isOpen, onClose, employee, onSuccess }: ModalProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string
  const [loading, setLoading] = useState(false)
  const [formData, setFormData] = useState({
    passport_no: employee.passport_no || '',
    passport_exp_date: employee.passport_exp_date || '',
    nationality: employee.nationality || '',
    religion: employee.religion || '',
    marital_status: employee.marital_status || '',
    marriage_date: employee.marriage_date || '',
    no_of_children: employee.no_of_children || '',
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { ...formData, id: employee.id }
      await apiClient.put(`/${boxSlug}/employees/${employee.id}`, payload)
      toast.success('Personal info updated successfully')
      onSuccess()
      onClose()
    } catch (err: any) {
      toast.error(err.message || 'Failed to update')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-md max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Edit Personal Information</DialogTitle>
        </DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div><Label>Passport No</Label><Input value={formData.passport_no} onChange={e => setFormData({...formData, passport_no: e.target.value})} /></div>
            <div><Label>Passport Expiry Date</Label><Input type="date" value={formData.passport_exp_date} onChange={e => setFormData({...formData, passport_exp_date: e.target.value})} /></div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>Nationality <span className="text-red-500">*</span></Label><Input value={formData.nationality} onChange={e => setFormData({...formData, nationality: e.target.value})} required /></div>
            <div><Label>Religion</Label><Input value={formData.religion} onChange={e => setFormData({...formData, religion: e.target.value})} /></div>
          </div>
          <div>
            <Label>Marital status <span className="text-red-500">*</span></Label>
            <select 
              className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background" 
              value={formData.marital_status} 
              onChange={e => setFormData({...formData, marital_status: e.target.value})}
            >
              <option value="">Select</option>
              <option value="single">Single</option>
              <option value="married">Married</option>
              <option value="divorced">Divorced</option>
            </select>
          </div>
          <div className="grid grid-cols-2 gap-4">
            {formData.marital_status === 'married' && (
              <div>
                <Label>Anniversary Date <span className="text-red-500">*</span></Label>
                <Input type="date" value={formData.marriage_date} onChange={e => setFormData({...formData, marriage_date: e.target.value})} />
              </div>
            )}
            <div>
              <Label>No. of Children</Label>
              <Input type="number" value={formData.no_of_children} onChange={e => setFormData({...formData, no_of_children: e.target.value})} />
            </div>
          </div>
          <div className="flex justify-end gap-2 pt-4">
            <Button variant="outline" type="button" onClick={onClose}>Cancel</Button>
            <Button type="submit" disabled={loading}>Save</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}

export function EditFamilyModal({ isOpen, onClose, employee, onSuccess, initialData }: ModalProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string
  const [loading, setLoading] = useState(false)
  
  const [formData, setFormData] = useState({
    id: initialData?.id || '',
    name: initialData?.name || '',
    relationship: initialData?.relationship || '', // Wait backend says relationship but db might have relationship or relation
    dob: initialData?.dob || '',
    contact: initialData?.contact || '' // Wait backend expects contact
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { employee_id: employee.id, ...formData }
      await apiClient.put(`/${boxSlug}/employees/update-family`, payload)
      toast.success('Family info updated')
      onSuccess()
      onClose()
    } catch (err: any) {
      toast.error(err.message || 'Failed to update')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader><DialogTitle>Edit Family Information</DialogTitle></DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div><Label>Name <span className="text-red-500">*</span></Label><Input value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})} required /></div>
          <div>
            <Label>Relationship <span className="text-red-500">*</span></Label>
            <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background" value={formData.relationship} onChange={e => setFormData({...formData, relationship: e.target.value})} required>
                <option value="" disabled>Select Relationship</option>
                <option value="Father">Father</option>
                <option value="Mother">Mother</option>
                <option value="Brother">Brother</option>
                <option value="Sister">Sister</option>
                <option value="Spouse">Spouse</option>
                <option value="Son">Son</option>
                <option value="Daughter">Daughter</option>
                <option value="Uncle">Uncle</option>
                <option value="Aunt">Aunt</option>
                <option value="Cousin">Cousin</option>
                <option value="Grandfather">Grandfather</option>
                <option value="Grandmother">Grandmother</option>
                <option value="Nephew">Nephew</option>
                <option value="Niece">Niece</option>
                <option value="Friend">Friend</option>
                <option value="Other">Other</option>
            </select>
          </div>
          <div><Label>Date of Birth</Label><Input type="date" value={formData.dob} onChange={e => setFormData({...formData, dob: e.target.value})} /></div>
          <div><Label>Contact</Label><Input value={formData.contact} onChange={e => setFormData({...formData, contact: e.target.value})} /></div>
          <div className="flex justify-end gap-2">
            <Button variant="outline" type="button" onClick={onClose}>Cancel</Button>
            <Button type="submit" disabled={loading}>Save</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}

export function EditEmergencyModal({ isOpen, onClose, employee, onSuccess }: ModalProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string
  const [loading, setLoading] = useState(false)
  
  const primaryContact = employee.emergency_contacts?.find(c => c.type === 'primary') || employee.emergency_contacts?.[0]
  const secondaryContact = employee.emergency_contacts?.find(c => c.type === 'secondary') || employee.emergency_contacts?.[1]

  const [formData, setFormData] = useState({
    contacts: [
      {
        id: primaryContact?.id || '',
        type: 'primary',
        name: primaryContact?.name || '',
        relation: primaryContact?.relation || '',
        phone: primaryContact?.phone || '',
        phone_alt: primaryContact?.phone_alt || ''
      },
      {
        id: secondaryContact?.id || '',
        type: 'secondary',
        name: secondaryContact?.name || '',
        relation: secondaryContact?.relation || '',
        phone: secondaryContact?.phone || '',
        phone_alt: secondaryContact?.phone_alt || ''
      }
    ]
  })

  const handleContactChange = (index: number, field: string, value: string) => {
    const newContacts = [...formData.contacts]
    newContacts[index] = { ...newContacts[index], [field]: value }
    setFormData({ contacts: newContacts })
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { employee_id: employee.id, contacts: formData.contacts }
      await apiClient.put(`/${boxSlug}/employees/update-emergency`, payload)
      toast.success('Emergency contacts updated')
      onSuccess()
      onClose()
    } catch (err: any) {
      toast.error(err.message || 'Failed to update')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
        <DialogHeader><DialogTitle>Edit Emergency Contacts</DialogTitle></DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-6">
          
          <div className="space-y-4 border-b pb-4">
            <h5 className="font-semibold text-lg">Primary Contact Details</h5>
            <div className="grid grid-cols-2 gap-4">
              <div><Label>Name <span className="text-red-500">*</span></Label><Input value={formData.contacts[0].name} onChange={e => handleContactChange(0, 'name', e.target.value)} required /></div>
              <div><Label>Relationship</Label><Input value={formData.contacts[0].relation} onChange={e => handleContactChange(0, 'relation', e.target.value)} /></div>
              <div><Label>Phone No 1 <span className="text-red-500">*</span></Label><Input value={formData.contacts[0].phone} onChange={e => handleContactChange(0, 'phone', e.target.value)} required /></div>
              <div><Label>Phone No 2</Label><Input value={formData.contacts[0].phone_alt} onChange={e => handleContactChange(0, 'phone_alt', e.target.value)} /></div>
            </div>
          </div>

          <div className="space-y-4">
            <h5 className="font-semibold text-lg">Secondary Contact Details</h5>
            <div className="grid grid-cols-2 gap-4">
              <div><Label>Name <span className="text-red-500">*</span></Label><Input value={formData.contacts[1].name} onChange={e => handleContactChange(1, 'name', e.target.value)} /></div>
              <div><Label>Relationship</Label><Input value={formData.contacts[1].relation} onChange={e => handleContactChange(1, 'relation', e.target.value)} /></div>
              <div><Label>Phone No 1 <span className="text-red-500">*</span></Label><Input value={formData.contacts[1].phone} onChange={e => handleContactChange(1, 'phone', e.target.value)} /></div>
              <div><Label>Phone No 2</Label><Input value={formData.contacts[1].phone_alt} onChange={e => handleContactChange(1, 'phone_alt', e.target.value)} /></div>
            </div>
          </div>

          <div className="flex justify-end gap-2 pt-2">
            <Button variant="outline" type="button" onClick={onClose}>Cancel</Button>
            <Button type="submit" disabled={loading}>Save</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}

export function EditEducationModal({ isOpen, onClose, employee, onSuccess, initialData }: ModalProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string
  const [loading, setLoading] = useState(false)
  
  const [formData, setFormData] = useState({
    id: initialData?.id || '',
    institute: initialData?.institute || '',
    degree: initialData?.degree || '',
    start_year: initialData?.start_year || '',
    end_year: initialData?.end_year || ''
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { employee_id: employee.id, ...formData }
      await apiClient.put(`/${boxSlug}/employees/update-education`, payload)
      toast.success('Education updated')
      onSuccess()
      onClose()
    } catch (err: any) {
      toast.error(err.message || 'Failed to update')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader><DialogTitle>Edit Education</DialogTitle></DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div><Label>Institution Name <span className="text-red-500">*</span></Label><Input value={formData.institute} onChange={e => setFormData({...formData, institute: e.target.value})} required /></div>
          <div><Label>Course <span className="text-red-500">*</span></Label><Input value={formData.degree} onChange={e => setFormData({...formData, degree: e.target.value})} required /></div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>Start Date <span className="text-red-500">*</span></Label><Input type="date" value={formData.start_year} onChange={e => setFormData({...formData, start_year: e.target.value})} required /></div>
            <div><Label>End Date <span className="text-red-500">*</span></Label><Input type="date" value={formData.end_year} onChange={e => setFormData({...formData, end_year: e.target.value})} required /></div>
          </div>
          <div className="flex justify-end gap-2">
            <Button variant="outline" type="button" onClick={onClose}>Cancel</Button>
            <Button type="submit" disabled={loading}>Save</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}

export function EditExperienceModal({ isOpen, onClose, employee, onSuccess, initialData }: ModalProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string
  const [loading, setLoading] = useState(false)
  
  const [formData, setFormData] = useState({
    id: initialData?.id || '',
    company_name: initialData?.company_name || '',
    position: initialData?.position || '',
    start_date: initialData?.start_date || '',
    end_date: initialData?.end_date || '',
    current_working: initialData?.current_working || 0
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { employee_id: employee.id, ...formData }
      await apiClient.put(`/${boxSlug}/employees/update-experience`, payload)
      toast.success('Experience updated')
      onSuccess()
      onClose()
    } catch (err: any) {
      toast.error(err.message || 'Failed to update')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader><DialogTitle>Edit Experience</DialogTitle></DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div><Label>Previous Company Name <span className="text-red-500">*</span></Label><Input value={formData.company_name} onChange={e => setFormData({...formData, company_name: e.target.value})} required /></div>
          <div><Label>Designation <span className="text-red-500">*</span></Label><Input value={formData.position} onChange={e => setFormData({...formData, position: e.target.value})} required /></div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>Start Date <span className="text-red-500">*</span></Label><Input type="date" value={formData.start_date} onChange={e => setFormData({...formData, start_date: e.target.value})} required /></div>
            <div><Label>End Date <span className="text-red-500">*</span></Label><Input type="date" value={formData.end_date} onChange={e => setFormData({...formData, end_date: e.target.value})} disabled={!!formData.current_working} /></div>
          </div>
          <div className="flex items-center gap-2 mt-2">
            <input type="checkbox" id="current_working" checked={!!formData.current_working} onChange={e => setFormData({...formData, current_working: e.target.checked ? 1 : 0})} />
            <Label htmlFor="current_working">Check if you working here</Label>
          </div>
          <div className="flex justify-end gap-2">
            <Button variant="outline" type="button" onClick={onClose}>Cancel</Button>
            <Button type="submit" disabled={loading}>Save</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}

export function EditBankModal({ isOpen, onClose, employee, onSuccess, initialData }: ModalProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string
  const [loading, setLoading] = useState(false)
  
  const [formData, setFormData] = useState({
    bank_id: initialData?.id || '',
    bank_name: initialData?.bank_name || '',
    account_number: initialData?.account_number || '',
    ifsc_code: initialData?.ifsc_code || '',
    branch: initialData?.branch || '',
    bank_type: initialData?.bank_type || 'primary'
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { employee_id: employee.id, ...formData }
      await apiClient.put(`/${boxSlug}/employees/update-bank`, payload)
      toast.success('Bank updated')
      onSuccess()
      onClose()
    } catch (err: any) {
      toast.error(err.message || 'Failed to update')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader><DialogTitle>Edit Bank Information</DialogTitle></DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div><Label>Bank Details <span className="text-red-500">*</span></Label><Input value={formData.bank_name} onChange={e => setFormData({...formData, bank_name: e.target.value})} required /></div>
          <div><Label>Bank account No</Label><Input value={formData.account_number} onChange={e => setFormData({...formData, account_number: e.target.value})} required /></div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>IFSC Code</Label><Input value={formData.ifsc_code} onChange={e => setFormData({...formData, ifsc_code: e.target.value})} required /></div>
            <div><Label>Branch Address</Label><Input value={formData.branch} onChange={e => setFormData({...formData, branch: e.target.value})} required /></div>
          </div>
          <div><Label>Bank Type <span className="text-red-500">*</span></Label>
             <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background" value={formData.bank_type} onChange={e => setFormData({...formData, bank_type: e.target.value})}>
                <option value="primary">Primary</option>
                <option value="secondary">Secondary</option>
             </select>
          </div>
          <div className="flex justify-end gap-2">
            <Button variant="outline" type="button" onClick={onClose}>Cancel</Button>
            <Button type="submit" disabled={loading}>Save</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}

export function AssignShiftModal({ isOpen, onClose, employee, onSuccess }: ModalProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string
  const [loading, setLoading] = useState(false)
  const [shifts, setShifts] = useState<{id: string, code: string}[]>([])
  const [selectedShift, setSelectedShift] = useState(employee.shift?.id?.toString() || '')

  React.useEffect(() => {
    if (isOpen && boxSlug && boxSlug !== '[box_slug]') {
      apiClient.get(`/${boxSlug}/employees/assign-shift-list`)
        .then(res => {
          if (res.data?.success && Array.isArray(res.data.data)) {
            setShifts(res.data.data)
          }
        })
        .catch(err => {
          toast.error("Failed to fetch shifts")
        })
    }
  }, [isOpen, boxSlug])

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { employee_id: employee.id, shift_id: selectedShift }
      await apiClient.post(`/${boxSlug}/employees/assign-shift`, payload)
      toast.success('Shift assigned successfully')
      onSuccess()
      onClose()
    } catch (err: any) {
      toast.error(err.message || 'Failed to assign shift')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader><DialogTitle>Assign Shift to Employee</DialogTitle></DialogHeader>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <Label>Select Shift <span className="text-red-500">*</span></Label>
            <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background" value={selectedShift} onChange={e => setSelectedShift(e.target.value)} required>
                <option value="" disabled>-- Select Shift --</option>
                {shifts.map(shift => (
                  <option key={shift.id} value={shift.id}>{shift.code}</option>
                ))}
            </select>
          </div>
          <div className="flex justify-end gap-2">
            <Button variant="outline" type="button" onClick={onClose}>Cancel</Button>
            <Button type="submit" disabled={loading || !selectedShift}>Save</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}

export interface DeleteConfirmModalProps {
  isOpen: boolean;
  onClose: () => void;
  onConfirm: () => void;
  loading?: boolean;
}

export function DeleteConfirmModal({ isOpen, onClose, onConfirm, loading }: DeleteConfirmModalProps) {
  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader><DialogTitle>Confirm Deletion</DialogTitle></DialogHeader>
        <div className="space-y-4">
          <p className="text-muted-foreground">Are you sure you want to delete this item? This action cannot be undone.</p>
          <div className="flex justify-end gap-2">
            <Button variant="outline" type="button" onClick={onClose} disabled={loading}>Cancel</Button>
            <Button variant="destructive" type="button" onClick={onConfirm} disabled={loading}>Delete</Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
