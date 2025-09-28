<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Events</h1>
    <ul>
      <li v-for="event in events" :key="event.id" class="mb-2 border-b pb-2">
        <strong>{{ event.title }}</strong> - {{ event.location }} ({{ event.start_time }})
      </li>
    </ul>
  </div>
</template>
<script setup>
  defineProps({
    events: Array
  })
</script>