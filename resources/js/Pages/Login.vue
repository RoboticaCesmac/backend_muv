<template>
  <div class="login-background">
    <div class="login-container">
      <div class="login-logo">
        <img src="/images/LogoMarcaCortada.png" alt="logo">
      </div>
      <h1 class="login-title">Acesse sua conta</h1>
      <form class="login-form" @submit.prevent="submit">
        <div class="form-group">
          <input
            type="text"
            v-model="form.email"
            placeholder="Email"
            required
            :disabled="form.processing"
          >
          <div v-if="form.errors.email" class="error-message">
            {{ form.errors.email }}
          </div>
        </div>
        <div class="form-group">
          <input
            type="password"
            v-model="form.password" 
            placeholder="Senha"
            required
            :disabled="form.processing"
          >
          <div v-if="form.errors.password" class="error-message">
            {{ form.errors.password }}
          </div>
        </div>
        <!-- Mensagem de erro geral -->
        <div v-if="form.errors.error" class="error-message">
          {{ form.errors.error }}
        </div>
        <button type="submit" class="login-button" :disabled="form.processing">
          {{ form.processing ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import '../../css/login.css'

const form = useForm({
  email: '',
  password: '',
})

const submit = () => {
  form.post('/login', {
    preserveScroll: true,
  })
}
</script>

<style scoped>
.error-message {
  margin-top: 10px;
  color: #ff6b6b;
  text-align: center;
  margin-bottom: 10px;
  font-size: 14px;
}

.login-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>