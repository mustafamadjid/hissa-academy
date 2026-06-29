import { flushPromises, mount } from '@vue/test-utils'
import { createPinia } from 'pinia'
import { describe, expect, it, vi } from 'vitest'
import { createMemoryHistory, createRouter } from 'vue-router'

import { useAuthStore } from '@/features/auth/stores/auth.store'
import { adminMenu, adminMenuFooter } from '../admin-menu'
import Sidebar from './Sidebar.vue'

const routeItems = [...adminMenu, ...adminMenuFooter]

async function mountSidebar() {
  const pinia = createPinia()
  const router = createRouter({
    history: createMemoryHistory(),
    routes: [
      ...routeItems.map((item, index) => ({
        path: index === 0 ? '/admin' : `/admin/${item.routeName.replace('admin-', '')}`,
        name: item.routeName,
        component: { template: '<div />' },
      })),
      {
        path: '/login/administrator',
        name: 'administrator-login',
        component: { template: '<div />' },
      },
    ],
  })

  await router.push({ name: 'admin-dashboard' })
  await router.isReady()

  return {
    authStore: useAuthStore(pinia),
    router,
    wrapper: mount(Sidebar, {
      global: { plugins: [pinia, router] },
    }),
  }
}

describe('Sidebar', () => {
  it('renders the academy identity and all menu labels', async () => {
    const { wrapper } = await mountSidebar()

    expect(wrapper.get('h1').text()).toBe('HISSA Academy')
    expect(wrapper.text()).toContain('Admin Console')
    expect(wrapper.text()).toContain('Dashboard')
    expect(wrapper.text()).toContain('Courses')
    expect(wrapper.text()).toContain('Students')
    expect(wrapper.text()).toContain('Certificates')
    expect(wrapper.text()).toContain('Settings')
    expect(wrapper.text()).toContain('Help Center')
    expect(wrapper.text()).toContain('Logout')
  })

  it('updates the active menu item when it is clicked', async () => {
    const { router, wrapper } = await mountSidebar()

    expect(wrapper.get('[aria-current="page"]').text()).toContain('Dashboard')

    const coursesLink = wrapper
      .findAll('nav[aria-label="Admin menu"] a')
      .find((link) => link.text().includes('Courses'))

    expect(coursesLink).toBeDefined()
    await coursesLink!.trigger('click')
    await flushPromises()

    const activeItem = wrapper.get('[aria-current="page"]')
    expect(activeItem.text()).toContain('Courses')
    expect(activeItem.classes()).toContain('bg-primary-dark-green')
    expect(router.currentRoute.value.name).toBe('admin-courses')
  })

  it('keeps the footer group at the bottom of the full-height sidebar', async () => {
    const { wrapper } = await mountSidebar()

    expect(wrapper.get('[data-testid="admin-sidebar"]').classes()).toContain('h-screen')
    expect(wrapper.get('[data-testid="sidebar-footer"]').classes()).toContain('mt-auto')
  })

  it('signs out and redirects to the administrator login page', async () => {
    const { authStore, router, wrapper } = await mountSidebar()
    const signOut = vi.spyOn(authStore, 'signOut').mockResolvedValue()

    await wrapper.get('[data-testid="sidebar-logout"]').trigger('click')
    await flushPromises()

    expect(signOut).toHaveBeenCalledOnce()
    expect(router.currentRoute.value.name).toBe('administrator-login')
  })
})
