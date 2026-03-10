HR L&D - Deep PHP Demo

Quick start

1) Import the database into MySQL (using phpMyAdmin or CLI):

Using CLI:

```bash
mysql -u root -p < database-creation.sql
```

Make sure the `database-creation.sql` file is available in your MySQL import context and adjust user/host as needed.

2) Place the `deep-php` folder inside your webroot (already scaffolded at `deep-php`).

3) Start XAMPP/Apache + MySQL, then visit:

http://localhost/davidz/deep-php/index.php

4) Use `login.php` to sign in for demo roles (employee, manager, admin). The Admin menu appears only for `manager` and `admin` roles.

Notes
- This demo uses a simple session-based demo login only for local development.
- Update `config.php` with real DB credentials for production use.
- The navigation items are static placeholders; connect them to real pages as needed.