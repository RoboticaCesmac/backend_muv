<template>
  <div class="dashboard-layout">
    <!-- Botão toggle independente -->
    <button @click="toggleSidebar" class="toggle-sidebar-btn" :class="{ 'toggle-collapsed': isCollapsed }">
      <i :class="isCollapsed ? 'fa-solid fa-chevron-right' : 'fa-solid fa-chevron-left'"></i>
    </button>

    <!-- Menu móvel para dispositivos pequenos -->
    <div class="mobile-header" v-show="isMobile">
      <div class="mobile-logo">
        <img src="/images/LogoMarcaCortada.png" alt="Logo" />
      </div>
      <button @click="toggleMobileMenu" class="mobile-menu-btn">
        <i :class="showMobileMenu ? 'fa-solid fa-times' : 'fa-solid fa-bars'"></i>
      </button>
    </div>

    <!-- Menu móvel overlay -->
    <div v-if="showMobileMenu && isMobile" class="mobile-menu-overlay" @click="showMobileMenu = false">
      <div class="mobile-menu" @click.stop>
        <div class="mobile-user-info">
          <div class="user-avatar">
            <img :src="userAvatar" :alt="userName" v-if="userAvatar">
            <span v-else>{{ userInitials }}</span>
          </div>
          <div class="user-details">
            <span class="user-name">{{ userName }}</span>
            <span class="user-email">{{ userEmail }}</span>
          </div>
        </div>
        <nav class="mobile-nav">
          <Link 
            v-for="item in menuItems" 
            :key="item.path"
            :href="item.path"
            class="mobile-nav-item"
            :class="{ 'active': $page.url.startsWith(item.path) }"
            @click="showMobileMenu = false"
          >
            <i :class="item.icon"></i>
            <span>{{ item.name }}</span>
          </Link>
          <Link 
            href="/logout"
            method="post"
            as="button"
            class="mobile-nav-item mobile-logout"
          >
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Sair</span>
          </Link>
        </nav>
      </div>
    </div>

    <div class="sidebar-container">
      <aside class="sidebar" :class="{ 'collapsed': isCollapsed, 'hidden': isMobile }">
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
    </div>

    <main class="main-content" :class="{ 'full-width': isMobile }">
      <header class="content-header">
        <h2>{{ currentPage }}</h2>
        <div class="user-profile">
          <div class="user-info">
            <span class="user-email" v-if="userEmail">{{ userEmail }}</span>
            <span class="user-role">{{ userRole }}</span>
          </div>
          <div class="user-avatar" @click="toggleUserMenu">
            <img :src="userAvatar" :alt="userName" v-if="userAvatar">
            <span v-else>{{ userInitials }}</span>
          </div>
          
          <!-- Menu de usuário dropdown -->
          <div v-if="showUserMenu" class="user-menu">
            <div class="user-menu-header">
              <div class="user-menu-avatar">
                <img :src="userAvatar" :alt="userName" v-if="userAvatar">
                <span v-else>{{ userInitials }}</span>
              </div>
              <div class="user-menu-info">
                <span class="user-menu-name">{{ userName }}</span>
                <span class="user-menu-email" v-if="userEmail">{{ userEmail }}</span>
              </div>
            </div>
            <div class="user-menu-body">
              <Link 
                href="/logout" 
                method="post" 
                as="button" 
                class="user-menu-item user-menu-logout"
              >
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Sair</span>
              </Link>
            </div>
          </div>
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
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const isCollapsed = ref(false);
const showUserMenu = ref(false);
const isMobile = ref(false);
const showMobileMenu = ref(false);

const menuItems = [
  { name: 'Usuários', path: '/home', icon: 'fa-solid fa-users' },
  { name: 'Veículos', path: '/vehicles', icon: 'fa-solid fa-car' },
  { name: 'Níveis', path: '/levels', icon: 'fa-solid fa-trophy' },
  { name: 'Avatares', path: '/avatars', icon: 'fa-solid fa-user-circle' }
];

const userName = computed(() => {
  const user = page.props.auth?.user;
  if (user) {
    return user.user_name || user.email || 'Administrador';
  }
  return 'Administrador';
});

const userEmail = computed(() => {
  const user = page.props.auth?.user;
  if (user) {
    return user.email || '';
  }
  return '';
});

const userRole = computed(() => {
  return 'Administrador';
});

const userAvatar = computed(() => {
  const user = page.props.auth?.user;
  if (user && user.avatar && user.avatar.avatar_path) {
    const path = user.avatar.avatar_path;
    return path.startsWith('/') ? path : `/${path}`;
  }
  return null;
});

const userInitials = computed(() => {
  if (!userName.value) return '?';
  return userName.value
    .split(' ')
    .map(name => name[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
});

const currentPage = computed(() => {
  const path = page.url;
  const current = menuItems.find(item => path.startsWith(item.path));
  return current ? current.name : 'Dashboard';
});

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
};

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value;
};

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
};

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768;
};

