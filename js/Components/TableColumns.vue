<template>
  <ButtonWithDropdown
    placement="bottom-end"
    dusk="columns-dropdown"
    :active="hasHiddenColumns"
  >
    <template #button>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        :class="{
          'text-gray-400': !hasHiddenColumns,
          'text-green-400': hasHiddenColumns,
        }"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
        <path
          fill-rule="evenodd"
          d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
          clip-rule="evenodd"
        />
      </svg>
    </template>

    <div
      role="menu"
      aria-orientation="horizontal"
      aria-labelledby="toggle-columns-menu"
      class="min-w-max"
    >
      <div class="relative">
        <input
          class="m-2 pl-9 text-sm rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
          :placeholder="label"
          :value="filter"
          type="text"
          name="global"
          @input="event => filter = event.target.value"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-400"

            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>

      <div class="px-2">
        <ul class="divide-y overflow-auto max-h-56 divide-gray-200">
          <li
            v-for="(column, key) in filteredColumns"
            v-show="column.can_be_hidden"
            :key="key"
            class="py-2 flex items-center justify-between"
          >
            <p
              class="text-left w-full px-4 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
            >
              {{ column.label }}
            </p>

            <button
              type="button"
              class="ml-4 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-light-blue-500"
              :class="{
                'bg-green-500': !column.hidden,
                'bg-gray-200': column.hidden,
              }"
              :aria-pressed="!column.hidden"
              :aria-labelledby="`toggle-column-${column.key}`"
              :aria-describedby="`toggle-column-${column.key}`"
              :dusk="`toggle-column-${column.key}`"
              @click.prevent="onChange(column.key, column.hidden)"
            >
              <span class="sr-only">Column status</span>
              <span
                aria-hidden="true"
                :class="{
                  'translate-x-5': !column.hidden,
                  'translate-x-0': column.hidden,
                }"
                class="inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
              />
            </button>
          </li>
        </ul>
      </div>
    </div>
  </ButtonWithDropdown>
</template>

<script setup>
import ButtonWithDropdown from "./ButtonWithDropdown.vue";
import {ref, computed} from 'vue';

const props = defineProps({
    columns: {
        type: Object,
        required: true,
    },

    hasHiddenColumns: {
        type: Boolean,
        required: true,
    },

    onChange: {
        type: Function,
        required: true,
    },

    label: {
        type: String,
        default: "Search...",
        required: false,
    },
});

const filter = ref(null);

const filteredColumns = computed(() => {
    if(filter.value) {
        return props.columns.filter(column => {
            return column.label.toLowerCase().indexOf(filter.value.toLowerCase()) > -1;
        });
    }

    return  props.columns;
});
</script>
