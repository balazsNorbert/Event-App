<template>
  <component :is="layout">
    <div class="p-6">
      <h1 class="text-xl sm:text-2xl xl:text-3xl font-bold mb-4">My Events</h1>
      <div v-if="events.length === 0" class="text-gray-500">No events yet.</div>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <button
          @click="createEvent"
          class="flex flex-col items-center justify-center border-2 border-dashed border-gray-400 rounded-lg p-6
          text-gray-700 hover:border-blue-500 hover:bg-blue-50 transition cursor-pointer"
        >
          <span class="text-lg font-semibold">Create New Event</span>
          <span class="text-3xl font-bold mt-2">+</span>
        </button>
        <div v-for="event in events" :key="event.id" class="flex flex-col gap-2 border rounded-lg p-4 pb-10 shadow hover:shadow-lg transition relative">
          <h2 class="text-md sm:text-lg xl:text-xl font-semibold">{{ event.title }}</h2>
          <p class="text-md sm:text-lg xl:text-xl text-gray-600 italic">{{ event.location }}</p>
          <div class="text-xs sm:text-sm xl:text-md text-gray-500">
            {{ formatEventTime(event) }}
          </div>
          <p class="text-xs sm:text-sm xl:text-md text-gray-600">{{ event.description }}</p>
          <div class="absolute bottom-2 right-2 flex gap-2 ">
            <button @click="editEvent(event.id)" class="px-2 py-1 text-xs sm:text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
              Edit
            </button>
            <button @click="deleteEvent(event.id)" class="px-2 py-1 text-xs sm:text-sm bg-red-500 text-white rounded hover:bg-red-600">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </component>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia'
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

  const sameDay = start.toDateString() === end.toDateString()

  if (sameDay) {
    return `${start.toLocaleDateString()} ${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} to ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
  } else {
    return `${start.toLocaleDateString()} ${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} to ${end.toLocaleDateString()} ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
  }
}

const editEvent = (id) => Inertia.get(`/events/${id}/edit`)
const deleteEvent = (id) => {
  if (confirm("Are you sure you want to delete this event?")) {
    Inertia.delete(`/events/${id}`)
  }
}
const createEvent = () => Inertia.get('/events/create')
</script>
