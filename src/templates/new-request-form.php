<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Vacation Request</title>
        <link rel="stylesheet" href="/styles/form.css">

        <!-- HTMX -->
        <script src="https://unpkg.com/htmx.org"></script>
        <script src="https://unpkg.com/htmx.org/dist/ext/json-enc.js"></script>
        <script src="/scripts/hmx-handler.js"></script>
    </head>
    <body>
        <div class="form-container">
            <h1>New Vacation Request</h1>
            <form hx-post="/requests"
                hx-ext="json-enc"
                hx-trigger="submit"
                hx-encoding="json"
                hx-swap="none"
                hx-on::after-request="handleFormResponse(event)"
            >
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason</label>
                    <textarea id="reason" name="reason" placeholder="Enter the reason for your vacation" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Submit Request</button>
            </form>
        </div>
    </body>
</html>