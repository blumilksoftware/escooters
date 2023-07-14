import { defineStore } from 'pinia'

export const useMapMarkerStore = defineStore('MapMarkerStore', {
  state: () => ({
    selectedCity: null,

    selectedCountryId: null,
    selectedProviderId: null,
  }),
  actions: {
    changeMarker(city) {
      this.selectedCity = city
    },
    changeSelectedCountry(countryId) {
      this.selectedCountryId = countryId
    },
    changeSelectedProvider(providerId) {
      this.selectedProviderId = providerId
    },
  },
})
