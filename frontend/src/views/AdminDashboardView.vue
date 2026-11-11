<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
      <div>
        <span class="badge badge-purple" style="margin-bottom: 6px;">Administrator</span>
        <h1 style="font-size: 26px; font-weight: 700;">Global Administration Dashboard</h1>
      </div>

      <div style="display: flex; gap: 8px;">
        <button
          v-for="tab in ['shops', 'owners', 'products']"
          :key="tab"
          @click="activeTab = tab"
          class="btn btn-sm"
          :class="activeTab === tab ? 'btn-primary' : 'btn-secondary'"
        >
          {{ tab === 'shops' ? 'Manage Shops' : tab === 'owners' ? 'Manage Shop Owners' : 'Master Catalog' }}
        </button>
      </div>
    </div>

    <div v-if="alertMessage" class="alert alert-success">
      {{ alertMessage }}
    </div>

    <div v-if="errorMessage" class="alert alert-danger">
      {{ errorMessage }}
    </div>

    <div v-if="activeTab === 'shops'">
      <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <h2 style="font-size: 18px; font-weight: 600;">Registered Stores</h2>
          <button @click="openCreateShopModal" class="btn btn-primary btn-sm">+ Register New Shop</button>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Products</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="shop in shops" :key="shop.id">
                <td>#{{ shop.id }}</td>
                <td><strong>{{ shop.name }}</strong></td>
                <td><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ shop.slug }}</code></td>
                <td>
                  <span class="badge" :class="shop.status === 'active' ? 'badge-green' : 'badge-amber'">
                    {{ shop.status }}
                  </span>
                </td>
                <td>{{ shop.products_count ?? 0 }} items</td>
                <td style="display: flex; gap: 8px;">
                  <button @click="openEditShopModal(shop)" class="btn btn-secondary btn-sm">Edit</button>
                  <button @click="deleteShop(shop.id)" class="btn btn-danger btn-sm">Delete</button>
                  <router-link :to="'/shops/' + shop.slug" class="btn btn-secondary btn-sm" target="_blank">Storefront</router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'owners'">
      <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <h2 style="font-size: 18px; font-weight: 600;">Shop Owner Profiles</h2>
          <button @click="showOwnerModal = true" class="btn btn-primary btn-sm">+ Create Shop Owner</button>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Assigned Shop</th>
                <th>Role</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="owner in owners" :key="owner.id">
                <td>#{{ owner.id }}</td>
                <td><strong>{{ owner.name }}</strong></td>
                <td>{{ owner.email }}</td>
                <td>
                  <span v-if="owner.shop" class="badge badge-blue">{{ owner.shop.name }}</span>
                  <span v-else style="color: var(--text-muted);">Unassigned</span>
                </td>
                <td><span class="badge badge-purple">{{ owner.role }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'products'">
      <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div>
            <h2 style="font-size: 18px; font-weight: 600;">Shared Global Product Catalog</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Master items available for all shop owners to attach to their stores</p>
          </div>
          <button @click="openCreateProductModal" class="btn btn-primary btn-sm">+ Create Master Product</button>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Title</th>
                <th>Base Price</th>
                <th>Created By</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products" :key="product.id">
                <td>#{{ product.id }}</td>
                <td style="width: 60px;">
                  <img
                    v-if="product.thumbnail"
                    :src="product.thumbnail"
                    style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px;"
                  />
                  <div v-else style="width: 48px; height: 48px; background: #e2e8f0; border-radius: 4px;"></div>
                </td>
                <td>
                  <strong>{{ product.title }}</strong>
                  <div style="font-size: 12px; color: var(--text-muted);">{{ product.slug }}</div>
                </td>
                <td><strong>${{ Number(product.price).toFixed(2) }}</strong></td>
                <td>
                  <span v-if="product.creator" class="badge badge-blue">{{ product.creator.name }} ({{ product.creator.role }})</span>
                  <span v-else style="color: var(--text-muted);">System</span>
                </td>
                <td style="display: flex; gap: 8px;">
                  <button @click="openEditProductModal(product)" class="btn btn-secondary btn-sm">Edit</button>
                  <button @click="deleteProduct(product.id)" class="btn btn-danger btn-sm">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="showShopModal" class="modal-overlay" @click.self="showShopModal = false">
      <div class="modal-content">
        <h3 style="margin-bottom: 16px;">{{ editingShop ? 'Edit Shop' : 'Register New Shop' }}</h3>
        <form @submit.prevent="saveShop">
          <div class="form-group">
            <label class="form-label">Shop Name</label>
            <input v-model="shopForm.name" type="text" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea v-model="shopForm.description" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select v-model="shopForm.status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
            <button type="button" @click="showShopModal = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">{{ editingShop ? 'Update' : 'Create' }}</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showOwnerModal" class="modal-overlay" @click.self="showOwnerModal = false">
      <div class="modal-content">
        <h3 style="margin-bottom: 16px;">Create Shop Owner Account</h3>
        <form @submit.prevent="saveOwner">
          <div class="form-group">
            <label class="form-label">Owner Name</label>
            <input v-model="ownerForm.name" type="text" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input v-model="ownerForm.email" type="email" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Password</label>
            <input v-model="ownerForm.password" type="password" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Assign To Shop</label>
            <select v-model="ownerForm.shop_id" class="form-control" required>
              <option value="" disabled>Select a shop</option>
              <option v-for="shop in shops" :key="shop.id" :value="shop.id">{{ shop.name }}</option>
            </select>
          </div>
          <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
            <button type="button" @click="showOwnerModal = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Account</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showProductModal" class="modal-overlay" @click.self="showProductModal = false">
      <div class="modal-content">
        <h3 style="margin-bottom: 16px;">{{ editingProduct ? 'Edit Master Product' : 'Add Master Product' }}</h3>
        <form @submit.prevent="saveProduct">
          <div class="form-group">
            <label class="form-label">Product Title</label>
            <input v-model="productForm.title" type="text" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Base Price ($)</label>
            <input v-model="productForm.price" type="number" step="0.01" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea v-model="productForm.description" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Thumbnail URL</label>
            <input v-model="productForm.thumbnail" type="text" class="form-control" placeholder="https://..." />
          </div>
          <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
            <button type="button" @click="showProductModal = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">{{ editingProduct ? 'Update' : 'Create' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../api'

const activeTab = ref('shops')
const alertMessage = ref('')
const errorMessage = ref('')

const shops = ref([])
const owners = ref([])
const products = ref([])

const showShopModal = ref(false)
const editingShop = ref(null)
const shopForm = ref({ name: '', description: '', status: 'active' })

const showOwnerModal = ref(false)
const ownerForm = ref({ name: '', email: '', password: '', shop_id: '' })

const showProductModal = ref(false)
const editingProduct = ref(null)
const productForm = ref({ title: '', price: '', description: '', thumbnail: '' })

const showNotice = (msg) => {
  alertMessage.value = msg
  setTimeout(() => { alertMessage.value = '' }, 4000)
}

const showError = (msg) => {
  errorMessage.value = msg
  setTimeout(() => { errorMessage.value = '' }, 5000)
}

const fetchAll = async () => {
  try {
    const [shopsRes, ownersRes, productsRes] = await Promise.all([
      api.get('/admin/shops'),
      api.get('/admin/shop-owners'),
      api.get('/admin/products')
    ])
    shops.value = shopsRes.data.data
    owners.value = ownersRes.data.data
    products.value = productsRes.data.data
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to load admin data.')
  }
}

const openCreateShopModal = () => {
  editingShop.value = null
  shopForm.value = { name: '', description: '', status: 'active' }
  showShopModal.value = true
}

const openEditShopModal = (shop) => {
  editingShop.value = shop
  shopForm.value = { name: shop.name, description: shop.description, status: shop.status }
  showShopModal.value = true
}

const saveShop = async () => {
  try {
    if (editingShop.value) {
      await api.put(`/admin/shops/${editingShop.value.id}`, shopForm.value)
      showNotice('Shop updated successfully.')
    } else {
      await api.post('/admin/shops', shopForm.value)
      showNotice('Shop created successfully.')
    }
    showShopModal.value = false
    fetchAll()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to save shop.')
  }
}

const deleteShop = async (id) => {
  if (!confirm('Are you sure you want to delete this shop?')) return
  try {
    await api.delete(`/admin/shops/${id}`)
    showNotice('Shop deleted successfully.')
    fetchAll()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to delete shop.')
  }
}

const saveOwner = async () => {
  try {
    await api.post('/admin/shop-owners', ownerForm.value)
    showNotice('Shop owner created successfully.')
    showOwnerModal.value = false
    ownerForm.value = { name: '', email: '', password: '', shop_id: '' }
    fetchAll()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to create shop owner.')
  }
}

const openCreateProductModal = () => {
  editingProduct.value = null
  productForm.value = { title: '', price: '', description: '', thumbnail: '' }
  showProductModal.value = true
}

const openEditProductModal = (product) => {
  editingProduct.value = product
  productForm.value = {
    title: product.title,
    price: product.price,
    description: product.description,
    thumbnail: product.thumbnail
  }
  showProductModal.value = true
}

const saveProduct = async () => {
  try {
    if (editingProduct.value) {
      await api.put(`/admin/products/${editingProduct.value.id}`, productForm.value)
      showNotice('Product updated successfully.')
    } else {
      await api.post('/admin/products', productForm.value)
      showNotice('Product added to master catalog.')
    }
    showProductModal.value = false
    fetchAll()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to save product.')
  }
}

const deleteProduct = async (id) => {
  if (!confirm('Are you sure you want to delete this master product?')) return
  try {
    await api.delete(`/admin/products/${id}`)
    showNotice('Product deleted from master catalog.')
    fetchAll()
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to delete product.')
  }
}

onMounted(() => {
  fetchAll()
})
</script>

