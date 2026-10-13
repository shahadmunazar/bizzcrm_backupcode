'use client'

import React, { createContext, useContext, useEffect, useState } from 'react'
import { useRouter, usePathname } from 'next/navigation'
import { apiClient } from '@/lib/api-client'

interface User {
  id: number
  name: string
  email: string
  mobile?: string
  role: string
  is_superadmin: boolean
  is_admin: boolean
}

interface Box {
  id: number
  name: string
  slug: string
  domain: string
  whatsapp_token?: string
}

interface BoxUser {
  id: number
  employee_id: string
  staff_role: string
  status: string
  company: string
  box: Box
}

interface Permissions {
  is_privileged_user: boolean
  modules: Record<string, boolean>
  pages: Record<string, boolean>
}

interface AuthState {
  user: User | null
  boxUser: BoxUser | null
  permissions: Permissions | null
  isLoading: boolean
  isAuthenticated: boolean
}

interface AuthContextType extends AuthState {
  login: (token: string, slug?: string) => void
  logout: (slug?: string) => void
  fetchUser: (slug?: string) => Promise<void>
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

const PUBLIC_ROUTES = [
  '/pages/auth/login',
  '/pages/auth/register',
  '/pages/auth/forgot-password',
  '/pages/auth/reset-password',
  '/pages/auth/verify-email'
]

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [state, setState] = useState<AuthState>({
    user: null,
    boxUser: null,
    permissions: null,
    isLoading: true,
    isAuthenticated: false,
  })

  const router = useRouter()
  const pathname = usePathname()

  const fetchUser = async (slug?: string) => {
    setState((prev) => ({ ...prev, isLoading: true }))
    try {
      let storedSlug = slug || localStorage.getItem('box_slug')
      if (storedSlug === 'undefined') {
        storedSlug = null
      }
      const endpoint = storedSlug ? `/${storedSlug}/me` : '/me'
      
      const response = await apiClient.get(endpoint)
      if (response && response.success) {
        const detectedSlug = response.box_user?.box?.slug || response.box?.slug || response.user?.box?.slug
        if (detectedSlug && detectedSlug !== 'undefined') {
          localStorage.setItem('box_slug', detectedSlug)
        }
        setState({
          user: response.user,
          boxUser: response.box_user,
          permissions: response.permissions,
          isLoading: false,
          isAuthenticated: true,
        })
      } else {
        throw new Error('Failed to fetch user data')
      }
    } catch (error) {
      setState({
        user: null,
        boxUser: null,
        permissions: null,
        isLoading: false,
        isAuthenticated: false,
      })
      // If we are not on a public route, redirect to login
      const isPublic = PUBLIC_ROUTES.some((route) => pathname.startsWith(route))
      if (!isPublic && pathname !== '/') {
        router.push('/pages/auth/login')
      }
    }
  }

  useEffect(() => {
    const token = localStorage.getItem('access_token')
    if (token) {
      fetchUser()
    } else {
      setState((prev) => ({ ...prev, isLoading: false }))
      const isPublic = PUBLIC_ROUTES.some((route) => pathname.startsWith(route))
      if (!isPublic && pathname !== '/') {
        router.push('/pages/auth/login')
      }
    }

    const handleUnauthorized = () => {
      setState({
        user: null,
        boxUser: null,
        permissions: null,
        isLoading: false,
        isAuthenticated: false,
      })
      router.push('/pages/auth/login')
    }

    window.addEventListener('unauthorized', handleUnauthorized)
    return () => window.removeEventListener('unauthorized', handleUnauthorized)
  }, [pathname, router])

  const login = (token: string, slug?: string) => {
    localStorage.setItem('access_token', token)
    if (slug && slug !== 'undefined') {
      localStorage.setItem('box_slug', slug)
    } else {
      localStorage.removeItem('box_slug')
    }
    fetchUser(slug === 'undefined' ? undefined : slug)
  }

  const logout = async (slug?: string) => {
    try {
      let storedSlug = slug || localStorage.getItem('box_slug')
      if (storedSlug === 'undefined') {
        storedSlug = null
      }
      const endpoint = storedSlug ? `/${storedSlug}/logout` : '/logout'
      await apiClient.post(endpoint)
    } catch (error) {
      console.error('Logout failed on backend:', error)
    } finally {
      localStorage.removeItem('access_token')
      localStorage.removeItem('box_slug')
      setState({
        user: null,
        boxUser: null,
        permissions: null,
        isLoading: false,
        isAuthenticated: false,
      })
      router.push('/pages/auth/login')
    }
  }

  return (
    <AuthContext.Provider value={{ ...state, login, logout, fetchUser }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => {
  const context = useContext(AuthContext)
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}
