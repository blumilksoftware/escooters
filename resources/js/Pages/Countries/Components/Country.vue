<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { FolderOpenIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import ErrorMessage from '@/Shared/Components/ErrorMessage.vue'
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
  country: Object,
})

function destroyCountry(countryId) {
  router.delete(`/admin/dashboard/countries/${countryId}`)
}

function updateCountry(countryId) {
  updateCountryForm.patch(`/admin/dashboard/countries/${countryId}`, {
    onSuccess: () => {
      toggleEditDialog()
    },
  })
}

const updateCountryForm = useForm({
  name: props.country.name,
  alternative_name: props.country.alternative_name,
  latitude: props.country.latitude,
  longitude: props.country.longitude,
  iso: props.country.iso,
})

const commaInputError = ref('')

function preventCommaInput(event) {
  if (event.key === ',') {
    event.preventDefault()
    commaInputError.value = 'Use \'.\' instead of \',\''
  }
}

const isEditDialogOpened = ref(false)
const editDialog = ref(null)
onClickOutside(editDialog, () => (isEditDialogOpened.value = false))

function toggleEditDialog() {
  isEditDialogOpened.value = !isEditDialogOpened.value
  commaInputError.value = ''
}


</script>

<template>
  <td class="relative py-4 pl-4 text-sm sm:pl-6 sm:pr-3">
    <div class="flex items-center font-medium text-gray-800">
      <i :class="country.iso" class="flat flag large mr-2 shrink-0" />
      <p class="cursor-pointer break-all">
        {{ country.name }}
      </p>
    </div>
    <div class="mt-1 flex flex-col break-all text-gray-500 xl:hidden">
      <span>{{ country.latitude }},</span>
      <span>{{ country.longitude }}</span>
    </div>
    <div class="absolute -top-px left-6 right-0 h-px bg-gray-200" />
  </td>
  <td class="table-cell break-all border-t border-gray-200 py-3.5 text-sm text-gray-500">
    {{ country.alternative_name }}
  </td>
  <td class="hidden break-all border-t border-gray-200 py-3.5 pl-1 text-sm text-gray-500 xl:table-cell">
    {{ country.latitude }}
  </td>

  <td class="hidden break-all border-t border-gray-200 py-3.5 pl-1 text-sm text-gray-500 xl:table-cell">
    {{ country.longitude }}
  </td>

  <td class="hidden break-all border-t border-gray-200 py-3.5 pl-1 text-sm text-gray-500 xl:table-cell">
    {{ country.iso }}
  </td>

  <td class="relative flex justify-end border-t border-transparent py-3.5 text-right text-xs font-medium sm:pl-3 md:pr-2 xl:pr-0">
    <span class="flex flex-wrap">
      <button class="mx-0.5 mb-1 flex w-fit shrink-0 items-center rounded py-1 pr-2 text-blumilk-500 hover:bg-blumilk-25" @click="toggleEditDialog">
        <PencilIcon class="h-5 w-8 text-blumilk-500" />
        Edit
      </button>

      <button class="mx-0.5 mb-1 flex w-fit shrink-0 items-center rounded py-1 pr-2 text-rose-500 hover:bg-rose-100" @click="destroyCountry(country.id)">
        <TrashIcon class="h-5 w-8 text-rose-500" />
        Delete
      </button>
    </span>
    <div class="absolute -top-px left-0 right-6 h-px bg-gray-200" />
  </td>


  <div v-if="isEditDialogOpened" class="flex flex-col overflow-y-auto">
    <div class="fixed inset-0 z-10 flex items-center overflow-y-auto bg-black/50">
      <div ref="editDialog" class="mx-auto w-11/12 rounded-lg bg-white pb-6 sm:w-5/6 md:w-3/4 lg:w-1/2 xl:w-1/3">
        <div class="flex w-full justify-end">
          <button class="px-4 pt-4" @click="toggleEditDialog()">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form class="flex flex-col rounded px-6 text-xs font-bold text-gray-600" @submit.prevent="updateCountry(country.id)">
          <label class="mb-1">Name</label>
          <input v-model="updateCountryForm.name" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text" required>
          <ErrorMessage :message="updateCountryForm.errors.name" />
          <label class="mb-1 mt-4">Alternative name</label>
          <input v-model="updateCountryForm.alternative_name" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text">
          <ErrorMessage :message="updateCountryForm.errors.alternative_name" />

          <label class="mb-1 mt-4">Latitude</label>
          <input v-model="updateCountryForm.latitude" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text" required @keydown="preventCommaInput">
          <ErrorMessage :message="updateCountryForm.errors.latitude" />

          <label class="mb-1 mt-4">Longitude</label>
          <input v-model="updateCountryForm.longitude" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text" required @keydown="preventCommaInput">
          <ErrorMessage :message="updateCountryForm.errors.longitude" />
          <label class="mb-1 mt-4">ISO</label>
          <input v-model="updateCountryForm.iso" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text" required>
          <ErrorMessage :message="updateCountryForm.errors.iso" />
          <small class="text-rose-600">{{ commaInputError }}</small>

          <div class="flex w-full justify-end">
            <button type="submit" class="mt-3 flex w-full shrink-0 justify-center rounded border border-blumilk-500 bg-white px-5 py-3 text-blumilk-500 hover:bg-blumilk-50 md:w-fit md:py-2">
              <span class="flex flex-wrap items-center justify-center space-x-2">
                <span class="font-bold">Save</span>
                <FolderOpenIcon class="h-5 w-5" />
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
