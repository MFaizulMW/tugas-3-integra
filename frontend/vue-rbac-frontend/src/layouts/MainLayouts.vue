<template>
  <div class="flex flex-col min-h-screen">
    <!-- Header -->
    <Header />

    <!-- Wrapper untuk sidebar dan konten -->
    <div class="flex flex-1">
      <!-- Sidebar -->
      <Navbar />

      <!-- Konten utama -->
      <main class="flex-1 p-6 bg-gray-50">
        <slot />
      </main>
    </div>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import Header from '@/components/Header.vue'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
</script>
