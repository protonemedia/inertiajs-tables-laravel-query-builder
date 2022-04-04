<style scoped>
/*
TODO: Convert to @apply
*/

table >>> th {
  font-weight: 500;
  font-size: 0.75rem;
  line-height: 1rem;
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  padding-left: 1.5rem;
  padding-right: 1.5rem;
  text-align: left;
  --tw-text-opacity: 1;
  color: rgba(107, 114, 128, var(--tw-text-opacity));
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

table >>> td {
  font-size: 0.875rem;
  line-height: 1.25rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  padding-left: 1.5rem;
  padding-right: 1.5rem;
  --tw-text-opacity: 1;
  color: rgba(107, 114, 128, var(--tw-text-opacity));
  white-space: nowrap;
}

table >>> tr:hover td {
  --tw-bg-opacity: 1;
  background-color: rgba(249, 250, 251, var(--tw-bg-opacity));
}
</style>

<template>
  <div>
    <div class="flex space-x-4">
      <slot
        name="tableFilter"
        :hasFilters="hasFilters"
        :filters="filters"
        :changeFilterValue="changeFilterValue"
      >
        <TableFilter v-if="hasFilters" :filters="filters" :on-change="changeFilterValue" />
      </slot>

      <slot
        name="tableGlobalSearch"
        :search="search"
        :changeGlobalSearchValue="changeGlobalSearchValue"
      >
        <div class="flex-grow">
          <TableGlobalSearch
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
        <TableColumns v-if="hasColumns" :columns="columns" :on-change="changeColumnStatus" />
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
          <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
              <slot name="head" />
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <slot name="body" />
            </tbody>
          </table>
        </slot>

        <slot name="pagination">
          <Pagination :meta="paginationMeta" />
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