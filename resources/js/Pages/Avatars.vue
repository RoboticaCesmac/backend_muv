<template>
  <DashboardLayout>
    <div class="avatars-container">
      <div class="avatars-header">
        <h1>Gerenciamento de Avatares</h1>
        <button @click="openUploadModal" class="add-avatar-btn">
          <span class="btn-content">
            <i class="fas fa-plus"></i>
            <span class="btn-text">Adicionar Avatar</span>
          </span>
          <span class="btn-bg"></span>
        </button>
      </div>

      <div class="card">
        <div class="card-content">
          <div class="avatars-grid">
            <div v-for="avatar in avatars.data || avatars" :key="avatar.id" class="avatar-card">
              <div class="avatar-image">
                <img :src="getImagePath(avatar.avatar_path)" :alt="avatar.name">
              </div>
              <div class="avatar-info">
                <h4>{{ avatar.name }}</h4>
                <div class="status-badge" :class="{ 'default': avatar.is_default }">
                  {{ avatar.is_default ? 'Padrão' : 'Personalizado' }}
                </div>
                <button 
                  v-if="!avatar.is_default" 
                  @click="confirmDelete(avatar)" 
                  class="delete-btn"
                >
                  <span class="btn-content">
                    <i class="fas fa-trash"></i>
                    <span class="btn-text">Excluir</span>
                  </span>
                  <span class="btn-bg"></span>
                </button>
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

    <!-- Modal de Upload -->
    <div v-if="showUploadModal" class="modal-overlay" @click.self="showUploadModal = false">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Adicionar Novo Avatar</h2>
          <button @click="showUploadModal = false" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="upload-steps">
            <!-- Step 1: Nome e seleção de arquivo -->
            <div v-if="uploadStep === 1">
              <div class="form-group">
                <label for="name">Nome do Avatar</label>
                <input 
                  type="text" 
                  id="name" 
                  v-model="uploadForm.name" 
                  required
                  placeholder="Digite um nome para o avatar"
                >
              </div>
              
              <div class="form-group">
                <label for="avatar">Imagem do Avatar</label>
                <div class="file-input-wrapper">
                  <input 
                    type="file" 
                    id="avatar" 
                    @change="handleFileChange" 
                    accept="image/*"
                    required
                  >
                  <div v-if="previewImage" class="image-preview">
                    <img :src="previewImage" alt="Preview">
                  </div>
                </div>
              </div>
              
              <div class="form-actions">
                <button type="button" @click="showUploadModal = false" class="cancel-btn">Cancelar</button>
                <button 
                  type="button" 
                  @click="goToStep2" 
                  class="submit-btn" 
                  :disabled="!canProceedToStep2"
                >
                  Próximo - Ajustar Imagem
                </button>
              </div>
            </div>

            <!-- Step 2: Ajustar imagem com cropper -->
            <div v-if="uploadStep === 2" class="cropper-container">
              <p class="crop-instructions">Ajuste o zoom da imagem para o formato do avatar</p>
              
              <div class="image-adjust-container">
                <div class="zoom-control">
                  <span class="zoom-label"><i class="fas fa-search-minus"></i></span>
                  <input 
                    type="range" 
                    min="0.2" 
                    max="3" 
                    step="0.1" 
                    v-model="zoomRatio" 
                  />
                  <span class="zoom-label"><i class="fas fa-search-plus"></i></span>
                </div>
                
                <div class="preview-image-wrapper">
                  <div class="preview-container-inner">
                    <div class="preview-image" :style="{ 
                      backgroundImage: `url(${previewImage})`,
                      transform: `scale(${zoomRatio})`,
                      transformOrigin: 'center center'
                    }"></div>
                  </div>
                </div>
              </div>
              
              <div class="form-actions">
                <button type="button" @click="uploadStep = 1" class="cancel-btn">Voltar</button>
                <button type="button" @click="uploadAvatar" class="submit-btn" :disabled="isUploading">
                  <span v-if="isUploading">Enviando...</span>
                  <span v-else>Salvar Avatar</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmação -->
    <div v-if="showConfirmModal" class="modal-overlay" @click.self="showConfirmModal = false">
      <div class="modal-content confirm-modal">
        <div class="modal-header">
          <h2>Confirmar Exclusão</h2>
          <button @click="showConfirmModal = false" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Tem certeza que deseja excluir o avatar <strong>{{ avatarToDelete?.name }}</strong>?</p>
          <p class="warning">Esta ação não pode ser desfeita.</p>
          
          <div class="form-actions">
            <button type="button" @click="showConfirmModal = false" class="cancel-btn">Cancelar</button>
            <button type="button" @click="deleteAvatar" class="delete-confirm-btn">
              <span v-if="isDeleting">Excluindo...</span>
              <span v-else>Sim, Excluir</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
  avatars: {
    type: Object,
    required: true
  }
});

