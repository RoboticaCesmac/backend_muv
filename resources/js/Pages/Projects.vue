<template>
  <DashboardLayout>
    <div class="projects-container">
      <div class="projects-header">
        <h3>Seus Projetos</h3>
        <button class="new-project-btn">
          <i class="fas fa-plus"></i>
          Novo Projeto
        </button>
      </div>

      <div class="projects-grid">
        <div class="project-card">
          <h4>Projeto 1</h4>
          <p>Descrição breve do projeto...</p>
          <div class="project-footer">
            <span class="project-status">Em andamento</span>
            <button class="view-project-btn">Ver mais</button>
          </div>
        </div>

        <div class="project-card">
          <h4>Projeto 2</h4>
          <p>Descrição breve do projeto...</p>
          <div class="project-footer">
            <span class="project-status completed">Concluído</span>
            <button class="view-project-btn">Ver mais</button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
</script>

<style scoped>
.projects-container {
  padding: 1rem;
}

.projects-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.projects-header h3 {
  font-size: 1.5rem;
  color: #2c3e50;
}

.new-project-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background-color: #3490dc;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.new-project-btn:hover {
  background-color: #2779bd;
}

.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.project-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}

.project-card h4 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.project-card p {
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.project-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.project-status {
  font-size: 0.875rem;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  background-color: #fbd38d;
  color: #975a16;
}

.project-status.completed {
  background-color: #9ae6b4;
  color: #276749;
}

.view-project-btn {
  background: none;
  border: none;
  color: #3490dc;
  cursor: pointer;
  font-size: 0.875rem;
}

.view-project-btn:hover {
  text-decoration: underline;
}
</style> 