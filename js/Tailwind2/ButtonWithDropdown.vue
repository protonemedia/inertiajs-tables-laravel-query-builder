<template>
  <OnClickOutside :do="hide">
    <div class="relative">
      <button
        @click.prevent="toggle"
        type="button"
        :disabled="disabled"
        class="w-full bg-white border rounded-md shadow-sm px-4 py-2 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        :class="{'border-green-300': active, 'border-gray-300': !active, 'cursor-not-allowed': disabled }"
        aria-haspopup="true"
        ref="button"
      >
        <slot name="button" />
      </button>

      <div ref="tooltip" class="absolute z-10" v-show="opened">
        <div class="mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
          <slot />
        </div>
      </div>
    </div>
  </OnClickOutside>
</template>

<script>
import ButtonWithDropdown from "./../Components/ButtonWithDropdown.vue";
import OnClickOutside from "./../Components/OnClickOutside.vue";

export default {
  mixins: [ButtonWithDropdown],

  components: {
    OnClickOutside,
  },
};
</script>
