<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { FolderOpenIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import ErrorMessage from '@/Shared/Components/ErrorMessage.vue'
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
  city: Object,
  providers: Object,
})

function destroyCity(cityId) {
  router.delete(`/admin/dashboard/cities/${cityId}`)
}

function updateCity(cityId) {
  updateCityForm.patch(`/admin/dashboard/cities/${cityId}`, {
    onSuccess: () => {
      toggleEditDialog()
    },
  })
}

const updateCityForm = useForm({
  name: props.city.name,
  latitude: props.city.latitude,
  longitude: props.city.longitude,
})


const storeAlternativeCityNameErrors = ref([])

function storeAlternativeCityName(cityId) {
  router.post('/city-alternative-name', {
    name: storeCityAlternativeNameForm.name,
    city_id: cityId,
  }, {
    onSuccess: () => {
      storeCityAlternativeNameForm.name = ''
      storeAlternativeCityNameErrors.value = []
    },
    onError: (errors) => {
      storeAlternativeCityNameErrors.value = errors
    },
  })
}

const storeCityAlternativeNameForm = reactive({
  name: '',
})

function destroyAlternativeCityName(alternativeCityNameId) {
  router.delete(`/city-alternative-name/${alternativeCityNameId}`, { replace: true })
}

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
  isProvidersFormOpened.value = false
  isCityFormOpened.value = false
  isAlternativeCityNameFormOpened.value = false
}

const selectedCityProviders = reactive([])

onMounted(() => {
  props.city.cityProviders.forEach(provider => {
    selectedCityProviders.push(provider.provider_id)
  })
})

function toggleProviderSelection(provider) {
  if (selectedCityProviders.includes(provider)) {
    const index = selectedCityProviders.indexOf(provider)
    selectedCityProviders.splice(index, 1)
  } else {
    selectedCityProviders.push(provider)
  }
}

function updateCityProviders(cityId) {
  router.patch(`/update-city-providers/${cityId}`, {
    providerIds: selectedCityProviders,
  }, {
    onSuccess: () => {
      toggleEditDialog()
    },
  })
}

const filteredSelectedCityProviders = computed(() => {
  return props.providers.filter(provider => selectedCityProviders.includes(provider.id))
})

function goToGoogleMaps(latitude, longitude) {
  window.open('https://www.google.com/maps/search/' + latitude + ',' + longitude, '_blank')
}

const isCityFormOpened = ref(false)

function toggleCityForm() {
  isCityFormOpened.value = !isCityFormOpened.value

  isAlternativeCityNameFormOpened.value = false
  isProvidersFormOpened.value = false
}

const isAlternativeCityNameFormOpened = ref(false)

function toggleAlternativeCityNameForm() {
  isAlternativeCityNameFormOpened.value = !isAlternativeCityNameFormOpened.value
  isCityFormOpened.value = false
  isProvidersFormOpened.value = false
}

const isProvidersFormOpened = ref(false)

function toggleProvidersForm() {
  isProvidersFormOpened.value = !isProvidersFormOpened.value

  isCityFormOpened.value = false
  isAlternativeCityNameFormOpened.value = false
}

</script>

