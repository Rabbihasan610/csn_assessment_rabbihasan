<template>
  <div>
    <div style="margin-bottom: 24px;">
      <router-link :to="`/shops/${route.params.slug}`" class="btn btn-secondary btn-sm">
        &larr; Back to Store
      </router-link>
    </div>

    <div v-if="loading" style="text-align: center; padding: 48px;">
      <p style="color: var(--text-muted);">Loading product detail...</p>
    </div>

    <div v-else-if="error" class="card" style="text-align: center; padding: 48px; border-color: #fecaca;">
      <span class="badge badge-amber" style="margin-bottom: 12px;">HTTP 404 Not Found</span>
      <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">Product Isolation Active</h2>
      <p style="color: var(--text-muted); margin-bottom: 20px;">
        {{ error }}
      </p>
      <router-link :to="`/shops/${route.params.slug}`" class="btn btn-primary btn-sm">
        Return to Storefront
      </router-link>
    </div>

    <div v-else-if="product" class="grid grid-cols-2">
      <div class="card" style="padding: 0; overflow: hidden; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 400px;">
        <img
          v-if="product.thumbnail"
          :src="product.thumbnail"
          :alt="product.title"
          style="width: 100%; height: 100%; max-height: 500px; object-fit: cover;"
        />
        <div v-else style="color: var(--text-muted);">No image available</div>
      </div>

      <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="margin-bottom: 16px;">
            <span class="badge badge-blue" style="margin-right: 8px;">Store Product</span>
            <span
              v-if="product.custom_overrides.custom_price !== null || product.custom_overrides.custom_title !== null"
              class="badge badge-purple"
            >
              Store Override Applied
            </span>
          </div>

          <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 12px;">
            {{ product.title }}
          </h1>

          <div style="font-size: 32px; font-weight: 800; color: var(--primary); margin-bottom: 20px;">
            ${{ Number(product.price).toFixed(2) }}
          </div>

          <p style="color: #475569; font-size: 15px; line-height: 1.6; margin-bottom: 24px;">
            {{ product.description }}
          </p>

          <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 24px;">
            <h4 style="font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #334155;">Inventory & Identification:</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px;">
              <div>Product ID: <strong>#{{ product.product_id }}</strong></div>
              <div>Store Mapping ID: <strong>#{{ product.id }}</strong></div>
              <div>Available Stock: <strong>{{ product.stock }} units</strong></div>
              <div>Status: <strong>{{ product.is_active ? 'Active' : 'Inactive' }}</strong></div>
            </div>
          </div>

          <div v-if="product.custom_overrides.custom_price || product.custom_overrides.custom_title" style="border-top: 1px dashed var(--border); padding-top: 16px;">
            <h4 style="font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #6b21a8;">Applied Store Overrides:</h4>
            <ul style="font-size: 13px; color: var(--text-muted); list-style-position: inside;">
              <li v-if="product.custom_overrides.custom_title">Custom Title: "{{ product.custom_overrides.custom_title }}"</li>
              <li v-if="product.custom_overrides.custom_price">Custom Price: ${{ Number(product.custom_overrides.custom_price).toFixed(2) }}</li>
              <li v-if="product.custom_overrides.custom_thumbnail">Custom Thumbnail: Active</li>
            </ul>
          </div>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 20px; margin-top: 24px;">
          <button class="btn btn-primary" style="width: 100%; padding: 12px;" disabled>
            Storefront Catalog View (Demo)
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api'

const route = useRoute()
const product = ref(null)
const loading = ref(true)
const error = ref(null)

const fetchProductDetail = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await api.get(`/public/shops/${route.params.slug}/products/${route.params.id}`)
    product.value = res.data.data
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'This product is not assigned to this store or does not exist.'
    } else {
      error.value = err.response?.data?.message || 'Failed to load product detail.'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProductDetail()
})
</script>

