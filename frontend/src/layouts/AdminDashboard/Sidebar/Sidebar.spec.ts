import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'

import Sidebar from './Sidebar.vue'

describe('Sidebar', () => {
  it('renders the academy identity, primary action, and all menu labels', () => {
    const wrapper = mount(Sidebar)

    expect(wrapper.get('h1').text()).toBe('HISSA Academy')
    expect(wrapper.text()).toContain('Admin Console')
    expect(wrapper.get('button[type="button"]').text()).toContain('New Course')
    expect(wrapper.text()).toContain('Dashboard')
    expect(wrapper.text()).toContain('Courses')
    expect(wrapper.text()).toContain('Students')
    expect(wrapper.text()).toContain('Certificates')
    expect(wrapper.text()).toContain('Settings')
    expect(wrapper.text()).toContain('Help Center')
  })

  it('marks Courses as the temporary active menu item', () => {
    const wrapper = mount(Sidebar)

    const activeItem = wrapper.get('[aria-current="page"]')
    expect(activeItem.text()).toContain('Courses')
    expect(activeItem.classes()).toContain('bg-emerald-200')
  })

  it('keeps the footer group at the bottom of the full-height sidebar', () => {
    const wrapper = mount(Sidebar)

    expect(wrapper.get('[data-testid="admin-sidebar"]').classes()).toContain('h-screen')
    expect(wrapper.get('[data-testid="sidebar-footer"]').classes()).toContain('mt-auto')
  })
})
