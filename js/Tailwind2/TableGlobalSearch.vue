<template>
  <div class="relative">
    <input
      class="block w-full sm:text-sm rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300"
      placeholder="Search..."
      :value="value"
      type="text"
      @input="onChange($event.target.value)"
    />
    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
      <SearchIcon class="h-5 w-5 text-gray-400" />
    </div>
  </div>
</template>

<script>
import TableGlobalSearch from "./../Components/TableGlobalSearch.vue";
import { SearchIcon } from "@vue-hero-icons/solid";

export default {
  mixins: [TableGlobalSearch],

  components: {
    SearchIcon,
  },
};
</script>
