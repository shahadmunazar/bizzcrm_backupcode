'use client'

import { useEffect, useState } from 'react'
import { useParams, useRouter } from 'next/navigation'
import Link from 'next/link'
import { CheckCircle2, XCircle, Loader2 } from 'lucide-react'
import { toast } from 'sonner'
import { apiClient } from '@/lib/api-client'

import Logo from '@/components/shared/Logo'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import AuthBackgroundShape from '@/assets/svg/auth-background-shape'

const VerifyToken = () => {
  const { token } = useParams()
  const router = useRouter()
  const [status, setStatus] = useState<'loading' | 'success' | 'error'>('loading')
  const [message, setMessage] = useState('Verifying your email address...')

  useEffect(() => {
    if (!token) return

    apiClient.get(`/verify-email/${token}`)
      .then((data) => {
        if (data?.success) {
          setStatus('success')
          setMessage(data.message || 'Your account has been verified successfully.')
          toast.success(data.message || 'Your account has been verified successfully.')
        } else {
          throw new Error(data?.message || 'Verification failed')
        }
      })
      .catch((error) => {
        setStatus('error')
        setMessage(error.message || 'Invalid or expired verification link.')
        toast.error(error.message || 'Invalid or expired verification link.')
      })
  }, [token])

  return (
    <div className='relative flex h-auto min-h-screen items-center justify-center overflow-x-hidden px-4 py-10 sm:px-6 lg:px-8'>
      <div className='absolute'>
        <AuthBackgroundShape />
      </div>

      <Card className='z-1 w-full gap-6 py-6 sm:max-w-md'>
        <CardHeader className='gap-6 px-6'>
          <Link href='/'>
            <Logo className='justify-center gap-3' />
          </Link>

          <div className='text-center'>
            {status === 'loading' && <Loader2 className="mx-auto h-12 w-12 animate-spin text-primary mb-4" />}
            {status === 'success' && <CheckCircle2 className="mx-auto h-12 w-12 text-green-500 mb-4" />}
            {status === 'error' && <XCircle className="mx-auto h-12 w-12 text-red-500 mb-4" />}
            
            <CardTitle className='mb-2 text-2xl font-semibold'>
              {status === 'loading' ? 'Verifying...' : status === 'success' ? 'Email Verified' : 'Verification Failed'}
            </CardTitle>
            <CardDescription className='text-base'>
              {message}
            </CardDescription>
          </div>
        </CardHeader>

        <CardContent className='px-6'>
          <div className='space-y-4'>
            {status === 'success' ? (
              <Button className='w-full' onClick={() => router.push('/pages/auth/login')}>
                Proceed to Login
              </Button>
            ) : status === 'error' ? (
              <Button className='w-full' variant="outline" onClick={() => router.push('/pages/auth/login')}>
                Back to Login
              </Button>
            ) : null}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}

export default VerifyToken
