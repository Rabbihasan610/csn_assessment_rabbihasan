<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
      <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
          <span class="badge badge-blue">Shop Owner Portal</span>
          <span v-if="user?.shop" class="badge badge-green">{{ user.shop.name }}</span>
        </div>
        <h1 style="font-size: 26px; font-weight: 700;">
          Store Catalog Management
        </h1>
      </div>

      <div style="display: flex; gap: 8px;">
        <button
          @click="activeTab = 'catalog'"
          class="btn btn-sm"
          :class="activeTab === 'catalog' ? 'btn-primary' : 'btn-secondary'"
        >
          Shared Global Catalog ({{ catalog.length }})
        </button>
        <button
          @click="activeTab = 'my-shop'"
          class="btn btn-sm"
          :class="activeTab === 'my-shop' ? 'btn-primary' : 'btn-secondary'"
        >
          My Store Catalog ({{ myProducts.length }})
        </button>
        <router-link v-if="user?.shop" :to="'/shops/' + user.shop.slug" class="btn btn-secondary btn-sm" target="_blank">
          Preview Storefront &rarr;
        </router-link>
      </div>
    </div>

    <div v-if="alertMessage" class="alert alert-success">
      {{ alertMessage }}
    </div>

    <div v-if="errorMessage" class="alert alert-danger">
      {{ errorMessage }}
    </div>

    <div v-if="activeTab === 'catalog'">
      <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
          <div>
            <h2 style="font-size: 18px; font-weight: 600;">Shared Global Catalog</h2>
            <p style="font-size: 13px; color: var(--text-muted);">
              Browse items created by Admin and other Shop Owners. Adopt items to your store with custom overrides.
            </p>
          </div>
          <button @click="showNewMasterModal = true" class="btn btn-primary btn-sm">
            + Create New Global Product
          </button>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Thumbnail</th>
                <th>Global Title</th>
                <th>Global Base Price</th>
                <th>Creator</th>
                <th>Status in My Store</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in catalog" :key="item.id">
                <td style="width: 50px;">
                  <img
                    v-if="item.thumbnail"
                    :src="item.thumbnail"
                    style="width: 44px; height: 44px; object-fit: cover; border-radius: 4px;"
                  />
                  <div v-else style="width: 44px; height: 44px; background: #e2e8f0; border-radius: 4px;"></div>
                </td>
                <td>
                  <strong>{{ item.title }}</strong>
                  <div style="font-size: 12px; color: var(--text-muted); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ item.description }}
                  </div>
                </td>
                <td>${{ Number(item.price).toFixed(2) }}</td>
                <td>
                  <span v-if="item.creator" class="badge badge-purple">{{ item.creator.name }}</span>
                  <span v-else class="badge badge-secondary">Admin</span>
                </td>
                <td>
                  <span v-if="isAttached(item.id)" class="badge badge-green">Attached to Store</span>
                  <span v-else class="badge badge-amber">Not in Store</span>
                </td>
                <td>
                  <button @click="openAttachModal(item)" class="btn btn-sm" :class="isAttached(item.id) ? 'btn-secondary' : 'btn-primary'">
                    {{ isAttached(item.id) ? 'Edit Store Overrides' : '+ Add to My Store' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'my-shop'">
      <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div>
            <h2 style="font-size: 18px; font-weight: 600;">Products in {{ user?.shop?.name }}</h2>
            <p style="font-size: 13px; color: var(--text-muted);">
              These are the only items visible to public customers browsing your storefront.
            </p>
          </div>
        </div>

        <div v-if="myProducts.length === 0" style="text-align: center; padding: 32px; color: var(--text-muted);">
          No products attached yet. Switch to "Shared Global Catalog" to add products.
        </div>

        <div v-else class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Thumbnail</th>
                <th>Store Title</th>
                <th>Store Price</th>
                <th>Stock</th>
                <th>Active</th>
                <th>Overrides</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sp in myProducts" :key="sp.id">
                <td style="width: 50px;">
                  <img
                    v-if="sp.thumbnail"
                    :src="sp.thumbnail"
                    style="width: 44px; height: 44px; object-fit: cover; border-radius: 4px;"
                  />
                  <div v-else style="width: 44px; height: 44px; background: #e2e8f0; border-radius: 4px;"></div>
                </td>
                <td>
                  <strong>{{ sp.title }}</strong>
                  <div v-if="sp.custom_overrides.custom_title" style="font-size: 11px; color: #6b21a8;">
                    Original: {{ sp.original_product?.title }}
                  </div>
                </td>
                <td>
                  <strong>${{ Number(sp.price).toFixed(2) }}</strong>
                  <div v-if="sp.custom_overrides.custom_price" style="font-size: 11px; color: #6b21a8;">
                    Original: ${{ Number(sp.original_product?.price).toFixed(2) }}
                  </div>
                </td>
                <td>{{ sp.stock }} units</td>
                <td>
                  <span class="badge" :class="sp.is_active ? 'badge-green' : 'badge-amber'">
                    {{ sp.is_active ? 'Active' : 'Hidden' }}
                  </span>
                </td>
                <td>
                  <span v-if="sp.custom_overrides.custom_price || sp.custom_overrides.custom_title || sp.custom_overrides.custom_thumbnail" class="badge badge-purple">
                    Customized
                  </span>
                  <span v-else style="color: var(--text-muted); font-size: 12px;">Default</span>
                </td>
                <td style="display: flex; gap: 6px;">
                  <button @click="openEditOverrideModal(sp)" class="btn btn-secondary btn-sm">Edit</button>
                  <button @click="detachProduct(sp.id)" class="btn btn-danger btn-sm">Remove</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="showAttachModal" class="modal-overlay" @click.self="showAttachModal = false">
      <div class="modal-content">
        <h3 style="margin-bottom: 12px;">Add Product to My Store</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
          Attach <strong>"{{ selectedMasterProduct?.title }}"</strong> with optional store-level overrides. Leave fields blank to inherit master values.
        </p>

        <form @submit.prevent="submitAttach">
          <div class="form-group">
            <label class="form-label">Custom Store Title (Optional)</label>
            <input v-model="attachForm.custom_title" type="text" class="form-control" :placeholder="selectedMasterProduct?.title" />
          </div>

          <div class="form-group">
            <label class="form-label">Custom Store Price ($)</label>
            <input v-model="attachForm.custom_price" type="number" step="0.01" class="form-control" :placeholder="'Master Price: $' + selectedMasterProduct?.price" />
          </div>

          <div class="form-group">
            <label class="form-label">Custom Store Description (Optional)</label>
            <textarea v-model="attachForm.custom_description" class="form-control" rows="2" :placeholder="selectedMasterProduct?.description"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Custom Store Thumbnail URL (Optional)</label>
            <input v-model="attachForm.custom_thumbnail" type="text" class="form-control" placeholder="https://..." />
          </div>

          <div class="grid grid-cols-2">
            <div class="form-group">
              <label class="form-label">Initial Stock</label>
              <input v-model="attachForm.stock" type="number" min="0" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="form-label">Visibility Status</label>
              <select v-model="attachForm.is_active" class="form-control">
                <option :value="true">Published (Active)</option>
                <option :value="false">Draft (Hidden)</option>
              </select>
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
            <button type="button" @click="showAttachModal = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Save to My Store</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showEditOverrideModal" class="modal-overlay" @click.self="showEditOverrideModal = false">
      <div class="modal-content">
        <h3 style="margin-bottom: 12px;">Edit Store Overrides</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
          Update override values for <strong>"{{ editingShopProduct?.title }}"</strong>.
        </p>

        <form @submit.prevent="submitUpdateOverride">
          <div class="form-group">
            <label class="form-label">Custom Store Title</label>
            <input v-model="editOverrideForm.custom_title" type="text" class="form-control" />
          </div>

          <div class="form-group">
            <label class="form-label">Custom Store Price ($)</label>
            <input v-model="editOverrideForm.custom_price" type="number" step="0.01" class="form-control" />
          </div>

          <div class="form-group">
            <label class="form-label">Custom Store Description</label>
            <textarea v-model="editOverrideForm.custom_description" class="form-control" rows="2"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Custom Store Thumbnail URL</label>
            <input v-model="editOverrideForm.custom_thumbnail" type="text" class="form-control" />
          </div>

          <div class="grid grid-cols-2">
            <div class="form-group">
              <label class="form-label">Store Stock</label>
              <input v-model="editOverrideForm.stock" type="number" min="0" class="form-control" required />
            </div>
            <div class="form-group">
              <label class="form-label">Visibility Status</label>
              <select v-model="editOverrideForm.is_active" class="form-control">
                <option :value="true">Published (Active)</option>
                <option :value="false">Draft (Hidden)</option>
              </select>
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
            <button type="button" @click="showEditOverrideModal = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Overrides</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showNewMasterModal" class="modal-overlay" @click.self="showNewMasterModal = false">
      <div class="modal-content">
        <h3 style="margin-bottom: 12px;">Create New Master Catalog Product</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
          Products added here will be immediately visible to all store owners in the Shared Catalog.
        </p>

        <form @submit.prevent="submitNewMasterProduct">
          <div class="form-group">
            <label class="form-label">Product Title</label>
            <input v-model="newMasterForm.title" type="text" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Base Catalog Price ($)</label>
            <input v-model="newMasterForm.price" type="number" step="0.01" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea v-model="newMasterForm.description" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Thumbnail URL</label>
            <input v-model="newMasterForm.thumbnail" type="text" class="form-control" placeholder="https://..." />
          </div>
          <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
            <button type="button" @click="showNewMasterModal = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Publish to Global Catalog</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../api'

const activeTab = ref('catalog')
const alertMessage = ref('')
const errorMessage = ref('')
const user = ref(null)

const catalog = ref([])
const myProducts = ref([])

const showAttachModal = ref(false)
const selectedMasterProduct = ref(null)
const attachForm = ref({
  product_id: null,
  custom_title: '',
  custom_price: '',
  custom_description: '',
  custom_thumbnail: '',
  stock: 10,
  is_active: true
})

const showEditOverrideModal = ref(false)
const editingShopProduct = ref(null)
const editOverrideForm = ref({
  custom_title: '',
  custom_price: '',
  custom_description: '',
  custom_thumbnail: '',
  stock: 0,
  is_active: true
})

const showNewMasterModal = ref(false)
const newMasterForm = ref({
  title: '',
  price: '',
  description: '',
  thumbnail: ''
})

const showNotice = (msg) => {
  alertMessage.value = msg
  setTimeout(() => { alertMessage.value = '' }, 4000)
}

const showError = (msg) => {
  errorMessage.value = msg
  setTimeout(() => { errorMessage.value = '' }, 5000)
}

const isAttached = (productId) => {
  return myProducts.value.some(p => p.product_id === productId)
}

const loadData = async () => {
  try {
    const meRes = await api.get('/auth/me')
    user.value = meRes.data.data

    const [catalogRes, myRes] = await Promise.all([
      api.get('/shop-owner/catalog'),
      api.get('/shop-owner/my-shop/products')
    ])

    catalog.value = catalogRes.data.data
    myProducts.value = myRes.data.data
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to load shop owner data.')
  }
}

const openAttachModal = (product) => {
  selectedMasterProduct.value = product
  const existing = myProducts.value.find(p => p.product_id === product.id)

  if (existing) {
    openEditOverrideModal(existing)
    return
  }

  attachForm.value = {
    product_id: product.id,
    custom_title: '',
    custom_price: '',
    custom_description: '',
    custom_thumbnail: '',
    stock: 10,
    is_active: true
  }
  showAttachModal.value = true
}

const submitAttach = async () => {
  try {
    await api.post('/shop-owner/my-shop/products/attach', attachForm.value)
    showNotice('Product attached to your shop.')
    showAttachModal.value = false
    loadData()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to attach product.')
  }
}

const openEditOverrideModal = (sp) => {
  editingShopProduct.value = sp
  editOverrideForm.value = {
    custom_title: sp.custom_overrides.custom_title || '',
    custom_price: sp.custom_overrides.custom_price !== null ? sp.custom_overrides.custom_price : '',
    custom_description: sp.custom_overrides.custom_description || '',
    custom_thumbnail: sp.custom_overrides.custom_thumbnail || '',
    stock: sp.stock,
    is_active: sp.is_active
  }
  showEditOverrideModal.value = true
}

const submitUpdateOverride = async () => {
  try {
    await api.put(`/shop-owner/my-shop/products/${editingShopProduct.value.id}`, editOverrideForm.value)
    showNotice('Store overrides updated.')
    showEditOverrideModal.value = false
    loadData()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to update overrides.')
  }
}

const detachProduct = async (id) => {
  if (!confirm('Are you sure you want to remove this product from your shop storefront?')) return
  try {
    await api.delete(`/shop-owner/my-shop/products/${id}`)
    showNotice('Product detached from shop.')
    loadData()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to detach product.')
  }
}

const submitNewMasterProduct = async () => {
  try {
    await api.post('/shop-owner/catalog', newMasterForm.value)
    showNotice('New product submitted to shared global catalog.')
    showNewMasterModal.value = false
    newMasterForm.value = { title: '', price: '', description: '', thumbnail: '' }
    loadData()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to create product.')
  }
}

onMounted(() => {
  loadData()
})
</script>