const previewEl = ref(null);
const uploadStep = ref(1);
const zoomRatio = ref(1);

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

// Upload modal and form
const showUploadModal = ref(false);
const uploadForm = ref({
  name: '',
  avatar: null
});
const previewImage = ref(null);
const isUploading = ref(false);

const canProceedToStep2 = computed(() => {
  return uploadForm.value.name && previewImage.value;
});

const openUploadModal = () => {
  uploadForm.value = {
    name: '',
    avatar: null
  };
  previewImage.value = null;
  uploadStep.value = 1;
  showUploadModal.value = true;
};

const handleFileChange = (e) => {
  const file = e.target.files[0];
  uploadForm.value.avatar = file;
  
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  } else {
    previewImage.value = null;
  }
};

const goToStep2 = () => {
  if (canProceedToStep2.value) {
    uploadStep.value = 2;
  } else {
    showToast('Por favor, preencha todos os campos', 'error');
  }
};

const uploadAvatar = async () => {
  if (!uploadForm.value.name) {
    showToast('Por favor, preencha o nome do avatar', 'error');
    return;
  }
  
  isUploading.value = true;
  
  try {
    // Create canvas to generate zoomed image
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    // Set up image load handler
    await new Promise((resolve, reject) => {
      img.onload = resolve;
      img.onerror = reject;
      img.src = previewImage.value;
    });
    
    // Define o tamanho final fixo para o avatar (250x250)
    const finalSize = 250;
    canvas.width = finalSize;
    canvas.height = finalSize;
    
    // Calculate scaled dimensions and position
    const scale = zoomRatio.value;
    
    // Determine which dimension (width or height) should be used as base for scaling
    // to ensure image fits within the circle while maintaining aspect ratio
    let scaledWidth, scaledHeight;
    const aspectRatio = img.width / img.height;
    
    if (aspectRatio > 1) {
      // Landscape image: scale based on height
      scaledHeight = finalSize;
      scaledWidth = scaledHeight * aspectRatio;
    } else {
      // Portrait image: scale based on width
      scaledWidth = finalSize;
      scaledHeight = scaledWidth / aspectRatio;
    }
    
    // Apply zoom
    scaledWidth *= scale;
    scaledHeight *= scale;
    
    // Center the image
    const x = (finalSize - scaledWidth) / 2;
    const y = (finalSize - scaledHeight) / 2;
    
    // Fill with transparent background
    ctx.clearRect(0, 0, finalSize, finalSize);
    
    // Draw the image scaled and centered
    ctx.drawImage(img, x, y, scaledWidth, scaledHeight);
    
    // Create circular clip
    ctx.globalCompositeOperation = 'destination-in';
    ctx.beginPath();
    ctx.arc(finalSize/2, finalSize/2, finalSize/2, 0, Math.PI * 2);
    ctx.closePath();
    ctx.fill();
    
    // Get data URL and convert to blob
    const dataUrl = canvas.toDataURL('image/png');
    const response = await fetch(dataUrl);
    const blob = await response.blob();
    
    const formData = new FormData();
    formData.append('name', uploadForm.value.name);
    formData.append('avatar', blob, 'cropped-avatar.png');
    
    const apiResponse = await axios.post('/avatars', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    // Add the new avatar to the list
    const newAvatar = apiResponse.data.avatar;
    if (Array.isArray(props.avatars)) {
      props.avatars.push(newAvatar);
    } else if (props.avatars.data) {
      props.avatars.data.push(newAvatar);
    }
    
    showUploadModal.value = false;
    showToast('Avatar adicionado com sucesso', 'success');
  } catch (error) {
    console.error('Erro ao adicionar avatar:', error);
    showToast(error.response?.data?.message || 'Erro ao adicionar avatar', 'error');
  } finally {
    isUploading.value = false;
  }
};

// Delete avatar
const showConfirmModal = ref(false);
const avatarToDelete = ref(null);
const isDeleting = ref(false);

const confirmDelete = (avatar) => {
  avatarToDelete.value = avatar;
  showConfirmModal.value = true;
};

const deleteAvatar = async () => {
  if (!avatarToDelete.value) return;
  
  isDeleting.value = true;
  
  try {
    await axios.delete(`/avatars/${avatarToDelete.value.id}`);
    
    // Remove the avatar from the list
    if (Array.isArray(props.avatars)) {
      const index = props.avatars.findIndex(a => a.id === avatarToDelete.value.id);
      if (index !== -1) {
        props.avatars.splice(index, 1);
      }
    } else if (props.avatars.data) {
      const index = props.avatars.data.findIndex(a => a.id === avatarToDelete.value.id);
      if (index !== -1) {
        props.avatars.data.splice(index, 1);
      }
    }
    
    showConfirmModal.value = false;
    showToast('Avatar excluído com sucesso', 'success');
  } catch (error) {
    console.error('Erro ao excluir avatar:', error);
    showToast(error.response?.data?.message || 'Erro ao excluir avatar', 'error');
  } finally {
    isDeleting.value = false;
  }
};
</script>

<style scoped>
.avatars-container {
  padding: 1rem;
}

.avatars-header {
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.avatars-header h1 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin: 0;
}

.add-avatar-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  overflow: hidden;
}

