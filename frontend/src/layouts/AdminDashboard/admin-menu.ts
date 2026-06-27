import type { Component } from 'vue'
import {
  BadgeCheck,
  BookOpenText,
  CircleHelp,
  LayoutDashboard,
  Settings,
  Users,
} from '@lucide/vue'

export interface MenuItem {
  label: string
  routeName: string
  icon: Component
  roles?: string[]
}

export const adminMenu: MenuItem[] = [
  { label: 'Dashboard', routeName: 'admin-dashboard', icon: LayoutDashboard, roles: ['admin'] },
  { label: 'Courses', routeName: 'admin-courses', icon: BookOpenText, roles: ['admin'] },
  { label: 'Students', routeName: 'admin-students', icon: Users, roles: ['admin'] },
  {
    label: 'Certificates',
    routeName: 'admin-certificates',
    icon: BadgeCheck,
    roles: ['admin'],
  },
]

export const adminMenuFooter: MenuItem[] = [
  { label: 'Settings', routeName: 'admin-settings', icon: Settings, roles: ['admin'] },
  { label: 'Help Center', routeName: 'admin-help', icon: CircleHelp, roles: ['admin'] },
]
