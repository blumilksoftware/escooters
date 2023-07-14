<script setup>

</script>

<template>
  <div class="bg-gradient-to-br from-blue-600 to-indigo-600">
    <InertiaHead title="Log In" />

    <main class="custom-main grid min-h-screen place-items-center">
      <div class="float-right mt-4 w-1/2">
        <section class="mx-auto max-w-md rounded-xl border bg-white p-4">
          <div class="flex items-center justify-between">
            <h1 class="mb-4 text-2xl font-semibold text-gray-600">
              Log In
            </h1>
            <a href="/" class="mr-2 inline-flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              <span>Back</span>
            </a>
          </div>

          <form @submit.prevent="attemptLogin">
            <div class="mb-4">
              <label class="mb-1 block text-xs font-bold uppercase text-gray-500" for="email">Email</label>
              <input id="email" v-model="form.email" class="w-full rounded-md bg-indigo-50 px-4 py-2 outline-none" type="email" name="email" placeholder="Email is required">
              <ErrorMessage :message="form.errors.loginError" />
            </div>

            <div class="mb-4">
              <label class="mb-1 block text-xs font-bold uppercase text-gray-500" for="password">Password</label>
              <input id="password" v-model="form.password" class="w-full rounded-md bg-indigo-50 px-4 py-2 outline-none" type="password" name="password" placeholder="Password is required">
              <ErrorMessage :message="form.errors.loginError" />
            </div>

            <div>
              <button type="submit" class="mt-4 w-full rounded-md bg-gradient-to-tr from-blue-600 to-indigo-600 py-2 text-lg tracking-wide text-indigo-100" :disabled="form.processing">
                Log In
              </button>
            </div>
          </form>
        </section>
      </div>
    </main>
  </div>
</template>
