<template>
  <select
    name="per_page"
    :dusk="dusk"
    :value="value"
    class="block focus:ring-indigo-500 focus:border-indigo-500 min-w-max shadow-sm text-sm border-gray-300 rounded-md"
    @change="onChange($event.target.value)"
  >
    <option
      v-for="option in perPageOptions"
      :key="option"
      :value="option"
    >
      {{ option }} {{ translations.per_page }}
    </option>
  </select>
</template>

<script setup>
import { computed } from "vue";
import uniq from "lodash-es/uniq";
import { getTranslations } from "../translations.js";

const translations = getTranslations();

const props = defineProps({
    dusk: {
        type: String,
        default: null,
        required: false,
    },

    value: {
        type: Number,
        default: 15,
        required: false,
    },

    options: {
        type: Array,
        default() {
            return [15, 30, 50, 100];
        },
        required: false,
    },

    onChange: {
        type: Function,
        required: true,
    },
});

const perPageOptions = computed(() => {
    let options = [...props.options];

    options.push(parseInt(props.value));

    return uniq(options).sort((a, b) => a - b);
});
</script>

