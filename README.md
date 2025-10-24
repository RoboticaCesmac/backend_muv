# Laravel TCC - Sistema de Gerenciamento de Rotas

Sistema de gerenciamento de rotas e trajetos desenvolvido com Laravel 12, Vue 3 e Inertia.js.

## 📋 Requisitos

- **PHP:** >= 8.2 (extensões: pdo, pdo_pgsql, mbstring, xml, bcmath, curl, zip)
- **Composer:** >= 2.6
- **Node.js:** >= 18.x
- **PostgreSQL:** >= 14
- **Nginx/Apache:** qualquer versão recente
- **Docker & Docker Compose:** opcional (desenvolvimento)

## 🚀 Instalação

### 1. Clone o repositório

```bash
git clone <repository-url>
cd Laravel-TCC
```

### 2. Configure as dependências

```bash
composer install
npm install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### 4. Configure o banco de dados

**Usando Docker:**

```bash
docker-compose up -d
```

**Manualmente:**

Crie um banco PostgreSQL e configure as credenciais no `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=postgres-tcc
DB_USERNAME=admin
DB_PASSWORD=admin
```

### 5. Execute as migrations e seeders

```bash
php artisan migrate --seed
```

### 6. Inicie o servidor

**Desenvolvimento:**

```bash
composer run dev
```

Ou individualmente:

```bash
php artisan serve
npm run dev
```

## 🔧 Configuração de Produção

### Variáveis de Ambiente Essenciais

**Nota sobre Email SMTP:**
- Para Gmail: use senha de aplicativo (não a senha da conta). Gere em: https://myaccount.google.com/apppasswords
- Para Outlook/Hotmail: use `smtp.office365.com` na porta `587`
- Para outros provedores: consulte a documentação do provedor

```env
# Aplicação
APP_NAME="Laravel TCC"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Banco de Dados
DB_CONNECTION=pgsql
DB_HOST=seu-host-postgres
DB_PORT=5432
DB_DATABASE=postgres-tcc
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha-segura

# JWT
JWT_SECRET=<gerado-por-php-artisan-jwt:secret>
JWT_TTL=60
JWT_REFRESH_TTL=20160
JWT_ALGO=HS256
JWT_BLACKLIST_ENABLED=true

# Email (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-de-aplicativo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Deploy em Produção

#### 1. No servidor, clone e configure:

```bash
git clone <repository-url>
cd Laravel-TCC
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### 2. Configure permissões:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 3. Configure o .env para produção e execute:

```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

## 🐳 Docker (Desenvolvimento)

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f postgres
```

## 🔨 Comandos Úteis

```bash
# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar caches (produção)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrations
php artisan migrate
php artisan migrate:fresh --seed
php artisan migrate:rollback