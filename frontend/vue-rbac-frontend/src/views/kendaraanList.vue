<template>
<main-layout>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Data Kendaraan</h1>
      <router-link
        v-if="canCreate"
        to="/kendaraan/create"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        + Tambah Kendaraan
      </router-link>
    </div>

    <!-- Error message -->
    <div v-if="kendaraanStore.error" class="bg-red-100 text-red-700 p-3 rounded mb-4">
      {{ kendaraanStore.error }}
    </div>

    <!-- Loading -->
    <div v-if="kendaraanStore.loading" class="text-gray-500">Memuat data...</div>

    <!-- Table -->
    <div
      v-if="!kendaraanStore.loading && kendaraanStore.kendaraans.length"
      class="overflow-hidden rounded-lg shadow-sm border border-gray-200"
    >
      <table class="min-w-full bg-white">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border">#</th>
            <th class="px-4 py-2 border">Kode</th>
            <th class="px-4 py-2 border">Nama Kendaraan</th>
            <th class="px-4 py-2 border">Tahun</th>
            <th class="px-4 py-2 border">Jenis</th>
            <th class="px-4 py-2 border">Nomor Polisi</th>
            <th class="px-4 py-2 border">Mesin</th>
            <th class="px-4 py-2 border">Warna</th>
            <th class="px-4 py-2 border">Gambar</th>
            <th class="px-4 py-2 border text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(kendaraan, index) in kendaraanStore.kendaraans"
            :key="kendaraan.kendaraan_id"
            class="hover:bg-gray-50 transition"
          >
            <td class="px-4 py-2 border">{{ index + 1 }}</td>
            <td class="px-4 py-2 border">{{ kendaraan.kendaraan_kode }}</td>
            <td class="px-4 py-2 border">{{ kendaraan.kendaraan }}</td>
            <td class="px-4 py-2 border">{{ kendaraan.kendaraan_tahun }}</td>
            <td class="px-4 py-2 border">{{ kendaraan.kendaraan_jenis }}</td>
            <td class="px-4 py-2 border">{{ kendaraan.kendaraan_nomor }}</td>
            <td class="px-4 py-2 border">{{ kendaraan.kendaraan_mesin }}</td>
            <td class="px-4 py-2 border">{{ kendaraan.kendaraan_warna }}</td>
            <td class="px-4 py-2 border">
              <img
                v-if="kendaraan.image"
                :src="kendaraan.image"
                alt="gambar kendaraan"
                class="w-16 h-10 object-cover rounded"
              />
              <span v-else class="text-gray-400 italic">Tidak ada gambar</span>
            </td>
            <td class="px-4 py-2 border text-center space-x-2">
              <button
                v-if="canEdit"
                @click="$router.push(`/kendaraan/${kendaraan.kendaraan_id}/edit`)"
                class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
              >
                Edit
              </button>
              <button
                v-if="canDelete"
                @click="handleDelete(kendaraan.kendaraan_id)"
                class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"
              >
                Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty state -->
    <div v-if="!kendaraanStore.loading && !kendaraanStore.kendaraans.length" class="text-gray-500">
      Tidak ada data kendaraan.
    </div>
  </div>
</main-layout>
</template>


<script setup>
import { onMounted, computed } from 'vue'
import { useKendaraanStore } from '@/stores/kendaraan'
import { useAuthStore } from '@/stores/auth'
import MainLayout from '@/layouts/MainLayouts.vue'



const kendaraanStore = useKendaraanStore()
const authStore = useAuthStore()

// Hak akses berdasarkan role
const canCreate = computed(() => {
  return ['admin', 'developer', 'grp-staf'].some(r => authStore.user?.roles.includes(r))
})
const canEdit = canCreate
const canDelete = canCreate

onMounted(() => {
  kendaraanStore.fetchKendaraans()
})

const handleDelete = async (id) => {
  if (confirm('Yakin ingin menghapus kendaraan ini?')) {
    const res = await kendaraanStore.deleteKendaraan(id)
    if (!res.success) {
      alert(res.message)
    }
  }
}
</script> 
