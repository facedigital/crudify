# FaceDigital - CRUD Generator

## Instalação
Instalando a partir de repositório privado. Adicionar estas linhas no `composer.json` do projeto.

```json
"require": {
    "facedigital/crudify": "*"
}
```
Caso não seja informada a versão será instalada a branch master.

```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@code.facedigital.net:facedigital/crud-generator.git"
    }
]
```

Após a instalação rodar o comando:

`php artisan vendor:publish --tag=crudify`

## Comandos

### Migration

Cria uma migration com base no `name` e no `--schema` que forem passados como parametro.

```bash
crudify:migration post --schema 'title:string, subtitle:string:nullable, content:text'
```

Cria uma migration com base em uma tabela já existente no banco de dados.

```bash
crudify:migration post
```

### Factory

Cria uma factory com base no `name` e no `--schema` que forem passados como parametro.

```bash
crudify:factory post --schema 'title:string, subtitle:string:nullable, content:text'
```

Cria uma factory com base em uma tabela já existente no banco de dados.

```bash
crudify:factory post
```

### Model

Cria um model com base no `name` e no `--schema` que forem passados como parametro.

```bash
crudify:model post --schema 'title:string, subtitle:string:nullable, content:text'
```

Cria uma model com base em uma tabela já existente no banco de dados.

```bash
crudify:model post
```

### Controller

Cria um controller com base no `name` e no `--schema` que forem passados como parametro.

```bash
crudify:controller post --schema 'title:string, subtitle:string:nullable, content:text'
```

Cria uma controller com base em uma tabela já existente no banco de dados.

```bash
crudify:controller post
```

### Views

Cria um view com base no `name` e no `--schema` que forem passados como parametro.

```bash
crudify:index post --schema 'title:string, subtitle:string:nullable, content:text'
```

Cria uma view com base em uma tabela já existente no banco de dados.

```bash
crudify:index post
```
