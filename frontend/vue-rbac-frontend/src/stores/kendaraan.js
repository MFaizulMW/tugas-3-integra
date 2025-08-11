import { defineStore } from 'pinia'
import api from '@/services/api'

export const useKendaraanStore = defineStore('kendaraan', {
  state: () => ({
    kendaraans: [],
    currentKendaraan: null,
    loading: false,
    error: null,
    pagination: {
      page: 1,
      limit: 10,
      total: 0,
      totalPages: 0
    }
  }),

  getters: {
    getKendaraanById: (state) => (id) => {
      return state.kendaraans.find(k => k.kendaraan_id === parseInt(id))
    }
  },

  actions: {
    async fetchKendaraans(params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('/ref_kendaraan', { params })

        if (response.data.code === 200) {
          this.kendaraans = response.data.data || []

          if (response.data.pagination) {
            this.pagination = response.data.pagination
          }
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch kendaraan data'
        console.error('Fetch kendaraans error:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchKendaraanById(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/ref_kendaraan/${id}`)

        if (response.data.code === 200) {
          this.currentKendaraan = response.data.data
          return response.data.data
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch kendaraan details'
        console.error('Fetch kendaraan by ID error:', error)
      } finally {
        this.loading = false
      }
    },

    async createKendaraan(kendaraanData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/ref_kendaraan', kendaraanData)

        if (response.data.code === 201) {
          this.kendaraans.unshift(response.data.data)
          return { success: true, data: response.data.data }
        } else {
          throw new Error(response.data.message || 'Failed to create kendaraan')
        }
      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Failed to create kendaraan'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async updateKendaraan(id, kendaraanData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.put(`/ref_kendaraan/${id}`, kendaraanData)

        if (response.data.code === 200) {
          const index = this.kendaraans.findIndex(k => k.kendaraan_id === parseInt(id))
          if (index !== -1) {
            this.kendaraans[index] = response.data.data
          }
          if (this.currentKendaraan?.kendaraan_id === parseInt(id)) {
            this.currentKendaraan = response.data.data
          }
          return { success: true, data: response.data.data }
        } else {
          throw new Error(response.data.message || 'Failed to update kendaraan')
        }
      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Failed to update kendaraan'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async deleteKendaraan(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.delete(`/ref_kendaraan/${id}`)

        if (response.data.code === 200) {
          this.kendaraans = this.kendaraans.filter(k => k.kendaraan_id !== parseInt(id))
          if (this.currentKendaraan?.kendaraan_id === parseInt(id)) {
            this.currentKendaraan = null
          }
          return { success: true }
        } else {
          throw new Error(response.data.message || 'Failed to delete kendaraan')
        }
      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Failed to delete kendaraan'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    },

    clearCurrentKendaraan() {
      this.currentKendaraan = null
    }
  }
})
