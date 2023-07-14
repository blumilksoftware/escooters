<script setup>
import City from './Components/City.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AdminNavigation from '@/Shared/Components/AdminNavigation.vue'
import { FolderOpenIcon, XMarkIcon,  MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import ErrorMessage from '@/Shared/Components/ErrorMessage.vue'
import { onClickOutside } from '@vueuse/core'

const page = usePage()

const props = defineProps({
  cities: Object,
  providers: Object,
  countries: Object,
})

const storeErrors = ref([])

const commaInputError = ref('')

function storeCity() {
  commaInputError.value = ''
  storeCityForm.post('/admin/dashboard/cities', {
    onSuccess: () => {
      storeCityForm.reset()
      toggleStoreDialog()
    },
    onError: (errors) => {
      storeErrors.value = errors
    },
  })
}

const storeCityForm = useForm({
  name: '',
  latitude: '',
  longitude: '',
  country_id: '',
})

const isStoreDialogOpened = ref(false)
const storeDialog = ref(null)
onClickOutside(storeDialog, () => (isStoreDialogOpened.value = false))

function toggleStoreDialog() {
  isStoreDialogOpened.value = !isStoreDialogOpened.value
}


function preventCommaInput(event) {
  if (event.key === ',') {
    event.preventDefault()
    commaInputError.value = 'Use \'.\' instead of \',\''
  }
}

const searchInput = ref('')

const filteredCities = computed(() => {
  return props.cities.data.filter(city => {
    if (city.name.toLowerCase().includes(searchInput.value.toLowerCase()) || city.city_alternative_names.some(alternativeName =>
      alternativeName.name.toLowerCase().includes(searchInput.value.toLowerCase()))) {
      return true
    }

    return false
  })
})

function clearInput() {
  searchInput.value = ''
}

</script>

<template>
  <div class="flex h-full min-h-screen flex-col md:flex-row">
    <AdminNavigation :url="page.url" />

    <div class="flex w-full md:justify-end">
      <div class="mt-16 flex h-full w-full flex-col justify-between md:mt-0 md:w-2/3 lg:w-3/4 xl:w-5/6">
        <div class="m-4 flex flex-col lg:mx-8">
          <div v-if="isStoreDialogOpened" class="fixed inset-0 z-50 flex items-center bg-black/50">
            <div ref="storeDialog" class="mx-auto w-11/12 rounded-lg bg-white shadow-lg sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/3">
              <div class="flex w-full justify-end">
                <button class="px-4 pt-4" @click="toggleStoreDialog">
                  <XMarkIcon class="h-6 w-6" />
                </button>
              </div>

              <div class="flex flex-col p-6 pt-0">
                <h1 class="mb-3 text-lg font-bold text-gray-800">
                  Create city
                </h1>

                <form class="flex flex-col text-xs font-bold text-gray-600" @submit.prevent="storeCity">
                  <label class="mb-1 mt-4">Name</label>
                  <input v-model="storeCityForm.name" class="rounded-md border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 md:p-3" type="text" required>
                  <ErrorMessage :message="storeCityForm.errors.name" />

                  <label class="mb-1 mt-4">Latitude</label>
                  <input v-model="storeCityForm.latitude" class="rounded-md border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text"
                         required @keydown="preventCommaInput"
                  >
                  <ErrorMessage :message="storeCityForm.errors.latitude" />

                  <label class="mb-1 mt-4">Longitude</label>
                  <input v-model="storeCityForm.longitude" class="rounded-md border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3" type="text"
                         required @keydown="preventCommaInput"
                  >
                  <ErrorMessage :message="storeCityForm.errors.longitude" />
                  <p v-if="commaInputError" class="text-xs text-rose-600">
                    {{ commaInputError }}
                  </p>
                  <label class="mb-1 mt-4">Country</label>
                  <select v-model="storeCityForm.country_id" required class="rounded-md border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3">
                    <option v-for="country in props.countries" :key="country.id" class="m-6 p-6 " :value="country.id">
                      {{ country.name }}
                    </option>
                  </select>

                  <div class="flex w-full justify-end">
                    <button type="submit" class="mt-4 flex w-full shrink-0 justify-center rounded bg-emerald-500 px-5 py-3 text-white hover:bg-emerald-600 md:w-fit md:justify-start md:py-2">
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


          <div class="mb-3 mt-4 flex flex-wrap items-center justify-end md:justify-between">
            <button class="m-1 rounded bg-blumilk-500 px-5 py-3 text-sm font-medium text-white shadow-md md:py-2" @click="toggleStoreDialog">
              Create city
            </button>

            <div class="m-1 flex w-full rounded-md shadow-sm md:w-fit">
              <div class="relative flex grow items-stretch focus-within:z-10">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <MagnifyingGlassIcon class="h-5 w-5 text-gray-800" />
                </div>
                <input id="email" v-model.trim="searchInput" type="email" name="email" class="block w-full rounded border-0 py-3 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blumilk-300 sm:text-sm sm:leading-6 md:py-1.5" placeholder="Search city">
              </div>
              <button v-if="searchInput.length" type="button" class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-gray-800 ring-1 ring-inset ring-gray-300 hover:bg-blumilk-25" @click="clearInput">
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between border-t border-gray-200 bg-white py-3 ">
            <div class="flex flex-1 justify-between sm:hidden">
              <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
              <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ cities.meta.from }}</span>
                  to
                  <span class="font-medium"> {{ cities.meta.to }}</span>
                  of
                  <span class="font-medium">{{ cities.meta.total }}</span>
                  results
                </p>
              </div>
              <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                  <Link v-if="cities.links.prev" :href="cities.links.prev" class="relative inline-flex items-center rounded-l-md p-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                  </Link>

                  <Link v-for="(link, index) in cities.meta.links.slice(1, -1)" :key="index"
                        :href="link.url ? link.url : cities.links.first"
                        :class="{'bg-blumilk-50': link.active}"
                        class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-600 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex"
                  >
                    {{ link.label }}
                  </Link>

                  <Link v-if="cities.links.next" :href="cities.links.next" class="relative inline-flex items-center rounded-r-md p-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:bg-gray-200">
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                  </Link>
                </nav>
              </div>
            </div>
          </div>
          <div v-if="filteredCities.length" class="rounded-lg ring-gray-300 sm:ring-1">
            <table class="min-w-full">
              <thead>
                <tr>
                  <th scope="col" class="py-3.5 pl-5 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:table-cell">
                    Name
                  </th>
                  <th scope="col" class="hidden py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">
                    Longitude
                  </th>
                  <th scope="col" class="hidden py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">
                    Latitude
                  </th>
                  <th scope="col" class="py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">
                    Providers
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="city in filteredCities" :key="city.id">
                  <City :providers="providers" :city="city" />
                </tr>
              </tbody>
            </table>
          </div>

          <div v-else>
            <p class="mt-6 text-lg font-medium text-gray-500">
              Sorry, we couldn't find any cities.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
