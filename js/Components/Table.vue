<template>
  <Transition>
    <fieldset
      ref="tableFieldset"
      :key="`table-${name}`"
      :dusk="`table-${name}`"
      class="min-w-0"
      :class="{'opacity-75': isVisiting}"
    >
      <div class="flex flex-row flex-wrap sm:flex-nowrap justify-start px-4 sm:px-0">
        <div class="order-2 sm:order-1 mr-2 sm:mr-4">
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
        </div>

        <div
          v-if="queryBuilderProps.globalSearch"
          class="flex flex-row w-full sm:w-auto sm:flex-grow order-1 sm:order-2 mb-2 sm:mb-0 sm:mr-4"
        >
          <slot
            name="tableGlobalSearch"
            :has-global-search="queryBuilderProps.globalSearch"
            :label="queryBuilderProps.globalSearch ? queryBuilderProps.globalSearch.label : null"
            :value="queryBuilderProps.globalSearch ? queryBuilderProps.globalSearch.value : null"
            :on-change="changeGlobalSearchValue"
          >
            <TableGlobalSearch
              v-if="queryBuilderProps.globalSearch"
              class="flex-grow"
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
          <div
            v-if="canBeReset"
            class="order-5 sm:order-3 sm:mr-4 ml-auto"
          >
            <TableReset :on-click="resetQuery" />
          </div>
        </slot>

        <slot
          name="tableAddSearchRow"
          :has-search-inputs="queryBuilderProps.hasSearchInputs"
          :has-search-inputs-without-value="queryBuilderProps.hasSearchInputsWithoutValue"
          :search-inputs="queryBuilderProps.searchInputsWithoutGlobal"
          :on-add="showSearchInput"
        >
          <TableAddSearchRow
            v-if="queryBuilderProps.hasSearchInputs"
            class="order-3 sm:order-4 mr-2 sm:mr-4"
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
            class="order-4 mr-4 sm:mr-0 sm:order-5"
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
        <TableWrapper :class="{ 'mt-3': !hasOnlyData }">
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
            :per-page-options="queryBuilderProps.perPageOptions"
            :on-per-page-change="onPerPageChange"
          >
            <Pagination
              :on-click="visit"
              :has-data="hasData"
              :meta="resourceMeta"
              :per-page-options="queryBuilderProps.perPageOptions"
              :on-per-page-change="onPerPageChange"
            />
          </slot>
        </TableWrapper>
      </slot>
    </fieldset>
  </Transition>
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
import { computed, onMounted, ref, watch, onUnmounted, getCurrentInstance, Transition } from "vue";
import qs from "qs";
import clone from "lodash-es/clone";
import filter from "lodash-es/filter";
import findKey from "lodash-es/findKey";
import forEach from "lodash-es/forEach";
import isEqual from "lodash-es/isEqual";
import map from "lodash-es/map";
import pickBy from "lodash-es/pickBy";

const props = defineProps({
    inertia: {
        type: Object,
        default: () => {
            return {};
        },
        required: false,
    },

    name: {
        type: String,
        default: "default",
        required: false,
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
        type: [Boolean, String],
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

const app = getCurrentInstance();
const $inertia = app ? app.appContext.config.globalProperties.$inertia : props.inertia;

const updates = ref(0);

const queryBuilderProps = computed(() => {
    let data = $inertia.page.props.queryBuilderProps
        ? $inertia.page.props.queryBuilderProps[props.name] || {}
        : {};

    data._updates = updates.value;

    return data;
});

const queryBuilderData = ref(queryBuilderProps.value);

const pageName = computed(() =>{
    return queryBuilderProps.value.pageName;
});

const forcedVisibleSearchInputs = ref([]);

const tableFieldset = ref(null);

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
});

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
            };
        }
    }

    if("meta" in props.resource) {
        return props.resource.meta;
    }

    return props.resource;
});

const hasData = computed(() => {
    if(resourceData.value.length > 0){
        return true;
    }

    if(resourceMeta.value.total > 0) {
        return true;
    }

    return false;
});

//

function disableSearchInput(key) {
    forcedVisibleSearchInputs.value = forcedVisibleSearchInputs.value.filter((search) => search != key);

    changeSearchInputValue(key, null);
}

function showSearchInput(key) {
    forcedVisibleSearchInputs.value.push(key);
}

