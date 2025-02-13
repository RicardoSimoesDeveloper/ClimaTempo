# 🌤️ ClimaTempo

Este é um sistema de previsão do tempo que utiliza a API do OpenWeatherMap para obter dados climáticos e o Google Maps para exibição de mapas. Desenvolvido com **PHP (Laravel)** no backend e **Blade** no frontend, ele permite a busca por cidade e exibe informações como temperatura, umidade e previsão do tempo em tempo real.

## 🚀 Tecnologias Utilizadas

- **PHP (Laravel)** – Framework para desenvolvimento backend
- **Blade** – Motor de templates para o frontend
- **OpenWeatherMap API** – Obtenção de dados climáticos
- **Google Maps API** – Exibição de mapas e localização
- **JavaScript** – Requisições assíncronas e interatividade
- **PostgreSQL** – Banco de dados para armazenar consultas

## 📌 Funcionalidades

✅ Consulta do clima por cidade
✅ Exibição de temperatura, umidade e condições do tempo em tempo real
✅ Integração com mapas para visualização geográfica
✅ Interface dinâmica e responsiva

## ⚙️ Como Rodar o Projeto

### 1️⃣ Pré-requisitos
Antes de começar, você precisa ter instalado:
- PHP 8+
- Composer
- PostgreSQL (ou outro banco compatível)
- Laravel

### 2️⃣ Clone o repositório
```bash
git clone https://github.com/RicardoSimoesDeveloper/ClimaTempo.git
cd ClimaTempo
```

### 3️⃣ Instale as dependências
```bash
composer install
```

### 4️⃣ Configure o ambiente
Crie um arquivo `.env` com as configurações do banco de dados e das APIs:
```bash
cp .env.example .env
```
Edite o `.env` e adicione suas chaves de API:
```
OPENWEATHER_API_KEY=your_openweathermap_key
GOOGLE_MAPS_API_KEY=your_google_maps_key
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=climatempo
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5️⃣ Gere a chave da aplicação
```bash
php artisan key:generate
```

### 6️⃣ Execute as migrações
```bash
php artisan migrate
```

### 7️⃣ Inicie o servidor
```bash
php artisan serve
```
O projeto estará disponível em `http://127.0.0.1:8000`

## 🖥️ Estrutura do Código
```
ClimaTempo/
│── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ClimaController.php
│   ├── Models/
│   │   ├── Clima.php
│── resources/
│   ├── views/
│   │   ├── clima.blade.php
│── routes/
│   ├── web.php
│── database/
│── public/
│── .env.example
│── composer.json
│── README.md
```

## 💡 Exemplo de Uso
Para buscar o clima de uma cidade, acesse:
```
http://127.0.0.1:8000/clima?cidade=São Paulo
```

