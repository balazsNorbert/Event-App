<template>
  <component :is="layout">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">My Interests Calendar</h1>
      <EventCalendar :events="events" />
    </div>
  </component>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import EventCalendar from '@/Components/EventCalendar.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()
const layout = AuthenticatedLayout
const events = page.props.events
</script>