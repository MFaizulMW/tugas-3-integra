// src/stores/auth.js
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userRoles: (state) => state.user?.roles || [],
    hasRole: (state) => (role) => state.user?.roles?.includes(role) || false,
    canDelete: (state) => state.user?.roles?.some(role => ['admin', 'developer'].includes(role)) || false,
    canModify: (state) => state.user?.roles?.some(role => ['admin', 'developer', 'grp-staf'].includes(role)) || false
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/auth/login', credentials)
        
        if (response.data.code === 200) {
          this.token = response.data.data.token
          this.user = response.data.data.user
          
          // Store token in localStorage
          localStorage.setItem('token', this.token)
          
          // Set token in API headers
          api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
          
          return { success: true }
        } else {
          throw new Error(response.data.message || 'Login failed')
        }
      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Login failed'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/auth/register', userData)
        
        if (response.data.code === 201) {
          return { success: true, message: 'Registration successful' }
        } else {
          throw new Error(response.data.message || 'Registration failed')
        }
      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Registration failed'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchUser() {
      if (!this.token) return

      this.loading = true

      try {
        const response = await api.get('/auth/me')
        
        if (response.data.code === 200) {
          this.user = response.data.data
        }
      } catch (error) {
        console.error('Failed to fetch user:', error)
        this.logout()
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.user = null
      this.token = null
      this.error = null
      
      localStorage.removeItem('token')
      delete api.defaults.headers.common['Authorization']
      
      // Redirect to login
      window.location.href = '/login'
    },

    initializeAuth() {
      if (this.token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        this.fetchUser()
      }
    }
  }
})