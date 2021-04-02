<script>
export default {
  props: ["do"],

  mounted() {
    const listener = (e) => {
      if (e.target === this.$el || this.$el.contains(e.target)) {
        return;
      }

      this.do();
    };

    document.addEventListener("click", listener);
    document.addEventListener("touchstart", listener);

    this.$once("hook:beforeDestroy", () => {
      document.removeEventListener("click", listener);
      document.removeEventListener("touchstart", listener);
    });
  },

  render() {
    return this.$slots.default[0];
  },
};
</script>