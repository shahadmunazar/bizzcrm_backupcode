'use client'

import React, { useState } from 'react'
import { Card, CardContent } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { EmployeeData } from './types'
import { Briefcase, Calendar, Edit, Heart, Mail, MapPin, Phone, User, Users, Cake, Hash, GraduationCap } from 'lucide-react'
import { EditBasicInfoModal, EditPersonalInfoModal, EditEmergencyModal } from './EmployeeForms'

import { ImageCropper } from '@/components/ui/image-cropper'
import { useParams } from 'next/navigation'
import { apiClient } from '@/lib/api-client'
import { toast } from 'sonner'

interface EmployeeProfileCardProps {
  employee: EmployeeData;
  onRefresh: () => void;
}

export function EmployeeProfileCard({ employee, onRefresh }: EmployeeProfileCardProps) {
  const params = useParams()
  const boxSlug = params?.box_slug as string

  const [basicOpen, setBasicOpen] = useState(false)
  const [personalOpen, setPersonalOpen] = useState(false)
  const [emergencyOpen, setEmergencyOpen] = useState(false)
  
  const [imageSrc, setImageSrc] = useState<string | null>(null)
  const [isCropperOpen, setIsCropperOpen] = useState(false)
  const [isUploading, setIsUploading] = useState(false)
  const fileInputRef = React.useRef<HTMLInputElement>(null)

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      const file = e.target.files[0]
      const reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = () => {
        setImageSrc(reader.result as string)
        setIsCropperOpen(true)
      }
      e.target.value = '' // reset input
    }
  }

  const handleCropComplete = async (croppedImageUrl: string) => {
    setIsCropperOpen(false)
    setIsUploading(true)
    try {
      // Convert blob URL to base64
      const blob = await fetch(croppedImageUrl).then(r => r.blob())
      const reader = new FileReader()
      reader.readAsDataURL(blob)
      reader.onloadend = async () => {
        const base64data = reader.result
        
        // Update employee via API
        const payload = {
          id: employee.id, // ID is required by the backend validation
          cropped_image: base64data
        }
        
        await apiClient.put(`/${boxSlug}/employees/${employee.id}`, payload)
        toast.success('Profile picture updated successfully')
        onRefresh()
      }
    } catch (err: any) {
      toast.error('Failed to update profile picture: ' + err.message)
    } finally {
      setIsUploading(false)
    }
  }

  return (
    <div className="space-y-4">
      {imageSrc && isCropperOpen && (
        <ImageCropper
          imageSrc={imageSrc}
          onCropComplete={handleCropComplete}
          onClose={() => setIsCropperOpen(false)}
        />
      )}
      <input 
        type="file" 
        accept="image/*" 
        ref={fileInputRef} 
        className="hidden" 
        onChange={handleFileChange} 
      />
      <Card>
        <CardContent className="p-0">
          <div className="relative w-full h-48 sm:h-56 bg-gradient-to-r from-blue-50 to-indigo-50 border-b flex justify-center items-center">
            <div 
              className={`relative cursor-pointer group mt-8 ${isUploading ? 'opacity-50 pointer-events-none' : ''}`}
              onClick={() => fileInputRef.current?.click()}
            >
              {employee.profile_image_url ? (
                <img 
                  src={employee.profile_image_url} 
                  alt="Profile" 
                  className="w-32 h-32 rounded-full border-4 border-white object-cover shadow-sm"
                />
              ) : (
                <div className="w-32 h-32 rounded-full border-4 border-white bg-primary text-primary-foreground flex items-center justify-center text-4xl font-bold shadow-sm">
                  {employee.first_name?.[0]}{employee.last_name?.[0]}
                </div>
              )}
              <div className="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <Edit className="w-6 h-6 text-white" />
              </div>
            </div>
          </div>
          
          <div className="text-center px-4 pb-4 pt-10 border-b">
            <h5 className="text-xl font-bold mb-2 flex items-center justify-center gap-2">
              {employee.name}
            </h5>
            <div className="flex justify-center gap-2 mb-4 flex-wrap">
              <Badge variant="secondary" className="bg-blue-50 text-blue-700 hover:bg-blue-100">
                {employee.department_name}
              </Badge>
              <Badge variant="outline">
                {employee.designation_name}
              </Badge>
            </div>
            
            <div className="space-y-3 text-sm text-left px-2">
              <div className="flex justify-between items-center">
                <span className="text-muted-foreground flex items-center gap-2"><Hash className="w-4 h-4"/> Employee ID</span>
                <span className="font-medium text-foreground">{employee.employee_id}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-muted-foreground flex items-center gap-2"><Briefcase className="w-4 h-4"/> Department</span>
                <span className="font-medium text-foreground">{employee.department_name}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-muted-foreground flex items-center gap-2"><GraduationCap className="w-4 h-4"/> Designation</span>
                <span className="font-medium text-foreground">{employee.designation_name}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-muted-foreground flex items-center gap-2"><User className="w-4 h-4"/> Employee Type</span>
                <span className="font-medium text-foreground">{employee.employee_type_name || '-'}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-muted-foreground flex items-center gap-2"><Users className="w-4 h-4"/> Reporting Manager</span>
                <span className="font-medium text-foreground">{employee.reporting_manager?.user?.name || '-'}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-muted-foreground flex items-center gap-2"><Calendar className="w-4 h-4"/> Date Of Join</span>
                <span className="font-medium text-foreground">{employee.joining_date || '-'}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-muted-foreground flex items-center gap-2"><MapPin className="w-4 h-4"/> Report Office</span>
                <span className="font-medium text-foreground">
                  {employee.assigned_offices?.map(o => o.branch?.name).join(', ') || '-'}
                </span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-muted-foreground flex items-center gap-2"><User className="w-4 h-4"/> Working Mode</span>
                <span className="font-medium text-foreground">{employee.work_mode?.name || '-'}</span>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-2 mt-4">
              <Button variant="outline" className="w-full" size="sm" onClick={() => setBasicOpen(true)}>
                <Edit className="w-4 h-4 mr-2" /> Edit Info
              </Button>
              <Button className="w-full" size="sm">
                Message
              </Button>
            </div>
          </div>

          <div className="p-4 border-b space-y-3 text-sm">
            <div className="flex justify-between items-center mb-2">
              <h6 className="font-bold text-base">Basic Information</h6>
              <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setBasicOpen(true)}><Edit className="w-3 h-3" /></Button>
            </div>
            
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground flex items-center gap-2"><Phone className="w-4 h-4"/> Phone</span>
              <span className="font-medium text-foreground">{employee.phone || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground flex items-center gap-2"><Mail className="w-4 h-4"/> Email</span>
              <span className="font-medium text-primary cursor-pointer hover:underline">{employee.email}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground flex items-center gap-2"><User className="w-4 h-4"/> Gender</span>
              <span className="font-medium text-foreground">{employee.gender || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground flex items-center gap-2"><Cake className="w-4 h-4"/> Birthday</span>
              <span className="font-medium text-foreground">{employee.birthday || '-'}</span>
            </div>
            <div className="flex justify-between items-start">
              <span className="text-muted-foreground flex items-center gap-2"><MapPin className="w-4 h-4"/> Address</span>
              <span className="font-medium text-foreground text-right max-w-[150px]">{employee.address || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground flex items-center gap-2"><Calendar className="w-4 h-4"/> Date of Resignation</span>
              <span className="font-medium text-foreground">{employee.resignation_date || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground flex items-center gap-2"><Hash className="w-4 h-4"/> ESI No.</span>
              <span className="font-medium text-foreground">{employee.esi_no || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground flex items-center gap-2"><Hash className="w-4 h-4"/> PF No.</span>
              <span className="font-medium text-foreground">{employee.pf_no || '-'}</span>
            </div>
          </div>

          <div className="p-4 space-y-3 text-sm border-b">
            <div className="flex justify-between items-center mb-2">
              <h6 className="font-bold text-base">Personal Information</h6>
              <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setPersonalOpen(true)}><Edit className="w-3 h-3" /></Button>
            </div>
            
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground">Passport No</span>
              <span className="font-medium text-foreground">{employee.passport_no || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground">Passport Exp Date</span>
              <span className="font-medium text-foreground">{employee.passport_exp_date || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground">Nationality</span>
              <span className="font-medium text-foreground">{employee.nationality || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground">Religion</span>
              <span className="font-medium text-foreground">{employee.religion || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground">Marital status</span>
              <span className="font-medium text-foreground">{employee.marital_status || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground">Anniversary Date</span>
              <span className="font-medium text-foreground">{employee.marriage_date || '-'}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-muted-foreground">No. of children</span>
              <span className="font-medium text-foreground">{employee.no_of_children || '-'}</span>
            </div>
          </div>

        </CardContent>
      </Card>

      <Card>
        <CardContent className="p-4">
          <div className="flex justify-between items-center mb-4">
            <h6 className="font-bold text-base">Emergency Contact Number</h6>
            <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setEmergencyOpen(true)}><Edit className="w-3 h-3" /></Button>
          </div>
          {employee.emergency_contacts && employee.emergency_contacts.length > 0 ? (
            <div className="space-y-4">
              {employee.emergency_contacts.map((contact, index) => (
                <div key={index} className="flex justify-between items-center p-3 border rounded-lg">
                  <div>
                    <p className="font-medium">{contact.name}</p>
                    <p className="text-xs text-muted-foreground">{contact.relation}</p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-medium">{contact.phone}</p>
                    {contact.phone_alt && <p className="text-xs text-muted-foreground">{contact.phone_alt}</p>}
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-sm text-muted-foreground text-center py-4">No emergency contacts available.</p>
          )}
        </CardContent>
      </Card>

      {basicOpen && <EditBasicInfoModal isOpen={basicOpen} onClose={() => setBasicOpen(false)} employee={employee} onSuccess={onRefresh} />}
      {personalOpen && <EditPersonalInfoModal isOpen={personalOpen} onClose={() => setPersonalOpen(false)} employee={employee} onSuccess={onRefresh} />}
      {emergencyOpen && <EditEmergencyModal isOpen={emergencyOpen} onClose={() => setEmergencyOpen(false)} employee={employee} onSuccess={onRefresh} />}
    </div>
  )
}
