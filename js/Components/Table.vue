<template>
  <fieldset
    :key="`table-${name}`"
    class="min-w-0"
    :class="{'opacity-75': isVisiting}"
    :disabled="preventOverlappingRequests && isVisiting"
  >
    <div class="flex space-x-4">
      <slot
        name="tableFilter"
        :has-filters="queryBuilderProps.hasFilters"
        :has-enabled-filters="queryBuilderProps.hasEnabledFilters"
        :filters="queryBuilderProps.filters"
        :on-filter-change="changeFilterValue"
      >
        <TableFilter
          v-if="queryBuilderProps.hasFilters"
          :has-enabled-filters="queryBuilderProps.hasEnabledFilters"
          :filters="queryBuilderProps.filters"
          :on-filter-change="changeFilterValue"
        />
      </slot>

      <div
        v-if="queryBuilderProps.globalSearch || canBeReset"
        class="flex flex-row "
        :class="{
          'space-x-4': queryBuilderProps.globalSearch && canBeReset,
          'flex-grow': queryBuilderProps.globalSearch
        }"
      >
        <div class="flex-grow">
          <slot
            name="tableGlobalSearch"
            :has-global-search="queryBuilderProps.globalSearch"
            :label="queryBuilderProps.globalSearch ? queryBuilderProps.globalSearch.label : null"
            :value="queryBuilderProps.globalSearch ? queryBuilderProps.globalSearch.value : null"
            :on-change="changeGlobalSearchValue"
          >
            <TableGlobalSearch
              v-if="queryBuilderProps.globalSearch"
              :label="queryBuilderProps.globalSearch.label"
              :value="queryBuilderProps.globalSearch.value"
              :on-change="changeGlobalSearchValue"
            />
          </slot>
        </div>

        <slot
          name="tableReset"
          can-be-reset="canBeReset"
          :on-click="resetQuery"
        >
          <div>
            <TableReset
              v-if="canBeReset"
              :on-click="resetQuery"
            />
          </div>
        </slot>
      </div>


      <slot
        name="tableAddSearchRow"
        :has-search-inputs="queryBuilderProps.hasSearchInputs"
        :has-search-inputs-without-value="queryBuilderProps.hasSearchInputsWithoutValue"
        :search-inputs="queryBuilderProps.searchInputsWithoutGlobal"
        :on-add="showSearchInput"
      >
        <TableAddSearchRow
          v-if="queryBuilderProps.hasSearchInputs"
          :search-inputs="queryBuilderProps.searchInputsWithoutGlobal"
          :has-search-inputs-without-value="queryBuilderProps.hasSearchInputsWithoutValue"
          :on-add="showSearchInput"
        />
      </slot>

      <slot
        name="tableColumns"
        :has-columns="queryBuilderProps.hasToggleableColumns"
        :columns="queryBuilderProps.columns"
        :has-hidden-columns="queryBuilderProps.hasHiddenColumns"
        :on-change="changeColumnStatus"
      >
        <TableColumns
          v-if="queryBuilderProps.hasToggleableColumns"
          :columns="queryBuilderProps.columns"
          :has-hidden-columns="queryBuilderProps.hasHiddenColumns"
          :on-change="changeColumnStatus"
        />
      </slot>
    </div>

    <slot
      name="tableSearchRows"
      :has-search-rows-with-value="queryBuilderProps.hasSearchInputsWithValue"
      :search-inputs="queryBuilderProps.searchInputsWithoutGlobal"
      :forced-visible-search-inputs="forcedVisibleSearchInputs"
      :on-change="changeSearchInputValue"
    >
      <TableSearchRows
        v-if="queryBuilderProps.hasSearchInputsWithValue || forcedVisibleSearchInputs.length > 0"
        :search-inputs="queryBuilderProps.searchInputsWithoutGlobal"
        :forced-visible-search-inputs="forcedVisibleSearchInputs"
        :on-change="changeSearchInputValue"
        :on-remove="disableSearchInput"
      />
    </slot>

    <slot
      name="tableWrapper"
      :meta="resourceMeta"
    >
      <TableWrapper :class="{ 'mt-2': !hasOnlyData }">
        <slot name="table">
          <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50">
              <slot
                name="head"
                :show="show"
                :sort-by="sortBy"
                :header="header"
              >
                <tr class="font-medium text-xs uppercase text-left tracking-wider text-gray-500 py-3 px-6">
                  <HeaderCell
                    v-for="column in queryBuilderProps.columns"
                    :key="`table-${name}-header-${column.key}`"
                    :cell="header(column.key)"
                  />
                </tr>
              </slot>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <slot
                name="body"
                :show="show"
              >
                <tr
                  v-for="(item, key) in resourceData"
                  :key="`table-${name}-row-${key}`"
                  class=""
                  :class="{
                    'bg-gray-50': striped && key % 2,
                    'hover:bg-gray-100': striped,
                    'hover:bg-gray-50': !striped
                  }"
                >
                  <td
                    v-for="column in queryBuilderProps.columns"
                    v-show="show(column.key)"
                    :key="`table-${name}-row-${key}-column-${column.key}`"
                    class="text-sm py-4 px-6 text-gray-500 whitespace-nowrap"
                  >
                    <slot
                      :name="`cell(${column.key})`"
                      :item="item"
                    >
                      {{ item[column.key] }}
                    </slot>
                  </td>
                </tr>
              </slot>
            </tbody>
          </table>
        </slot>

        <slot
          name="pagination"
          :on-click="visit"
          :has-data="hasData"
          :meta="resourceMeta"
        >
          <Pagination
            :on-click="visit"
            :has-data="hasData"
            :meta="resourceMeta"
          />
        </slot>
      </TableWrapper>
    </slot>
  </fieldset>
