<template>
  <component :is="layout">
    <div class="p-6 space-y-4">
      <h1 class="text-2xl font-bold">{{ title }}</h1>
      <h2 class="text-gray-600">
        Showing location for: <b>{{ location }}</b>
      </h2>
      <div id="map" class="w-full h-[400px] md:h-[600px] rounded-xl shadow"></div>
    </div>
  </component>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const layout = usePage().props.auth?.user ? AuthenticatedLayout : GuestLayout

const params = new URLSearchParams(window.location.search)
const location = params.get('location')
const title = params.get('title')
const coords = ref(null)

onMounted(async () => {
  const map = L.map('map').setView([47.4979, 19.0402], 7)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)

  if (location) {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`
    )
    const data = await response.json()

    if (data.length > 0) {
      const { lat, lon, display_name } = data[0]
      coords.value = [parseFloat(lat), parseFloat(lon)]

      map.setView(coords.value, 13)
      const marker = L.marker(coords.value).addTo(map)
      marker.bindPopup(`<b>${display_name}</b>`).openPopup()
    }
  }
})
</script>
