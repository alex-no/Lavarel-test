<template>
  <nav>
    <ul class="pagination justify-content-center">
      <li class="page-item" :class="{ disabled: !links.first }">
        <button
          class="page-link"
          @click="$emit('load', getPageFromUrl(links.first))"
          :disabled="!links.first"
          :title="t('pagination.first')"
        >
          &laquo;
        </button>
      </li>

      <li
        v-for="link in meta.links"
        :key="link.label + (link.url ?? '')"
        class="page-item"
        :class="{ active: link.active, disabled: !link.url }"
      >
        <button
          class="page-link text-nowrap"
          @click="$emit('load', getPageFromUrl(link.url))"
          :disabled="!link.url"
        >
          {{ formatLabel(link.label) }}
        </button>
      </li>

      <li class="page-item" :class="{ disabled: !links.last }">
        <button
          class="page-link"
          @click="$emit('load', getPageFromUrl(links.last))"
          :disabled="!links.last"
          :title="t('pagination.last')"
        >
          &raquo;
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
  pagination: Object,
  meta: Object,
  links: Object
})

function getPageFromUrl(url) {
  if (!url) return null
  const match = url.match(/[\?&]page=(\d+)/)
  return match ? Number(match[1]) : null
}

function formatLabel(label) {
  const match =  String(label).toLowerCase().match(/previous|next/)
  const key = match ? match[0] : null

  if (key === 'previous') return `← ${t('pagination.previous')}`
  if (key === 'next') return `${t('pagination.next')} →`

  return label
}
</script>
