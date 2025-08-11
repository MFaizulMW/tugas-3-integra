import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Lazy load komponen agar bundle lebih kecil dan loading cepat
const Login = () => import('@/views/login.vue')
const Register = () => import('@/views/register.vue')
const Dashboard = () => import('@/views/dashboard.vue')
const KendaraanList = () => import('@/views/kendaraanList.vue')
const KendaraanCreate = () => import('@/views/kendaraanCreate.vue')
const KendaraanEdit = () => import('@/views/kendaraanEdit.vue')



// Definisikan rute
const routes = [
  {
    path: '/',
    redirect: '/dashboard' // default redirect ke dashboard setelah login
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true } // Hanya untuk guest (belum login)
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true } // Harus login
  },
  {
    path: '/kendaraan',
    name: 'KendaraanList',
    component: KendaraanList,
    meta: { requiresAuth: true }
  },
  {
    path: '/kendaraan/create',
    name: 'KendaraanCreate',
    component: KendaraanCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/kendaraan/:id/edit',
    name: 'KendaraanEdit',
    component: KendaraanEdit,
    meta: { requiresAuth: true },
    props: true // biar param id bisa diterima sebagai props
  },
  
]

// Buat router instance
const router = createRouter({
  history: createWebHistory(),
  routes
})

// Route guard global untuk proteksi route berdasar auth
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Jika token ada tapi user belum terisi, tunggu fetchUser selesai dulu
  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch {
      // Jika fetchUser gagal, otomatis logout di auth store
      return next({ name: 'Login' })
    }
  }

  const isLoggedIn = authStore.isAuthenticated

  if (to.meta.requiresAuth && !isLoggedIn) {
    return next({ name: 'Login' })
  }
  
  if (to.meta.guest && isLoggedIn) {
    return next({ name: 'Dashboard' })
  }

  next()
})

export default router
