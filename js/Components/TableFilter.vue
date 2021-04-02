<script>
import find from "lodash-es/find";

export default {
  props: {
    filters: {
      type: Object,
      required: true,
    },

    onChange: {
      type: Function,
      required: true,
    },
  },

  computed: {
    hasEnabledFilter() {
      return find(
        this.filters,
        (filter, key) => filter.value != "" && filter.value != null
      )
        ? true
        : false;
    },
  },
};
</script>
