<template>
  <nav>
    <ul class="pagination justify-content-center">
      <li class="page-item" :class="{ disabled: !pagination.prev }">
        <button class="page-link" @click="$emit('load', pagination.prev)" :disabled="!pagination.prev">
          &laquo;
        </button>
      </li>

      <li
        v-for="link in meta.links"
        :key="link.label"
        class="page-item"
        :class="{ active: link.active, disabled: !link.url || link.label.includes('pagination') }"
      >
        <button
          class="page-link"
          @click="$emit('load', link.url)"
          :disabled="!link.url || link.label.includes('pagination')"
        >
          {{ formatLabel(link.label) }}
        </button>
      </li>

      <li class="page-item" :class="{ disabled: !pagination.next }">
        <button class="page-link" @click="$emit('load', pagination.next)" :disabled="!pagination.next">
          &raquo;
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
defineProps({
  pagination: Object,
  meta: Object
})

function formatLabel(label) {
  if (label === 'pagination.previous') return '← Назад'
  if (label === 'pagination.next') return 'Вперед →'
  return label
}
</script>
