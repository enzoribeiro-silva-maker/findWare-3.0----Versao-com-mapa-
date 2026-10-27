# FindWare — Sistema Inteligente de Localização de Produtos

Sistema web para controle e localização de produtos em um Centro de
Distribuição (CD), reestruturado no padrão **MVC** (Model-View-Controller)
em PHP puro, sem frameworks — fácil de entender, rodar e apresentar.

## Estrutura do projeto (padrão MVC)

```
findware-mvc/
├── config.php                 # dados de acesso ao banco de dados
├── database/
│   └── schema.sql              # script de criação do banco (corrigido)
├── public/                     # ÚNICA pasta que fica exposta no navegador
│   ├── index.php                # Front Controller (ponto de entrada único)
│   ├── .htaccess                 # URLs amigáveis (/login, /menu, /mapa...)
│   └── assets/                   # CSS, JS e mídia
└── app/
    ├── helpers.php              # funções auxiliares (url, e, sessão...)
    ├── routes.php               # todas as rotas do sistema
    ├── Core/                    # Database, Controller base e Router
    ├── Models/                  # Produto, Usuario, Movimentacao (acesso a dados)
    ├── Controllers/             # regras de negócio de cada módulo
    └── Views/                   # telas (HTML + PHP), organizadas por módulo
        ├── layout/                # layout logado (sidebar) e layout de login
        ├── auth/, dashboard/, produtos/, movimentacoes/, mapa/, scanner/
```

**Model** → `app/Models` (conversa com o banco via PDO / consultas preparadas)
**View** → `app/Views` (somente exibição, sem lógica de negócio)
**Controller** → `app/Controllers` (recebe a requisição, chama o Model, escolhe a View)

## Como rodar (XAMPP)

1. Copie a pasta `findware-mvc` para dentro de `htdocs`.
2. Crie o banco importando `database/schema.sql` (já corrige colunas que
   faltavam no dump original: `nivel` em `produtos` e `codigo_barras`/`tipo`
   em `movimentacoes`).
3. Ajuste, se necessário, o usuário/senha do banco em `config.php`.
4. Acesse pelo navegador: `http://localhost/findware-mvc/public/`
5. Login de teste: **admin@findware.com** / **123456** (ou crie um novo
   usuário pela tela "Criar novo usuário").

> Se o Apache não tiver `mod_rewrite` habilitado, o sistema também funciona
> sem URLs amigáveis, bastando acessar `public/index.php`.

## O que foi melhorado nesta versão

- **Organização em MVC**: cada página antes era um arquivo `.php` avulso
  misturando HTML, SQL e regra de negócio; agora cada camada tem seu lugar.
- **Um único ponto de entrada** (`public/index.php`) com rotas nomeadas
  (`/login`, `/menu`, `/produtos/cadastrar`, `/mapa`, etc.).
- **Banco de dados via PDO** com consultas preparadas (mais seguro que a
  versão anterior com `mysqli` e strings concatenadas).
- **Senhas com hash** (`password_hash`) para novos cadastros.
- **Tela de mensagem reutilizável** (sucesso/erro) usada por várias ações,
  eliminando páginas de resultado duplicadas.
- **Visual novo**: layout com menu lateral, cards, indicadores e tela de
  login/cadastro em duas colunas, mantendo tema claro/escuro.
- Todas as funcionalidades originais foram mantidas: cadastro, busca,
  recebimento, saída, mapa do CD, produto perdido (busca por proximidade)
  e leitor de QR Code.

## Telas para apresentação

Sugestão de roteiro (mais de 3 telas prontas e bonitas):

1. **Login** — tela dividida com identidade visual do FindWare.
2. **Painel Principal** — indicadores + atalhos para todas as operações.
3. **Dashboard** — indicadores + tabelas de produtos e movimentações.
4. **Mapa do CD** — grade visual destacando a posição do produto.
5. **Cadastro de Produto** — formulário com geração automática de QR Code.
6. **Scanner** — leitura de QR Code para entrada, saída e localização.

## Observação sobre tecnologia usada

Projeto propositalmente simples: PHP puro + PDO + HTML/CSS/JS, sem
Composer, sem frameworks de front-end e sem build step — só copiar,
importar o banco e rodar.
