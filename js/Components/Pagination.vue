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

    hasPagination() {
      return Object.keys(this.pagination).length > 0;
    },

    pagination() {
      if ("total" in this.meta && "to" in this.meta && "from" in this.meta) {
        return this.meta;
      }

      if ("meta" in this.meta) {
        return this.meta.meta;
      }

      return {};
    },

    previousPageUrl() {
      if ("prev_page_url" in this.pagination) {
        return this.pagination.prev_page_url;
      }

      if ("links" in this.meta) {
        return this.meta.links.prev;
      }
    },

    nextPageUrl() {
      if ("next_page_url" in this.pagination) {
        return this.pagination.next_page_url;
      }

      if ("links" in this.meta) {
        return this.meta.links.next;
      }
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
