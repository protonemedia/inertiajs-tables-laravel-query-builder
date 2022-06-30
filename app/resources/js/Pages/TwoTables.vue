<script setup>
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";

// use this one for development:
// import Table from "../../../../js/Components/Table.vue"

defineProps(["companies", "users"])
</script>

<template>
  <BreezeGuestLayout>
    <Table
      :inertia="$inertia"
      :resource="companies"
      name="companies"
      preserve-scroll="table-top"
      :input-debounce-ms="50"
    />

    <Table
      class="mt-8"
      :inertia="$inertia"
      :resource="users"
      name="users"
      preserve-scroll="table-top"
      :input-debounce-ms="50"
    />
  </BreezeGuestLayout>
</template>
