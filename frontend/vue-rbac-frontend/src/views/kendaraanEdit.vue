<template>
<main-layout>
  <div class="p-6 max-w-xl mx-auto">
    <div class="border rounded-lg shadow-sm bg-white p-6">
      <h1 class="text-2xl font-bold mb-4">Edit Kendaraan</h1>

      <form @submit.prevent="submitForm" class="space-y-4">
        <!-- Kode Kendaraan -->
        <div>
          <label class="block mb-1 font-semibold">Kode Kendaraan</label>
          <input v-model="form.kendaraan_kode" type="text" required class="input" />
        </div>

        <!-- Nama Kendaraan -->
        <div>
          <label class="block mb-1 font-semibold">Nama Kendaraan</label>
          <input v-model="form.kendaraan" type="text" required class="input" />
        </div>

        <!-- Tahun -->
        <div>
          <label class="block mb-1 font-semibold">Tahun</label>
          <input v-model="form.kendaraan_tahun" type="number" required class="input" />
        </div>

        <!-- Jenis -->
        <div>
          <label class="block mb-1 font-semibold">Jenis Kendaraan</label>
          <input v-model="form.kendaraan_jenis" type="text" required class="input" />
        </div>

        <!-- Nomor Polisi -->
        <div>
          <label class="block mb-1 font-semibold">Nomor Polisi</label>
          <input v-model="form.kendaraan_nomor" type="text" required class="input" />
        </div>

        <!-- Mesin -->
        <div>
          <label class="block mb-1 font-semibold">Mesin</label>
          <input v-model="form.kendaraan_mesin" type="text" required class="input" />
        </div>

        <!-- Warna -->
        <div>
          <label class="block mb-1 font-semibold">Warna</label>
          <input v-model="form.kendaraan_warna" type="text" required class="input" />
        </div>

        <!-- Image URL (optional) -->
        <div>
          <label class="block mb-1 font-semibold">URL Gambar</label>
          <input v-model="form.image" type="text" placeholder="URL gambar kendaraan" class="input" />
        </div>

        <!-- Tombol Aksi -->
        <div class="flex gap-2">
          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Simpan Perubahan
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
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useKendaraanStore } from '@/stores/kendaraan'
import MainLayout from '@/layouts/MainLayouts.vue'


const route = useRoute()
const router = useRouter()
const kendaraanStore = useKendaraanStore()

const form = ref({
  kendaraan_kode: '',
  kendaraan: '',
  kendaraan_tahun: '',
  kendaraan_jenis: '',
  kendaraan_nomor: '',
  kendaraan_mesin: '',
  kendaraan_warna: '',
  image: ''
})

const fetchKendaraan = async () => {
  const id = route.params.id
  const kendaraan = await kendaraanStore.fetchKendaraanById(id)
  if (!kendaraan) {
    alert('Data kendaraan tidak ditemukan')
    router.push('/kendaraan')
    return
  }
  // copy semua field kecuali kendaraan_id ke form
  form.value = {
    kendaraan_kode: kendaraan.kendaraan_kode || '',
    kendaraan: kendaraan.kendaraan || '',
    kendaraan_tahun: kendaraan.kendaraan_tahun || '',
    kendaraan_jenis: kendaraan.kendaraan_jenis || '',
    kendaraan_nomor: kendaraan.kendaraan_nomor || '',
    kendaraan_mesin: kendaraan.kendaraan_mesin || '',
    kendaraan_warna: kendaraan.kendaraan_warna || '',
    image: kendaraan.image || ''
  }
}

const submitForm = async () => {
  const id = route.params.id
  const res = await kendaraanStore.updateKendaraan(id, form.value)
  if (res.success) {
    alert('Data kendaraan berhasil diupdate')
    router.push('/kendaraan')
  } else {
    alert(res.message)
  }
}

onMounted(() => {
  fetchKendaraan()
})
</script>

<style scoped>
.input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}


</style>