</template>

<script setup>
import Pagination from "./Pagination.vue";
import HeaderCell from "./HeaderCell.vue";
import TableAddSearchRow from "./TableAddSearchRow.vue";
import TableColumns from "./TableColumns.vue";
import TableFilter from "./TableFilter.vue";
import TableGlobalSearch from "./TableGlobalSearch.vue";
import TableSearchRows from "./TableSearchRows.vue";
import TableReset from "./TableReset.vue";
import TableWrapper from "./TableWrapper.vue";
import { computed, ref, watch } from "vue"
import qs from "qs";
import clone from "lodash-es/clone";
import each from "lodash-es/each";
import filter from "lodash-es/filter";
import findKey from "lodash-es/findKey";
import forEach from "lodash-es/forEach";
import isEqual from "lodash-es/isEqual";
import map from "lodash-es/map";
import pickBy from "lodash-es/pickBy";

const props = defineProps({
    name: {
        type: String,
        default: "default",
        required: false,
    },

    inertia: {
        type: Object,
        required: true,
    },

    striped: {
        type: Boolean,
        default: false,
        required: false,
    },

    preventOverlappingRequests: {
        type: Boolean,
        default: true,
        required: false,
    },

    inputDebounceMs: {
        type: Number,
        default: 350,
        required: false,
    },

    preserveScroll: {
        type: Boolean,
        default: false,
        required: false,
    },

    resource: {
        type: Object,
        default: () => {
            return {};
        },
        required: false,
    },

    meta: {
        type: Object,
        default: () => {
            return {};
        },
        required: false,
    },

    data: {
        type: Object,
        default: () => {
            return {};
        },
        required: false,
    },
});

const queryBuilderProps = ref(
    props.inertia.page.props.queryBuilderProps[props.name] || {}
);

const queryBuilderData = ref(queryBuilderProps.value);

const forcedVisibleSearchInputs = ref([]);

const hasOnlyData = computed(() => {
    if(queryBuilderProps.value.hasToggleableColumns) {
        return false;
    }

    if(queryBuilderProps.value.hasFilters) {
        return false;
    }

    if(queryBuilderProps.value.hasSearchInputs) {
        return false;
    }

    if(queryBuilderProps.value.globalSearch) {
        return false;
    }

    return true;

});

const resourceData = computed(() => {
    if(Object.keys(props.resource).length === 0){
        return props.data;
    }

    if("data" in props.resource) {
        return props.resource.data;
    }

    return props.resource;
})

const resourceMeta = computed(() => {
    if(Object.keys(props.resource).length === 0){
        return props.meta;
    }

    if("links" in props.resource && "meta" in props.resource) {
        if(Object.keys(props.resource.links).length === 4
          && "next" in props.resource.links
          && "prev" in props.resource.links) {
            return {
                ...props.resource.meta,
                next_page_url: props.resource.links.next,
                prev_page_url: props.resource.links.prev
            }
        }
    }

    if("meta" in props.resource) {
        return props.resource.meta;
    }

    return props.resource;
});

const hasData = computed(() => {
    return resourceData.value.length > 0;
});

//

function disableSearchInput(key) {
    forcedVisibleSearchInputs.value = forcedVisibleSearchInputs.value.filter((search) => search != key);

    changeSearchInputValue(key, null);
}

function showSearchInput(key) {
    forcedVisibleSearchInputs.value.push(key);
}

const updates = ref(0)

const canBeReset = computed(() => {
    if(updates.value < 0){
        return false;
    }

    if(forcedVisibleSearchInputs.value.length > 0){
        return true;
    }

    const query = qs.parse(location.search.substring(1))
    const keys = Object.keys(query);

    if(keys.length === 0) {
        return false;
    }

    if(keys.length === 1 && query.remember === "forget"){
        return false;
    }
    if(keys.length === 1 && query.page == 1){
        return false;
    }

    return true;
});

function resetQuery() {
    forcedVisibleSearchInputs.value = [];

    each(queryBuilderData.value.filters, (filter, key)=>{
        queryBuilderData.value.filters[key].value = null;
    })

    each(queryBuilderData.value.searchInputs, (filter, key)=>{
        queryBuilderData.value.searchInputs[key].value = null;
    })

    each(queryBuilderData.value.columns, (column, key) => {
        queryBuilderData.value.columns[key].hidden = column.can_be_hidden
            ? !queryBuilderProps.value.defaultVisibleToggleableColumns.includes(column.key)
            : false;
    })

    queryBuilderData.value.sort = null;
    queryBuilderData.value.cursor = null;
    queryBuilderData.value.page = 1;
}

