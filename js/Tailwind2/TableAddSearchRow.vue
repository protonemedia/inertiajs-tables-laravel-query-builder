<template>
  <ButtonWithDropdown ref="dropdown" v-if="hasRows" :disabled="!rowsLeft" class="w-auto">
    <template #button>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5 mr-2 text-gray-400"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
      <span>Filter</span>
    </template>

    <div role="menu" aria-orientation="vertical" aria-labelledby="sort-menu">
      <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
        <button
          v-for="searchOption in rows"
          v-show="showRow(searchOption)"
          :key="searchOption.key"
          @click.prevent="enableSearch(searchOption.key)"
          class="text-left w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
          role="menuitem"
        >{{ searchOption.label }}</button>
      </div>
    </div>
  </ButtonWithDropdown>
</template>

<script>
import ButtonWithDropdown from "./ButtonWithDropdown.vue";
import OnClickOutside from "./../Components/OnClickOutside.vue";
import TableAddSearchRow from "./../Components/TableAddSearchRow.vue";

export default {
  mixins: [TableAddSearchRow],

  components: {
    ButtonWithDropdown,
    OnClickOutside,
  },
};
</script>
