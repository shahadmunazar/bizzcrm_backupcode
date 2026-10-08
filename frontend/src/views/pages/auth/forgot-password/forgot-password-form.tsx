'use client'

import { useState, useEffect } from 'react'
import { toast } from 'sonner'
import { Loader2 } from 'lucide-react'

import { Button } from '@/components/ui/button'
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { apiClient } from '@/lib/api-client'
import { cn } from '@/lib/utils'

const ForgotPasswordForm = () => {
  const [email, setEmail] = useState('')
  const [isLoading, setIsLoading] = useState(false)
  const [isSuccess, setIsSuccess] = useState(false)
  const [countdown, setCountdown] = useState(0)

  useEffect(() => {
    let timer: NodeJS.Timeout
    if (countdown > 0) {
      timer = setInterval(() => {
        setCountdown((prev) => prev - 1)
      }, 1000)
    }
    return () => {
      if (timer) clearInterval(timer)
    }
  }, [countdown])

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!email) {
      toast.error('Please enter your email address')
      return
    }

    setIsLoading(true)
    try {
      const data = await apiClient.post('/forget-password-email', { email })
      if (data?.success) {
        setIsSuccess(true)
        setCountdown(300) // 5 minutes countdown
        toast.success(data.message || 'Password reset link sent to your email.')
      }
    } catch (error: any) {
      toast.error(error.message || 'Failed to send reset link')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      <FieldGroup className='gap-4'>
        {isSuccess && (
          <p className="text-green-600 font-medium text-sm">
            If your email exists in our system, you will receive a password reset link shortly.
          </p>
        )}

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
            disabled={isLoading || countdown > 0}
            className="bg-slate-50"
          />
        </Field>
        
        <Field>
          <Button 
            className={cn(
              'w-full',
              countdown > 0 ? 'bg-green-400 hover:bg-green-500 text-white opacity-100 disabled:opacity-100' : ''
            )} 
            type='submit' 
            disabled={isLoading || countdown > 0}
          >
            {isLoading ? (
              <Loader2 className="animate-spin" />
            ) : countdown > 0 ? (
              `Please wait to Resend Email (${countdown}s)`
            ) : (
              'Send Reset Link'
            )}
          </Button>
        </Field>
      </FieldGroup>
    </form>
  )
}

export default ForgotPasswordForm
