'use client'

// React Imports
import { Suspense, useEffect } from 'react'
import type { ReactNode } from 'react'
import { useParams, usePathname, useRouter } from 'next/navigation'

// Component Imports
import Footer from '@/components/layout/Footer'
import Header from '@/components/layout/Header'
import Sidebar from '@/components/layout/Sidebar'
import { SidebarInset } from '@/components/ui/sidebar'
import { Toaster } from '@/components/ui/sonner'
import { useAuth } from '@/contexts/AuthContext'

const PagesLayout = ({ children }: Readonly<{ children: ReactNode }>) => {
  const params = useParams()
  const pathname = usePathname()
  const router = useRouter()
  const { boxUser, isAuthenticated, isLoading } = useAuth()

  useEffect(() => {
    if (isLoading) return

    const token = typeof window !== 'undefined' ? localStorage.getItem('access_token') : null
    if (!token && !isAuthenticated) {
      router.replace('/pages/auth/login')
      return
    }

    const routeSlug = (params?.box_slug as string) || ''
    if (routeSlug === 'undefined' || !routeSlug) {
      const realSlug = boxUser?.box?.slug || (typeof window !== 'undefined' ? localStorage.getItem('box_slug') : null)
      if (realSlug && realSlug !== 'undefined') {
        const fixedPath = pathname.replace(/^\/undefined(\/|$)/, `/${realSlug}$1`)
        if (fixedPath !== pathname) {
          router.replace(fixedPath)
        }
      }
    }
  }, [params, pathname, boxUser, isAuthenticated, isLoading, router])

  if (isLoading) {
    return (
      <div className="flex h-screen w-full items-center justify-center">
        <div className="text-muted-foreground animate-pulse text-sm">Loading...</div>
      </div>
    )
  }

  return (
    <div className='flex h-full w-full min-w-0'>
      <Suspense>
        <Sidebar />
      </Suspense>
      <SidebarInset className='flex flex-1 flex-col'>
        <Header />
        <main className='mx-auto size-full max-w-360 flex-1 px-4 py-6 sm:px-6'>{children}</main>
        <Toaster />
        <Footer />
      </SidebarInset>
    </div>
  )
}

export default PagesLayout
