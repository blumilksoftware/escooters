<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="flex items-center space-x-4 bg-gray-100 p-2">
    <Link href="/admin/dashboard/countries" class="border bg-gray-50 p-4">
      Countries
    </Link>
    <Link href="/admin/dashboard/cities" class="border bg-gray-50 p-4">
      Cities
    </Link>
    <Link href="/run-importers" class="rounded-lg border bg-cyan-700 p-4 font-bold text-white">
      Run importers
    </Link>
  </div>
</template>
