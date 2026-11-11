<template>
  <div style="min-height: 100vh; display: flex; flex-direction: column;">
    <header style="background: #ffffff; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100;">
      <div class="container" style="display: flex; justify-content: space-between; align-items: center; height: 64px;">
        <div style="display: flex; align-items: center; gap: 24px;">
          <router-link to="/shops" style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 18px; color: var(--text-main); text-decoration: none;">
            <div style="width: 32px; height: 32px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 800; font-size: 16px;">
              M
            </div>
            <span>CatalogHub</span>
          </router-link>

          <nav style="display: flex; gap: 16px; font-size: 14px; font-weight: 500;">
            <router-link to="/shops" class="nav-link" active-class="active">Stores</router-link>
            <router-link v-if="user?.role === 'admin'" to="/admin" class="nav-link" active-class="active">Admin Portal</router-link>
            <router-link v-if="user?.role === 'shop_owner'" to="/shop-owner" class="nav-link" active-class="active">Owner Portal</router-link>
          </nav>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
          <template v-if="user">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
              <span style="font-weight: 600;">{{ user.name }}</span>
              <span class="badge" :class="user.role === 'admin' ? 'badge-purple' : 'badge-blue'">{{ user.role }}</span>
              <span v-if="user.shop" class="badge badge-green">{{ user.shop.name }}</span>
            </div>
            <button @click="handleLogout" class="btn btn-secondary btn-sm">Sign Out</button>
          </template>
          <template v-else>
            <router-link to="/login" class="btn btn-primary btn-sm">Sign In / Switch Role</router-link>
          </template>
        </div>
      </div>
    </header>

    <main class="container" style="flex: 1; padding-top: 32px; padding-bottom: 48px;">
      <router-view />
    </main>

    <footer style="background: #ffffff; border-top: 1px solid var(--border); padding: 24px 0; text-align: center; font-size: 13px; color: var(--text-muted);">
      <div class="container">
        Multi-Tenant Shared Catalog E-Commerce Management Platform
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from './api'

const router = useRouter()
const user = ref(null)

const checkUser = () => {
  const stored = localStorage.getItem('user')
  if (stored) {
    try {
      user.value = JSON.parse(stored)
    } catch {
      user.value = null
    }
  } else {
    user.value = null
  }
}

const handleLogout = async () => {
  try {
    await api.post('/auth/logout')
  } catch {
  } finally {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    user.value = null
    router.push('/login')
  }
}

onMounted(() => {
  checkUser()
  window.addEventListener('auth-changed', checkUser)
})
</script>

<style scoped>
.nav-link {
  color: var(--text-muted);
  text-decoration: none;
  padding: 6px 10px;
  border-radius: var(--radius);
  transition: all 0.2s ease;
}

.nav-link:hover {
  color: var(--text-main);
  background: #f1f5f9;
  text-decoration: none;
}

.nav-link.active {
  color: var(--primary);
  background: #eff6ff;
  font-weight: 600;
}
</style>
