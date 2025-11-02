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
        <div class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-2 rounded-xl shadow transition">
          <a
            :href="route('events.eventsCalendar')"
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
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        <div v-for="event in events" :key="event.id" class="flex flex-col justify-between items-center gap-2 border rounded-lg p-4 shadow hover:shadow-lg transition w-full">
          <div class="flex flex-col gap-2">
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
                <MapPin class="w-5 h-5"/>
                <span class="underline-offset-2 hover:underline">View on Map</span>
              </a>
            </div>
            <div class="text-xs sm:text-sm xl:text-md text-gray-500">
              {{ formatEventTime(event) }}
            </div>
            <p class="text-xs sm:text-sm xl:text-md text-gray-600">{{ event.description }}</p>
          </div>
          <div class="flex gap-2 text-sm">
            <button @click="rsvp(event.id, 'going')" :class="[event.user_status === 'going' ? 'bg-green-500' : 'bg-green-500/50 hover:bg-green-500','px-2 py-1  text-white rounded']">Going</button>
            <button @click="rsvp(event.id, 'interested')" :class="[event.user_status === 'interested' ? 'bg-yellow-500' : 'bg-yellow-500/50 hover:bg-yellow-500','px-2 py-1 text-white rounded']">Interested</button>
            <button @click="rsvp(event.id, 'not_going')" :class="[event.user_status === 'not_going' ? 'bg-red-500' : 'bg-red-500/50 hover:bg-red-500','px-2 py-1 text-white rounded']">Not Going</button>
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
import { Search, MapPin, Calendar } from 'lucide-vue-next'

const props = defineProps({
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

const events = ref(props.events.map(e => ({ ...e })))

const rsvp = (eventId, status) => {
  router.post(`/events/${eventId}/rsvp`, { status }, {
    onSuccess: () => {
      const event = events.value.find(e => e.id === eventId)
      if (event) event.user_status = status
    }
  })
}

const applyEventFilters = () => {
  router.get('/events', eventFilters.value, { preserveState: true, replace: true })
}

</script>