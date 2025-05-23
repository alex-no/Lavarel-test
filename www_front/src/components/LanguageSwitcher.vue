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

<script>
export default {
  props: ['modelValue'],
  data() {
    return {
      languages: [],
      currentLanguage: null
    }
  },
  emits: ['update:modelValue'],
  mounted() {
    this.fetchLanguages()
  },
  methods: {
    async fetchLanguages() {
      try {
        const res = await fetch('https://www.laravel.4n.com.ua/api/languages')
        const data = await res.json()
        this.languages = data.data
        this.currentLanguage = this.languages.find(l => l.code === this.modelValue)
      } catch (e) {
        console.error('Error loading languages:', e)
      }
    },
    changeLanguage(code) {
      this.$emit('update:modelValue', code)
      this.currentLanguage = this.languages.find(l => l.code === code)
    }
  }
}
</script>

<style scoped>
.dropdown-toggle::after {
  margin-left: 0.5rem;
}
</style>