const debounceTimeouts = {};

function changeSearchInputValue(key, value) {
    clearTimeout(debounceTimeouts[key]);

    debounceTimeouts[key] = setTimeout(() => {
        if(visitCancelToken.value && props.preventOverlappingRequests){
            visitCancelToken.value.cancel();
        }

        const intKey = findDataKey("searchInputs", key);

        queryBuilderData.value.searchInputs[intKey].value = value;
        queryBuilderData.value.cursor = null;
        queryBuilderData.value.page = 1;
    }, props.inputDebounceMs);
}

function changeGlobalSearchValue(value) {
    changeSearchInputValue("global", value);
}

function changeFilterValue(key, value) {
    const intKey = findDataKey("filters", key);

    queryBuilderData.value.filters[intKey].value = value;
    queryBuilderData.value.cursor = null;
    queryBuilderData.value.page = 1;
}

function findDataKey(dataKey, key) {
    return findKey(queryBuilderData.value[dataKey], (value) => {
        return value.key == key;
    })
}

function changeColumnStatus(key, visible) {
    const intKey = findDataKey("columns", key);

    queryBuilderData.value.columns[intKey].hidden = !visible;
}

function getFilterForQuery() {
    let filtersWithValue = {};

    forEach(queryBuilderData.value.searchInputs, (searchInput) => {
        if (searchInput.value !== null) {
            filtersWithValue[searchInput.key] = searchInput.value;
        }
    });

    forEach(queryBuilderData.value.filters, (filters) => {
        if (filters.value !== null) {
            filtersWithValue[filters.key] = filters.value;
        }
    });

    return filtersWithValue;
}

function getColumnsForQuery() {
    const columns = queryBuilderData.value.columns;

    let visibleColumns = filter(columns, (column) => {
        return !column.hidden;
    });

    let visibleColumnKeys = map(visibleColumns, (column) => {
        return column.key;
    }).sort();

    if (isEqual(visibleColumnKeys, queryBuilderProps.value.defaultVisibleToggleableColumns)){
        return {};
    }

    return visibleColumnKeys;
}

function dataForNewQueryString() {
    const filterForQuery = getFilterForQuery()
    const columnsForQuery = getColumnsForQuery()

    const queryData = {};

    if(Object.keys(filterForQuery).length > 0) {
        queryData.filter = filterForQuery;
    }

    if(Object.keys(columnsForQuery).length > 0) {
        queryData.columns = columnsForQuery;
    }

    const cursor = queryBuilderData.value.cursor;
    const page = queryBuilderData.value.page;
    const sort = queryBuilderData.value.sort;

    if(cursor) {
        queryData.cursor = cursor;
    }

    if(page > 1) {
        queryData.page = page;
    }

    if(sort) {
        queryData.sort = sort;
    }

    return queryData;
}

function generateNewQueryString() {
    let query = qs.stringify(dataForNewQueryString(), {
        filter(prefix, value) {
            if (typeof value === "object" && value !== null) {
                return pickBy(value);
            }

            return value;
        },

        skipNulls: true,
        strictNullHandling: true,
    });

    if (!query || query === "page=1") {
        query = "remember=forget";
    }

    return query;
}

const isVisiting = ref(false)
const visitCancelToken = ref(null)

function visit(url) {
    if(!url) {
        return;
    }

    props.inertia.get(
        url,
        {},
        {
            replace: true,
            preserveState: true,
            preserveScroll: props.preserveScroll.value,
            onBefore(){
                if(isVisiting.value && props.preventOverlappingRequests) {
                    return false;
                }

                isVisiting.value = true
            },
            onCancelToken(cancelToken) {
                visitCancelToken.value = cancelToken
            },
            onFinish() {
                isVisiting.value = false
            },
            onSuccess() {
                queryBuilderProps.value = props.inertia.page.props.queryBuilderProps[props.name] || {}
                queryBuilderData.value.cursor = queryBuilderProps.value.cursor
                queryBuilderData.value.page = queryBuilderProps.value.page
                updates.value++;
            }
        }
    );
}

watch(queryBuilderData, () => {
    visit(location.pathname + "?" +  generateNewQueryString())
}, {deep: true})

//

function sortBy(column) {
    if(queryBuilderData.value.sort == column) {
        queryBuilderData.value.sort = `-${column}`;
    } else {
        queryBuilderData.value.sort = column;
    }

    queryBuilderData.value.cursor = null;
    queryBuilderData.value.page = 1;
}

function show(key) {
    const intKey = findDataKey("columns", key);

    return !queryBuilderData.value.columns[intKey].hidden;
}

function header(key) {
    const intKey = findDataKey("columns", key);
    const columnData = clone(queryBuilderProps.value.columns[intKey]);

    columnData.onSort = sortBy;

    return columnData;
}
</script>
