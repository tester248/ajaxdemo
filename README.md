# AJAX Student Search Demo

A small demonstration that shows how to load data dynamically from a server using AJAX (the Fetch API) with PHP and MySQL.

This repository contains a minimal single-page UI (`index.html`) that sends a search query to a server-side script (`search.php`) and renders the JSON response without a full page refresh.

Files
- `index.html` — Front-end: search input and JavaScript (fetch + debounce) to call the server and render results.
- `search.php` — Server-side endpoint that accepts `?q=` and returns matching `students` rows as JSON.
- `db.php` — Database helper and connection constants (update to match your environment).
- `setup.sql` — SQL script to create the `ajaxdemo` DB, `students` table and insert sample rows (Indian sample names).

Prerequisites
- PHP (8.x recommended) with a web SAPI or the built-in PHP dev server.
- MySQL / MariaDB server.

Quick setup
1. Install required packages (Debian/Ubuntu example):

```bash
sudo apt update
sudo apt install -y php php-mysqli php-pdo-mysql mysql-server
```

2. Create the sample database and insert rows:

```bash
mysql -u root -p < setup.sql
```

3. Update database credentials in `db.php` if your MySQL user, host or password are different.

Run the demo (quick)

1. From the project folder run the built-in PHP server for quick testing:

```bash
php -S 127.0.0.1:8000
```

2. Open the demo in your browser:

```
http://127.0.0.1:8000/index.html
```

3. Type into the search box. Results update without a full page refresh.

Example search terms
- `Asha`, `Rahul`, `Priya`, `David` — sample student names.
- `R1001` — sample roll number.
- `Computer` — sample branch text.

Troubleshooting

- 500 error or missing MySQL extension

   If `search.php` returns HTTP 500 or you see errors about missing MySQL extensions in the PHP logs, install the PHP MySQL package and restart the server:

   ```bash
   sudo apt install -y php-mysql
   # stop the dev server and start it again: php -S 127.0.0.1:8000
   ```

- Development fallback

   For local convenience `search.php` contains a pragmatic fallback that invokes the `mysql` CLI when the PHP MySQL driver (PDO / mysqli) is unavailable. This keeps the demo working in constrained environments (like some containers). The CLI fallback uses shell commands and optional `sudo` to allow socket-authenticated `root` — do not use that fallback in production.

Notes & security

- Use a dedicated, low-privilege MySQL user in production; avoid `root` for application access.
- Prepared statements are used where possible to mitigate SQL injection risks.
- The demo limits returned results to 50 rows; change the LIMIT in `search.php` if you need more or add pagination.

Possible next steps

- Replace the CLI fallback with a clean PDO-only implementation (recommended for production).
- Add server-side pagination and a "Load more" UI control.
- Add filters (branch, subjects) and highlighting of matched text in results.

---

Project: ajaxdemo

---

Project: ajaxdemo