const canBeReset = computed(() => {
    if(forcedVisibleSearchInputs.value.length > 0){
        return true;
    }

    const queryStringData = qs.parse(location.search.substring(1));

    const page = queryStringData[pageName.value];

    if(page > 1) {
        return true;
    }

    const prefix = props.name === "default" ? "" : (props.name + "_");
    let dirty = false;

    forEach(["filter", "columns", "cursor", "sort"], (key) => {
        const value = queryStringData[prefix + key];

        if(key === "sort" && value === queryBuilderProps.value.defaultSort) {
            return;
        }

        if(value !== undefined) {
            dirty = true;
        }
    });

    return dirty;
});

function resetQuery() {
    forcedVisibleSearchInputs.value = [];

    forEach(queryBuilderData.value.filters, (filter, key) => {
        queryBuilderData.value.filters[key].value = null;
    });

    forEach(queryBuilderData.value.searchInputs, (filter, key) => {
        queryBuilderData.value.searchInputs[key].value = null;
    });

    forEach(queryBuilderData.value.columns, (column, key) => {
        queryBuilderData.value.columns[key].hidden = column.can_be_hidden
            ? !queryBuilderProps.value.defaultVisibleToggleableColumns.includes(column.key)
            : false;
    });

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

function onPerPageChange(value) {
    queryBuilderData.value.cursor = null;
    queryBuilderData.value.perPage = value;
    queryBuilderData.value.page = 1;
}

function findDataKey(dataKey, key) {
    return findKey(queryBuilderData.value[dataKey], (value) => {
        return value.key == key;
    });
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
    const filterForQuery = getFilterForQuery();
    const columnsForQuery = getColumnsForQuery();

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
    const perPage = queryBuilderData.value.perPage;

    if(cursor) {
        queryData.cursor = cursor;
    }

    if(page > 1) {
        queryData.page = page;
    }

    if(perPage > 1) {
        queryData.perPage = perPage;
    }


    if(sort) {
        queryData.sort = sort;
    }

    return queryData;
}

function generateNewQueryString() {
    const queryStringData = qs.parse(location.search.substring(1));

    const prefix = props.name === "default" ? "" : (props.name + "_");

    forEach(["filter", "columns", "cursor", "sort"], (key) => {
        delete queryStringData[prefix + key];
    });

    delete queryStringData[pageName.value];

    forEach(dataForNewQueryString(), (value, key) =>{
        if(key === "page") {
            queryStringData[pageName.value] = value;
        } else if(key === "perPage") {
            queryStringData.perPage = value;
        } else {
            queryStringData[prefix + key] = value;
        }
    });

    let query = qs.stringify(queryStringData, {
        filter(prefix, value) {
            if (typeof value === "object" && value !== null) {
                return pickBy(value);
            }

            return value;
        },

        skipNulls: true,
        strictNullHandling: true,
    });

    if (!query || query === (pageName.value + "=1")) {
        query = "";
    }

    return query;
}

const isVisiting = ref(false);
const visitCancelToken = ref(null);

function visit(url) {
    if(!url) {
        return;
    }

    $inertia.get(
        url,
        {},
        {
            replace: true,
            preserveState: true,
            preserveScroll: props.preserveScroll !== false,
            onBefore(){
                isVisiting.value = true;
            },
            onCancelToken(cancelToken) {
                visitCancelToken.value = cancelToken;
            },
            onFinish() {
                isVisiting.value = false;
            },
            onSuccess() {
                if("queryBuilderProps" in $inertia.page.props){
                    queryBuilderData.value.cursor = queryBuilderProps.value.cursor;
                    queryBuilderData.value.page = queryBuilderProps.value.page;
                }

                if(props.preserveScroll === "table-top") {
                    const offset = -8;
                    const top = tableFieldset.value.getBoundingClientRect().top + window.pageYOffset + offset;

                    window.scrollTo({ top });
                }

                updates.value++;
            }
        }
    );
}

watch(queryBuilderData, () => {
    visit(location.pathname + "?" +  generateNewQueryString());
}, { deep: true });

const inertiaListener = () => {
    updates.value++;
};

onMounted(() => {
    document.addEventListener("inertia:success", inertiaListener);
});

onUnmounted(() => {
    document.removeEventListener("inertia:success", inertiaListener);
});

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
