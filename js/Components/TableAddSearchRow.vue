<template>
  <ButtonWithDropdown
    ref="dropdown"
    dusk="add-search-row-dropdown"
    :disabled="!hasSearchInputsWithoutValue"
    class="w-auto"
  >
    <template #button>
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
    </template>

    <div
      role="menu"
      aria-orientation="horizontal"
      aria-labelledby="add-search-input-menu"
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
        <ul class="divide-y divide-gray-200">
          <li
            v-for="(searchInput, key) in filteredSearchInputs"
            class="py-2 flex items-center justify-between"
          >
            <button
              :key="key"
              :dusk="`add-search-row-${searchInput.key}`"
              class="text-left w-full px-4 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
              role="menuitem"
              @click.prevent="enableSearch(searchInput.key)"
            >
              {{ searchInput.label }}
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
    searchInputs: {
        type: Object,
        required: true,
    },

    hasSearchInputsWithoutValue: {
        type: Boolean,
        required: true,
    },

    onAdd: {
        type: Function,
        required: true,
    },

    label: {
        type: String,
        default: "Search...",
        required: false,
    },
});

const dropdown = ref(null)

function enableSearch(key) {
    props.onAdd(key);
    dropdown.value.hide()
}

const filter = ref(null);

const filteredSearchInputs = computed(() => {
    if(filter.value) {
        return Object.values(props.searchInputs).filter(column => {
            return column.label.toLowerCase().indexOf(filter.value.toLowerCase()) > -1;
        });
    }

    return  props.searchInputs;
});
</script>
