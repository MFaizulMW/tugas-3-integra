// src/services/api.js
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// Create axios instance
const api = axios.create({
  baseURL: 'http://localhost:8000', // Adjust base URL as needed
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add token to headers if exists
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    console.error('API Error:', error)

    // Handle different error scenarios
    if (error.response) {
      const { status, data } = error.response

      switch (status) {
        case 401:
          // Unauthorized - redirect to login
          localStorage.removeItem('token')
          delete api.defaults.headers.common['Authorization']
          
          if (window.location.pathname !== '/login') {
            toast.error('Session expired. Please login again.')
            window.location.href = '/login'
          }
          break

        case 403:
          toast.error('Access denied. You do not have permission to perform this action.')
          break

        case 404:
          toast.error(data.message || 'Resource not found')
          break

        case 422:
          // Validation errors
          if (data.errors) {
            Object.values(data.errors).flat().forEach(error => {
              toast.error(error)
            })
          } else {
            toast.error(data.message || 'Validation failed')
          }
          break

        case 500:
          toast.error('Internal server error. Please try again later.')
          break

        default:
          toast.error(data.message || 'An error occurred')
      }
    } else if (error.request) {
      // Network error
      toast.error('Network error. Please check your connection.')
    } else {
      toast.error('An unexpected error occurred')
    }

    return Promise.reject(error)
  }
)

export default api