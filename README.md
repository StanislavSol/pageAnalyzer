# 🌐 Page Analyzer

[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Slim](https://img.shields.io/badge/Slim-4.x-194A7C?style=flat-square&logo=slim&logoColor=white)](https://www.slimframework.com/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-336791?style=flat-square&logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Render](https://img.shields.io/badge/Deployed_on-Render-46E3B7?style=flat-square&logo=render&logoColor=white)](https://render.com)

**Page Analyzer** — веб-приложение для SEO-анализа страниц (H1, Title, Description, HTTP-статус). Построено с помощью фреймворка **Slim**.

🔗 **Демо:** [https://pageanalyzer-2qu3.onrender.com/](https://pageanalyzer-2qu3.onrender.com/)

---

## 📋 Требования

| Компонент | Версия |
|-----------|--------|
| PHP | 8.1+ (рекомендуется 8.3) |
| PostgreSQL | 13+ |
| Composer | 2.0+ |
| Git | 2.0+ |
| Make | 3.81+ |
| ОС | Linux/macOS/WSL2 |

---

## 🚀 Установка и запуск

```bash
# 1. Клонирование
git clone git@github.com:StanislavSol/pageAnalyzer.git
cd pageAnalyzer

# 2. Установка зависимостей
make install

# 3. Настройка .env
cp .env.example .env
# Добавьте DATABASE_URL=postgresql://user:pass@localhost:5432/db

# 4. Инициализация БД
psql -a -d $DATABASE_URL -f database.sql

# 5. Запуск
make start
