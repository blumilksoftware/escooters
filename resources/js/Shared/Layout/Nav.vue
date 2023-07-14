<script setup>
import { computed, ref } from 'vue'
import { Dialog, DialogPanel } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon, UserCircleIcon, ArrowRightOnRectangleIcon, ComputerDesktopIcon } from '@heroicons/vue/24/outline'
import { Link, router, usePage } from '@inertiajs/vue3'
import { onClickOutside } from '@vueuse/core'
import { useForm } from '@inertiajs/vue3'
import ErrorMessage from '@/Shared/Components/ErrorMessage.vue'


const page = usePage()
const isAuth= computed(() => page.props.auth.isAuth)
const isAdmin = computed(() => page.props.auth.isAdmin)

const registerForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function register() {
  registerForm.post('/register', {
    onSuccess: () => {
      toggleAuthDialog()
      registerForm.reset()
    },
  })
}

const loginForm = useForm({
  email: '',
  password: '',
})

function login() {
  loginForm.post('/login', {
    onSuccess: () => {
      toggleAuthDialog()
      loginForm.reset()
    },
  })
}

function logout() {
  router.post('/logout')
  isMobileMenuOpened.value = false
}

const navigation = [
  { name: 'Prices', href: '#' },
  { name: 'Find a ride', href: '#' },
  { name: 'Rules', href: '#' },
  { name: 'Run importers', href: '/run-importers' },
]

const isMobileMenuOpened = ref(false)

function toggleMobileMenu() {
  isMobileMenuOpened.value = !isMobileMenuOpened.value
}

const isAuthDialogOpened = ref(false)
const authDialog = ref(null)
onClickOutside(authDialog, () => (isAuthDialogOpened.value = false))

function toggleAuthDialog() {
  isLoginFormSelected.value = true
  isAuthDialogOpened.value = !isAuthDialogOpened.value
  isMobileMenuOpened.value = false
}

const isLoginFormSelected = ref(true)

function toggleAuthOption() {
  isLoginFormSelected.value = !isLoginFormSelected.value
  loginForm.reset()
  loginForm.errors = []

  registerForm.reset()
  registerForm.errors = []
}

function toggleCreateAccountOption() {
  toggleAuthDialog()
  isLoginFormSelected.value = false
}

defineExpose({
  toggleCreateAccountOption,
})

</script>

