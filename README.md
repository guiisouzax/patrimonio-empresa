# Inventário de T.I

Sistema web interno para controle patrimonial de equipamentos e hardware do departamento de T.I da Raízes Tecnologia. Substitui a planilha de Excel usada anteriormente, centralizando os dados em um banco MySQL com acesso restrito por login.

## Funcionalidades

- **Login com sessão**: não há cadastro público. Os usuários são criados manualmente pela T.I direto no banco, com senha em hash.
- **CRUD de equipamentos**: cadastrar, listar, editar e excluir, tudo por modais, sem sair da tela.
- **Mensagens de confirmação** dentro do próprio modal (verde para sucesso, vermelha para exclusão ou erro).
- **Busca em tempo real** (AJAX) por patrimônio, descrição, responsável e setor.
- **Filtros** por setor, tipo de equipamento e status.
- **Ordenação por coluna** na tabela (patrimônio, equipamento, setor, responsável, status e data).
- **Cards de métricas**: total de itens, em uso, disponíveis e em manutenção.
- **Badges coloridos** por setor.
- **Modo escuro**, com a preferência salva no navegador.

## Tecnologias

- PHP 8 (puro, sem framework)
- MySQL com PDO (prepared statements)
- Tailwind CSS (via CDN) e Font Awesome
- JavaScript puro (`fetch` para busca, cadastro, edição e exclusão)
- Hospedagem: Clever Cloud (aplicação PHP + add-on MySQL)

## Estrutura do projeto

```
INVENTARIO.RAIZES/
├── index.php                  # Tela de login
├── autenticar.php             # Valida e-mail/senha e cria a sessão
├── logout.php                 # Encerra a sessão
├── inventario.php             # Tela principal (protegida por sessão)
├── buscar_equipamentos.php    # Retorna as linhas da tabela para a busca em tempo real
├── cadastrar_equipamento.php  # Insere um equipamento (responde JSON)
├── editar_equipamento.php     # Atualiza um equipamento (responde JSON)
├── excluir_equipamento.php    # Exclui um equipamento (responde JSON)
├── Database.php               # Conexão PDO
└── assets/                    # CSS e imagens da tela de login
```

## Rodando localmente (XAMPP)

1. Instale o [XAMPP](https://www.apachefriends.org/) e inicie o **Apache** e o **MySQL**.
2. Copie a pasta do projeto para `C:\xampp\htdocs\`.
3. Acesse `http://localhost/phpmyadmin` e crie um banco de dados (ex.: `invetario`).
4. Execute o SQL da seção abaixo para criar as tabelas.
5. Confira em `Database.php` se o nome do banco usado no valor padrão é o mesmo que você criou.
6. Crie o primeiro usuário (veja "Criando usuários").
7. Acesse `http://localhost/INVENTARIO.RAIZES/`.

## Banco de dados

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'consulta') DEFAULT 'consulta',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE equipamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patrimonio VARCHAR(50) NOT NULL UNIQUE,
    tipo ENUM('notebook','desktop','monitor','impressora','periferico','coletor') NOT NULL,
    descricao TEXT NULL,
    setor ENUM('financeiro','operacoes','expedicao','comercial','ti','rh','servidor') NOT NULL,
    responsavel VARCHAR(100),
    status ENUM('em_uso','disponivel','manutencao') DEFAULT 'disponivel',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Criando usuários

Não existe tela de cadastro de contas. Para liberar acesso a alguém:

1. Gere o hash da senha em um arquivo PHP temporário (não deixe esse arquivo no projeto nem no servidor):

   ```php
   <?php
   echo password_hash("senha_do_usuario", PASSWORD_DEFAULT);
   ```

2. No phpMyAdmin, insira uma linha na tabela `usuarios` com `nome`, `email`, o hash na coluna `senha` e o `tipo` (`admin` ou `consulta`).
3. Apague o arquivo temporário.

## Configuração do banco (variáveis de ambiente)

O `Database.php` lê as credenciais das variáveis de ambiente que o add-on MySQL da Clever Cloud injeta. Se elas não existirem, usa os valores padrão do XAMPP local:

| Variável | Valor padrão (local) |
|---|---|
| `MYSQL_ADDON_HOST` | `localhost` |
| `MYSQL_ADDON_DB` | nome do banco local |
| `MYSQL_ADDON_USER` | `root` |
| `MYSQL_ADDON_PASSWORD` | *(vazio)* |

Nenhuma senha real deve ser escrita no código nem enviada ao repositório.

## Deploy na Clever Cloud

1. Crie o add-on **MySQL** e importe as tabelas pelo phpMyAdmin (entre no banco antes de importar e remova do `.sql` as linhas `CREATE DATABASE` e `USE`).
2. Crie uma aplicação **PHP** e vincule o add-on em *Service dependencies*.
3. Se o `index.php` estiver dentro de uma subpasta do repositório, defina a variável de ambiente `CC_WEBROOT` com o caminho dela (ex.: `/INVENTARIO.RAIZES`).
4. Envie o código:

   ```bash
   git add .
   git commit -m "descreva a alteração"
   git push origin main
   git push clever main:master
   ```

5. Acompanhe a aba **Logs** até aparecer "Successfully deployed".

### Atenção: maiúsculas e minúsculas

O servidor roda em Linux, que diferencia maiúsculas de minúsculas. O arquivo precisa se chamar exatamente `Database.php`, igual ao usado nos `require_once`. No Windows o Git pode não detectar a mudança de caixa; nesse caso, rode `git config core.ignorecase false` antes de renomear.

## Segurança

- Senhas armazenadas com `password_hash()` e conferidas com `password_verify()`.
- Todas as consultas usam prepared statements (PDO).
- Dados exibidos são escapados com `htmlspecialchars()`.
- Todas as páginas e endpoints verificam a sessão antes de executar qualquer ação.

## Próximos passos

- Permissões reais por tipo de usuário (`admin` x `consulta`)
- Histórico de alterações (auditoria)
- Exportação em CSV/Excel
- Revisão da responsividade em celular
- Alerta para equipamentos parados em manutenção
