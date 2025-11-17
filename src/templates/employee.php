<!-- Technically it should be added to <HEAD> -->
<link rel="stylesheet" href="/styles/employee.css"/>
<script src="https://unpkg.com/htmx.org"></script>
<script src="https://unpkg.com/htmx.org/dist/ext/json-enc.js"></script>
<script src="/scripts/hmx-handler.js"></script>

<section>
    <header class="section-header">
        <h2>My Requests (<?= sizeof($requests) ?>)</h2>
        <a href="/requests/new" class="new-request-btn">New Request</a>
    </header>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Submission Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request["id"]) ?></td>
                    <td><?= htmlspecialchars($request["start_date"]) ?></td>
                    <td><?= htmlspecialchars($request["end_date"]) ?></td>
                    <td><?= htmlspecialchars($request["reason"]) ?></td>
                    <td><?= htmlspecialchars($request["submission_date"]) ?></td>
                    <td><?= htmlspecialchars($request["status"]) ?></td>
                    <td>
                        <?php if ($request["status"] === "pending"): ?>
                        <form hx-delete="/requests/<?= htmlspecialchars($request["id"]) ?>"
                            hx-trigger="submit"
                            hx-target="closest tr"
                            hx-swap="delete"
                            onsubmit="return confirm('Are you sure you want to delete this request?');"
                            hx-on::after-request="handleFormResponse(event)"
                        >
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>