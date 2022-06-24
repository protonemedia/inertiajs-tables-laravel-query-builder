<template>
  <div
    class="
            w-full
            bg-gradient-to-r
            from-blue-50
            to-blue-100
            bg-white
            p-4
            mb-4
            rounded
            mt-4
        "
  >
    <div class="flex flex-row space-x-4">
      <div class="space-y-4">
        <div
          v-for="(searchInput, key) in searchInputs"
          v-show="searchInput.value !== null || isForcedVisible(searchInput.key)"
          :key="key"
          class="h-8 flex items-center"
        >
          <span class="text-sm">{{ searchInput.label }}</span>
        </div>
      </div>

      <div class="flex-grow space-y-4">
        <div
          v-for="(searchInput, key) in searchInputs"
          v-show="searchInput.value !== null || isForcedVisible(searchInput.key)"
          :key="key"
          class="h-8 flex items-center"
        >
          <div class="flex-grow relative">
            <input
              :ref="skipUnwrap.el"
              :key="searchInput.key"
              class="
                                block
                                w-full
                                sm:text-sm
                                rounded-md
                                shadow-sm
                                focus:ring-indigo-500 focus:border-indigo-500
                                border-gray-300
                            "
              :value="searchInput.value"
              type="text"
              @input="onChange(searchInput.key, $event.target.value)"
            >
            <div
              class="
                                absolute
                                inset-y-0
                                right-0
                                pr-3
                                flex
                                items-center
                            "
            >
              <button
                class="
                                    rounded-md
                                    text-gray-400
                                    hover:text-gray-500
                                    focus:outline-none
                                    focus:ring-2
                                    focus:ring-offset-2
                                    focus:ring-indigo-500
                                "
                @click.prevent="onRemove(searchInput.key)"
              >
                <span class="sr-only">Remove search</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, nextTick } from "vue"
import find from "lodash-es/find"

const skipUnwrap = { el: ref([]) };
let el = computed(() => skipUnwrap.el.value);

const props = defineProps({
    searchInputs: {
        type: Object,
        required: true,
    },

    forcedVisibleSearchInputs: {
        type: Array,
        required: true,
    },

    onChange: {
        type: Function,
        required: true,
    },

    onRemove: {
        type: Function,
        required: true,
    },
});

function isForcedVisible(key) {
    return props.forcedVisibleSearchInputs.includes(key)
}

watch(props.forcedVisibleSearchInputs, (inputs) => {
    const latestInput = inputs.length > 0 ? inputs[inputs.length -1] : null;

    if(!latestInput) {
        return;
    }

    nextTick().then(() => {
        const inputElement = find(el.value, (el) => {
            return el.__vnode.key ===  latestInput
        })

        if(inputElement) {
            inputElement.focus();
        }
    })
}, {immediate: true})
</script>

