'use client'

import { useState, useEffect } from 'react'
import { useRouter, useSearchParams } from 'next/navigation'
import { toast } from 'sonner'
import { EyeIcon, EyeOffIcon, Loader2 } from 'lucide-react'

import { Button } from '@/components/ui/button'
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group'
import { apiClient } from '@/lib/api-client'

const ResetPasswordForm = () => {
  const router = useRouter()
  const searchParams = useSearchParams()
  const token = searchParams?.get('token') || ''
  
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  
  const [isPasswordVisible, setIsPasswordVisible] = useState(false)
  const [isConfirmPasswordVisible, setIsConfirmPasswordVisible] = useState(false)
  const [isLoading, setIsLoading] = useState(false)

  useEffect(() => {
    // If there is no token in URL, warn the user
    if (!token) {
      toast.error('Invalid password reset link. No token provided.')
    }
    const emailParam = searchParams?.get('email')
    if (emailParam) setEmail(emailParam)
  }, [token, searchParams])

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    
    if (!token) {
      toast.error('Missing reset token. Please use the link from your email.')
      return
    }

    if (password !== passwordConfirmation) {
      toast.error('Passwords do not match.')
      return
    }

    setIsLoading(true)
    try {
      const data = await apiClient.post('/reset-password-submit', {
        token,
        email, // API needs email optionally or required
        password,
        password_confirmation: passwordConfirmation,
      })

      if (data?.success) {
        toast.success(data.message || 'Password has been successfully reset.')
        router.push('/pages/auth/login')
      }
    } catch (error: any) {
      toast.error(error.message || 'Failed to reset password.')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      <FieldGroup className='gap-4'>
        {/* Email */}
        <Field>
          <FieldLabel className='leading-5' htmlFor='userEmail'>
            Email Address*
          </FieldLabel>
          <Input 
            type='email' 
            id='userEmail' 
            placeholder='Enter your email address' 
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            disabled={isLoading || !!searchParams?.get('email')}
          />
        </Field>

        {/* Password */}
        <Field>
          <FieldLabel className='leading-5' htmlFor='password'>
            New Password*
          </FieldLabel>
          <InputGroup>
            <InputGroupInput
              id='password'
              type={isPasswordVisible ? 'text' : 'password'}
              placeholder='••••••••••••••••'
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              disabled={isLoading}
            />
            <InputGroupAddon align='inline-end' className='pr-1.5'>
              <Button
                type='button'
                variant='ghost'
                size='icon'
                onClick={() => setIsPasswordVisible(prevState => !prevState)}
                className='text-muted-foreground rounded-l-none hover:bg-transparent'
              >
                {isPasswordVisible ? <EyeOffIcon /> : <EyeIcon />}
                <span className='sr-only'>{isPasswordVisible ? 'Hide password' : 'Show password'}</span>
              </Button>
            </InputGroupAddon>
          </InputGroup>
        </Field>

        {/* Confirm Password */}
        <Field>
          <FieldLabel className='leading-5' htmlFor='confirmPassword'>
            Confirm Password*
          </FieldLabel>
          <InputGroup>
            <InputGroupInput
              id='confirmPassword'
              type={isConfirmPasswordVisible ? 'text' : 'password'}
              placeholder='••••••••••••••••'
              value={passwordConfirmation}
              onChange={(e) => setPasswordConfirmation(e.target.value)}
              required
              disabled={isLoading}
            />
            <InputGroupAddon align='inline-end' className='pr-1.5'>
              <Button
                type='button'
                variant='ghost'
                size='icon'
                onClick={() => setIsConfirmPasswordVisible(prevState => !prevState)}
                className='text-muted-foreground rounded-l-none hover:bg-transparent'
              >
                {isConfirmPasswordVisible ? <EyeOffIcon /> : <EyeIcon />}
                <span className='sr-only'>{isConfirmPasswordVisible ? 'Hide password' : 'Show password'}</span>
              </Button>
            </InputGroupAddon>
          </InputGroup>
        </Field>

        <Field>
          <Button className='w-full' type='submit' disabled={isLoading}>
            {isLoading ? <Loader2 className="animate-spin" /> : 'Set New Password'}
          </Button>
        </Field>
      </FieldGroup>
    </form>
  )
}

export default ResetPasswordForm