// Fechar o menu quando clicar fora dele
const closeUserMenu = (e) => {
  if (!e.target.closest('.user-profile')) {
    showUserMenu.value = false;
  }
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
  document.addEventListener('click', closeUserMenu);
  
  console.log('Dados do usuário:', {
    auth: page.props.auth,
    user: page.props.auth?.user,
    nome: page.props.auth?.user?.user_name,
    email: page.props.auth?.user?.email,
    props: page.props
  });
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkMobile);
  document.removeEventListener('click', closeUserMenu);
});
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8f9fa;
  position: relative;
}

/* Mobile Styles */
.mobile-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 60px;
  background-color: #ffffff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 100;
}

.mobile-logo {
  height: 40px;
}

.mobile-logo img {
  height: 100%;
}

.mobile-menu-btn {
  width: 40px;
  height: 40px;
  border: none;
  background-color: transparent;
  font-size: 1.2rem;
  color: #3490dc;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mobile-menu-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 200;
}

.mobile-menu {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  width: 85%;
  max-width: 320px;
  background-color: #ffffff;
  box-shadow: -2px 0 15px rgba(0, 0, 0, 0.1);
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  z-index: 300;
  overflow-y: auto;
  animation: slide-in 0.25s ease;
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

.mobile-user-info {
  display: flex;
  align-items: center;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  border-bottom: 1px solid #e2e8f0;
  background-color: #f8fafc;
  border-radius: 10px;
}

.mobile-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.mobile-nav-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  text-decoration: none;
  color: #6c757d;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.mobile-nav-item.active {
  background-color: #3490dc;
  color: white;
  box-shadow: 0 4px 8px rgba(52, 144, 220, 0.2);
}

.mobile-nav-item i {
  margin-right: 1rem;
  width: 20px;
  text-align: center;
}

.mobile-logout {
  margin-top: auto;
  color: #dc3545;
}

/* Sidebar Styles */
.sidebar-container {
  position: relative;
  height: 100vh;
}

.sidebar {
  width: 280px;
  min-width: 280px;
  background-color: #ffffff;
  padding: 2rem;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
  overflow-y: auto;
  height: 100%;
  z-index: 50;
}

.sidebar.collapsed {
  width: 80px;
  min-width: 80px;
  padding: 2rem 1rem;
}

.sidebar.hidden {
  display: none;
}

.toggle-sidebar-btn {
  position: fixed;
  top: 2rem;
  left: 266px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background-color: #3490dc;
  border: 2px solid #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 2000;
  color: #ffffff;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

.toggle-sidebar-btn.toggle-collapsed {
  left: 66px;
}

.toggle-sidebar-btn:hover {
  background-color: #2779bd;
  transform: scale(1.1);
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
  padding-left: 2rem;
  overflow-x: hidden;
  position: relative;
  z-index: 10;
}

.main-content.full-width {
  padding-top: 80px;
  padding-left: 3rem;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.content-header h2 {
  color: #1e293b;
  font-weight: 600;
  position: relative;
  padding-left: 0.75rem;
}

.content-header h2::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background-color: #3490dc;
  border-radius: 2px;
}

/* User Profile Styles */
.user-profile {
  display: flex;
  align-items: center;
  gap: 1rem;
  position: relative;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-name {
  font-weight: 500;
  color: #2c3e50;
}

.user-email {
  font-size: 0.85rem;
  color: #6c757d;
}

.user-role {
  font-size: 0.75rem;
  color: #3490dc;
  font-weight: 600;
  background-color: rgba(52, 144, 220, 0.1);
  padding: 2px 8px;
  border-radius: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.user-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #3490dc;
  color: white;
  font-weight: 600;
  cursor: pointer;
  overflow: hidden;
  border: 2px solid #e2e8f0;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(52, 144, 220, 0.2);
}

.user-avatar:hover {
  border-color: #3490dc;
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(52, 144, 220, 0.3);
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* User Menu Dropdown */
.user-menu {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  width: 280px;
  z-index: 100;
  overflow: hidden;
  transform-origin: top right;
  animation: menu-appear 0.2s ease;
}

@keyframes menu-appear {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.user-menu-header {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #e2e8f0;
  background-color: #f8fafc;
}

.user-menu-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #3490dc;
  color: white;
  font-weight: 600;
  overflow: hidden;
}

.user-menu-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-menu-info {
  display: flex;
  flex-direction: column;
}

.user-menu-name {
  font-weight: 600;
  color: #2c3e50;
}

.user-menu-email {
  font-size: 0.85rem;
  color: #6c757d;
}

.user-menu-body {
  padding: 0.5rem;
}

.user-menu-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  color: #6c757d;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
  width: 100%;
  background: none;
  border: none;
  text-align: left;
}

.user-menu-item i {
  margin-right: 0.75rem;
  width: 20px;
  text-align: center;
}

.user-menu-item:hover {
  background-color: #f8f9fa;
}

.user-menu-logout {
  color: #dc3545;
}

.user-menu-logout:hover {
  background-color: #dc3545;
  color: white;
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
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .content-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .content-area {
    padding: 1.5rem;
  }
}

@media (max-width: 576px) {
  .content-area {
    padding: 1rem;
  }
  
  .user-info {
    display: none;
  }
}
</style> 