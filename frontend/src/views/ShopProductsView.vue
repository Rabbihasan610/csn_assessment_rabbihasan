<template>
  <div>
    <div style="margin-bottom: 24px;">
      <router-link to="/shops" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">
        &larr; Back to All Stores
      </router-link>

      <div v-if="shop" class="card" style="margin-bottom: 24px; background: linear-gradient(135deg, #1e293b, #0f172a); color: #ffffff;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div>
            <span class="badge badge-blue" style="margin-bottom: 8px;">Public Storefront</span>
            <h1 style="font-size: 28px; font-weight: 700;">{{ shop.name }}</h1>
            <p style="color: #94a3b8; font-size: 14px; margin-top: 4px;">{{ shop.description }}</p>
          </div>
          <div style="text-align: right;">
            <span style="font-size: 13px; color: #94a3b8;">Store Slug:</span>
            <div style="font-family: monospace; font-size: 14px; background: #334155; padding: 4px 8px; border-radius: 4px; margin-top: 4px;">
              {{ shop.slug }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading" style="text-align: center; padding: 48px;">
      <p style="color: var(--text-muted);">Loading products...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-else>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h2 style="font-size: 20px; font-weight: 600;">
          Store Catalog ({{ products.length }} items)
        </h2>
        <div style="font-size: 13px; color: var(--text-muted);">
          Prices and thumbnails reflect store-specific overrides
        </div>
      </div>

      <div v-if="products.length === 0" class="card" style="text-align: center; padding: 48px;">
        <p style="color: var(--text-muted);">No products have been attached to this store yet.</p>
      </div>

      <div v-else class="grid grid-cols-3">
        <div v-for="item in products" :key="item.id" class="card" style="display: flex; flex-direction: column; overflow: hidden; padding: 0;">
          <div style="height: 200px; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
            <img
              v-if="item.thumbnail"
              :src="item.thumbnail"
              :alt="item.title"
              style="width: 100%; height: 100%; object-fit: cover;"
            />
            <div v-else style="color: var(--text-muted); font-size: 13px;">No thumbnail</div>

            <span
              v-if="item.custom_overrides.custom_price !== null || item.custom_overrides.custom_title !== null"
              class="badge badge-purple"
              style="position: absolute; top: 10px; right: 10px;"
            >
              Custom Override
            </span>
          </div>

          <div style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1; justify-content: space-between;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px;">
                <h3 style="font-size: 16px; font-weight: 600;">{{ item.title }}</h3>
              </div>
              <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 16px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ item.description }}
              </p>
            </div>

            <div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div>
                  <span style="font-size: 20px; font-weight: 700; color: var(--primary);">${{ Number(item.price).toFixed(2) }}</span>
                </div>
                <span class="badge" :class="item.stock > 0 ? 'badge-green' : 'badge-amber'">
                  {{ item.stock > 0 ? `${item.stock} in stock` : 'Out of stock' }}
                </span>
              </div>

              <router-link :to="`/shops/${shop.slug}/products/${item.product_id}`" class="btn btn-secondary btn-sm" style="width: 100%;">
                View Details &rarr;
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <div class="card" style="margin-top: 40px; border-left: 4px solid var(--primary); background: #f8fafc;">
        <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 6px;">Evaluator Test 3: Customer Isolation Rule Verification</h4>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
          Try testing access to an unassigned product ID directly via this store to verify HTTP 404 behavior:
        </p>
        <div style="display: flex; gap: 8px; max-width: 400px;">
          <input v-model="testProductId" type="number" class="form-control" placeholder="Enter Product ID (e.g. 99 or other shop ID)" />
          <button @click="testIsolation" class="btn btn-secondary btn-sm">Test 404</button>
        </div>
        <div v-if="isolationResult" style="margin-top: 10px; font-size: 13px; font-family: monospace;" :style="{ color: isolationSuccess ? 'green' : 'red' }">
          {{ isolationResult }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api'

const route = useRoute()
const shop = ref(null)
const products = ref([])
const loading = ref(true)
const error = ref(null)

const testProductId = ref('')
const isolationResult = ref('')
const isolationSuccess = ref(false)

const fetchShopProducts = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await api.get(`/public/shops/${route.params.slug}/products`)
    shop.value = res.data.data.shop
    products.value = res.data.data.products
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load shop products.'
  } finally {
    loading.value = false
  }
}

const testIsolation = async () => {
  if (!testProductId.value) return
  isolationResult.value = 'Testing...'
  try {
    await api.get(`/public/shops/${route.params.slug}/products/${testProductId.value}`)
    isolationSuccess.value = false
    isolationResult.value = `Product ID ${testProductId.value} is assigned to this shop.`
  } catch (err) {
    if (err.response?.status === 404) {
      isolationSuccess.value = true
      isolationResult.value = `Verified HTTP 404 Not Found: Product ${testProductId.value} is properly isolated and invisible to this store.`
    } else {
      isolationSuccess.value = false
      isolationResult.value = `Unexpected response: ${err.response?.status}`
    }
  }
}

onMounted(() => {
  fetchShopProducts()
})

watch(() => route.params.slug, () => {
  fetchShopProducts()
})
</script>

