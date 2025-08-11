<template>
  <main-layout>
    <div class="container mx-auto">
      <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
      <p class="text-gray-600">
        Selamat datang, <span class="font-semibold">{{ authStore.user?.username || 'User' }}</span>!
      </p>

      <!-- Contoh menu navigasi -->
      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <router-link
          to="/kendaraan"
          class="bg-white shadow rounded-lg p-4 hover:shadow-md transition"
        >
          <h2 class="text-lg font-semibold mb-2">Data Kendaraan</h2>
          <p class="text-sm text-gray-500">Lihat dan kelola kendaraan</p>
        </router-link>
      </div>
    </div>
  <div class="p-6">
    

    <!-- Contoh menu navigasi -->
    
  </div>
    </main-layout>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import MainLayout from '@/layouts/MainLayouts.vue'


const authStore = useAuthStore()
</script>
