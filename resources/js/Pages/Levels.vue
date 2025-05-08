<template>
  <DashboardLayout>
    <div class="levels-container">
      <div class="levels-header">
        <h1>Gerenciamento de Níveis</h1>
      </div>

      <div class="card">
        <div class="card-content">
          <div class="levels-grid">
            <div v-for="level in levels.data || levels" :key="level.id" class="level-card">
              <div class="level-image">
                <img :src="getImagePath(level.icon_path)" :alt="`Nível ${level.level_number}`">
              </div>
              <div class="level-info">
                <h4>Nível {{ level.level_number }}</h4>
                <div class="points-input">
                  <label>Pegada de carbono requerida:</label>
                  <div class="points-control">
                    <input 
                      type="number" 
                      v-model.number="pointsForm[level.id]" 
                      step="1"
                      min="0"
                      :disabled="level.level_number === 1"
                    >
                    <button 
                      @click="updatePoints(level)" 
                      class="save-btn"
                      :class="{
                        'disabled': pointsForm[level.id] === level.carbon_footprint_required || level.level_number === 1
                      }"
                      :disabled="pointsForm[level.id] === level.carbon_footprint_required || level.level_number === 1"
                    >
                      <span class="btn-content">
                        <i class="fas fa-save"></i>
                        <span class="btn-text">Salvar</span>
                      </span>
                      <span class="btn-bg"></span>
                    </button>
                  </div>
                  <p v-if="level.level_number === 1" class="help-text">
                    O Nível 1 deve ter sempre 0 pontos
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast de notificação -->
    <div v-if="toast.show" class="toast" :class="toast.type">
      <i :class="getToastIcon()"></i>
      <span>{{ toast.message }}</span>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
  levels: {
    type: Object,
    required: true
  }
});

const pointsForm = ref({});
const errors = ref({});

// Initialize the form with current values
onMounted(() => {
  const levelsList = props.levels.data || props.levels;
  levelsList.forEach(level => {
    pointsForm.value[level.id] = level.carbon_footprint_required;
  });
});

const getImagePath = (path) => {
  if (!path) return '';
  return path.startsWith('/') ? path : `/${path}`;
};

// Toast notification
const toast = ref({
  show: false,
  message: '',
  type: 'success',
  timeout: null
});

const showToast = (message, type = 'success', duration = 3000) => {
  if (toast.value.timeout) {
    clearTimeout(toast.value.timeout);
  }
  
  toast.value.show = true;
  toast.value.message = message;
  toast.value.type = type;
  
  toast.value.timeout = setTimeout(() => {
    toast.value.show = false;
  }, duration);
};

const getToastIcon = () => {
  switch (toast.value.type) {
    case 'success':
      return 'fas fa-check-circle';
    case 'error':
      return 'fas fa-exclamation-circle';
    case 'warning':
      return 'fas fa-exclamation-triangle';
    default:
      return 'fas fa-info-circle';
  }
};

const updatePoints = async (level) => {
  // Não permitir atualizar nível 1
  if (level.level_number === 1) {
    showToast('O Nível 1 deve ter sempre 0 pontos', 'warning');
    return;
  }

  errors.value = {};
  
  try {
    const response = await axios.put(`/levels/${level.id}`, {
      carbon_footprint_required: pointsForm.value[level.id]
    });
    
    level.carbon_footprint_required = pointsForm.value[level.id];
    showToast('Pegada de carbono do nível atualizada com sucesso', 'success');
  } catch (error) {
    console.error('Erro ao atualizar pegada de carbono do nível:', error);
    
    if (error.response && error.response.data && error.response.data.errors) {
      errors.value = error.response.data.errors;
      
      // Exibir o primeiro erro
      const firstError = Object.values(error.response.data.errors)[0][0];
      showToast(firstError, 'error');
      
      // Resetar o valor do formulário para o valor original
      pointsForm.value[level.id] = level.carbon_footprint_required;
    } else {
      showToast('Erro ao atualizar a pegada de carbono do nível', 'error');
    }
  }
};
</script>

<style scoped>
.levels-container {
  padding: 1rem;
}

.levels-header {
  margin-bottom: 2rem;
}

.levels-header h1 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin: 0;
}

.card {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.card-content {
  padding: 1.5rem;
}

.levels-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.level-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: transform 0.2s, box-shadow 0.2s;
}

.level-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.level-card:nth-child(1) .level-image::after {
  background: linear-gradient(135deg, #CD7F32, #A05A2C);
}

.level-card:nth-child(2) .level-image::after {
  background: linear-gradient(135deg, #C0C0C0, #A9A9A9);
}

.level-card:nth-child(3) .level-image::after {
  background: linear-gradient(135deg, #FFD700, #DAA520);
}

.level-card:nth-child(4) .level-image::after {
  background: linear-gradient(135deg, #B9F2FF, #00BFFF);
}

.level-card:nth-child(5) .level-image::after {
  background: linear-gradient(135deg, #A3FFA3, #32CD32);
}

.level-image {
  width: 100px;
  height: 100px;
  margin-bottom: 1rem;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  z-index: 0;
}

.level-image img {
  max-width: 90%;
  max-height: 90%;
  object-fit: contain;
  z-index: 1;
  border-radius: 50%;
}

.level-info {
  text-align: center;
  width: 100%;
}

.level-info h4 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 1.25rem;
}

.points-input {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.points-input label {
  font-size: 0.875rem;
  color: #4a5568;
  font-weight: 500;
}

.points-control {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.points-control input {
  width: 80px;
  padding: 0.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  text-align: center;
  font-weight: 500;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.points-control input:focus {
  outline: none;
  border-color: #3490dc;
  box-shadow: 0 0 0 3px rgba(52, 144, 220, 0.15);
}

.points-control input:disabled {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

.help-text {
  font-size: 0.75rem;
  color: #718096;
  font-style: italic;
  margin-top: 0.25rem;
}

.save-btn {
  position: relative;
  overflow: hidden;
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  background-color: transparent;
  transition: all 0.3s ease;
  z-index: 1;
}

.btn-content {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: transform 0.3s ease;
}

.btn-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #4CAF50, #2E7D32);
  z-index: 0;
  border-radius: 8px;
  transition: transform 0.3s ease, opacity 0.3s;
}

.save-btn:hover .btn-bg {
  transform: scale(1.05);
}

.save-btn:hover .btn-content {
  transform: scale(1.05);
}

.save-btn:active .btn-bg {
  transform: scale(0.98);
}

.save-btn:active .btn-content {
  transform: scale(0.98);
}

.save-btn:disabled,
.save-btn.disabled {
  cursor: not-allowed;
}

.save-btn.disabled .btn-bg {
  background: linear-gradient(135deg, #a5d6a7, #81c784);
  opacity: 0.7;
}

.btn-text {
  font-weight: 600;
}

/* Toast Notification */
.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  animation: slideIn 0.3s ease-out;
}

.toast.success {
  background-color: #d4edda;
  color: #155724;
}

.toast.error {
  background-color: #f8d7da;
  color: #721c24;
}

.toast.warning {
  background-color: #fff3cd;
  color: #856404;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .levels-grid {
    grid-template-columns: 1fr;
  }
  
  .save-btn {
    padding: 0.5rem 1rem;
  }
}
</style> 