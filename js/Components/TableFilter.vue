<template>
  <ButtonWithDropdown
    placement="bottom-end"
    dusk="filters-dropdown"
    :active="hasEnabledFilters"
  >
    <template #button>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        :class="{
          'text-gray-400': !hasEnabledFilters,
          'text-green-400': hasEnabledFilters,
        }"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          fill-rule="evenodd"
          d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
          clip-rule="evenodd"
        />
      </svg>
    </template>

    <div
      role="menu"
      aria-orientation="horizontal"
      aria-labelledby="filter-menu"
      class="min-w-max"
    >
      <div
        v-for="(filter, key) in filters"
        :key="key"
      >
        <h3 class="text-xs uppercase tracking-wide bg-gray-100 p-3">
          {{ filter.label }}
        </h3>
        <div class="p-2">
          <select
            v-if="filter.type === 'select'"
            :name="filter.key"
            :value="filter.value"
            class="block focus:ring-indigo-500 focus:border-indigo-500 w-full shadow-sm text-sm border-gray-300 rounded-md"
            @change="onFilterChange(filter.key, $event.target.value)"
          >
            <option
              v-for="(option, optionKey) in filter.options"
              :key="optionKey"
              :value="optionKey"
            >
              {{ option }}
            </option>
          </select>
        </div>
      </div>
    </div>
  </ButtonWithDropdown>
</template>

<script setup>
import ButtonWithDropdown from "./ButtonWithDropdown.vue";

defineProps({
    hasEnabledFilters: {
        type: Boolean,
        required: true,
    },

    filters: {
        type: Object,
        required: true,
    },

    onFilterChange: {
        type: Function,
        required: true,
    },
});
</script>

