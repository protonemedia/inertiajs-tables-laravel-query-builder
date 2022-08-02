<script setup>
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";

// use this one for development:
// import Table from "../../../../js/Components/Table.vue"

defineProps(["users"]);
</script>

<template>
  <BreezeGuestLayout>
    <Table
      :meta="users"
    >
      <template #head>
        <tr>
          <th>User</th>
        </tr>
      </template>

      <template #body>
        <tr
          v-for="(user, key) in users.data"
          :key="key"
        >
          <td>{{ user.name }}</td>
        </tr>
      </template>
    </Table>
  </BreezeGuestLayout>
</template>
