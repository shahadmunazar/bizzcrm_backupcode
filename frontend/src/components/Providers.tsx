// React Imports
import type { ReactNode } from 'react'

// Component Imports
import { ThemeProvider } from './ThemeProvider'
import { SidebarProvider } from './ui/sidebar'
import { TooltipProvider } from './ui/tooltip'
import { AuthProvider } from '@/contexts/AuthContext'

type Props = {
  children: ReactNode
  sidebarDefaultOpen?: boolean
}

const Providers = ({ children, sidebarDefaultOpen }: Props) => {
  return (
    <ThemeProvider attribute='class' defaultTheme='system' enableSystem={true}>
      <AuthProvider>
        <TooltipProvider>
          <SidebarProvider defaultOpen={sidebarDefaultOpen}>{children}</SidebarProvider>
        </TooltipProvider>
      </AuthProvider>
    </ThemeProvider>
  )
}

export default Providers
