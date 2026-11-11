<template>
  <div style="max-width: 480px; margin: 40px auto;">
    <div class="card">
      <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 6px;">Sign In to Portal</h1>
        <p style="color: var(--text-muted); font-size: 14px;">Select a test account or enter your credentials</p>
      </div>

      <div style="background: #f1f5f9; border-radius: var(--radius); padding: 12px; margin-bottom: 24px;">
        <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: #475569; margin-bottom: 8px;">
          Quick Login Presets (Seeded Test Accounts):
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px;">
          <button @click="fillCredentials('admin@example.com', 'password')" class="btn btn-secondary btn-sm" style="justify-content: space-between;">
            <span>System Admin (Global Access)</span>
            <span class="badge badge-purple">Admin</span>
          </button>
          <button @click="fillCredentials('owner1@example.com', 'password')" class="btn btn-secondary btn-sm" style="justify-content: space-between;">
            <span>Shop Owner 1 (Apex Electronics)</span>
            <span class="badge badge-blue">Owner</span>
          </button>
          <button @click="fillCredentials('owner2@example.com', 'password')" class="btn btn-secondary btn-sm" style="justify-content: space-between;">
            <span>Shop Owner 2 (Metro Fashion)</span>
            <span class="badge badge-blue">Owner</span>
          </button>
        </div>
      </div>

      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input v-model="email" type="email" class="form-control" placeholder="name@example.com" required />
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input v-model="password" type="password" class="form-control" placeholder="••••••••" required />
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;" :disabled="loading">
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()
const email = ref('admin@example.com')
const password = ref('password')
const loading = ref(false)
const error = ref(null)

const fillCredentials = (e, p) => {
  email.value = e
  password.value = p
}

const handleLogin = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await api.post('/auth/login', {
      email: email.value,
      password: password.value
    })

    const { token, user } = res.data.data
    localStorage.setItem('auth_token', token)
    localStorage.setItem('user', JSON.stringify(user))

    window.dispatchEvent(new Event('auth-changed'))

    if (user.role === 'admin') {
      router.push('/admin')
    } else if (user.role === 'shop_owner') {
      router.push('/shop-owner')
    } else {
      router.push('/shops')
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed. Please check your credentials.'
  } finally {
    loading.value = false
  }
}
</script>

