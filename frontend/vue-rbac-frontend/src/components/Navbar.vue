<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <nav class="bg-gray-50 border-r border-gray-200 w-60 p-4 flex flex-col justify-between">
      <div>
        <h2 class="text-lg font-semibold text-gray-600 mb-6 flex items-center">
          <span class="material-icons text-blue-500 mr-3">Menu</span>
        </h2>
        <ul class="flex flex-col space-y-1">
          <li>
            <router-link
              to="/dashboard"
              class="flex items-center p-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition"
              :class="{ 'bg-blue-100 text-blue-700 border-l-4 border-blue-500 shadow-sm': $route.path === '/dashboard' }"
            >
              Dashboard
            </router-link>
          </li>
          <li>
            <router-link
              to="/kendaraan"
              class="flex items-center p-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition"
              :class="{ 'bg-blue-100 text-blue-700 border-l-4 border-blue-500 shadow-sm': $route.path === '/kendaraan' }"
            >
              Data Kendaraan
            </router-link>
          </li>
        </ul>
      </div>

      <!-- Logout di bawah -->
      <div>
        <button
          @click="logout"
          class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition"
        >
          Logout
        </button>
      </div>
    </nav>

    <!-- Konten -->
    <main class="flex-1 bg-gray-100">
      <slot />
    </main>
  </div>
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