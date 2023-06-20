<div align='center'>
  <img width="180px" alt="mysql logo" src="https://cdn-icons-png.flaticon.com/512/1087/1087097.png" />
  <h1>Invoices API</h1>
  <p>
    Uma API RESTful para gerenciamento de Invoices. API completa utilizando dos últimos recursos do Laravel, além de contar com testes.
  </p>
</div>

<br /><hr /><br />

## 🖥️ Tecnologias
Este projeto foi desenvolvido usando as seguintes tecnologias:

-  **PHP** como linguagem;
-  **SQLite e EloquentORM** para criação e gerenciamento do banco de dados;
-  **Laravel Sanctum** para validação e segurança de rotas;
-  **PHPUnit** para testes automatizados;
-  **Git** para versionamento de código;
-  **Visual Studio Code** para edição de código.

## ℹ️ Como usar
Para testar este projeto, precisará das seguintes ferramentas instaladas:

- PHP
- Git

<br/>

```bash
# Clone o repositório
git clone https://github.com/LucasCavalheri/api-completa-laravel-10.git
# Instale as dependências
composer install
# Crie o arquivo .env
cp .env.example .env
# Gere a chave da aplicação
php artisan key:generate
# Crie o banco de dados
touch database/database.sqlite
# Execute as migrations
php artisan migrate
# Execute os testes
php artisan test
# Inicie o servidor
php artisan serve
```

<br /><hr /><br />

<p align="center">
  Criado e desenvolvido por <b>Lucas Cavalheri</b>
  <br/><br/>
  
  <a href="https://www.linkedin.com/in/lucas-cavalheri/">
    <img alt="linkedIn" height="30px" src="https://i.imgur.com/TQRXxhT.png" />
  </a>
  &nbsp;&nbsp;
</p>
