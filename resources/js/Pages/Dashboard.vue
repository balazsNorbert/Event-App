<template>
    <AuthenticatedLayout>
        <div class="flex flex-col gap-6 py-10 px-4 sm:px-10 lg:px-28">
          <h1 class="text-2xl xl:text-3xl font-bold mb-4">
            Welcome back, {{ user.name }}!
          </h1>
          <h2 class="text-xl font-semibold mb-2 text-gray-700">Upcoming Events</h2>
          <div v-if="upcomingEvents.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            <div v-for="event in upcomingEvents" :key="event.id" class="p-4 border rounded-xl shadow hover:shadow-lg transition flex flex-col justify-between">
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
          <div v-else class="text-gray-500 mb-6">
            No upcoming events found.
          </div>
          <div class="text-center">
            <a
              :href="route('events.index')"
              class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl shadow transition"
            >
              <Calendar class="w-5 h-5" />
              View all events
            </a>
          </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Calendar, MapPin } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/core'

const props = defineProps({
  user: Object,
  events: Array
})

const expandedEvents = ref(new Set())

const toggleDescription = (eventId) => {
  if (expandedEvents.value.has(eventId)) {
    expandedEvents.value.delete(eventId)
  } else {
    expandedEvents.value.add(eventId)
  }
  expandedEvents.value = new Set(expandedEvents.value)
}

const upcomingEvents = computed(() =>
  [...events.value]
    .filter(e => new Date(e.start_time) > new Date())
    .slice(0, 3)
)

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
</script>