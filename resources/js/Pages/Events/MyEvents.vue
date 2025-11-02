<template>
  <component :is="layout">
    <div class="flex flex-col gap-6 p-6">
      <h1 class="text-xl sm:text-2xl xl:text-3xl font-bold mb-4">My Events</h1>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div class="relative w-full sm:w-1/2">
          <Search class="absolute top-2 left-2 w-5 h-5 text-gray-400" />
          <input
          v-model="myEventFilters.search"
          type="text"
            placeholder="Search events..."
            class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
            @input="applyMyEventFilters"
            />
          </div>
          <label
          class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-4 py-2 cursor-pointer shadow-sm transition-all"
          >
          <input
          type="checkbox"
          v-model="myEventFilters.future"
          class="rounded text-indigo-600 focus:ring-indigo-500"
          @change="applyMyEventFilters"
          />
          <span class="text-sm font-medium text-gray-700">Future events only</span>
        </label>
        <div class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-2 rounded-xl shadow transition">
          <a
            :href="route('events.myEventsCalendar')"
            class="flex items-center gap-2"
          >
            <Calendar class="w-5 h-5" />
            View Calendar
          </a>
        </div>
      </div>
      <div v-if="events.length === 0" class="text-gray-700">
        <div v-if="filters.search || filters.future">
          No events match your search criteria.
        </div>
        <div v-else>
          No events yet.
        </div>
      </div>
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
          <img v-if="event.image" :src="`/storage/${event.image}`" class="w-full rounded" />
          <div v-if="event.going_count > 0 || event.interested_count > 0" class="flex gap-1 text-sm sm:text-md xl:text-lg text-gray-500 font-semibold">
            <span v-if="event.interested_count > 0">{{ event.interested_count }} Interested</span>
            <span v-if="event.going_count > 0 && event.interested_count > 0">|</span>
            <span v-if="event.going_count > 0">{{ event.going_count }} Going</span>
          </div>
          <div class="flex flex-wrap justify-between md:items-center">
            <p class="text-md sm:text-lg xl:text-xl text-gray-600 italic">
              {{ event.location }}
            </p>
            <a
              :href="route('events.map', { location: event.location , title: event.title })"
              class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors ml-2"
            >
              <MapPin class="w-5 h-5" />
              <span class="underline-offset-2 hover:underline">View on Map</span>
            </a>
          </div>
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
import { router } from '@inertiajs/core'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Search, MapPin, Calendar } from 'lucide-vue-next'

defineProps({
  events: Array,
  filters: Object,
})

const page = usePage()
const layout = AuthenticatedLayout

const myEventFilters = ref({
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

const applyMyEventFilters = () => {
  router.get('/my-events', myEventFilters.value, { preserveState: true, replace: true })
}

const editEvent = (id) => router.get(`/events/${id}/edit`)
const deleteEvent = (id) => {
  if (confirm("Are you sure you want to delete this event?")) {
    router.delete(`/events/${id}`)
  }
}
const createEvent = () => router.get('/events/create')


</script>
