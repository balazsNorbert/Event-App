<template>
  <component :is="layout">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Events Calendar</h1>
      <EventCalendar :events="events" />
    </div>
  </component>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import EventCalendar from '@/Components/EventCalendar.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const page = usePage()
const layout = page.props.auth?.user ? AuthenticatedLayout : GuestLayout
const events = page.props.events
</script>