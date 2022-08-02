<template>
  <nav
    v-if="hasPagination"
    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
  >
    <p v-if="!hasData || pagination.total < 1">
      {{ translations.no_results_found }}
    </p>

    <!-- simple and mobile -->
    <div
      v-if="hasData"
      class="flex-1 flex justify-between"
      :class="{'sm:hidden': hasLinks}"
    >
      <component
        :is="previousPageUrl ? 'a' : 'div'"
        :class="{
          'cursor-not-allowed text-gray-400': !previousPageUrl,
          'text-gray-700 hover:text-gray-500': previousPageUrl
        }"
        :href="previousPageUrl"
        :dusk="previousPageUrl ? 'pagination-simple-previous' : null"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md bg-white"
        @click.prevent="onClick(previousPageUrl)"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M7 16l-4-4m0 0l4-4m-4 4h18"
          />
        </svg>
        <span class="hidden sm:inline ml-2">{{ translations.previous }}</span>
      </component>
      <PerPageSelector
        dusk="per-page-mobile"
        :value="perPage"
        :options="perPageOptions"
        :on-change="onPerPageChange"
      />
      <component
        :is="nextPageUrl ? 'a' : 'div'"
        :class="{
          'cursor-not-allowed text-gray-400': !nextPageUrl,
          'text-gray-700 hover:text-gray-500': nextPageUrl
        }"
        :href="nextPageUrl"
        :dusk="nextPageUrl ? 'pagination-simple-next' : null"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md bg-white"
        @click.prevent="onClick(nextPageUrl)"
      >
        <span class="hidden sm:inline mr-2">{{ translations.next }}</span>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17 8l4 4m0 0l-4 4m4-4H3"
          />
        </svg>
      </component>
    </div>

    <!-- full pagination -->
    <div
      v-if="hasData && hasLinks"
      class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
    >
      <div class="flex flex-row space-x-4 items-center flex-grow">
        <PerPageSelector
          dusk="per-page-full"
          :value="perPage"
          :options="perPageOptions"
          :on-change="onPerPageChange"
        />

        <p class="hidden lg:block text-sm text-gray-700 flex-grow">
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
            :is="previousPageUrl ? 'a' : 'div'"
            :class="{
              'cursor-not-allowed text-gray-400': !previousPageUrl,
              'text-gray-500 hover:bg-gray-50': previousPageUrl
            }"
            :href="previousPageUrl"
            :dusk="previousPageUrl ? 'pagination-previous' : null"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium"
            @click.prevent="onClick(previousPageUrl)"
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

          <div
            v-for="(link, key) in pagination.links"
            :key="key"
          >
            <slot name="link">
              <component
                :is="link.url ? 'a' : 'div'"
                v-if="
                  !isNaN(link.label) || link.label === '...'
                "
                :href="link.url"
                :dusk="link.url ? `pagination-${link.label}` : null"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                :class="{
                  'cursor-not-allowed': !link.url,
                  'hover:bg-gray-50': link.url,
                  'bg-gray-100': link.active,
                }"
                @click.prevent="onClick(link.url)"
              >
                {{ link.label }}
              </component>
            </slot>
          </div>

          <component
            :is="nextPageUrl ? 'a' : 'div'"
            :class="{
              'cursor-not-allowed text-gray-400': !nextPageUrl,
              'text-gray-500 hover:bg-gray-50': nextPageUrl
            }"
            :href="nextPageUrl"
            :dusk="nextPageUrl ? 'pagination-next' : null"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium"
            @click.prevent="onClick(nextPageUrl)"
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

<script setup>
import PerPageSelector from "./PerPageSelector.vue";
import { computed } from "vue";
import { getTranslations } from "../translations.js";

const translations = getTranslations();

const props = defineProps({
    onClick: {
        type: Function,
        required: false,
    },
    perPageOptions: {
        type: Array,
        default() {
            return () => [15, 30, 50, 100];
        },
        required: false
    },
    onPerPageChange: {
        type: Function,
        default() {
            return () => {};
        },
        required: false,
    },
    hasData: {
        type: Boolean,
        required: true,
    },
    meta: {
        type: Object,
        required: false,
    }
});

const hasLinks = computed(() => {
    if(!("links" in pagination.value)) {
        return false;
    }

    return pagination.value.links.length > 0;
});

const hasPagination = computed(() => {
    return Object.keys(pagination.value).length > 0;
});

const pagination = computed(() => {
    return props.meta;
});

const previousPageUrl = computed(() => {
    if ("prev_page_url" in pagination.value) {
        return pagination.value.prev_page_url;
    }

    return null;
});

const nextPageUrl = computed(() => {
    if ("next_page_url" in pagination.value) {
        return pagination.value.next_page_url;
    }

    return null;
});

const perPage = computed(() => {
    return parseInt(pagination.value.per_page);
});
</script>
