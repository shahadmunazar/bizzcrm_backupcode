'use client'

// React Import
import { useState, useEffect } from 'react'

// Next Imports
import Link from 'next/link'
import { useRouter } from 'next/navigation'
import { toast } from 'sonner'

// Third-party Imports
import { ChevronsUpDown, Check } from 'lucide-react'

// Component Import
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field'
import { Input } from '@/components/ui/input'

import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover'
import { Command, CommandInput, CommandList, CommandEmpty, CommandGroup, CommandItem } from '@/components/ui/command'
import { cn } from '@/lib/utils'
import { apiClient } from '@/lib/api-client'

type Option = { id: number | string; name: string }

function SearchableSelect({ 
  options, 
  value, 
  onChange, 
  placeholder,
  disabled
}: { 
  options: Option[]; 
  value: string; 
  onChange: (val: string) => void;
  placeholder: string;
  disabled?: boolean;
}) {
  const [open, setOpen] = useState(false)
  const selectedOption = options.find(opt => opt.id.toString() === value)

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger render={
        <Button
          variant="outline"
          role="combobox"
          aria-expanded={open}
          disabled={disabled}
          className="w-full justify-between font-normal shadow-none bg-input/10"
        />
      }>
        <span className="truncate">{selectedOption ? selectedOption.name : placeholder}</span>
        <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
      </PopoverTrigger>
      <PopoverContent className="w-[--radix-popover-trigger-width] p-0" align="start">
        <Command>
          <CommandInput placeholder={`Search...`} />
          <CommandList>
            <CommandEmpty>No results found.</CommandEmpty>
            <CommandGroup>
              {options.map((option) => (
                <CommandItem
                  key={option.id}
                  value={option.name}
                  onSelect={() => {
                    onChange(option.id.toString())
                    setOpen(false)
                  }}
                >
                  <Check
                    className={cn(
                      "mr-2 h-4 w-4",
                      value === option.id.toString() ? "opacity-100" : "opacity-0"
                    )}
                  />
                  {option.name}
                </CommandItem>
              ))}
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  )
}

