<template>
  <component :is="layout">
    <div class="max-w-xl mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Edit Event</h1>
      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block font-medium">Title</label>
          <input v-model="form.title" type="text" class="w-full border p-2 rounded" />
        </div>
        <div class="mb-4">
          <label class="block font-medium">Description</label>
          <textarea v-model="form.description" class="w-full border p-2 rounded"></textarea>
        </div>
        <div class="mb-4">
          <label class="block font-medium">Location</label>
          <input v-model="form.location" type="text" class="w-full border p-2 rounded" />
        </div>
        <div class="mb-4">
          <label class="block font-medium">Start Time</label>
          <input v-model="form.start_time" type="datetime-local" class="w-full border p-2 rounded" />
        </div>
        <div class="mb-4">
          <label class="block font-medium">End Time</label>
          <input v-model="form.end_time" type="datetime-local" class="w-full border p-2 rounded" />
        </div>
        <button type="submit" class="px-4 py-2 rounded">
          Update Event
        </button>
      </form>
    </div>
  </component>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { reactive } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = usePage().props
const event = props.event
const layout = usePage().props.auth?.user ? AuthenticatedLayout : GuestLayout

const form = reactive({
  title: event.title,
  description: event.description,
  location: event.location,
  start_time: event.start_time,
  end_time: event.end_time,
})

const submit = () => {
  router.put(`/events/${event.id}`, form)
}
</script>
