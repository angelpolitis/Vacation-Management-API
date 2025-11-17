<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit User - Vacation Management API</title>
        <link rel="stylesheet" href="/styles/form.css">

        <!-- HTMX -->
        <script src="https://unpkg.com/htmx.org"></script>
        <script src="https://unpkg.com/htmx.org/dist/ext/json-enc.js"></script>
        <script src="/scripts/hmx-handler.js"></script>
    </head>
    <body>
        <div class="form-container">
            <h1>Edit User</h1>
            <form hx-patch="/users/<?= htmlspecialchars($user["id"]) ?>"
                hx-ext="json-enc"
                hx-trigger="submit"
                hx-encoding="json"
                hx-swap="none"
                hx-on::after-request="handleFormResponse(event)"
            >
                <?php require __DIR__ . "/user-form.php"; ?>
            </form>
        </div>
    </body>
</html>