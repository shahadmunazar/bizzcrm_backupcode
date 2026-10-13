'use client'

import { useEffect } from 'react'
import { useRouter } from 'next/navigation'
import { useAuth } from '@/contexts/AuthContext'

export default function RootPage() {
  const router = useRouter()
  const { boxUser, isLoading, isAuthenticated } = useAuth()

  useEffect(() => {
    if (isLoading) return

    const slug = boxUser?.box?.slug || (typeof window !== 'undefined' ? localStorage.getItem('box_slug') : null)
    const token = typeof window !== 'undefined' ? localStorage.getItem('access_token') : null

    if (token && slug && slug !== 'undefined') {
      router.replace(`/${slug}/dashboard/admin`)
    } else {
      router.replace('/pages/auth/login')
    }
  }, [boxUser, isLoading, isAuthenticated, router])

  return (
    <div className="flex h-screen w-full items-center justify-center">
      <div className="text-muted-foreground animate-pulse text-sm">Loading...</div>
    </div>
  )
}
