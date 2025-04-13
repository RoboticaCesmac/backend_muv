<template>
  <div class="dashboard-layout">
    <aside class="sidebar" :class="{ 'collapsed': isCollapsed }">
      <button @click="toggleSidebar" class="toggle-btn">
        <i class="fa-solid fa-bars"></i>
      </button>

      <div class="logo-container">
        <img src="/images/LogoMarcaCortada.png" alt="Logo" class="logo" />
      </div>

      <nav class="nav-menu">
        <Link 
          v-for="item in menuItems" 
          :key="item.path"
          :href="item.path"
          class="nav-item"
          :class="{ 
            'active': !isCollapsed && $page.url.startsWith(item.path),
            'active-collapsed': isCollapsed && $page.url.startsWith(item.path),
            'collapsed': isCollapsed 
          }"
          :title="isCollapsed ? item.name : ''"
        >
          <i :class="item.icon"></i>
          <span v-show="!isCollapsed">{{ item.name }}</span>
        </Link>
      </nav>

      <div class="logout-container">
        <Link 
          href="/logout" 
          method="post" 
          as="button" 
          class="nav-item logout-btn-bottom" 
          :class="{ 'collapsed': isCollapsed }"
        >
          <i class="fa-solid fa-right-from-bracket"></i>
          <span v-show="!isCollapsed">Sair</span>
        </Link>
      </div>
    </aside>

    <main class="main-content">
      <header class="content-header">
        <h2>{{ currentPage }}</h2>
        <div class="user-profile">
          <span class="user-name">{{ userName }}</span>
        </div>
      </header>

      <!-- Page Content -->
      <div class="content-area">
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const isCollapsed = ref(false);

const menuItems = [
  { name: 'Dashboard', path: '/home', icon: 'fa-solid fa-gauge-high' },
  { name: 'Projetos', path: '/projects', icon: 'fa-solid fa-diagram-project' },
  { name: 'Relatórios', path: '/reports', icon: 'fa-solid fa-chart-line' },
  { name: 'Configurações', path: '/settings', icon: 'fa-solid fa-gear' }
];

const userName = computed(() => {
  try {
    return page.props.auth?.user?.name || 'Usuário';
  } catch (error) {
    return 'Usuário';
  }
});

const currentPage = computed(() => {
  const path = page.url;
  const current = menuItems.find(item => path.startsWith(item.path));
  return current ? current.name : 'Dashboard';
});

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
};
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.sidebar {
  width: 280px;
  background-color: #ffffff;
  padding: 2rem;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  position: relative;
  transition: all 0.3s ease;
}

.sidebar.collapsed {
  width: 80px;
  padding: 2rem 1rem;
}

.toggle-btn {
  position: absolute;
  right: -12px;
  top: 2rem;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  color: #6c757d;
}

.toggle-btn:hover {
  background-color: #f8f9fa;
  color: #3490dc;
}

.logo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 3rem;
}

.logo {
  width: 40%;
  transition: all 0.3s ease;
}

.sidebar.collapsed .logo {
  width: 100%;
  margin-right: 0;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
  transition: opacity 0.3s ease;
}

.nav-menu {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  text-decoration: none;
  color: #6c757d;
  border-radius: 8px;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.nav-item.collapsed {
  justify-content: center;
  padding: 1rem 0;
}

.nav-item i {
  margin-right: 1rem;
  width: 20px;
  text-align: center;
  font-size: 1.1rem;
}

.nav-item.collapsed i {
  margin-right: 0;
}

.nav-item:hover {
  background-color: #e9ecef;
  color: #3490dc;
}

.nav-item.active {
  background-color: #3490dc;
  color: white;
}

.nav-item.active:hover {
  background-color: #2779bd;
  color: white;
}

.nav-item.active-collapsed {
  color: #3490dc;
}

.nav-item.active-collapsed:hover {
  background-color: #e9ecef;
}

.main-content {
  flex: 1;
  padding: 2rem;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  font-weight: 500;
  color: #2c3e50;
}

.logout-container {
  margin-top: auto;
  padding-top: 1rem;
}

.logout-btn-bottom {
  margin: 0;
  color: #dc3545;
  width: 100%;
  font-size: 1.1rem;
  font-weight: 500;
  background: none;
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
}

.logout-btn-bottom i {
  color: #dc3545;
  font-size: 1.2rem;
}

.logout-btn-bottom:hover {
  background-color: #dc3545;
  color: white;
  transform: translateY(-1px);
}

.logout-btn-bottom:hover i {
  color: white;
}

.content-area {
  background-color: #ffffff;
  border-radius: 12px;
  padding: 2rem;
  min-height: calc(100vh - 200px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
</style> 