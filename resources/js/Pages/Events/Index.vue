<template>
  <component :is="layout">
    <div class="flex flex-col gap-6 p-6">
      <h1 class="text-xl sm:text-2xl xl:text-3xl font-bold mb-4">All Events</h1>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div class="relative w-full sm:w-1/2">
          <Search class="absolute top-2 left-2 w-5 h-5 text-gray-400" />
          <input
            v-model="eventFilters.search"
            type="text"
            placeholder="Search events..."
            class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
            @input="applyEventFilters"
          />
        </div>
        <label
          class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-4 py-2 cursor-pointer shadow-sm transition-all"
        >
          <input
            type="checkbox"
            v-model="eventFilters.future"
            class="rounded text-indigo-600 focus:ring-indigo-500"
            @change="applyEventFilters"
          />
          <span class="text-sm font-medium text-gray-700">Future events only</span>
        </label>
      </div>
      <div v-if="events.length === 0" class="text-gray-700">
        <div v-if="filters.search || filters.future">
          No events match your search criteria.
        </div>
        <div v-else>
          No events yet.
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        <div v-for="event in events" :key="event.id" class="flex flex-col gap-2 border rounded-lg p-4 pb-12 shadow hover:shadow-lg transition w-full relative">
          <h2 class="text-md sm:text-lg xl:text-xl font-semibold">{{ event.title }}</h2>
          <img v-if="event.image" :src="`/storage/${event.image}`" class="w-full rounded" />
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
import { ref } from 'vue'
import { Search } from 'lucide-vue-next'

defineProps({
  events: Array,
  filters: Object,
})

const page = usePage()
const layout = page.props.auth?.user ? AuthenticatedLayout : GuestLayout


const eventFilters = ref({
  search: page.props.filters?.search || '',
  future: page.props.filters?.future || false,
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

const applyEventFilters = () => {
  router.get('/events', eventFilters.value, { preserveState: true, replace: true })
}

</script>