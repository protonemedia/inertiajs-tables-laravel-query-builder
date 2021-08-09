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
  
  defaultTranslations: {
    placeholder: "Search...",
  },

  setTranslations(translations) {
    TableGlobalSearch.defaultTranslations = translations;
  },  
};
</script>
