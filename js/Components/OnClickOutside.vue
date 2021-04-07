<template>
  <div>
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    do: {
      type: Function,
      required: true,
    },
  },

  data() {
    return {
      listener: null,
    };
  },

  mounted() {
    this.listener = (e) => {
      if (e.target === this.$el || this.$el.contains(e.target)) {
        return;
      }

      this.do();
    };

    document.addEventListener("click", this.listener);
    document.addEventListener("touchstart", this.listener);
  },

  beforeUnmount() {
    document.removeEventListener("click", this.listener);
    document.removeEventListener("touchstart", this.listener);
  },
};
</script>