<template>
  <DashboardLayout>
    <div class="welcome-section">
      <h1>{{ title}}</h1>
      <p>{{ description }}</p>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { onMounted } from 'vue';

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  },
  description: {
    type: String,
    default: 'Bem-vindo ao seu painel de controle'
  }
});

onMounted(() => {
  console.log('Props recebidas:', props);
});
</script>

<style scoped>
.welcome-section {
  text-align: center;
  padding: 2rem;
}

.welcome-section h1 {
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 1rem;
}

.welcome-section p {
  color: #6c757d;
  font-size: 1.1rem;
}
</style> 