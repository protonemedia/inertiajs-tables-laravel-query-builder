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

    onAdd: {
      type: Function,
      required: true,
    },
  },

  computed: {
    hasRows() {
      return filter(this.rows, (row) => row.key !== "global").length > 0;
    },

    rowsLeft() {
      return filter(this.rows, (row) => this.showRow(row)).length > 0;
    },
  },

  methods: {
    showRow(row) {
      if (row.key === "global") {
        return false;
      }

      if (this.new.includes(row.key)) {
        return false;
      }

      if (row.enabled) {
        return false;
      }

      return true;
    },

    enableSearch(key) {
      this.onAdd(key);
      this.$refs["dropdown"].hide();
    },
  },
};
</script>
