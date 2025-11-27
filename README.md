# 📚 Library Tracking System

Welcome to the **Library Tracking System**! This project is a sample application built with **PHP**, **Laravel**, **MySQL**, **Vue** and **Vuetify**. It manages authors, books, users and loans within a library context. The application is fully containerized using **Docker**, allowing for easy setup and deployment.

---

## 📌 **Project Overview**

### **Tech Stack**
- **PHP 8.2** – Backend development
- **Laravel 12** – Web framework
- **Vue 3** - Frontend development
- **Vuetify 3.10** - UI framework
- **Redis 7** – Queue backend
- **MySQL 8.0.39** – Database
- **Docker & Docker Compose** – Containerized setup

---

## 🛠 **Setup Instructions**

### 1️⃣ **Clone the Repository**
**Note:** For this assessment, forking is **not allowed**.
```sh
git clone https://gitlab.com/search-atlas-interviews/laravel-vue.git
cd laravel-vue
```

### 2️⃣ **Configure Your Git Remote**
To work with your own repository, you need to replace the default remote with one you control. We recommend using **GitHub** for this, it's free.

#### 🏗 **Create an Empty Public Repository on GitHub**
1. Go to [GitHub](https://github.com/) and sign in.
2. Click on the **+** in the top-right corner and select **New repository**.
3. Enter a repository name.
4. Choose **Public**.
5. **Do not** initialize with a README, `.gitignore`, or license.
6. Click **Create repository**.
7. Copy the repository URL (it should look like `https://github.com/your-username/your-repo.git`).

#### 🔧 **Replace the Default Git Remote**
Run the following commands to rename the existing remote and add your newly created repository:

```sh
git remote rename origin upstream
git remote add origin [YOUR_GITHUB_REPOSITORY_URL]
git push -u origin main
```

### 3️⃣ **Create a `.env` File**
Create a `.env` file by copying over the example file:
```sh
cp .env.example .env
```

### 4️⃣ **Build and Run Docker Containers**
```sh
docker-compose build
docker-compose up
```

### 5️⃣ **Initialize and Run the Laravel Project**
**Important:** Open up a shell to the `library-app` Docker container, navigate to the project code directory and execute the following:
```sh
composer install
./artisan migrate
./artisan db:seed
npm install

# Run the hot module reloader for the frontend
npm run dev

# Done, you can now access the application in a browser
```

---

## 📌 **Accessing the Application**

### 🔑 **Web Interface**
- **UI:** [http://localhost:8080/](http://localhost:8080/)
- **API:** [http://localhost:8080/api/v1/](http://localhost:8080/api/v1/)

---

## 🎯 **License**
This project is licensed under the **MIT License**.

---

🚀 **Happy coding!** 🎉

docker compose down

docker compose up --build -d

docker exec -it library-app bash

cd /code/library-tracker

composer install
cp .env.example .env
php artisan key:generate 
php artisan migrate

php artisan db:seed
npm install
npm run dev
