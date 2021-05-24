<script>
export default {
  props: {
    cell: {
      type: Object,
      required: true,
    },
  },

  methods: {
    onClick() {
      if (this.cell.sortable) {
        this.cell.onSort(this.cell.key);
      }
    },
  },
};
</script>