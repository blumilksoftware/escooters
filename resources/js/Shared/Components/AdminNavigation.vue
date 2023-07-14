<script setup>
import { Link } from '@inertiajs/vue3'
import { ChartBarIcon, ClipboardIcon, FlagIcon, MapPinIcon } from '@heroicons/vue/24/solid'
import { Bars3Icon } from '@heroicons/vue/24/outline'

const props = defineProps({
  url: String,
})

</script>

<template>
  <div class="fixed z-10 flex w-full border-blumilk-50 bg-blumilk-25 shadow md:h-full md:w-1/3 md:border-r lg:w-1/4 xl:w-1/6">
    <div class="flex w-full justify-between md:flex-col md:justify-normal">
      <Link href="/" class="mt-3 flex shrink-0 items-center pb-3 pl-6 pr-2">
        <img class="h-10" src="@/assets/scooter.png" alt="escooter logo">
        <span class="ml-3 hidden text-2xl font-semibold text-gray-800 md:flex">e&#8209;scooters</span>
      </Link>

      <div class="mr-3.5 flex sm:hidden">
        <button type="button" class="inline-flex items-center justify-center rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = true">
          <span class="sr-only">Open main menu</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>
      </div>

      <ul class="hidden h-full items-center text-sm font-medium text-gray-800 sm:flex md:mt-12 md:flex-col md:items-stretch md:space-y-2">
        <Link class="flex h-full md:h-fit" href="#">
          <li
            :class="props.url === '/admin/dashboard' ? 'bg-blumilk-50' : ''"
            class="mx-auto flex w-11/12 items-center bg-blumilk-25 px-6 hover:bg-blumilk-50 md:rounded-lg md:px-2 md:py-3"
          >
            <ClipboardIcon class="h-7 w-7" />
            <span class="ml-3 hidden md:flex">Dashboard</span>
          </li>
        </Link>

        <Link class="flex h-full md:h-fit" href="/admin/dashboard/countries">
          <li :class="$page.url.startsWith('/admin/dashboard/countries')? 'bg-blumilk-50' : ''"
              class="mx-auto flex w-11/12 items-center bg-blumilk-25 px-6 hover:bg-blumilk-50 md:rounded-lg md:px-2 md:py-3"
          >
            <FlagIcon class="h-7 w-7" />
            <span class="ml-3 hidden md:flex">Countries</span>
          </li>
        </Link>
        <Link class="flex h-full md:h-fit" href="/admin/dashboard/cities">
          <li :class="$page.url.startsWith('/admin/dashboard/cities') ? 'bg-blumilk-50' : ''"
              class="mx-auto flex w-11/12 items-center bg-blumilk-25 px-6 hover:bg-blumilk-50 md:rounded-lg md:px-2 md:py-3"
          >
            <MapPinIcon class="h-7 w-7" />
            <span class="ml-3 hidden md:flex">Cities</span>
          </li>
        </Link>

        <Link class="flex h-full md:h-fit" href="#">
          <li
            :class="props.url === '/admin/dashboard/statistics' ? 'bg-blumilk-50' : ''"
            class="mx-auto flex w-11/12 items-center bg-blumilk-25 px-6 hover:bg-blumilk-50 md:rounded-lg md:px-2 md:py-3"
          >
            <ChartBarIcon class="h-7 w-7" />
            <span class="ml-3 hidden md:flex">Statistics</span>
          </li>
        </Link>
      </ul>
    </div>
  </div>
</template>

