<template>
  <ButtonWithDropdown ref="dropdown" :disabled="!rowsLeft" class="w-auto">
    <template #button>
      <PlusCircleIcon class="h-5 w-5 mr-2 text-gray-400" />
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
import { PlusCircleIcon } from "@vue-hero-icons/outline";

export default {
  mixins: [TableAddSearchRow],

  components: {
    ButtonWithDropdown,
    OnClickOutside,
    PlusCircleIcon,
  },
};
</script>
