<template>
  <component :is="layout">
    <div class="p-6">
      <h1 class="text-xl sm:text-2xl xl:text-3xl font-bold mb-4">All Events</h1>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        <div v-for="event in events" :key="event.id" class="flex flex-col gap-2 border rounded-lg p-4 pb-12 shadow hover:shadow-lg transition w-full relative">
          <h2 class="text-md sm:text-lg xl:text-xl font-semibold">{{ event.title }}</h2>
          <p class="text-md sm:text-lg xl:text-xl text-gray-600 italic">{{ event.location }}</p>
          <div class="text-xs sm:text-sm xl:text-md text-gray-500">
            {{ formatEventTime(event) }}
          </div>
          <p class="text-xs sm:text-sm xl:text-md text-gray-600">{{ event.description }}</p>
          <div class="absolute bottom-2 flex gap-2 text-sm">
            <button @click="rsvp(event.id, 'going')" class="px-2 py-1 bg-green-500 text-white rounded">Going</button>
            <button @click="rsvp(event.id, 'interested')" class="px-2 py-1 bg-yellow-500 text-white rounded">Interested</button>
            <button @click="rsvp(event.id, 'not_going')" class="px-2 py-1 bg-red-500 text-white rounded">Not Going</button>
          </div>
        </div>
      </div>
    </div>
  </component>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/core'

const layout = usePage().props.auth?.user ? AuthenticatedLayout : GuestLayout

defineProps({
  events: Array
})

const formatEventTime = (event) => {
  const start = new Date(event.start_time)
  const end = new Date(event.end_time)

  const sameDay = start.toDateString() === end.toDateString()

  if (sameDay) {
    return `${start.toLocaleDateString()} ${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} to ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
  } else {
    return `${start.toLocaleDateString()} ${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} to ${end.toLocaleDateString()} ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
  }
}

const rsvp = (eventId, status) => {
  router.post(`/events/${eventId}/rsvp`, { status })
}

</script>