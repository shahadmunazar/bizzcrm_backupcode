'use client'

import { useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'
import { EyeIcon, EyeOffIcon, Loader2 } from 'lucide-react'
import { toast } from 'sonner'

import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group'
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'

import { apiClient, ApiError } from '@/lib/api-client'
import { useAuth } from '@/contexts/AuthContext'

const LoginForm = () => {
  const router = useRouter()
  const { login } = useAuth()
  
  const [activeTab, setActiveTab] = useState<'password' | 'otp'>('password')
  
  // Standard Login State
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [isVisible, setIsVisible] = useState(false)
  const [isLoading, setIsLoading] = useState(false)
  
  // OTP Login State
  const [userInput, setUserInput] = useState('')
  const [otp, setOtp] = useState('')
  const [otpSent, setOtpSent] = useState(false)
  const [isOtpLoading, setIsOtpLoading] = useState(false)

  // Modal State
  const [showForceLoginModal, setShowForceLoginModal] = useState(false)
  const [forceLoginMessage, setForceLoginMessage] = useState('')
  const [pendingLoginType, setPendingLoginType] = useState<'standard' | 'otp' | null>(null)

  const handleStandardLogin = async (e?: React.FormEvent, forceLogin = false) => {
    e?.preventDefault()
    setIsLoading(true)
    
    try {
      const data = await apiClient.post('/login', {
        email,
        password,
        ...(forceLogin && { force_login: true })
      })

      if (data?.success && data?.access_token) {
        toast.success(data.message || 'Login successful!')
        login(data.access_token, data.box?.slug)
        router.push(`/${data.box?.slug || 'dashboard'}`)
      }
    } catch (error: any) {
      if (error instanceof ApiError && error.status === 409) {
        setForceLoginMessage(error.message)
        setPendingLoginType('standard')
        setShowForceLoginModal(true)
      } else {
        toast.error(error.message || 'Login failed')
      }
    } finally {
      setIsLoading(false)
    }
  }

  const handleSendOtp = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!userInput) {
      toast.error('Please enter an email or mobile number')
      return
    }
    
    setIsOtpLoading(true)
    try {
      const data = await apiClient.post('/send-otp', { user_input: userInput })
      if (data?.success) {
        setOtpSent(true)
        toast.success(data.message || 'OTP sent successfully')
      }
    } catch (error: any) {
      toast.error(error.message || 'Failed to send OTP')
    } finally {
      setIsOtpLoading(false)
    }
  }

  const handleVerifyOtp = async (e?: React.FormEvent, forceLogin = false) => {
    e?.preventDefault()
    if (!otp) {
      toast.error('Please enter the OTP')
      return
    }

    setIsOtpLoading(true)
    try {
      const data = await apiClient.post('/verify-otp', {
        user_input: userInput,
        otp,
        ...(forceLogin && { force_login: true })
      })

      if (data?.success && data?.access_token) {
        toast.success(data.message || 'Login successful!')
        login(data.access_token, data.box?.slug)
        router.push(`/${data.box?.slug || 'dashboard'}`)
      }
    } catch (error: any) {
      if (error instanceof ApiError && error.status === 409) {
        setForceLoginMessage(error.message)
        setPendingLoginType('otp')
        setShowForceLoginModal(true)
      } else {
        toast.error(error.message || 'Failed to verify OTP')
      }
    } finally {
      setIsOtpLoading(false)
    }
  }

  return (
    <>
      <Tabs value={activeTab} onValueChange={(v) => setActiveTab(v as any)} className="w-full">
        <TabsList className="grid w-full grid-cols-2 mb-6">
          <TabsTrigger value="password">Password</TabsTrigger>
          <TabsTrigger value="otp">OTP</TabsTrigger>
        </TabsList>

        <TabsContent value="password">
          <form onSubmit={(e) => handleStandardLogin(e, false)}>
            <FieldGroup className='gap-4'>
              <Field className='gap-2'>
                <FieldLabel htmlFor='userEmail' className='leading-5'>
                  Email address*
                </FieldLabel>
                <Input 
                  type='email' 
                  id='userEmail' 
                  placeholder='Enter your email address' 
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                />
              </Field>
              
              <Field className='w-full gap-2'>
                <FieldLabel htmlFor='password' className='leading-5'>
                  Password*
                </FieldLabel>
                <InputGroup>
                  <InputGroupInput 
                    id='password' 
                    type={isVisible ? 'text' : 'password'} 
                    placeholder='••••••••••••••••' 
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                  />
                  <InputGroupAddon align='inline-end' className='pr-1.5'>
                    <Button
                      type='button'
                      variant='ghost'
                      size='icon'
                      onClick={() => setIsVisible(!isVisible)}
                      className='text-muted-foreground rounded-l-none hover:bg-transparent'
                    >
                      {isVisible ? <EyeOffIcon /> : <EyeIcon />}
                      <span className='sr-only'>{isVisible ? 'Hide password' : 'Show password'}</span>
                    </Button>
                  </InputGroupAddon>
                </InputGroup>
              </Field>
              
              <div className='flex items-center justify-between gap-y-2'>
                <Field orientation='horizontal' className='flex items-center gap-2'>
                  <Checkbox id='rememberMe' />
                  <FieldLabel htmlFor='rememberMe' className='text-muted-foreground'>
                    Remember Me
                  </FieldLabel>
                </Field>
                <Link href='/pages/auth/forgot-password' className='text-base text-nowrap hover:underline'>
                  Forgot Password?
                </Link>
              </div>
              
              <Field>
                <Button className='w-full' type='submit' disabled={isLoading}>
                  {isLoading ? <Loader2 className="animate-spin" /> : 'Sign In'}
                </Button>
              </Field>
            </FieldGroup>
          </form>
        </TabsContent>

        <TabsContent value="otp">
          {!otpSent ? (
            <form onSubmit={handleSendOtp}>
              <FieldGroup className='gap-4'>
                <Field className='gap-2'>
                  <FieldLabel htmlFor='userInput' className='leading-5'>
                    Email or Mobile*
                  </FieldLabel>
                  <Input 
                    type='text' 
                    id='userInput' 
                    placeholder='Enter email or mobile' 
                    value={userInput}
                    onChange={(e) => setUserInput(e.target.value)}
                    required
                  />
                </Field>
                <Field>
                  <Button className='w-full' type='submit' disabled={isOtpLoading}>
                    {isOtpLoading ? <Loader2 className="animate-spin" /> : 'Send OTP'}
                  </Button>
                </Field>
              </FieldGroup>
            </form>
          ) : (
            <form onSubmit={(e) => handleVerifyOtp(e, false)}>
              <FieldGroup className='gap-4'>
                <Field className='gap-2'>
                  <FieldLabel htmlFor='otp' className='leading-5'>
                    Enter OTP*
                  </FieldLabel>
                  <Input 
                    type='text' 
                    id='otp' 
                    placeholder='Enter 6-digit OTP' 
                    value={otp}
                    onChange={(e) => setOtp(e.target.value)}
                    maxLength={6}
                    required
                  />
                </Field>
                <Field>
                  <Button className='w-full' type='submit' disabled={isOtpLoading}>
                    {isOtpLoading ? <Loader2 className="animate-spin" /> : 'Verify & Login'}
                  </Button>
                </Field>
                <Button 
                  type="button" 
                  variant="ghost" 
                  className="w-full text-sm mt-2" 
                  onClick={() => setOtpSent(false)}
                >
                  Use a different email/mobile
                </Button>
              </FieldGroup>
            </form>
          )}
        </TabsContent>
      </Tabs>

      <Dialog open={showForceLoginModal} onOpenChange={setShowForceLoginModal}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Already Logged In</DialogTitle>
            <DialogDescription>
              {forceLoginMessage}
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" onClick={() => setShowForceLoginModal(false)}>
              Cancel
            </Button>
            <Button onClick={() => {
              setShowForceLoginModal(false)
              if (pendingLoginType === 'standard') {
                handleStandardLogin(undefined, true)
              } else if (pendingLoginType === 'otp') {
                handleVerifyOtp(undefined, true)
              }
            }}>
              Force Login
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  )
}

export default LoginForm
