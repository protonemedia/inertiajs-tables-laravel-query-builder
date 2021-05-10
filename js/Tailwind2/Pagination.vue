<template>
  <nav
    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
    v-if="hasPagination"
  >
    <p v-if="pagination.total < 1">{{ translations.no_results_found }}</p>
    <div v-if="pagination.total > 0" class="flex-1 flex justify-between sm:hidden">
      <component
        :is="previousPageUrl ? 'inertia-link' : 'div'"
        :href="previousPageUrl"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500"
      >{{ translations.previous }}</component>
      <component
        :is="nextPageUrl ? 'inertia-link' : 'div'"
        :href="nextPageUrl"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500"
      >{{ translations.next }}</component>
    </div>
    <div
      v-if="pagination.total > 0"
      class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
    >
      <div>
        <p class="hidden lg:block text-sm text-gray-700">
          <span class="font-medium">{{ pagination.from }}</span>
          {{ translations.to }}
          <span class="font-medium">{{ pagination.to }}</span>
          {{ translations.of }}
          <span class="font-medium">{{ pagination.total }}</span>
          {{ translations.results }}
        </p>
      </div>
      <div>
        <nav
          class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
          aria-label="Pagination"
        >
          <component
            :is="previousPageUrl ? 'inertia-link' : 'div'"
            :href="previousPageUrl"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
          >
            <span class="sr-only">{{ translations.previous }}</span>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </component>

          <div v-for="(link, key) in pagination.links" :key="key">
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
            :is="nextPageUrl ? 'inertia-link' : 'div'"
            :href="nextPageUrl"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
          >
            <span class="sr-only">{{ translations.next }}</span>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </component>
        </nav>
      </div>
    </div>
  </nav>
</template>

<script>
import Pagination from "./../Components/Pagination.vue";

export default {
  mixins: [Pagination],
};
</script>
