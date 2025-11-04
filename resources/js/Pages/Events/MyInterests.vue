<template>
  <component :is="layout">
    <div class="flex flex-col gap-6 py-10 px-4 sm:px-10 lg:px-28">
      <h1 class="text-2xl xl:text-3xl font-bold mb-4">My Interests</h1>
      <div class="flex flex-wrap justify-between gap-2 mb-6 w-full">
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-3/4 lg:w-1/2">
          <div class="relative w-full">
            <Search class="absolute top-2 left-2 w-5 h-5 text-gray-400" />
            <input
              v-model="myInterestFilters.search"
              type="text"
              placeholder="Search events..."
              class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
              @input="applyMyInterestFilters"
            />
          </div>
          <label
            class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl px-4 py-2 cursor-pointer shadow-sm transition-all"
          >
            <input
              type="checkbox"
              v-model="myInterestFilters.future"
              class="rounded text-indigo-600 focus:ring-indigo-500"
              @change="applyMyInterestFilters"
            />
            <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Future events only</span>
          </label>
        </div>
        <div class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-2 rounded-xl shadow transition">
          <a
            :href="route('events.myInterestsCalendar')"
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
          You haven't indicated interest in any events yet.
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4">
        <div v-for="event in events" :key="event.id" class="flex flex-col justify-between items-center gap-2 border rounded-2xl p-4 shadow hover:shadow-lg transition w-full">
          <div class="flex flex-col gap-2">
            <div class="-m-4 mb-2">
              <img v-if="event.image" :src="`/storage/${event.image}`" class="w-full rounded-t-2xl" />
            </div>
            <div v-if="event.going_count > 0 || event.interested_count > 0" class="flex gap-1 text-sm xl:text-base text-gray-500 font-semibold">
              <span v-if="event.interested_count > 0">{{ event.interested_count }} Interested</span>
              <span v-if="event.going_count > 0 && event.interested_count > 0">|</span>
              <span v-if="event.going_count > 0">{{ event.going_count }} Going</span>
            </div>
            <h2 class="text-lg xl:text-xl font-semibold">{{ event.title }}</h2>
            <div>
              <a
                :href="route('events.map', { location: event.location , title: event.title })"
                class="flex items-center gap-2 text-blue-600 hover:text-blue-700 underline-offset-2 hover:underline transition-colors w-fit group"
              >
                <span class="text-base xl:text-lg text-blue-600 hover:text-blue-700 italic">
                  {{ event.location }}
                </span>
                <MapPin class="w-5 h-5 group-hover:scale-110 transition-transform duration-300"/>
              </a>
            </div>
            <div class="text-sm xl:text-base text-gray-500">
              {{ formatEventTime(event) }}
            </div>
            <div>
              <p :class="[ 'transition-all duration-300 text-sm xl:text-base', expandedEvents.has(event.id) ? 'line-clamp-none' : 'line-clamp-3']">
                {{ event.description }}
              </p>
              <button
                v-if="event.description && event.description.length > 100"
                @click="toggleDescription(event.id)"
                class="text-blue-600 hover:text-blue-700 text-xs xl:text-sm font-medium mt-1 focus:outline-none relative z-10"
              >
                {{ expandedEvents.has(event.id) ? 'Show less' : 'Show more' }}
              </button>
            </div>
          </div>
          <div class="flex gap-2 text-sm">
            <button @click="rsvp(event.id, 'going')" :class="[event.user_status === 'going' ? 'bg-green-500' : 'bg-green-500/50 hover:bg-green-500','px-3 py-2  text-white rounded']">Going</button>
            <button @click="rsvp(event.id, 'interested')" :class="[event.user_status === 'interested' ? 'bg-yellow-500' : 'bg-yellow-500/50 hover:bg-yellow-500','px-3 py-2 text-white rounded']">Interested</button>
            <button @click="rsvp(event.id, 'not_going')" :class="[event.user_status === 'not_going' ? 'bg-red-500' : 'bg-red-500/50 hover:bg-red-500','px-3 py-2 text-white rounded']">Not Going</button>
          </div>
        </div>
      </div>
    </div>
  </component>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router } from '@inertiajs/core'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Search, MapPin, Calendar } from 'lucide-vue-next'

const props = defineProps({
  events: Array,
  filters: Object
})
const page = usePage();
const layout = AuthenticatedLayout

const myInterestFilters = ref({
  search: page.props.filters?.search || '',
  future: page.props.filters?.future || false,
})

const formatEventTime = (event) => {
  const start = new Date(event.start_time)
  const end = new Date(event.end_time)

  if (start.toDateString() === end.toDateString()) {
    return `${start.toLocaleDateString()} ${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} to ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
  } else {
    return `${start.toLocaleDateString()} ${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} to ${end.toLocaleDateString()} ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
  }
}

const events = ref(props.events.map(e => ({ ...e })))

const rsvp = (eventId, status) => {
  router.post(`/events/${eventId}/rsvp`, { status }, {
    onSuccess: () => {
      const index = events.value.findIndex(e => e.id === eventId)
      if (index !== -1) {
        if (status === 'not_going') {
          events.value.splice(index, 1)
        } else {
          events.value[index].user_status = status
        }
      }
    }
  })
}

const expandedEvents = ref(new Set())

const toggleDescription = (eventId) => {
  if (expandedEvents.value.has(eventId)) {
    expandedEvents.value.delete(eventId)
  } else {
    expandedEvents.value.add(eventId)
  }
}

const applyMyInterestFilters = () => {
  router.get('/my-interests', myInterestFilters.value, { preserveState: true, replace: true })
}

</script>
