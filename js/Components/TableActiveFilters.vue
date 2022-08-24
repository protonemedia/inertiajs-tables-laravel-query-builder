<template>
  <div class="flex items-center justify-start my-3 text-sm">
    <div class="">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5 text-gray-300"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          fill-rule="evenodd"
          d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
          clip-rule="evenodd"
        />
      </svg>
    </div>

    <button
      v-for="(filter) in filters.filter((filter) => filter.value)"
      :key="filter.label"
      class="block group ml-3 flex items-center justify-between px-2 py-1 rounded bg-gray-100"
      @click="onFilterChange(filter.key, null)"
    >
      <span class="capitalize text-gray-500">{{ filter.label.replace('_', ' ') }}</span>:
      <span class="ml-1 group-hover:line-through">{{ filter.options[filter.value] }}</span>

      <svg
        :class="`group-hover:${activeClasses.text}`"
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4 ml-1 text-gray-400"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          fill-rule="evenodd"
          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { inject } from "vue";

defineProps({
    filters: {
        type: Object,
        required: true,
    },

    onFilterChange: {
        type: Function,
        required: true,
    },
});

const activeClasses = inject("activeClasses");

</script>
