<template>
  <component :is="layout">
    <div class="p-6">
      <h1 class="text-xl sm:text-2xl xl:text-3xl font-bold mb-4">My Interests</h1>
      <div v-if="events.length === 0" class="text-gray-500">
        You haven't indicated interest in any events yet.
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="event in events" :key="event.id" class="border rounded-lg p-4 shadow hover:shadow-lg transition relative">
          <h2 class="text-md sm:text-lg xl:text-xl font-semibold">{{ event.title }}</h2>
          <p class="text-md sm:text-lg xl:text-xl text-gray-600 italic">{{ event.location }}</p>
          <div class="text-xs sm:text-sm xl:text-md text-gray-500 mt-1">{{ formatEventTime(event) }}</div>
          <p class="text-xs sm:text-sm xl:text-md text-gray-600 mt-2">{{ event.description }}</p>
        </div>
      </div>
    </div>
  </component>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { usePage } from '@inertiajs/vue3'

const layout = usePage().props.auth?.user ? AuthenticatedLayout : GuestLayout

defineProps({
  events: Array
})

const formatEventTime = (event) => {
  const start = new Date(event.start_time)
  const end = new Date(event.end_time)

  if (start.toDateString() === end.toDateString()) {
    return `${start.toLocaleDateString()} ${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
  } else {
    return `${start.toLocaleDateString()} ${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${end.toLocaleDateString()} ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
  }
}
</script>
