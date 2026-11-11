<template>
  <div>
    <div style="margin-bottom: 32px; text-align: center;">
      <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 8px;">Explore Independent Stores</h1>
      <p style="color: var(--text-muted); font-size: 16px;">Browse distinct shops powered by the multi-tenant shared catalog platform</p>
    </div>

    <div v-if="loading" style="text-align: center; padding: 48px;">
      <p style="color: var(--text-muted);">Loading stores...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-else class="grid grid-cols-2">
      <div v-for="shop in shops" :key="shop.id" class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
            <h2 style="font-size: 20px; font-weight: 600;">{{ shop.name }}</h2>
            <span class="badge badge-green">{{ shop.status }}</span>
          </div>
          <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 14px;">{{ shop.description || 'No description provided.' }}</p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 16px;">
          <span style="font-size: 13px; color: var(--text-muted);">
            Products: <strong>{{ shop.products_count ?? 0 }}</strong>
          </span>
          <router-link :to="'/shops/' + shop.slug" class="btn btn-primary btn-sm">
            Visit Storefront &rarr;
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../api'

const shops = ref([])
const loading = ref(true)
const error = ref(null)

const fetchShops = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await api.get('/public/shops')
    shops.value = res.data.data
  } catch (err) {
    error.value = 'Failed to load shops. Please check backend API server.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchShops()
})
</script>