const RegisterForm = () => {
  const router = useRouter()
  
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    company_name: '',
    industry: '',
    country_id: '',
    state_id: '',
    city_id: '',
    pincode: '',
    address: ''
  })

  // API Data States
  const [industries, setIndustries] = useState<Option[]>([])
  const [countries, setCountries] = useState<Option[]>([])
  const [states, setStates] = useState<Option[]>([])
  const [cities, setCities] = useState<Option[]>([])

  // Initial Fetches
  useEffect(() => {
    apiClient.get('/industries')
      .then(data => { if (data?.success) setIndustries(data.data) })
      .catch(err => console.error('Error fetching industries:', err))

    apiClient.get('/countries')
      .then(data => { if (data?.success) setCountries(data.data) })
      .catch(err => console.error('Error fetching countries:', err))
  }, [])

  // Cascade states when country changes
  useEffect(() => {
    if (formData.country_id) {
      apiClient.get(`/countries/${formData.country_id}/states`)
        .then(data => { if (data?.success) setStates(data.data) })
        .catch(err => console.error('Error fetching states:', err))
    } else {
      setStates([])
      setCities([])
    }
  }, [formData.country_id])

  // Cascade cities when state changes
  useEffect(() => {
    if (formData.state_id) {
      apiClient.get(`/states/${formData.state_id}/cities`)
        .then(data => { if (data?.success) setCities(data.data) })
        .catch(err => console.error('Error fetching cities:', err))
    } else {
      setCities([])
    }
  }, [formData.state_id])

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { id, value } = e.target
    setFormData(prev => ({ ...prev, [id]: value }))
  }

  const handleSelectChange = (id: string, value: string) => {
    setFormData(prev => ({ ...prev, [id]: value }))
    // Reset cascaded fields when parent changes
    if (id === 'country_id') {
      setFormData(prev => ({ ...prev, state_id: '', city_id: '' }))
    } else if (id === 'state_id') {
      setFormData(prev => ({ ...prev, city_id: '' }))
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    // Basic validation for select boxes
    if (!formData.industry || !formData.country_id || !formData.state_id || !formData.city_id) {
      setError('Please fill in all dropdown fields')
      return
    }

    setIsLoading(true)
    setError(null)

    try {
      const response = await apiClient.post('/register', {
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        company_name: formData.company_name,
        industry: formData.industry,
        country_id: formData.country_id,
        state_id: formData.state_id,
        city_id: formData.city_id,
        pincode: formData.pincode,
        address: formData.address
      })

      toast.success('Registration successful! Please check your email to verify your account.')
      router.push('/pages/auth/login')
      
      
    } catch (err: any) {
      setError(err.message)
      toast.error(err.message)
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      <FieldGroup className='gap-4'>
        {error && (
          <div className="bg-red-100 text-red-700 p-3 rounded-md text-sm">
            {error}
          </div>
        )}
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Name */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='name'>Name*</FieldLabel>
            <Input type='text' id='name' placeholder='Enter your name' value={formData.name} onChange={handleChange} required />
          </Field>
          
          {/* Email */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='email'>Email address*</FieldLabel>
            <Input type='email' id='email' placeholder='Enter your email address' value={formData.email} onChange={handleChange} required />
          </Field>

          {/* Phone */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='phone'>Phone Number*</FieldLabel>
            <Input type='text' id='phone' placeholder='Enter your phone' value={formData.phone} onChange={handleChange} required />
          </Field>

          {/* Company Name */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='company_name'>Company Name*</FieldLabel>
            <Input type='text' id='company_name' placeholder='Enter company name' value={formData.company_name} onChange={handleChange} required />
          </Field>

          {/* Industry */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='industry'>Industry*</FieldLabel>
            <SearchableSelect 
              options={industries} 
              value={formData.industry} 
              onChange={(val) => handleSelectChange('industry', val)} 
              placeholder="Select Industry..."
            />
          </Field>

          {/* Country ID */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='country_id'>Country*</FieldLabel>
            <SearchableSelect 
              options={countries} 
              value={formData.country_id} 
              onChange={(val) => handleSelectChange('country_id', val)} 
              placeholder="Select Country..."
            />
          </Field>

          {/* State ID */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='state_id'>State/Province*</FieldLabel>
            <SearchableSelect 
              options={states} 
              value={formData.state_id} 
              onChange={(val) => handleSelectChange('state_id', val)} 
              placeholder="Select State..."
              disabled={!formData.country_id}
            />
          </Field>

          {/* City ID */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='city_id'>City*</FieldLabel>
            <SearchableSelect 
              options={cities} 
              value={formData.city_id} 
              onChange={(val) => handleSelectChange('city_id', val)} 
              placeholder="Select City..."
              disabled={!formData.state_id}
            />
          </Field>

          {/* Pincode */}
          <Field className='gap-2'>
            <FieldLabel className='leading-5' htmlFor='pincode'>Pincode/Zipcode*</FieldLabel>
            <Input type='text' id='pincode' placeholder='Enter pincode' value={formData.pincode} onChange={handleChange} required />
          </Field>

          {/* Address */}
          <Field className='gap-2 md:col-span-2'>
            <FieldLabel className='leading-5' htmlFor='address'>Address*</FieldLabel>
            <Input type='text' id='address' placeholder='Enter full address' value={formData.address} onChange={handleChange} required />
          </Field>
        </div>

        {/* Privacy policy */}
        <Field orientation='horizontal' className='flex items-center gap-2'>
          <Checkbox id='privacyPolicy' required />
          <FieldLabel htmlFor='privacyPolicy'>
            <span className='text-muted-foreground'>I agree to</span> <Link href='#'>privacy policy & terms</Link>
          </FieldLabel>
        </Field>
        
        <Field>
          <Button className='w-full' type='submit' disabled={isLoading}>
            {isLoading ? 'Signing Up...' : 'Sign Up to Shadcn Studio'}
          </Button>
        </Field>
      </FieldGroup>
    </form>
  )
}

export default RegisterForm
