<template>
  <div class="rounded-xl shadow p-4 bg-white">
    <FullCalendar
      :options="calendarOptions"
    />
  </div>
</template>

<script setup>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  events: Array,
})

const calendarOptions = computed(() => ({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  events: props.events.map(event => ({
    title: event.title,
    start: event.start_time,
    end: event.end_time,
  })),
}))
</script>
