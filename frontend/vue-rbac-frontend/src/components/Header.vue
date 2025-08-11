<!-- HEADER -->
<template>
  <header class="bg-blue-400 text-white px-6 py-3 flex justify-between items-center shadow-sm">
    <h1 class="text-lg font-bold flex items-center">
      <span class="material-icons mr-2">Tugas Integra 3</span>
    </h1>
    <div class="flex items-center space-x-4">
<span class="text-base">Halo, {{ authStore.user?.username || 'User' }}</span>
      
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
body {
  font-family: 'Inter', sans-serif;
}
</style>
