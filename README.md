# FACE Digital - Crudify for laravel 9.x

<a href="LICENSE.md" title="MIT"><img src="https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square"></a>

<div align="center">
  <a href="https://github.com/facedigital/crudify">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="assets/images/logo-white.svg">
        <source media="(prefers-color-scheme: light)" srcset="assets/images/logo.svg">
        <img alt="Cudify Logo" src="assets/images/logo.svg" width="500" style="padding: 50px;">
    </picture>
  </a>

  <h3 align="center">FACE Digital - Crudify</h3>

  <p align="center">
    Package implements a CRUD generator based on schema or table existing in the database. Ideal for laravel projects with legacy database.
    <br />
    <a href="https://github.com/facedigital/crudify"><strong>Explore the docs »</strong></a>
    <br />
    <a href="https://github.com/facedigital/crudify/issues">Report Bug</a>
    ·
    <a href="https://github.com/facedigital/crudify/issues">Request Feature</a>
  </p>
</div>


<!-- TABLE OF CONTENTS -->
<details open>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#installation">Installation</a></li>
        <li><a href="#usage">Usage</a></li>
      </ul>
    </li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->
## About The Project

This Package is developed by FACE Digital and implements a CRUD generator for Laravel Framework designed to simplify CRUD operations by automatically generating code based on existing schemas or tables in a database. It is particularly well-suited for Laravel projects that rely on legacy databases. With this package, developers can quickly create, read, update, and delete data without the need for manual coding.

Note: As an early version this tool still has a lot of limitations but it can be improved with community contributions.

## Installation
Installing from Git repository. Add these lines to the project's `composer.json`.


```json
"require": {
    "facedigital/crudify": "*"
}
```
Add this from repositories section.
```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:facedigital/crudify.git"
    }
]
```
If the version is not informed, the master branch will be installed.

Run `composer install` or `composer update` command.


After installation run the command:

`php artisan vendor:publish --tag=crudify`

## Usage

### Crudify All Command
Creates all files based on the `name` and `--schema` that are passed as a parameter.

```bash
php artisan crudify:all post --schema "title:string, subtitle:string:nullable, content:text"
```

Creates all files based on an existing table in the database.

```bash
php artisan crudify:all post
```

Created Files:
- [x] Model
- [x] Controller
- [x] StoreRequest
- [x] UpdateRequest
- [x] Factory
- [x] View Index
- [x] View Show
- [x] View Create
- [x] View Edit

### Crudify Migration Command

Creates a migration based on the `name` and `--schema` that are passed as a parameter.

```bash
php artisan crudify:migration post --schema "title:string, subtitle:string:nullable, content:text"
```

Creates a migration based on an existing table in the database.

```bash
php artisan crudify:migration post
```
Created Files:
- [x] Migration

### Crudify Factory Command

Creates a factory based on the `name` and `--schema` that are passed as a parameter.

```bash
php artisan crudify:factory post --schema 'title:string, subtitle:string:nullable, content:text'
```

Creates a factory based on an existing table in the database.

```bash
php artisan crudify:factory post
```

Created Files:
- [x] factory

### Crudify Model Commnad

Creates a model based on the `name` and `--schema` that are passed as a parameter.

```bash
php artisan crudify:model post --schema 'title:string, subtitle:string:nullable, content:text'
```

Creates a model based on an existing table in the database.

```bash
php artisan crudify:model post
```

Created Files:
- [x] Model

### Crudify Controller Command

Creates a controller based on the `name` and `--schema` that are passed as a parameter.

```bash
php artisan crudify:controller post --schema 'title:string, subtitle:string:nullable, content:text'
```

Creates a controller based on an existing table in the database.

```bash
php artisan crudify:controller post
```

Created Files:
- [x] Controller

### Crudify Views Command [index|show|create|edit]

Creates a view based on the `name` and `--schema` that are passed as a parameter.

```bash
php artisan crudify:index post --schema 'title:string, subtitle:string:nullable, content:text'
```

Creates a view based on an existing table in the database.

```bash
php artisan crudify:index post
```

Created Files:
- [x] View Index
- [x] View Show
- [x] View Create
- [x] View Edit

### Change Theme

In the config file located at `config/crudify.php` has the `theme` definition.

```php
'theme' => 'bootstrap' // [bootstrap|tailwind]
```

### Customize Stubs

```php
'stubs' => [
    'views' => [
        'index' => null, // [null = default|project]
        'create' => 'stubs/crudify/views/create.blade.php.stub',
        'edit' => null,
        'show' => null,
    ]
],
```

<!-- ROADMAP -->
## Roadmap

- [x] Add Changelog
- [x] Add README and basic documentation
- [x] Add MIT License
- [x] Add Theme definition in config
- [x] Add Basic Class Components
- [x] CLI Commands
    - [x] Generate All Command
    - [x] Generate Migration Command
    - [x] Generate Model Command
    - [x] Generate Controller Command
    - [x] Generate StoreRequest Command
    - [x] Generate UpdateRequest Command
    - [x] Generate View Index Command
    - [x] Generate View Edit Command
    - [x] Generate View Show Command

See the [open issues](https://github.com/facedigital/crudify/issues) for a full list of proposed features (and known issues).

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.

## Contact

FACE Digital - [facedigital.com.br](https://facedigital.com.br/) - dev@facedigital.com.br

Project Link: [https://github.com/facedigital/crudify](https://github.com/facedigital/crudify)
