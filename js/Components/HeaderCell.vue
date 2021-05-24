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
      this.cell.onSort(this.cell.key);
    },
  },
};
</script>