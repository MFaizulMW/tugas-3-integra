<template>
<main-layout>
  <div class="p-6 max-w-xl mx-auto">
    <div class="bg-white border border-gray-300 rounded-lg shadow p-6">
      <h1 class="text-2xl font-bold mb-6">Tambah Kendaraan Baru</h1>

      <!-- Error message -->
      <div v-if="error" class="bg-red-100 text-red-700 p-3 rounded mb-4">
        {{ error }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block font-semibold mb-1">Instansi ID</label>
          <input
            type="number"
            v-model.number="form.instansi_id"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan Instansi ID (boleh kosong)"
          />
        </div>

        <div>
          <label class="block font-semibold mb-1">Kode Kendaraan</label>
          <input
            type="text"
            v-model="form.kendaraan_kode"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan kode kendaraan"
            required
          />
        </div>

        <div>
          <label class="block font-semibold mb-1">Nama Kendaraan</label>
          <input
            type="text"
            v-model="form.kendaraan"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan nama kendaraan"
            required
          />
        </div>

        <div>
          <label class="block font-semibold mb-1">Tahun</label>
          <input
            type="text"
            v-model="form.kendaraan_tahun"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan tahun kendaraan"
            required
          />
        </div>

        <div>
          <label class="block font-semibold mb-1">Jenis</label>
          <input
            type="text"
            v-model="form.kendaraan_jenis"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan jenis kendaraan"
            required
          />
        </div>

        <div>
          <label class="block font-semibold mb-1">Nomor Polisi</label>
          <input
            type="text"
            v-model="form.kendaraan_nomor"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan nomor polisi"
            required
          />
        </div>

        <div>
          <label class="block font-semibold mb-1">Mesin</label>
          <input
            type="text"
            v-model="form.kendaraan_mesin"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan jenis mesin"
            required
          />
        </div>

        <div>
          <label class="block font-semibold mb-1">Warna</label>
          <input
            type="text"
            v-model="form.kendaraan_warna"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan warna kendaraan"
            required
          />
        </div>

        <div>
          <label class="block font-semibold mb-1">Gambar (URL atau base64)</label>
          <input
            type="text"
            v-model="form.image"
            class="w-full border rounded px-3 py-2"
            placeholder="Masukkan URL gambar atau base64"
          />
        </div>

        <!-- Tombol Aksi -->
        <div class="flex gap-2">
          <button
            type="submit"
            :disabled="loading"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50"
          >
            {{ loading ? 'Menyimpan...' : 'Simpan' }}
          </button>
          <router-link
            to="/kendaraan"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center justify-center"
          >
            Kembali
          </router-link>
        </div>
      </form>
    </div>
  </div>
</main-layout>
</template>



<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useKendaraanStore } from '@/stores/kendaraan'
import MainLayout from '@/layouts/MainLayouts.vue'

const router = useRouter()
const kendaraanStore = useKendaraanStore()

const loading = ref(false)
const error = ref(null)

// Inisialisasi form data
const form = reactive({
  instansi_id: null,
  kendaraan_kode: '',
  kendaraan: '',
  kendaraan_tahun: '',
  kendaraan_jenis: '',
  kendaraan_nomor: '',
  kendaraan_mesin: '',
  kendaraan_warna: '',
  image: ''
})

const handleSubmit = async () => {
  loading.value = true
  error.value = null

  // Kamu bisa tambahkan validasi manual jika perlu

  try {
    const payload = {
      ...form,
      // Pastikan instansi_id dikirim null jika kosong
      instansi_id: form.instansi_id === '' ? null : form.instansi_id
    }

    const res = await kendaraanStore.createKendaraan(payload)
    if (res.success) {
      alert('Data kendaraan berhasil disimpan!')
      router.push('/kendaraan') // arahkan kembali ke list kendaraan
    } else {
      error.value = res.message || 'Gagal menyimpan data kendaraan'
    }
  } catch (e) {
    error.value = e.message || 'Terjadi kesalahan saat menyimpan data'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* opsional styling */
</style>
