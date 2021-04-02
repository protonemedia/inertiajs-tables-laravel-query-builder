<script>
import filter from "lodash-es/filter";

export default {
  props: {
    rows: {
      type: Object,
      required: true,
    },

    new: {
      type: Array,
      default: () => [],
      required: false,
    },

    onChange: {
      type: Function,
      required: true,
    },

    onRemove: {
      type: Function,
      required: true,
    },
  },

  methods: {
    searchOptionIsEnabled(key) {
      return this.rows[key].enabled || this.new.includes(key);
    },

    focus(key) {
      this.$refs[key][0].focus();
    },
  },

  computed: {
    hasFiltersEnabled() {
      return Object.keys(this.enabledFilters || {}).length > 0;
    },

    enabledFilters() {
      return filter(this.rows, (search) => {
        if (search.key === "global") {
          return false;
        }

        return this.searchOptionIsEnabled(search.key);
      });
    },
  },
};
</script>
