<script>
export default {
  props: {
    value: {
      type: String,
      default: "",
      required: false,
    },

    onChange: {
      type: Function,
      required: true,
    },
  },
};
</script>
