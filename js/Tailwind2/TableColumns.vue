<template>
  <ButtonWithDropdown placement="bottom-end" :active="hasDisabledFilter">
    <template #button>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        :class="{'text-gray-400': !hasDisabledFilter, 'text-green-400': hasDisabledFilter}"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
        />
      </svg>
    </template>

    <div role="menu" aria-orientation="vertical" aria-labelledby="sort-menu">
      <div class="px-2">
        <ul class="divide-y divide-gray-200">
          <li
            class="py-2 flex items-center justify-between"
            v-for="(column, key) in columns"
            :key="key"
          >
            <p
              class="text-sm font-medium text-gray-900"
              id="privacy-option-1-label"
            >{{ column.label }}</p>

            <button
              @click.prevent="toggle(key)"
              type="button"
              class="ml-4 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-light-blue-500"
              :class="{'bg-green-500': column.enabled,'bg-gray-200': !column.enabled, 'cursor-not-allowed': isLastEnabledFilter(key) }"
              :disabled="isLastEnabledFilter(key)"
              aria-pressed="true"
              aria-labelledby="privacy-option-1-label"
              aria-describedby="privacy-option-1-description"
            >
              <span class="sr-only">Use setting</span>
              <span
                aria-hidden="true"
                :class="{'translate-x-5':column.enabled,'translate-x-0':!column.enabled }"
                class="translate-x-0 inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
              ></span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </ButtonWithDropdown>
</template>

<script>
import ButtonWithDropdown from "./ButtonWithDropdown.vue";
import TableColumns from "./../Components/TableColumns.vue";

export default {
  mixins: [TableColumns],

  components: {
    ButtonWithDropdown,
  },
};
</script>
