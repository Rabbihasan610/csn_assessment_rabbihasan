import { createRouter, createWebHistory } from 'vue-router'
import PublicShopsView from './views/PublicShopsView.vue'
import ShopProductsView from './views/ShopProductsView.vue'
import ProductDetailView from './views/ProductDetailView.vue'
import LoginView from './views/LoginView.vue'
import AdminDashboardView from './views/AdminDashboardView.vue'
import ShopOwnerDashboardView from './views/ShopOwnerDashboardView.vue'

const routes = [
  { path: '/', redirect: '/shops' },
  { path: '/shops', name: 'public-shops', component: PublicShopsView },
  { path: '/shops/:slug', name: 'shop-products', component: ShopProductsView },
  { path: '/shops/:slug/products/:id', name: 'product-detail', component: ProductDetailView },
  { path: '/login', name: 'login', component: LoginView },
  { path: '/admin', name: 'admin-dashboard', component: AdminDashboardView },
  { path: '/shop-owner', name: 'shop-owner-dashboard', component: ShopOwnerDashboardView },
  { path: '/:pathMatch(.*)*', redirect: '/shops' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router

