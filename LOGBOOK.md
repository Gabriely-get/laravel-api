# Processo, Dificuldades e Soluções
Processo de decisões, dificuldades e erros encontrados e raciocínio por trás da solução.
<br> Problemas, pensamentos e soluções organizados em lista.

- [Api REST](#api)
- [Conteinerização](#docker)
- [IaC](#terraform)

<a id="api"></a>
# API REST
Desenvolvimento da API

## **1. Framework:**
* Em primeiro momento precisei decidir entre as duas tecnologias PHP que eu conhecia: O micro-framework [Lumen](https://lumen.laravel.com/docs/11.x) e o mais robusto [Laravel](https://laravel.com/). 
1. Lumen está deprecado e a própria documentação nos orienta a utilizar Laravel
2. Laravel é um framework mvc e não achei interessante ter uma pasta *bootstap* vazia, uma vez que faria apenas uma api rest sem front-end
3. Consultei em uma [IA](https://www.deepseek.com/) opções de tecnologias PHP enxutas para desenvolvimento exclusivo de back-end e cogitei usar o Slim
4. A escolha final foi o Laravel levando em consideração a minha experiência prévia, prazo de entrega, curva de aprendizagem, facilidades como migration e conexão com banco embutidas e também devido ao fato de, ao criar o projeto, a entidade "User" vir praticamente completa

## **2. Start do Projeto:**
>O que entendia ser simples, se tornou um desafio...

1. Após limpar do meu projeto os arquivos front-end que não utilizaria, não consegui "startar" o projeto com *php artisan serve*. 
2. Pesquisei bastante c/ o Google e sugestões de IA's, alterei o possível no arquivo *php.ini* mas por fim, percebi que dediquei muito tempo para fazer esse comando funcionar e optei por em desenvolvimento utilizar: **php -S 127.0.0.1:8000 -t public**. Que funcionou perfeitamente. 
3.  Inclusive, acredito que por razão do laravel já iniciar um projeto com Vite, o start com artisan não funcionou devido a algum arquivo de configuração no projeto, que não sabia aonde procurar o host/porta e/ou executar apenas o back-end com esse comando

## 3. Pattern:
Escolhi utilizar a estrutura que tenho melhor fluência que se resume em: 
1. **Repository** para acesso a Base de Dados
2. **Service** para armazenar lógicas complexas e tratar o dado antes de ser inserido na base
3. **Controller** para expor o endpoint e chamar o service apropriado.

## 4. Routes:
1. Após desenvolver os endpoints, as rotas retornavam apenas **status 404**, mesmo sendo definidas em *routes/api.php*.
2. Executando o comando **php artisan route:list**, me foi retornado as rotas presentes no projeto e, de fato, minhas rotas */user* não estavam presentes, porém '/storage' estava.
3. Solucionei ao encontrar o arquivo *bootstrap/app.php* e definir minha rota /user neste. Assim, consegui consumir os endpoints.

## 5. Exceptions e Validations:
1. Após conseguir criar um usuário tentei armazenar o mesmo *body*, o que gerou um erro 500 e um response de exceção horrível ao usuário
2. Inseri try-catchs por toda a cadeia (Repository -> Service -> Controller)
3. Tentei utilizar um Request customizado (para não armazenar as validações e mensagens customizadas de dados diretamente no Controller), mas não houve sucesso uma vez que o endpoint retornava 404 ao tentar acessar este custom Request. Resultando em não utilizar Custom Requests e chamar o Validate no próprio Controller pois assim funcionava corretamente.

## 6. Testing
Com a ajuda da IA ChatGPT e stack overflow consegui ir acertando a sintaxe e arrumar os seguintes tópicos em meus testes:
- utilizar *new JsonResponse()* no controller ao invés de *response()->json()*
- Criar estrura de *expect* com **createMock** ao invés de **Mock::**
- Importar **Tests\TestCase;** ao invés de **PHPUnit\Framework\TestCase;** devido a versão
- Nomes mais concisos para meus testes
- Sintaxe em geral

Infelizmente não consegui contar tanto com a [doc](https://docs.phpunit.de/en/10.5/test-doubles.html#createmock) devido a pressa..Mas os exemplos da sintaxe do Mock presentes nela são claros.

<a id="docker"></a>
# Docker

1. Criei uma instância EC2 em minha conta AWS pessoal, clonei o repositório e criei o Dockerfile para a API Rest lá. O comando ``php artisan serve`` funcionou perfeitamente dentro do container, diferente de quando tentei rodar localmente no meu Windows.
2. Após criar um docker-compose simples, o container da aplicação não estava conseguindo conectar no container da base de dados. Era necessário instalar um driver php-mysql. A solução foi integrada ao Dockerfile.
3. Outro problema de conexão entre os containeres, foi conseguir definir corretamente o **DB_HOST**.
4. Através da IA foi gerado um trecho de script bash que espera o container **db** estar executando e após isso, decidi inserir os comando de execução de migration no script também. 
5. Foi necessário executar o comando ``php artisan serve --host=0.0.0.0`` para conseguirmos acessar a aplicação fora do container
6. O ENTRYPOINT do container da API é executando um bash com tudo que preciso que seja executado ao iniciar o container.

<a id="terraform"></a>
# Terraform
1. [Aqui](https://registry.terraform.io/providers/hashicorp/aws/latest/docs/resources/instance) peguei o template de criação básica de uma EC2 e, seguindo outras partes da documentação, adicionei variáveis, filtros e criação do *security group*.

# Packer
Decidi criar uma imagem customizada de uma EC2 para que o script terraform já crie uma instancia com tudo o que preciso

1. Tomei essa decisão pois imaginei que subir uma instância com imagem padrão com terraform e, apenas após isso instalar minhas dependências, iria resultar em uma *Pipeline* demorada.
2. Gostaria de ter feito uma estrutura de instalação de pacotes com Ansible mas levando em consideração o prazo de entrega, executei os scripts (*install_docker.sh* e *install_K8S_locally.sh*) que havia feito no mesmo dia.
