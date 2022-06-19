<script setup>
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";

defineProps(["users"])
</script>

<template>
  <BreezeGuestLayout>
    <Table
      :inertia="$inertia"
      :resource="users"
    >
      <template #cell(actions)="{ item: user }">
        <a
          :href="`/users/${user.id}/edit`"
        >
          Edit
        </a>
      </template>
    </Table>
  </BreezeGuestLayout>
</template>
