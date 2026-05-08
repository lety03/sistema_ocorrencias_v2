#!/bin/bash
# Script de configuração do PostgreSQL para o projeto
# Execute com: bash setup_pgsql.sh

set -e

echo "=== 1. Instalando PostgreSQL e extensão PHP ==="
sudo apt-get update
sudo apt-get install -y postgresql postgresql-client php8.2-pgsql

echo ""
echo "=== 2. Iniciando o PostgreSQL ==="
sudo systemctl start postgresql
sudo systemctl enable postgresql

echo ""
echo "=== 3. Criando banco de dados e usuário ==="
sudo -u postgres psql -c "CREATE USER laravel WITH PASSWORD 'laravel123';" 2>/dev/null || echo "Usuário 'laravel' já existe."
sudo -u postgres psql -c "CREATE DATABASE sistema_ocorrencias OWNER laravel;" 2>/dev/null || echo "Banco 'sistema_ocorrencias' já existe."
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE sistema_ocorrencias TO laravel;"

echo ""
echo "=== 4. Limpando cache do Laravel ==="
php artisan config:clear
php artisan cache:clear

echo ""
echo "=== 5. Rodando migrations ==="
php artisan migrate --force

echo ""
echo "=== 6. Iniciando o servidor ==="
echo "Tudo pronto! Execute: composer dev"
echo "Ou apenas: php artisan serve"