<template>
  <td class="relative py-4 pl-4 text-sm sm:pl-6 sm:pr-3">
    <div class="flex items-center font-medium text-gray-800">
      <i :class="city.country.iso" class="flat flag large mr-2 shrink-0" />
      <p class="cursor-pointer break-all" @click="goToGoogleMaps(city.latitude, city.longitude)">
        {{ city.name }}
      </p>
    </div>
    <div class="mt-1 flex flex-col break-all text-gray-500 hover:bg-blumilk-25 hover:font-medium sm:block lg:hidden">
      <span>{{ city.latitude }}</span>
      <span class="hidden sm:inline">, </span>
      <br class="hidden sm:inline">
      <span>{{ city.longitude }}</span>
    </div>
    <div class="absolute -top-px left-6 right-0 h-px bg-gray-200" />
  </td>
  <td class="hidden break-all border-t border-gray-200 py-3.5 text-sm text-gray-500 lg:table-cell">
    {{ city.latitude }}
  </td>
  <td class="hidden break-all border-t border-gray-200 py-3.5 pl-1 text-sm text-gray-500 lg:table-cell">
    {{ city.longitude }}
  </td>

  <td class="border-t border-gray-200 py-3.5 text-sm text-gray-500 lg:table-cell">
    <div class="flex lg:hidden">
      <div v-if="selectedCityProviders.length > 0" class="m-1 flex h-5 w-fit items-center justify-center rounded border border-zinc-300 bg-zinc-300 p-1">
        <div class="flex h-5 w-5 items-center justify-center text-xs text-gray-500">
          {{ selectedCityProviders.length }}
        </div>
      </div>
      <div v-else class="m-1 flex h-5 w-fit items-center justify-center p-1">
        <div class="flex h-5 w-5 items-center justify-center text-xs text-gray-500">
          -
        </div>
      </div>
    </div>
    <div class="hidden items-center lg:flex">
      <div class="items-top flex h-1/2 flex-wrap items-center">
        <div
          v-for="provider in filteredSelectedCityProviders.slice(0, 4)"
          :key="provider.id"
          :style="{'background-color': selectedCityProviders.includes(provider.id) ? provider.color : ''}"
          :class="selectedCityProviders.includes(provider.id) ? 'border-zinc-600 drop-shadow-lg' : 'hidden'"
          class="m-1 flex h-5 w-fit items-center justify-center rounded border border-zinc-300 bg-zinc-300 p-1 "
        >
          <img class="w-5" :src="'/providers/' + provider.name + '.png'" alt="">
        </div>

        <div
          v-if="selectedCityProviders.length > 4 "
          class="m-1 flex h-5 w-fit items-center justify-center rounded border border-zinc-300 bg-zinc-300 p-1"
        >
          <div class="flex h-5 w-5 items-center justify-center text-xs text-gray-500">
            +{{ selectedCityProviders.length - 4 }}
          </div>
        </div>
        <div
          v-else-if="selectedCityProviders.length === 0"
          class="m-1 flex h-5 w-fit items-center justify-center p-1"
        >
          <div class="flex h-5 w-5 items-center justify-center text-xs text-gray-500">
            -
          </div>
        </div>
      </div>
    </div>
  </td>

  <td class="relative flex justify-end border-t border-transparent py-3.5 text-right text-xs font-medium sm:pl-3 md:pr-2">
    <span class="flex flex-wrap">
      <button class="mx-0.5 mb-1 flex w-fit shrink-0 items-center rounded py-1 pr-2 text-blumilk-500 hover:bg-blumilk-25"
              @click="toggleEditDialog()"
      >
        <PencilIcon class="h-5 w-8 text-blumilk-500" />
        Edit
      </button>

      <button class="mx-0.5 mb-1 flex w-fit shrink-0 items-center rounded py-1 pr-2 text-rose-500 hover:bg-rose-100"
              @click="destroyCity(city.id)"
      >
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

        <button :class="isCityFormOpened ? 'bg-blumilk-50' : ''" class="mb-3 ml-6 rounded-lg bg-blumilk-25 px-3 py-1 text-sm font-bold text-gray-800 hover:bg-blumilk-50" @click="toggleCityForm">
          Update city
        </button>
        <form v-if="isCityFormOpened" class="flex flex-col rounded px-6 text-xs font-bold text-gray-600"
              @submit.prevent="updateCity(city.id)"
        >
          <label class="mb-1 mt-4">Name</label>
          <input v-model="updateCityForm.name" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text"
                 required
          >
          <ErrorMessage :message="updateCityForm.errors.name" />
          <label class="mb-1 mt-4">Latitude</label>
          <input v-model="updateCityForm.latitude" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text"
                 required @keydown="preventCommaInput"
          >
          <ErrorMessage :message="updateCityForm.errors.latitude" />
          <label class="mb-1 mt-4">Longitude</label>
          <input v-model="updateCityForm.longitude" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text"
                 required @keydown="preventCommaInput"
          >
          <ErrorMessage :message="updateCityForm.errors.longitude" />
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

        <br>
        <button :class="isAlternativeCityNameFormOpened ? 'bg-blumilk-50' : ''" class="mb-3 ml-6 rounded-lg bg-blumilk-25 px-3 py-1 text-sm font-bold text-gray-800 hover:bg-blumilk-50" @click="toggleAlternativeCityNameForm">
          Add alternative city name
        </button>
        <form v-if="isAlternativeCityNameFormOpened" class="flex flex-col rounded p-6"
              @submit.prevent="storeAlternativeCityName(city.id)"
        >
          <div class="flex flex-col">
            <label class="mb-1 mt-4 text-xs font-bold text-gray-600">Alternative name</label>
            <input v-model="storeCityAlternativeNameForm.name" class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3"
                   type="text" required
            >
            <ErrorMessage :message="storeAlternativeCityNameErrors.name" />
            <div class="flex w-full justify-end">
              <button type="submit" class="mt-6 flex w-full shrink-0 justify-center rounded border border-blumilk-500 bg-white px-5 py-3 text-blumilk-500 hover:bg-blumilk-50 md:w-fit md:py-2">
                <span class="flex flex-wrap items-center justify-center space-x-2">
                  <span class="text-xs font-bold">Save</span>
                  <FolderOpenIcon class="h-5 w-5" />
                </span>
              </button>
            </div>
          </div>
        </form>

        <div v-if="isAlternativeCityNameFormOpened" class="flex flex-wrap">
          <div v-for="alternativeName in props.city.city_alternative_names" :key="alternativeName.id" class="ml-6">
            <div class="group flex w-fit cursor-pointer break-all rounded py-1 pl-1 pr-3 text-sm font-bold text-zinc-500 hover:bg-blumilk-25"
                 @click="destroyAlternativeCityName(alternativeName.id)"
            >
              <p class="mr-1">
                {{ alternativeName.name }}
              </p>
              <span class="hidden group-hover:block">
                <XMarkIcon class="h-5 w-5" />
              </span>
            </div>
          </div>
        </div>

        <hr v-if="isAlternativeCityNameFormOpened" class="mx-6 my-2 h-px border-0 bg-gray-300">

        <br>
        <button :class="isProvidersFormOpened ? 'bg-blumilk-50' : ''" class="ml-6 flex rounded-lg bg-blumilk-25 px-3 py-1 text-sm font-bold text-gray-800 hover:bg-blumilk-50" @click="toggleProvidersForm">
          Providers
        </button>

        <div v-if="isProvidersFormOpened" class="mt-4 flex flex-col rounded border-blumilk-100 px-6">
          <div class="flex flex-wrap">
            <div
              v-for="provider in props.providers"
              :key="provider.id"
              :style="{'background-color': selectedCityProviders.includes(provider.id) ? provider.color : ''}"
              :class="selectedCityProviders.includes(provider.id) ? 'border-zinc-600 drop-shadow-lg' : ''"
              class="mx-1 my-2 flex h-10 w-fit cursor-pointer items-center justify-center rounded-lg border border-zinc-300 bg-zinc-300 p-1 "
              @click="toggleProviderSelection(provider.id)"
            >
              <input
                v-model="selectedCityProviders"
                class="hidden"
                type="checkbox"
              >
              <label class="cursor-pointer">
                <img class="w-10" :src="'/providers/' + provider.name + '.png'" alt="">
              </label>
            </div>
          </div>
          <div class="flex w-full justify-end">
            <button type="submit" class="mt-6 flex w-full shrink-0 justify-center rounded border border-blumilk-500 bg-white px-5 py-3 text-blumilk-500 hover:bg-blumilk-100 md:w-fit md:py-2"
                    @click="updateCityProviders(city.id)"
            >
              <span class="flex flex-wrap items-center justify-center space-x-2">
                <span class="text-xs font-bold">Save</span>
                <FolderOpenIcon class="h-5 w-5" />
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