.btn-content {
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  z-index: 1;
}

.btn-bg {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: #429944;
  transform: scaleX(0);
  transform-origin: right;
  transition: transform 0.3s ease;
}

.add-avatar-btn:hover .btn-bg {
  transform: scaleX(1);
  transform-origin: left;
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

.avatars-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.5rem;
}

.avatar-card {
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

.avatar-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.avatar-image {
  width: 100px;
  height: 100px;
  margin-bottom: 1rem;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  overflow: hidden;
}

.avatar-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-info {
  text-align: center;
  width: 100%;
}

.avatar-info h4 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
  margin-bottom: 1rem;
  background-color: #e2e8f0;
  color: #4a5568;
}

.status-badge.default {
  background-color: #3490dc;
  color: white;
}

.delete-btn {
  background-color: #e53e3e;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  position: relative;
  overflow: hidden;
}

.delete-btn .btn-bg {
  background-color: #c53030;
}

.toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  padding: 12px 20px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  font-weight: 500;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  animation: slide-in 0.3s ease;
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.toast.success {
  background-color: #48bb78;
}

.toast.error {
  background-color: #f56565;
}

.toast.warning {
  background-color: #ed8936;
}

.toast.info {
  background-color: #4299e1;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 900;
}

.modal-content {
  background-color: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #2d3748;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #718096;
  cursor: pointer;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #4a5568;
}

.form-group input[type="text"] {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  color: #2d3748;
  background-color: #ffffff;
}

.form-group input[type="text"]::placeholder {
  color: #a0aec0;
}

.file-input-wrapper {
  border: 2px dashed #e2e8f0;
  padding: 1.5rem;
  border-radius: 8px;
  text-align: center;
  transition: border-color 0.3s;
  color: #4a5568;
}

.file-input-wrapper:hover {
  border-color: #cbd5e0;
}

.image-preview {
  margin-top: 1rem;
  max-width: 100%;
  max-height: 200px;
}

.image-preview img {
  max-width: 100%;
  max-height: 200px;
  object-fit: contain;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.cancel-btn {
  padding: 0.75rem 1.25rem;
  background-color: #e2e8f0;
  color: #4a5568;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background-color: #cbd5e0;
}

.submit-btn {
  padding: 0.75rem 1.25rem;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.submit-btn:not(:disabled):hover {
  background-color: #429944;
}

.delete-confirm-btn {
  padding: 0.75rem 1.25rem;
  background-color: #e53e3e;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.delete-confirm-btn:hover {
  background-color: #c53030;
}

.confirm-modal .warning {
  color: #e53e3e;
  margin-top: 0.5rem;
}

/* Cropper styles */
.cropper-container {
  display: flex;
  flex-direction: column;
}

.crop-instructions {
  text-align: center;
  color: #4a5568;
  margin-bottom: 1rem;
  font-weight: 500;
}

.image-adjust-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.preview-image-wrapper {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid #e2e8f0;
  background-color: #f7fafc;
  position: relative;
}

.preview-container-inner {
  width: 100%;
  height: 100%;
  overflow: hidden;
  position: relative;
}

.preview-image {
  width: 100%;
  height: 100%;
  background-size: contain;
  background-position: center;
  background-repeat: no-repeat;
  transition: transform 0.2s ease;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.zoom-control {
  display: flex;
  align-items: center;
  width: 100%;
  max-width: 300px;
  gap: 10px;
}

.zoom-control input {
  flex: 1;
  height: 6px;
  appearance: none;
  background: #e2e8f0;
  border-radius: 3px;
  outline: none;
}

.zoom-control input::-webkit-slider-thumb {
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #3490dc;
  cursor: pointer;
}

.zoom-control input::-moz-range-thumb {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #3490dc;
  cursor: pointer;
  border: none;
}

.zoom-label {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #718096;
  font-size: 1rem;
  width: 20px;
}
</style> 