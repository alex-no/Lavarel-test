<template>
  <div class="d-flex justify-content-end align-items-center mb-3">
    <div class="btn-group">
      <button
        type="button"
        class="btn btn-outline-secondary dropdown-toggle"
        data-bs-toggle="dropdown"
        aria-expanded="false"
      >
        {{ currentLanguage?.short_name || '🌐 Language' }}
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li
          v-for="lang in languages"
          :key="lang.code"
        >
          <a
            class="dropdown-item"
            href="#"
            @click.prevent="changeLanguage(lang.code)"
          >
            {{ lang.full_name }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

const languages = ref([])
const currentLanguage = ref(null)

watch(
  () => props.modelValue,
  (newCode) => {
    currentLanguage.value = languages.value.find((l) => l.code === newCode)
  }
)

async function fetchLanguages() {
  try {
    const res = await fetch('https://www.laravel.4n.com.ua/api/languages')
    const data = await res.json()
    languages.value = data.data
    currentLanguage.value = languages.value.find((l) => l.code === props.modelValue)
  } catch (e) {
    console.error('Error loading languages:', e)
  }
}

function changeLanguage(code) {
  emit('update:modelValue', code)
  currentLanguage.value = languages.value.find((l) => l.code === code)
}

onMounted(fetchLanguages)
</script>

<style scoped>
.dropdown-toggle::after {
  margin-left: 0.5rem;
}
</style>
