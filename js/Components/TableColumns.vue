<script>
import each from "lodash-es/each";
import filter from "lodash-es/filter";
import find from "lodash-es/find";

export default {
  props: {
    columns: {
      type: Object,
      required: true,
    },

    onChange: {
      type: Function,
      required: true,
    },
  },

  methods: {
    toggle(key) {
      this.onChange(key, !this.columns[key].enabled);
    },

    isLastEnabledFilter(key) {
      const enabledFilters = filter(
        this.columns,
        (filter, key) => filter.enabled
      );

      if (enabledFilters.length === 1) {
        return enabledFilters[0].key === key;
      }

      return false;
    },
  },

  computed: {
    hasDisabledFilter() {
      return find(this.columns, (filter, key) => !filter.enabled)
        ? true
        : false;
    },
  },
};
</script>
