<template>
  <div>
    <h1>My Events</h1>
    <ul>
      <li v-for="event in events" :key="event.id">
        {{ event.title }} - {{ event.date }}
        <button @click="editEvent(event.id)">Edit</button>
        <button @click="deleteEvent(event.id)">Delete</button>
      </li>
    </ul>
    <button @click="createEvent">Create New Event</button>
  </div>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia'

defineProps({
  events: Array
})

const editEvent = (id) => Inertia.get(`/events/${id}/edit`)
const deleteEvent = (id) => {
  if (confirm("Are you sure you want to delete this event?")) {
    Inertia.delete(`/events/${id}`)
  }
}
const createEvent = () => Inertia.get('/events/create')
</script>
