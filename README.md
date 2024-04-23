# API ChatGPT-4 PHP

## Descrição
Este repositório oferece uma API simples implementada em PHP para interagir com o ChatGPT-4 da OpenAI. É ideal para desenvolvedores que desejam explorar as capacidades do GPT-4 integrando-o em suas próprias aplicações PHP.

## Tecnologias Utilizadas
- **PHP**: Linguagem de programação usada para criar a API.
- **OpenAI API**: Fornece acesso ao modelo GPT-4 para geração de texto.

## Instalação
Siga estas instruções para configurar e rodar a API em seu ambiente local.

### Pré-requisitos
- PHP 7.4 ou superior.
- Composer para gerenciamento de dependências.

### Clonando o Repositório
```
git clone https://github.com/aeusteixeira/api-chatgpt4-php
cd api-chatgpt4-php
```

### Instalando Dependências
```
composer install
```

## Configuração
Antes de iniciar a API, configure as variáveis de ambiente, incluindo sua chave de API da OpenAI (`OPEN_IA_KEY`), no arquivo `.env`.

## Uso
A API possui duas rotas principais:

### GET `/questions`
Exibe uma mensagem de boas-vindas e serve para verificar se a API está operacional.

#### Exemplo de Requisição
```
curl http://localhost:8000/questions
```

### POST `/questions/respond`
Aceita uma pergunta em formato JSON e retorna a resposta do modelo GPT-4.

#### Exemplo de Requisição
```
curl -X POST http://localhost:8000/questions/respond -H "Content-Type: application/json" -d '{"question": "Explique o teorema de Bayes."}'
```

#### Resposta Esperada
```json
{
  "response": "O teorema de Bayes é uma fórmula que descreve como atualizar as probabilidades de hipóteses quando mais evidências ou informações ficam disponíveis."
}
```

### POST `/questions/image`
Aceita uma pergunta e uma URL de imagem em formato JSON e retorna a resposta do modelo GPT-4.

#### Exemplo de Requisição
```
curl -X POST http://localhost:8000/questions/image -H "Content-Type: application/json" -d '{"question": "O que representa esta imagem?", "image": "https://cdn-icons-png.flaticon.com/512/25/25231.png"}'
```

#### Resposta Esperada
```json
{
  "response": "Logo do GitHub."
}
```

## Contribuição
Contribuições para melhorar a API são sempre bem-vindas. Se você tem sugestões de melhorias ou correções, por favor, faça um fork do repositório, faça suas alterações, e submeta um pull request.

## Licença
Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## Contato
Se você tiver qualquer dúvida ou sugestão, não hesite em entrar em contato via Twitter: [@aeusteixeira](https://twitter.com/aeusteixeira).