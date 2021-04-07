<template>
  <ButtonWithDropdown placement="bottom-end" :active="hasEnabledFilter">
    <template #button>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        :class="{'text-gray-400': !hasEnabledFilter, 'text-green-400': hasEnabledFilter}"
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

    <div role="menu" aria-orientation="vertical" aria-labelledby="sort-menu">
      <div v-for="filter in filters" :key="filter.key">
        <h3 class="text-xs uppercase tracking-wide bg-gray-100 p-3">{{filter.label}}</h3>
        <div class="p-2">
          <select
            :value="filter.value"
            @change="onChange(filter.key, $event.target.value)"
            class="block focus:ring-indigo-500 focus:border-indigo-500 w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          >
            <option v-for="(option, key) in filter.options" :value="key" :key="key">{{ option }}</option>
          </select>
        </div>
      </div>
    </div>
  </ButtonWithDropdown>
</template>

<script>
import ButtonWithDropdown from "./ButtonWithDropdown.vue";
import TableFilter from "./../Components/TableFilter.vue";

export default {
  mixins: [TableFilter],

  components: {
    ButtonWithDropdown,
  },
};
</script>
