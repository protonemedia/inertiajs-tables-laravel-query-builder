<template>
  <nav
    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
    v-if="meta"
  >
    <p v-if="meta.total < 1" class>No results found</p>
    <div v-if="meta.total > 0" class="flex-1 flex justify-between sm:hidden">
      <component
        :is="meta.prev_page_url ? 'inertia-link' : 'div'"
        :href="meta.prev_page_url"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500"
      >Previous</component>
      <component
        :is="meta.next_page_url ? 'inertia-link' : 'div'"
        :href="meta.next_page_url"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500"
      >Next</component>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="hidden lg:block text-sm text-gray-700">
          <span class="font-medium">{{ meta.from }}</span>
          to
          <span class="font-medium">{{ meta.to }}</span>
          of
          <span class="font-medium">{{ meta.total }}</span>
          results
        </p>
      </div>
      <div>
        <nav
          class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
          aria-label="Pagination"
        >
          <component
            :is="meta.prev_page_url ? 'inertia-link' : 'div'"
            :href="meta.prev_page_url"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
          >
            <span class="sr-only">Previous</span>
            <ChevronLeftIcon class="h-5 w-5" />
          </component>

          <div v-for="(link, key) in meta.links" :key="key">
            <slot name="link">
              <component
                v-if="!isNaN(link.label) || link.label === '...'"
                :is="link.url ? 'inertia-link' : 'div'"
                :href="link.url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                :class="{'hover:bg-gray-50': link.url, 'bg-gray-100': link.active}"
              >{{ link.label }}</component>
            </slot>
          </div>

          <component
            :is="meta.next_page_url ? 'inertia-link' : 'div'"
            :href="meta.next_page_url"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
          >
            <span class="sr-only">Next</span>
            <ChevronRightIcon class="h-5 w-5" />
          </component>
        </nav>
      </div>
    </div>
  </nav>
</template>

<script>
import { ChevronLeftIcon, ChevronRightIcon } from "@vue-hero-icons/solid";
import Pagination from "./../Components/Pagination.vue";

export default {
  mixins: [Pagination],

  components: {
    ChevronLeftIcon,
    ChevronRightIcon,
  },
};
</script>
