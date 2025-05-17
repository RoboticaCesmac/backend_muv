<template>
  <DashboardLayout>
    <div class="users-container">
      <div class="users-header">
        <h1>Gerenciamento de Usuários</h1>
        <div class="search-create">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              placeholder="Buscar por email..." 
              v-model="searchTerm"
              @input="searchUsers"
            />
          </div>
          <div class="excel-export">
            <div class="date-filters">
              <div class="date-input">
                <label>Data Inicial:</label>
                <input 
                  type="month" 
                  v-model="startDate"
                  :max="endDate || undefined"
                />
              </div>
              <div class="date-input">
                <label>Data Final:</label>
                <input 
                  type="month" 
                  v-model="endDate"
                  :min="startDate || undefined"
                />
              </div>
            </div>
            <button @click="generateExcel" class="excel-btn" :disabled="!startDate || !endDate">
              <i class="fas fa-file-excel"></i>
              Gerar Excel
            </button>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-content">
          <div v-if="loading" class="loading-indicator">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Carregando usuários...</span>
          </div>
          <div v-else class="table-responsive">
            <table class="users-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Data de Cadastro</th>
                  <th>Primeiro Login</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="userData.length === 0">
                  <td colspan="6" class="no-data">Nenhum usuário encontrado</td>
                </tr>
                <tr v-for="user in userData" :key="user.id">
                  <td>#{{ user.id }}</td>
                  <td>
                    <div class="user-info">
                      <!-- <div class="avatar">{{ getInitials(user.user_name) }}</div> -->
                      <span>{{ user.user_name }}</span>
                    </div>
                  </td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span class="status" :class="!user.deleted_at ? 'active' : 'inactive'">
                      {{ !user.deleted_at ? 'Ativo' : 'Inativo' }}
                    </span>
                  </td>
                  <td>{{ formatDate(user.created_at) }}</td>
                  <td>
                    <span class="status" :class="user.first_login ? 'active' : 'inactive'">
                      {{ user.first_login ? 'Sim' : 'Não' }}
                    </span>
                  </td>
                  <td>
                    <div class="actions">
                      <button @click="viewUser(user)" class="action-btn view-btn" title="Visualizar">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button @click="editUser(user)" class="action-btn edit-btn" title="Editar">
                        <i class="fas fa-pen"></i>
                      </button>
                      <button @click="confirmDelete(user)" class="action-btn delete-btn" title="Excluir">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginação -->
          <div class="pagination" v-if="!loading && userData.length > 0">
            <button 
              class="pagination-btn" 
              :disabled="currentPage === 1" 
              @click="changePage(currentPage - 1)"
            >
              <i class="fas fa-chevron-left"></i>
            </button>
            <span class="page-info">
              Página {{ currentPage }} de {{ totalPages }}
            </span>
            <button 
              class="pagination-btn" 
              :disabled="currentPage === totalPages" 
              @click="changePage(currentPage + 1)"
            >
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Visualização -->
    <div v-if="showViewModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Detalhes do Usuário</h2>
          <button @click="showViewModal = false" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="user-details">
            <div class="user-avatar big">{{ getInitials(selectedUser.user_name) }}</div>
            <h3>{{ selectedUser.user_name }}</h3>
            <p class="user-role">{{ getUserRole(selectedUser) }}</p>
            
            <div class="detail-section">
              <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ selectedUser.email }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span class="detail-value" :class="!selectedUser.deleted_at ? 'text-success' : 'text-danger'">
                  {{ !selectedUser.deleted_at ? 'Ativo' : 'Inativo' }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Data de Cadastro:</span>
                <span class="detail-value">{{ formatDate(selectedUser.created_at) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Primeiro Login:</span>
                <span class="detail-value" :class="selectedUser.first_login ? 'text-success' : 'text-danger'">
                  {{ selectedUser.first_login ? 'Sim' : 'Não' }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Última Atualização:</span>
                <span class="detail-value">{{ formatDate(selectedUser.updated_at) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showViewModal = false" class="btn-secondary">Fechar</button>
          <button @click="editUser(selectedUser)" class="btn-primary">Editar</button>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteModal" class="modal">
      <div class="modal-content delete-modal">
        <div class="modal-header">
          <h2>Confirmar Exclusão</h2>
          <button @click="showDeleteModal = false" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="delete-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <p>Você tem certeza que deseja excluir o usuário <strong>{{ selectedUser.user_name }}</strong>?</p>
          <p class="warning-text">Esta ação não pode ser desfeita.</p>
        </div>
        <div class="modal-footer">
          <button @click="showDeleteModal = false" class="btn-secondary">Cancelar</button>
          <button @click="deleteUser" class="btn-danger" :disabled="isDeleting">
            <i v-if="isDeleting" class="fas fa-spinner fa-spin"></i>
            <span v-else>Confirmar Exclusão</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Edição de Usuário -->
    <div v-if="showEditModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Editar Usuário</h2>
          <button @click="showEditModal = false" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="updateUser" class="edit-form">
            <div class="form-group">
              <label for="user_name">Nome de Usuário</label>
              <div class="input-with-label">
                <input 
                  type="text" 
                  id="user_name" 
                  v-model="editForm.user_name" 
                  class="form-input"
                  placeholder="Digite o nome de usuário (opcional)"
                >
                <div class="current-value" v-if="editForm.user_name">
                  <span>Valor atual: </span>
                  <strong>{{ editForm.original_user_name || 'Não definido' }}</strong>
                </div>
              </div>
              <span v-if="errors.user_name" class="error-message">{{ errors.user_name }}</span>
            </div>
            
            <div class="form-group">
              <label for="email">Email</label>
              <div class="input-with-label">
                <input 
                  type="email" 
                  id="email" 
                  v-model="editForm.email" 
                  required
                  class="form-input"
                  placeholder="Digite o email"
                >
                <div class="current-value" v-if="editForm.email">
                  <span>Valor atual: </span>
                  <strong>{{ editForm.original_email }}</strong>
                </div>
              </div>
              <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
            </div>
            
            <div class="form-group">
              <label for="password">
                Nova Senha <span class="optional">(opcional)</span>
              </label>
              <div class="password-input">
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  id="password" 
                  v-model="editForm.password"
                  class="form-input"
                  placeholder="Deixe em branco para manter a senha atual"
                >
                <button 
                  type="button" 
                  class="toggle-password" 
                  @click="showPassword = !showPassword"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button @click="showEditModal = false" class="btn-secondary">Cancelar</button>
          <button @click="updateUser" class="btn-primary" :disabled="isUpdating">
            <i v-if="isUpdating" class="fas fa-spinner fa-spin"></i>
            <span v-else>Salvar Alterações</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Toast para mensagens -->
    <div v-if="toast.show" :class="['toast', `toast-${toast.type}`]">
      <div class="toast-content">
        <i :class="getToastIcon()"></i>
        <span>{{ toast.message }}</span>
      </div>
      <button @click="toast.show = false" class="toast-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

// Props recebidas do controller - note que mudou de initialUsers para users
const props = defineProps({
  users: Object
});

const users = ref(props.users || { data: [] });
const searchTerm = ref('');
const perPage = ref(10);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const showCreateModal = ref(false);
const selectedUser = ref({});
const loading = ref(false);
const isDeleting = ref(false);
const startDate = ref('');
const endDate = ref('');

const userData = computed(() => {
  return users.value.data || [];
});

const currentPage = computed(() => {
  return users.value.current_page || 1;
});

const totalPages = computed(() => {
  return users.value.last_page || 1;
});

// Método para buscar usuários com paginação
const searchUsers = async () => {
  clearTimeout(window.searchTimeout);
  
  window.searchTimeout = setTimeout(async () => {
    if (searchTerm.value === '' && !loading.value) {
      await fetchUsers(1);
      return;
    }
    
    loading.value = true;
    await fetchUsers(1);
  }, 500);
};

const fetchUsers = async (page) => {
  loading.value = true;
  
  try {
    const response = await axios.get('/users/search', {
      params: {
        search: searchTerm.value,
        page: page,
        per_page: perPage.value
      }
    });
    users.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar usuários:', error);
  } finally {
    loading.value = false;
  }
};

// Método para mudar de página
const changePage = async (page) => {
  if (page < 1 || page > totalPages.value) return;
  
  await fetchUsers(page);
};

const getInitials = (name) => {
  if (!name) return '';
  return name
    .split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

const getUserRole = (user) => {
  // Implemente a lógica para determinar a função do usuário
  // Exemplo: verificar se o usuário é admin
  return user.is_admin ? 'Administrador' : 'Usuário';
};

const viewUser = (user) => {
  selectedUser.value = { ...user };
  showViewModal.value = true;
};

// Estado para o formulário de edição
const showEditModal = ref(false);
const showPassword = ref(false);
const isUpdating = ref(false);
const editForm = ref({
  id: null,
  user_name: '',
  email: '',
  password: '',
  original_user_name: '',
  original_email: ''
});
const errors = ref({});

const editUser = (user) => {
  // Preencher o formulário com os dados do usuário
  editForm.value = {
    id: user.id,
    user_name: user.user_name || '',
    email: user.email,
    password: '',
    original_user_name: user.user_name || 'Não definido',
    original_email: user.email
  };
  
  // Limpar erros anteriores
  errors.value = {};
  
  // Mostrar o modal de edição
  showEditModal.value = true;
};

// Estado para o toast
const toast = ref({
  show: false,
  message: '',
  type: 'success', // success, error, warning
  timeout: null
});

// Mostrar toast
const showToast = (message, type = 'success', duration = 3000) => {
  // Limpar timeout anterior se existir
  if (toast.value.timeout) {
    clearTimeout(toast.value.timeout);
  }
  
  // Configurar e mostrar o toast
  toast.value.show = true;
  toast.value.message = message;
  toast.value.type = type;
  
  // Fechar automaticamente após a duração especificada
  toast.value.timeout = setTimeout(() => {
    toast.value.show = false;
  }, duration);
};

// Obter o ícone apropriado para o tipo de toast
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

const updateUser = async () => {
  isUpdating.value = true;
  errors.value = {};
  
  try {
    // Criar objeto com os dados a enviar
    const data = {
      id: editForm.value.id,
      user_name: editForm.value.user_name,
      email: editForm.value.email,
      password: editForm.value.password
    };
    
    // Se a senha estiver vazia, não enviar
    if (!data.password) {
      delete data.password;
    }
    
    // Enviar requisição para atualizar o usuário
    const response = await axios.put(`/users/${editForm.value.id}`, data);
    
    // Atualizar a lista de usuários
    await fetchUsers(currentPage.value);
    
    // Fechar o modal
    showEditModal.value = false;
    
    // Mostrar toast de sucesso em vez de alert
    showToast('Usuário atualizado com sucesso!', 'success');
    
  } catch (error) {
    console.error('Erro ao atualizar usuário:', error);
    
    // Tratar erros de validação
    if (error.response && error.response.data && error.response.data.errors) {
      errors.value = error.response.data.errors;
    } else {
      showToast('Erro ao atualizar usuário. Por favor, tente novamente.', 'error');
    }
  } finally {
    isUpdating.value = false;
  }
};

const confirmDelete = (user) => {
  selectedUser.value = { ...user };
  showDeleteModal.value = true;
};

const deleteUser = async () => {
  isDeleting.value = true;
  
  try {
    await axios.delete(`/users/${selectedUser.value.id}`);
    
    // Após excluir, buscar os dados atualizados
    await fetchUsers(currentPage.value);
    
    // Fechar o modal
    showDeleteModal.value = false;
    
    // Mostrar toast de sucesso
    showToast('Usuário excluído com sucesso!', 'success');
  } catch (error) {
    console.error('Erro ao excluir usuário:', error);
    showToast('Erro ao excluir usuário. Por favor, tente novamente.', 'error');
  } finally {
    isDeleting.value = false;
  }
};

// Função para gerar arquivo Excel
const generateExcel = async () => {
  try {
    const response = await axios.get('/users/export-excel', {
      params: {
        start_date: startDate.value,
        end_date: endDate.value
      },
      responseType: 'blob'
    });
    
    // Format dates for filename
    const formatDateForFilename = (dateStr) => {
      const date = new Date(dateStr);
      return date.toISOString().split('T')[0]; // YYYY-MM-DD format
    };
    
    // Create filename with the specified format
    const filename = `${formatDateForFilename(startDate.value)}-${formatDateForFilename(endDate.value)}-usuarios.xlsx`;
    
    // Create a link for download
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
    
    showToast('Arquivo Excel gerado com sucesso!', 'success');
  } catch (error) {
    console.error('Erro ao gerar arquivo Excel:', error);
    
    // If it's an error response, try to read the message from the blob
    if (error.response?.data instanceof Blob) {
      const reader = new FileReader();
      reader.onload = () => {
        try {
          const errorData = JSON.parse(reader.result);
          showToast(errorData.message || 'Erro ao gerar arquivo Excel', 'warning');
        } catch (e) {
          showToast('Erro ao gerar arquivo Excel. Por favor, tente novamente.', 'error');
        }
      };
      reader.readAsText(error.response.data);
    } else {
      showToast('Erro ao gerar arquivo Excel. Por favor, tente novamente.', 'error');
    }
  }
};

onMounted(() => {
  // Se não recebemos dados iniciais, buscamos
  if (!props.users) {
    fetchUsers(1);
  }
});
</script>

<style scoped>
.loading-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  color: #6c757d;
  gap: 1rem;
}

.loading-indicator i {
  font-size: 2rem;
  color: #3490dc;
}

.users-container {
  width: 100%;
}

.users-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.users-header h1 {
  font-size: 1.75rem;
  color: #2c3e50;
  margin: 0;
}

.search-create {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.search-box {
  position: relative;
  width: 300px;
}

.search-box i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
}

.search-box input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border-radius: 8px;
  border: 1px solid #ced4da;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.search-box input:focus {
  outline: none;
  border-color: #3490dc;
  box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
}

.create-btn {
  background-color: #3490dc;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.25rem;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.create-btn:hover {
  background-color: #2779bd;
  transform: translateY(-1px);
}

.card {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.card-content {
  padding: 1.5rem;
}

.table-responsive {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}

.users-table th {
  text-align: left;
  padding: 1rem;
  background-color: #f8f9fa;
  color: #495057;
  font-weight: 600;
  border-bottom: 2px solid #e9ecef;
}

.users-table td {
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
  color: #495057;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: #3490dc;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
}

.status {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status.active {
  background-color: #d4edda;
  color: #155724;
}

.status.inactive {
  background-color: #f8d7da;
  color: #721c24;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: none;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.view-btn {
  color: #3490dc;
}

.view-btn:hover {
  background-color: #e3f2fd;
}

.edit-btn {
  color: #ffc107;
}

.edit-btn:hover {
  background-color: #fff8e1;
}

.delete-btn {
  color: #dc3545;
}

.delete-btn:hover {
  background-color: #f8d7da;
}

.pagination {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-top: 1.5rem;
  gap: 0.5rem;
}

.pagination-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: 1px solid #ced4da;
  background-color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-btn:hover:not(:disabled) {
  background-color: #e9ecef;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 0.9rem;
  color: #6c757d;
  margin: 0 0.5rem;
}

.no-data {
  text-align: center;
  color: #6c757d;
  padding: 2rem !important;
  font-style: italic;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 12px;
  width: 500px;
  max-width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.delete-modal {
  width: 400px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e9ecef;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #2c3e50;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  color: #6c757d;
  transition: color 0.2s ease;
}

.close-btn:hover {
  color: #343a40;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.25rem 1.5rem;
  border-top: 1px solid #e9ecef;
}

.btn-primary, .btn-secondary, .btn-danger {
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  border: none;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background-color: #3490dc;
  color: white;
}

.btn-primary:hover {
  background-color: #2779bd;
}

.btn-secondary {
  background-color: #f8f9fa;
  color: #6c757d;
  border: 1px solid #ced4da;
}

.btn-secondary:hover {
  background-color: #e9ecef;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-danger:hover {
  background-color: #c82333;
}

.btn-danger:disabled {
  background-color: #e9899a;
  cursor: not-allowed;
  transform: none;
}

.user-details {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem 0;
}

.user-avatar.big {
  width: 80px;
  height: 80px;
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.user-role {
  color: #6c757d;
  margin-top: 0;
}

.detail-section {
  width: 100%;
  margin-top: 1.5rem;
}

.detail-item {
  display: flex;
  padding: 0.75rem 0;
  border-bottom: 1px solid #e9ecef;
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: 600;
  color: #6c757d;
  width: 40%;
}

.detail-value {
  color: #2c3e50;
}

.text-success {
  color: #28a745;
}

.text-danger {
  color: #dc3545;
}

.delete-icon {
  font-size: 3rem;
  color: #dc3545;
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.warning-text {
  color: #dc3545;
  font-style: italic;
  margin-top: 1rem;
}

@media (max-width: 768px) {
  .users-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .search-create {
    width: 100%;
    flex-direction: column;
    align-items: stretch;
  }

  .search-box {
    width: 100%;
  }

  .create-btn {
    width: 100%;
    justify-content: center;
  }
}

/* Estilos para o formulário de edição */
.edit-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #495057;
  font-size: 0.9rem;
}

.input-with-label {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.current-value {
  font-size: 0.8rem;
  color: #6c757d;
  font-style: italic;
  padding: 0.25rem 0;
}

.current-value strong {
  color: #3490dc;
}

.form-input {
  padding: 0.75rem;
  border-radius: 8px;
  border: 1px solid #ced4da;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  color: #495057;
  background-color: #fff;
}

.form-input::placeholder {
  color: #adb5bd;
}

.form-input:focus {
  outline: none;
  border-color: #3490dc;
  box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
}

.password-input {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
}

.toggle-password:hover {
  color: #495057;
}

.error-message {
  color: #dc3545;
  font-size: 0.85rem;
}

.optional {
  font-size: 0.8rem;
  font-weight: normal;
  color: #6c757d;
  font-style: italic;
}

/* Estilos do Toast */
.toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  min-width: 300px;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 2000;
  animation: slideIn 0.3s ease-out;
  background-color: #fff;
}

.toast-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.toast-content i {
  font-size: 1.25rem;
}

.toast-success {
  background-color: #edfdf7;
  border-left: 4px solid #38c172;
}

.toast-success .toast-content i {
  color: #38c172;
}

.toast-error {
  background-color: #fdf2f2;
  border-left: 4px solid #dc3545;
}

.toast-error .toast-content i {
  color: #dc3545;
}

.toast-warning {
  background-color: #fff6ed;
  border-left: 4px solid #f39c12;
}

.toast-warning .toast-content i {
  color: #f39c12;
}

.toast-close {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
  color: #6c757d;
  padding: 0.25rem;
  transition: color 0.2s;
}

.toast-close:hover {
  color: #343a40;
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

.excel-btn {
  background-color: #217346;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.25rem;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.excel-btn:hover {
  background-color: #1e6b3d;
  transform: translateY(-1px);
}

.excel-btn i {
  font-size: 1.1rem;
}

.excel-export {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.date-filters {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.date-input {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.date-input label {
  font-size: 0.8rem;
  color: #6c757d;
}

.date-input input {
  padding: 0.5rem;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 0.9rem;
}

.date-input input:focus {
  outline: none;
  border-color: #3490dc;
  box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
}

.excel-btn:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
  transform: none;
}
</style> 