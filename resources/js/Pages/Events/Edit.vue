<template>
  <component :is="layout">
    <div class="max-w-xl mx-auto p-6">
      <div class="flex flex-col gap-4 p-6 bg-gray-100 rounded-2xl shadow-2xl max-w-md mx-auto">
        <h1 class="text-2xl font-bold mb-2 text-gray-800">Edit Event</h1>
        <form @submit.prevent="submit" class="flex flex-col gap-4">
          <div>
            <label class="font-semibold text-gray-700">Title</label>
            <input v-model="form.title" type="text" class="w-full border border-gray-300 focus:border-none p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
          </div>
          <div>
            <label class="font-semibold text-gray-700">Location</label>
            <input v-model="form.location" type="text" class="w-full border border-gray-300 focus:border-none p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
          </div>
          <div>
            <label class="font-semibold text-gray-700">Start Time</label>
            <input v-model="form.start_time" type="datetime-local" class="w-full border border-gray-300 focus:border-none p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
          </div>
          <div>
            <label class="font-semibold text-gray-700">End Time</label>
            <input v-model="form.end_time" type="datetime-local" class="w-full border border-gray-300 focus:border-none p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
          </div>
          <div>
            <label class="font-semibold text-gray-700">Description</label>
            <textarea v-model="form.description" class="w-full border border-gray-300 focus:border-none p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
          </div>
          <button type="submit" class="w-full px-4 py-2 text-white font-semibold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700">
            Update Event
          </button>
        </form>
      </div>
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
