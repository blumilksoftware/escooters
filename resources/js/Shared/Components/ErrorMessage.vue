<script setup>

defineProps({
  message: {
    type: String,
  },
})
</script>

<template>
  <div v-if="message" class="mt-1 text-xs text-red-500">
    {{ message }}
  </div>
</template>

