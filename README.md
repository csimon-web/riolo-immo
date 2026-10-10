<!-- PROJECT SHIELDS -->
<!--
*** This template uses markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->

[![Contributors][contributors-shield]][contributors-url] [![Forks][forks-shield]][forks-url] [![Stargazers][stars-shield]][stars-url] [![Issues][issues-shield]][issues-url]

<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://example.com)

This project was realized during my training as a web applications developer for a fictitious real estate agency. It comes from an [online tutorial provided by Grafikart](https://grafikart.fr/formations/symfony-4-pratique) originally developed using Symfony 4, that I updated to Symfony 6.4.

### Built With

- Html 5, Css 3, Php 8
- Symfony 6.4 (LTS), Doctrine ORM, Webpack Encore, Bootstrap 4
- MariaDB / MySQL
- Git, Github
- VS Code

<!-- GETTING STARTED -->
## Getting Started

To get a local copy up and running follow these simple steps.

### Prerequisites

* Git
* PHP 8.1 or higher (tested with PHP 8.4) and [Composer](https://getcomposer.org/)
* MySQL or MariaDB (tested with MariaDB 13.0)
* Node.js and npm
* Symfony CLI (optional, PHP's built-in server also works)

### Installation
 
1. Clone the repo and enter the project directory:
   ```sh
   git clone https://github.com/csimon-web/riolo-immo.git
   cd riolo-immo
   ```
2. Install the PHP dependencies:
   ```sh
   composer install
   ```
3. Create a `.env.local` file at the root of the project (it is ignored by Git) to override the default settings of `.env`. Set your database username, password and server version:
   ```dotenv
   DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/riolo_immo_dev?serverVersion=mariadb-11.4.2&charset=utf8mb4"
   APP_SECRET=a_random_string
   ```
   For a MySQL server, use `serverVersion=8.0.32` (adapt to your version). You can generate a secret with `php -r "echo bin2hex(random_bytes(16));"`.
4. Create the database and its tables:
   ```sh
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
5. Load the sample data (this empties the database first):
   ```sh
   php bin/console doctrine:fixtures:load
   ```
6. Install and build the front-end assets:
   ```sh
   npm install
   npm run dev
   ```
7. Launch the server:
   ```sh
   symfony serve -d
   ```
   Without the Symfony CLI: `php -S 127.0.0.1:8000 -t public`
8. Open https://127.0.0.1:8000 in your browser (or the port displayed by the Symfony server).

### Optional: contact form emails

By default, `MAILER_DSN=null://null` discards emails. To test the contact form, set a `MAILER_DSN` in `.env.local` (for example a [Mailtrap](https://mailtrap.io) sandbox). Emails are sent through Symfony Messenger, so run a worker in a separate terminal:
```sh
php bin/console messenger:consume async -vv
```


<!-- USAGE EXAMPLES -->
## Usage

You can use the web application as a default visitor at the root address.

To manage the properties, log in at `/login` and use the agency area at `/agency`. There you can add new properties and options, and edit or delete existing ones.

With the sample data, the local account is `admin` / `admin`. This account is for local development only: never use it in production.



<!-- CONTACT -->
## Contact

Christophe Simon: [personnal website](https://www.csimon.info)

Project Link: [https://github.com/csimon-web/riolo-immo](https://github.com/csimon-web/riolo-immo)



<!-- ACKNOWLEDGEMENTS -->
## Acknowledgements

- This readme version is a customized version of this [github repository](https://github.com/NicolasBrondin/basic-readme-template) by NicolasBrondin





<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/csimon-web/riolo-immo.svg?style=flat-square
[contributors-url]: https://github.com/csimon-web/riolo-immo/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/csimon-web/riolo-immo.svg?style=flat-square
[forks-url]: https://github.com/csimon-web/riolo-immo/network/members
[stars-shield]: https://img.shields.io/github/stars/csimon-web/riolo-immo.svg?style=flat-square
[stars-url]: https://github.com/csimon-web/riolo-immo/stargazers
[issues-shield]: https://img.shields.io/github/issues/csimon-web/riolo-immo.svg?style=flat-square
[issues-url]: https://github.com/csimon-web/riolo-immo/issues
[product-screenshot]: docs/cover.jpg
