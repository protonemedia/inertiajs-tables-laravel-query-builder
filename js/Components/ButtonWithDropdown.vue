<script>
import { createPopper } from "@popperjs/core/lib/popper-lite";
import preventOverflow from "@popperjs/core/lib/modifiers/preventOverflow";
import flip from "@popperjs/core/lib/modifiers/flip";

export default {
  props: {
    placement: {
      type: String,
      default: "bottom-start",
      required: false,
    },

    active: {
      type: Boolean,
      default: false,
      required: false,
    },

    disabled: {
      type: Boolean,
      default: false,
      required: false,
    },
  },

  data() {
    return {
      opened: false,
      popper: null,
    };
  },

  watch: {
    opened() {
      this.popper.update();
    },
  },

  methods: {
    toggle() {
      this.opened = !this.opened;
    },

    hide() {
      this.opened = false;
    },
  },

  mounted() {
    this.popper = createPopper(this.$refs.button, this.$refs.tooltip, {
      placement: this.placement,
      modifiers: [flip, preventOverflow],
    });
  },
};
</script>
