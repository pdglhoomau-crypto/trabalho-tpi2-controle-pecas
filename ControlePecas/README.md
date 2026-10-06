# Controle de Peças - TPI II / Momento I

Projeto baseado somente nos requisitos visíveis das fotos do enunciado. HTML, CSS, JavaScript e PHP 8+. Sete formulários, cada um com pelo menos cinco campos; sete entidades planejadas; nenhum banco criado; nenhuma gravação de dados.

## Executar no Windows (XAMPP)
1. Copie a pasta ControlePecas para C:\xampp\htdocs\ControlePecas.
2. Inicie Apache no painel XAMPP.
3. Abra http://localhost/ControlePecas/ no navegador.
Não abra apenas o HTML por duplo clique: o envio precisa de PHP.

Com PHP instalado, dentro da pasta execute `php -S localhost:8000` e acesse http://localhost:8000.

## Fluxo
O JavaScript intercepta o envio, usa fetch com POST/FormData e recebe JSON. O endpoint valida todos os campos e aplica uma regra própria a cada entidade. HTML e PHP estão em arquivos separados. CPF/CNPJ são validados somente quanto ao formato e quantidade de dígitos.

## Banco futuro
Categorias 1:N Peças; Fornecedores 1:N Peças; Clientes 1:N Vendas; Usuários 1:N Vendas; Vendas 1:N ItensVenda; Peças 1:N ItensVenda. Todas terão id como chave primária. Os IDs digitados são referências planejadas: existência e integridade referencial dependerão do banco futuro. Usuários é cadastro demonstrativo, sem autenticação.

## Entrega e autoria
O relatório explica o sistema e os testes. O roteiro Git precisa ser executado pelos dois integrantes em suas contas: não equivale a um histórico de colaboração real. Inclua no relatório os comandos efetivamente utilizados e o nome/usuário GitHub de cada integrante antes de entregar. O questionário está como roteiro para respostas pessoais honestas. Publicar o repositório e realizar a colaboração ainda são etapas pendentes.

Repositório informado: https://github.com/pdglhoomau-crypto/trabalho-tpi2-controle-pecas
