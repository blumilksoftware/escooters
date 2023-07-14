<script setup>
import { defineProps, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { TrashIcon  } from '@heroicons/vue/24/solid'
const props = defineProps({
  notes: Array,
})

const noteText = ref('')

const saveNotes = () => {
  const NewNote = props.notes[props.notes.length - 1]
  const noteText = NewNote ? [NewNote.text] : []

  Inertia.post('/notes', { noteText })
}

const addNote = () => {
  if (noteText.value) {
    // eslint-disable-next-line vue/no-mutating-props
    props.notes.push({ text: noteText.value })
    noteText.value = ''
    saveNotes()
  }
}

</script>


<template>
  <div>
    <div class="note-container mb-4 flex">
      <div class="relative w-3/4">
        <textarea v-model="noteText" class="note-input h-24 w-full px-2 py-1" placeholder="Write your note here" />
        <button class="note-button absolute right-2 top-1/2 -translate-y-1/2 rounded bg-green-500 p-3 text-sm font-semibold text-white" @click="addNote">
          Add Note
        </button>
      </div>
    </div>
    <ul class="note-list">
      <li v-for="(note, index) in props.notes" :key="index" class="note-item mb-4">
        <div class="relative w-1/4 rounded-lg border border-gray-300 bg-gray-100 p-4">
          <p class="grow">
            {{ note.text }}
          </p>
          <div class="absolute right-2 top-2">
            <InertiaLink
              as="button"
              method="delete"
              :preserve-scroll="true"
              :href="`/notes/${note.id}`"
              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full px-4 py-2 text-left text-sm font-medium']"
            >
              <TrashIcon class="mr-2 h-7 w-7 text-red-500" aria-hidden="true" />
            </InertiaLink>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>
