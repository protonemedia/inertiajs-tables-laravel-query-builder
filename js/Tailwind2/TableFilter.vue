<template>
  <ButtonWithDropdown placement="bottom-end" :active="hasEnabledFilter">
    <template #button>
      <FilterIcon
        class="h-5 w-5"
        :class="{'text-gray-400': !hasEnabledFilter, 'text-green-400': hasEnabledFilter}"
      />
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
import { FilterIcon } from "@heroicons/vue/solid";

export default {
  mixins: [TableFilter],

  components: {
    ButtonWithDropdown,
    FilterIcon,
  },
};
</script>
