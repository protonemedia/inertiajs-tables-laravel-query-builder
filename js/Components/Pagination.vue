<script>
const Pagination = {
  props: {
    meta: {
      type: Object,
      required: false,
    },
  },

  computed: {
    translations() {
      return Pagination.defaultTranslations;
    },
  },

  defaultTranslations: {
    no_results_found: "No results found",
    previous: "Previous",
    next: "Next",
    to: "to",
    of: "of",
    results: "results",
  },

  setTranslations(translations) {
    Pagination.defaultTranslations = translations;
  },
};

export default Pagination;
</script>