<template>
  <header class="fixed w-full bg-white">
    <nav class="mx-auto flex items-center justify-between px-6 py-3" aria-label="Global">
      <Link href="/" class="flex items-center justify-center">
        <img class="h-10" src="@/assets/scooter.png" alt="escooter logo">
        <span class="ml-3 hidden text-2xl font-semibold text-gray-800 sm:flex">e&#8209;scooters</span>
      </Link>
      <div class="flex md:hidden">
        <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700" @click="toggleMobileMenu">
          <span class="sr-only">Open main menu</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>
      </div>
      <div class="hidden items-center md:flex md:gap-x-12">
        <Link v-for="item in navigation" :key="item.name" :href="item.href" class="text-sm font-medium leading-6 text-gray-800 lg:text-base">
          {{ item.name }}
        </Link>
        <Link v-if="isAdmin" href="/admin/dashboard/cities">
          <ComputerDesktopIcon class="h-6 w-6" />
        </Link>
        <button>
          <ArrowRightOnRectangleIcon v-if="isAuth" class="h-6 w-6" @click="logout" />
          <UserCircleIcon v-else class="h-6 w-6" @click="toggleAuthDialog" />
        </button>
      </div>
    </nav>

    <div v-if="isAuthDialogOpened" class="fixed inset-0 z-50 flex items-center bg-black/50">
      <div ref="authDialog" class="mx-auto w-11/12 rounded-lg bg-white shadow-lg sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/3">
        <div class="flex w-full justify-end">
          <button class="p-4" @click="toggleAuthDialog">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>
        <div v-if="isLoginFormSelected" class="rounded-lg px-6 pb-8">
          <form class="space-y-5" @submit.prevent="login">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-800">Email</label>
              <input v-model="loginForm.email" type="email" class="w-full rounded-lg border-blumilk-200 py-3 md:p-2" required>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-800">Password</label>
              <input v-model="loginForm.password" type="password" class="w-full rounded-lg border-blumilk-200 py-3 md:p-2" required>
              <ErrorMessage :message="loginForm.errors.loginError" />
            </div>
            <div class="flex w-full md:w-fit">
              <button type="submit" class="w-full rounded-lg bg-blumilk-500 p-4 font-semibold text-white hover:bg-blumilk-600 md:py-2">
                Log in
              </button>
            </div>
          </form>
          <button :disabled="loginForm.processing" class="mt-6 text-xs font-light" @click="toggleAuthOption">
            Don't have an account? <span class="font-normal">Sign up</span>
          </button>
        </div>

        <div v-else class="rounded-lg px-6 pb-8">
          <form class="space-y-5" @submit.prevent="register">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-800">Name</label>
              <input v-model="registerForm.name" type="text" class="w-full rounded-lg border-blumilk-200 py-3 md:p-2" required>
              <ErrorMessage :message="registerForm.errors.name" />
            </div>

            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-800">Email</label>
              <input v-model="registerForm.email" type="email" class="w-full rounded-lg border-blumilk-200 py-3 md:p-2" required>
              <ErrorMessage :message="registerForm.errors.email" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-800">Password</label>
              <input v-model="registerForm.password" type="password" class="w-full rounded-lg border-blumilk-200 py-3 md:p-2" required>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-800">Confirm password</label>
              <input v-model="registerForm.password_confirmation" type="password" class="w-full rounded-lg border-blumilk-200 py-3 md:p-2" required>
              <ErrorMessage :message="registerForm.errors.password" />
            </div>
            <div class="flex w-full md:w-fit">
              <button type="submit" class="w-full rounded-lg bg-blumilk-500 p-4 font-semibold text-white hover:bg-blumilk-600 md:py-2">
                Sign up
              </button>
            </div>
          </form>
          <button :disabled="registerForm.processing" class="mt-6 text-xs font-light" @click="toggleAuthOption">
            Already have an account? <span class="font-normal">Log in</span>
          </button>
        </div>
      </div>
    </div>

    <Dialog v-if="isMobileMenuOpened" as="div" class="z-30 lg:hidden" :open="isMobileMenuOpened" @close="toggleMobileMenu">
      <div class="fixed inset-0 z-30 " />
      <DialogPanel class="fixed inset-y-0 right-0 z-30 w-full overflow-y-auto border-b-2 bg-white px-6 py-3 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
        <div class="flex items-center justify-between sm:justify-end">
          <img class="h-10 sm:hidden" src="@/assets/scooter.png" alt="escooter logo">
          <button type="button" class="-m-2.5 rounded-md px-2.5 text-gray-700 sm:pt-4" @click="toggleMobileMenu">
            <span class="sr-only">Close menu</span>
            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
          </button>
        </div>
        <div class="mt-6 flow-root">
          <div class="-my-6 divide-y divide-gray-500/10">
            <div class="space-y-2 py-6">
              <a v-for="item in navigation" :key="item.name" :href="item.href" class=" -mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-800 hover:bg-blumilk-25">{{ item.name }}</a>
            </div>
            <div class="py-6">
              <button v-if="isAdmin" class="-mx-3 mb-4 flex w-full font-semibold text-gray-800">
                <Link v-if="isAdmin" class="flex w-full items-center rounded px-3 py-2.5 hover:bg-blumilk-25" href="/admin/dashboard/cities">
                  <ComputerDesktopIcon class="h-6 w-6" />
                  <span class="ml-2">Admin panel</span>
                </Link>
              </button>
              <button class="-mx-3 flex w-full font-semibold text-gray-800">
                <span v-if="isAuth" class="flex w-full items-center rounded px-3 py-2.5 hover:bg-blumilk-25" @click="logout">
                  <ArrowRightOnRectangleIcon class="h-6 w-6" />
                  <span class="ml-2">Log out </span>
                </span>

                <span v-if="!isAuth" class="flex w-full items-center rounded px-3 py-2.5 hover:bg-blumilk-25" @click="toggleAuthDialog">
                  <UserCircleIcon class="h-6 w-6" />
                  <span class="ml-2">Log in </span>
                </span>
              </button>
            </div>
          </div>
        </div>
      </DialogPanel>
    </Dialog>
  </header>
</template>
