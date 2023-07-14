import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import { createApp, h } from 'vue'
import '../css/app.css'
import { createPinia } from 'pinia'

const pinia = createPinia()

createInertiaApp({
  progress: {
    delay: 0,
    color: '#14b8a6',
  },
  title: (title) => title ? `${title} - e-scooters` : 'e-scooters',
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })

    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .component('InertiaLink', Link)
      .component('InertiaHead', Head)
      .mount(el)
  },
})
