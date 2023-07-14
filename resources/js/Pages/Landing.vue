<script setup>
import Map from '@/Shared/Components/Map.vue'
import { computed, onMounted, reactive, ref } from 'vue'
import Info from '@/Shared/Components/Info.vue'
import SearchPanel from '@/Shared/Components/SearchPanel.vue'
import Nav from '@/Shared/Layout/Nav.vue'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'
import { XMarkIcon, MapIcon } from '@heroicons/vue/24/outline'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import SearchPanelSkeleton from '../Shared/Components/SearchPanelSkeleton.vue'

import { useMapMarkerStore } from '../Shared/Stores/MapMarkerStore'
const mapMarkerStore = useMapMarkerStore()

const breakpoints = useBreakpoints(breakpointsTailwind)
const showInfo = ref(true)
const isMobile = ref(breakpoints.smaller('lg'))
const isDesktop = ref(breakpoints.greaterOrEqual('lg'))
const showMap = ref(false)

function switchPanel() {
  showInfo.value = !showInfo.value
}

function switchMap() {
  showMap.value = !showMap.value
}

const nav = ref(null)

const page = usePage()
const isAuth = computed(() => page.props.auth.isAuth)


const data = reactive({
  cities: [],
  providers: [],
  countries: [],
})


async function fetchProviders() {
  await axios.get('/api/providers').then(response => {
    data.cities = response.data.cities
    data.providers = response.data.providers
    data.countries = response.data.countries
  })
}

onMounted(async () => {
  await fetchProviders()
})

</script>

<template>
  <div class="flex h-screen flex-col">
    <Nav ref="nav" class="z-30" />

    <div class="relative mt-16 flex grow flex-col lg:flex-row">
      <div v-if="!showMap || (showMap && isDesktop)" class="grow lg:w-1/2">
        <Info v-if="showInfo && !isAuth" @create-account="nav.toggleCreateAccountOption()" @try-it-out="switchPanel" />

        <div v-else class="w-full">
          <SearchPanel v-if="data.providers.length" :cities="data.cities" :providers="data.providers" :countries="data.countries" />
          <SearchPanelSkeleton v-else />
        </div>
      </div>

      <div v-show="isDesktop || (showMap && isMobile)" class="relative h-full lg:w-1/2">
        <Map v-if="data.providers.length" :key="`${mapMarkerStore.selectedCountryId}-${mapMarkerStore.selectedProviderId}`" :cities="data.cities" class="z-10" />
        <div v-else class="flex h-full flex-col items-center justify-center bg-blumilk-25" aria-label="Loading..." role="status">
          <svg class="h-24 w-24 animate-spin" viewBox="3 3 18 18">
            <path
              class="fill-gray-200"
              d="M12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5ZM3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12Z"
            />
            <path
              class="fill-gray-800"
              d="M16.9497 7.05015C14.2161 4.31648 9.78392 4.31648 7.05025 7.05015C6.65973 7.44067 6.02656 7.44067 5.63604 7.05015C5.24551 6.65962 5.24551 6.02646 5.63604 5.63593C9.15076 2.12121 14.8492 2.12121 18.364 5.63593C18.7545 6.02646 18.7545 6.65962 18.364 7.05015C17.9734 7.44067 17.3403 7.44067 16.9497 7.05015Z"
            />
          </svg>
          <p class="mt-4 text-xs font-medium text-gray-400">
            Filling map with providers...
          </p>
        </div>
      </div>


      <div v-if="(!showInfo && isMobile) || (isAuth && isMobile)" class="flex justify-center">
        <button class="hover:blumilk-600 fixed bottom-5 z-20 flex items-center justify-center rounded-full bg-blumilk-500 px-2 py-1.5 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" @click="switchMap">
          <XMarkIcon v-if="showMap" class="h-6 w-6" />
          <MapIcon v-else class="h-6 w-6" />
        </button>
      </div>
    </div>
  </div>
</template>
