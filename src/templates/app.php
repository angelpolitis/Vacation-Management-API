<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Vacation Management API</title>
        
        <link rel="stylesheet" href="/styles/app.css"/>

        <!-- HTMX -->
        <script src="https://unpkg.com/htmx.org"></script>
        <script src="https://unpkg.com/htmx.org/dist/ext/json-enc.js"></script>
    </head>
    <body>
        <header class="main-header">
            <h1>Vacation Management Dashboard</h1>
            <nav>
                <a href="/logout">Logout</a>
            </nav>
        </header>

        <?php require __DIR__ . $appTemplate; ?>
    </body>
</html>