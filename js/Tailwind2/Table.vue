<template>
  <div>
    <div class="flex flex-wrap space-x-4 justify-end md:justify-between">
      <slot
        name="tableFilter"
        :hasFilters="hasFilters"
        :filters="filters"
        :changeFilterValue="changeFilterValue"
      >
        <TableFilter class="mt-2" v-if="hasFilters" :filters="filters" :on-change="changeFilterValue"/>
      </slot>

      <slot
        name="tableGlobalSearch"
        :search="search"
        :changeGlobalSearchValue="changeGlobalSearchValue"
      >
        <div class="flex-grow">
          <TableGlobalSearch
            class="mt-2"
            v-if="search && search.global"
            :value="search.global.value"
            :on-change="changeGlobalSearchValue"
          />
        </div>
      </slot>

      <slot
        name="tableAddSearchRow"
        :hasSearchRows="hasSearchRows"
        :search="search"
        :newSearch="newSearch"
        :enableSearch="enableSearch"
      >
        <TableAddSearchRow
          class="mt-2"
          v-if="hasSearchRows"
          :rows="search"
          :new="newSearch"
          :on-add="enableSearch"
        />
      </slot>

      <slot
        name="tableColumns"
        :hasColumns="hasColumns"
        :columns="columns"
        :changeColumnStatus="changeColumnStatus"
      >
        <TableColumns class="mt-2" v-if="hasColumns" :columns="columns" :on-change="changeColumnStatus"/>
      </slot>
    </div>

    <slot
      name="tableSearchRows"
      :hasSearchRows="hasSearchRows"
      :search="search"
      :newSearch="newSearch"
      :disableSearch="disableSearch"
      :changeSearchValue="changeSearchValue"
    >
      <TableSearchRows
        ref="rows"
        v-if="hasSearchRows"
        :rows="search"
        :new="newSearch"
        :on-remove="disableSearch"
        :on-change="changeSearchValue"
      />
    </slot>

    <slot name="tableWrapper" :meta="meta">
      <TableWrapper :class="{'mt-2': !onlyData}">
        <slot name="table">
          <table class="mt-2 min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
            <slot name="head"/>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
            <slot name="body"/>
            </tbody>
          </table>
        </slot>

        <slot name="pagination">
          <Pagination :meta="paginationMeta"/>
        </slot>
      </TableWrapper>
    </slot>
  </div>
</template>

<script>
import Pagination from "./Pagination.vue";
import Table from "./../Components/Table.vue";
import TableAddSearchRow from "./TableAddSearchRow.vue";
import TableColumns from "./TableColumns.vue";
import TableFilter from "./TableFilter.vue";
import TableGlobalSearch from "./TableGlobalSearch.vue";
import TableSearchRows from "./TableSearchRows.vue";
import TableWrapper from "./TableWrapper.vue";

export default {
  mixins: [Table],

  components: {
    Pagination,
    TableAddSearchRow,
    TableColumns,
    TableFilter,
    TableGlobalSearch,
    TableSearchRows,
    TableWrapper,
  },
};
</script>
