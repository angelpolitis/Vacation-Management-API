<!-- Technically it should be added to <HEAD> -->
<link rel="stylesheet" href="/styles/employee.css"/>
<link rel="stylesheet" href="/styles/manager.css"/>
<script src="https://unpkg.com/htmx.org"></script>
<script src="https://unpkg.com/htmx.org/dist/ext/json-enc.js"></script>
<script src="/scripts/hmx-handler.js"></script>

<section>
    <header class="section-header">
        <h2>Users (<?= sizeof($users) ?>)</h2>
        <a href="/users/new" class="new-request-btn">New User</a>
    </header>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Employee Code</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($users as $user):
                    $id = htmlspecialchars($user["id"]);
            ?>
                <tr>
                    <td><?= $id ?></td>
                    <td><?= htmlspecialchars($user["name"]) ?></td>
                    <td><?= htmlspecialchars($user["email"]) ?></td>
                    <td><?= htmlspecialchars($user["employee_code"]) ?></td>
                    <td><?= htmlspecialchars($user["type"]) ?></td>
                    <td>
                        <a href="/users/<?= $id ?>/edit" class="positive-cta">Edit</a>
                        <form hx-delete="/users/<?= $id ?>"
                            hx-trigger="submit"
                            hx-target="closest tr"
                            hx-swap="delete"
                            onsubmit="return confirm('Are you sure you want to delete this user?');"
                            hx-on::after-request="handleFormResponse(event)"
                        >
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section>
    <header class="section-header">
        <h2>Incoming Requests (<?= sizeof($requests) ?>)</h2>
    </header>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Author</th>
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
                    <td><?= htmlspecialchars($request["requested_by"]["name"]) ?></td>
                    <td><?= htmlspecialchars($request["start_date"]) ?></td>
                    <td><?= htmlspecialchars($request["end_date"]) ?></td>
                    <td><?= htmlspecialchars($request["reason"]) ?></td>
                    <td><?= htmlspecialchars($request["submission_date"]) ?></td>
                    <td><?= htmlspecialchars($request["status"]) ?></td>
                    <td>
                        <?php
                            if ($request["status"] === "pending"):
                                $id = htmlspecialchars($request["id"]);
                        ?>
                        <form hx-patch="/requests/<?= $id ?>/approve"
                            hx-trigger="submit"
                            hx-swap="none"
                            hx-on::after-request="handleFormResponse(event)"
                        >
                            <button type="submit" class="positive-cta">Approve</button>
                        </form>
                        <form hx-patch="/requests/<?= $id ?>/reject"
                            hx-trigger="submit"
                            hx-swap="none"
                            hx-on::after-request="handleFormResponse(event)"
                        >
                            <button type="submit" class="mid-cta">Reject</button>
                        </form>
                        <?php endif ?>
                        <form hx-delete="/requests/<?= $id ?>"
                            hx-trigger="submit"
                            hx-target="closest tr"
                            hx-swap="delete"
                            onsubmit="return confirm('Are you sure you want to delete this request?');"
                            hx-on::after-request="handleFormResponse(event)"
                        >
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section>
    <header class="section-header">
        <h2>Settled Requests (<?= sizeof($history) ?>)</h2>
    </header>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Author</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Submission Date</th>
                <th>Status</th>
                <th>Settled By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $historyItem): ?>
                <tr>
                    <td><?= htmlspecialchars($historyItem["id"]) ?></td>
                    <td><?= htmlspecialchars($historyItem["requested_by"]["name"]) ?></td>
                    <td><?= htmlspecialchars($historyItem["start_date"]) ?></td>
                    <td><?= htmlspecialchars($historyItem["end_date"]) ?></td>
                    <td><?= htmlspecialchars($historyItem["reason"]) ?></td>
                    <td><?= htmlspecialchars($historyItem["submission_date"]) ?></td>
                    <td><?= htmlspecialchars($historyItem["status"]) ?></td>
                    <td><?= htmlspecialchars($historyItem["decided_by"]["name"]) ?></td>
                    <td>
                        <?php
                            if ($historyItem["status"] === "pending"):
                                $id = htmlspecialchars($historyItem["id"]);
                        ?>
                        <form hx-patch="/requests/<?= $id ?>/approve"
                            hx-trigger="submit"
                            hx-swap="none"
                            hx-on::after-request="handleFormResponse(event)"
                        >
                            <button type="submit" class="positive-cta">Approve</button>
                        </form>
                        <form hx-patch="/requests/<?= $id ?>/reject"
                            hx-trigger="submit"
                            hx-swap="none"
                            hx-on::after-request="handleFormResponse(event)"
                        >
                            <button type="submit" class="mid-cta">Reject</button>
                        </form>
                        <?php endif ?>
                        <form hx-delete="/requests/<?= $id ?>"
                            hx-trigger="submit"
                            hx-target="closest tr"
                            hx-swap="delete"
                            onsubmit="return confirm('Are you sure you want to delete this request?');"
                            hx-on::after-request="handleFormResponse(event)"
                        >
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>