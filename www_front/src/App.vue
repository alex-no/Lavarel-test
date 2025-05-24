<template>
  <div class="container py-5">
    <LanguageSwitcher v-model="selectedLang" />

    <h1 class="mb-4">{{ $t('pageTitle') }}</h1>

    <div v-if="loading" class="text-center">
      <div class="spinner-border" role="status"></div>
      <span class="ms-2">{{ $t('pageLoading') }}...</span>
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div v-if="!loading && !error">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>{{ $t('table.columnFeature') }}</th>
            <th>{{ $t('table.columnTechnology') }}</th>
            <th>{{ $t('table.columnResult') }}</th>
            <th>{{ $t('table.columnStatus') }}</th>
            <th>{{ $t('table.columnUpdated') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id">
            <td>{{ item.sort_order }}</td>
            <td>{{ item.feature }}</td>
            <td>{{ item.technology }}</td>
            <td>{{ item.result }}</td>
            <td>{{ item.status_adv }}</td>
            <td>{{ item.updated }}</td>
          </tr>
        </tbody>
      </table>

      <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: !pagination.prev }">
            <button class="page-link" @click="loadPage(pagination.prev)" :disabled="!pagination.prev">
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
              @click="loadPage(link.url)"
              :disabled="!link.url || link.label.includes('pagination')"
            >
              {{ formatLabel(link.label) }}
            </button>
          </li>

          <li class="page-item" :class="{ disabled: !pagination.next }">
            <button class="page-link" @click="loadPage(pagination.next)" :disabled="!pagination.next">
              &raquo;
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import LanguageSwitcher from './components/LanguageSwitcher.vue'

const items = ref([])
const loading = ref(true)
const error = ref(null)
const pagination = ref({ next: null, prev: null })
const meta = ref({ links: [] })

const selectedLang = ref('en')
const baseUrl = '/api/development-plan'

onMounted(() => {
  //fetchData(baseUrl)
  fetchData(`${baseUrl}?lang=${selectedLang.value}`)
})

watch(selectedLang, (newLang) => {
  console.log('Selected language changed to', newLang)
  fetchData(`${baseUrl}?lang=${newLang}`)
})

function fetchData(url) {
  loading.value = true
  error.value = null

  fetch(url)
    .then((response) => {
      if (!response.ok) throw new Error('Network error')
      return response.json()
    })
    .then((data) => {
      items.value = data.data
      pagination.value.next = data.links.next
      pagination.value.prev = data.links.prev
      meta.value = data.meta
    })
    .catch((err) => {
      console.error(err)
      error.value = $t('pageLoadingError')
    })
    .finally(() => {
      loading.value = false
    })
}

function loadPage(url) {
  if (url) fetchData(url)
}

function formatLabel(label) {
  if (label === 'pagination.previous') return '← Назад'
  if (label === 'pagination.next') return 'Вперед →'
  return label
}
</script>

<style>
body {
  font-family: system-ui, sans-serif;
}
</style>
