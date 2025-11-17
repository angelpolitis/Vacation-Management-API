<?php
    namespace App;

    use App\Models\Request;
    use App\Models\User;
    use InvalidArgumentException;
    use PDOException;
    use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
    use Pecee\SimpleRouter\SimpleRouter;

    # GET /: Serve the page.
    SimpleRouter::get("/", function () {
        header("Content-Type: text/html");
        
        if (USER_ID === null) {
            require __DIR__ . "/templates/login-form.php";
            return;
        }

        if (IS_MANAGER) {
            $appTemplate = "/manager.php";

            $users = User::select([], ["id", "name", "email", "employee_code", "type"]);
            
            $requests = Request::selectWithUsers(["status" => "pending"]);
            
            $history = Request::selectSettled();
        }
        else {
            $appTemplate = "/employee.php";

            $requests = Request::selectWithUsers(["requested_by" => USER_ID]);
        }
        
        require __DIR__ . "/templates/app.php";
    });

    # POST /login: Log in a user.
    SimpleRouter::post("/login", function () {
        $data = jsonInput();

        if (empty($data["email"]) || empty($data["password"])) {
            header("Location: /?error=missing_credentials");
            exit;
        }

        $users = User::select(["email" => $data["email"]], ["id", "password"]);

        if (count($users) === 0 || !password_verify($data["password"], $users[0]["password"])) {
            header("Location: /?error=invalid_credentials");
            exit;
        }

        $_SESSION["user_id"] = $users[0]["id"];

        header("Location: /");
        exit;
    });

    # GET /logout: Log out as a user.
    SimpleRouter::GET("/logout", function () {
        $_SESSION["user_id"] = null;

        header("Location: /");
        exit;
    });

    # GET /users: Get a list of all users.
    SimpleRouter::get("/users", function () {
        guardAgainstEmployee();

        $users = User::select([], ["id", "name", "email", "employee_code", "type"]);
        return jsonResponse($users);
    });

    # GET /users/new: Displays a form that creates a new user.
    SimpleRouter::get("/users/new", function () {
        guardAgainstEmployee();

        require __DIR__ . "/templates/new-user-form.php";
    });

    # GET /users/{id}: Get a user by ID.
    SimpleRouter::get("/users/{id}", function ($id) {
        guardAgainstEmployee();

        $user = User::select(["id" => $id], ["id", "name", "email", "employee_code", "type"])[0] ?? null;

        if (!$user) {
            return jsonResponse(["status" => false, "error" => "User not found"], 404);
        }
        
        return jsonResponse($user);
    });

    # POST /users: Create a new user.
    SimpleRouter::post("/users", function () {
        guardAgainstEmployee();

        $data = jsonInput();
        $user = User::from($data);
        $ok = $user->create();
        return jsonResponse(["status" => $ok, "redirect" => '/'], $ok ? 201 : 400);
    });

    # DELETE /users/{id}: Delete a user by ID.
    SimpleRouter::delete("/users/{id}", function ($id) {
        guardAgainstEmployee();

        $user = User::from(["id" => $id]);
        $ok = $user->delete();
        return jsonResponse(["status" => $ok]);
    });

    # GET /users/{id}/edit: Displays a form that edits an existing user.
    SimpleRouter::get("/users/{id}/edit", function ($id) {
        guardAgainstEmployee();
        
        $user = User::select(["id" => $id])[0] ?? null;

        if (!$user) {
            http_response_code(404);
            exit;
        }

        require __DIR__ . "/templates/edit-user-form.php";
    });

    # PATCH /users/{id}: Update a user by ID.
    SimpleRouter::patch("/users/{id}", function ($id) {
        guardAgainstEmployee();

        $user = User::from(["id" => $id]);

        $exists = User::select(["id" => $id], ["id"])[0] ?? null !== null;

        if (!$exists) {
            return jsonResponse(["status" => false, "error" => "User not found"], 404);
        }

        $data = jsonInput();
        
        try {   
            $ok = $user->update($data);
        }
        catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
    
            if (isset($errorInfo[1]) && $errorInfo[1] === 1265) {
                http_response_code(400);
                
                return jsonResponse([
                    "status" => false,
                    "error" => "Invalid value for the 'type' field. Please provide a valid option."
                ]);
            }
            else throw $e;
        }

        return jsonResponse(["status" => $ok, "redirect" => '/']);
    });

    # GET /requests: Get a list of all vacation requests.
    SimpleRouter::get("/requests", function () {
        guardAgainstGuest();

        # Employees can only see their own requests.
        if (User::select(["id" => USER_ID], ["type"])[0]["type"] === "employee") {
            return jsonResponse(Request::selectWithUsers(["requested_by" => USER_ID]));
        }

        # Managers can see all requests unless filtered.
        $filter = [];
        if ($_GET["own"] ?? false) {
            $filter = ["requested_by" => USER_ID];
        }

        return jsonResponse(Request::selectWithUsers($filter));
    });

    # GET /requests/new: Displays a form that creates a new vacation request.
    SimpleRouter::get("/requests/new", function () {
        guardAgainstGuest();

        require __DIR__ . "/templates/new-request-form.php";
    });

    # GET /requests/{id}: Get a vacation request.
    SimpleRouter::get("/requests/{id}", function ($id) {
        guardAgainstGuest();

        $filter = ["id" => $id];

        # Employees can only see the request if they made it.
        if (User::select(["id" => USER_ID], ["type"])[0]["type"] === "employee") {
            $filter["requested_by"] = USER_ID;
        }

        $request = Request::selectWithUsers($filter)[0] ?? null;

        if (!$request) {
            return jsonResponse(["status" => false, "error" => "Request not found"], 404);
        }

        return jsonResponse($request);
    });

    # POST /requests: Create a new vacation request.
    SimpleRouter::post("/requests", function () {
        guardAgainstGuest();

        $data = jsonInput();

        if (empty($data["start_date"]) || empty($data["end_date"]) || empty($data["reason"])) {
            return jsonResponse(["status" => false, "error" => "Missing required fields"], 400);
        }

        if (strtotime($data["end_date"]) < strtotime($data["start_date"])) {
            return jsonResponse(["status" => false, "error" => "End date cannot be before start date"], 400);
        }

        $request = Request::from($data + ["requested_by" => USER_ID]);
        $ok = $request->create();
        return jsonResponse(["status" => $ok, "redirect" => '/'], $ok ? 201 : 400);
    });

    # PATCH /requests/{id}/approve: Approve a vacation request.
    SimpleRouter::patch("/requests/{id}/approve", function ($id) {
        guardAgainstEmployee();

        $exists = Request::select(["id" => $id], ["id"])[0] ?? null !== null;

        if (!$exists) {
            return jsonResponse(["status" => false, "error" => "Request not found"], 404);
        }

        $ok = Request::from(["id" => $id])->update(["status" => "approved", "decided_by" => USER_ID]);

        return jsonResponse(["status" => $ok, "redirect" => '/']);
    });

    # PATCH /requests/{id}/reject: Reject a vacation request.
    SimpleRouter::patch("/requests/{id}/reject", function ($id) {
        guardAgainstEmployee();

        $exists = Request::select(["id" => $id], ["id"])[0] ?? null !== null;

        if (!$exists) {
            return jsonResponse(["status" => false, "error" => "Request not found"], 404);
        }

        $ok = Request::from(["id" => $id])->update(["status" => "rejected", "decided_by" => USER_ID]);
        
        return jsonResponse(["status" => $ok, "redirect" => '/']);
    });

    # DELETE /requests/{id}: Delete a vacation request.
    SimpleRouter::delete("/requests/{id}", function ($id) {
        guardAgainstGuest();

        $request = Request::select(["id" => $id], ["id", "status"])[0] ?? null;

        if ($request === null) {
            return jsonResponse(["status" => false, "error" => "Request not found"], 404);
        }

        # Employees can only delete the request if it's pending.
        if (User::select(["id" => USER_ID], ["type"])[0]["type"] === "employee") {
            if ($request["status"] !== "pending") {
                return jsonResponse(["status" => false, "error" => "Only pending requests can be deleted"], 403);
            }
        }

        $req = Request::from(["id" => $id]);
        $ok = $req->delete();

        return jsonResponse(["status" => $ok]);
    });

    # Start the router and catch various errors to display the appropriate status.
    try {
        SimpleRouter::start();
    }
    catch (NotFoundHttpException $e) {
        http_response_code(404);
    }
    catch (InvalidArgumentException $e) {
        http_response_code(400);
        jsonResponse(["status" => "false", "error" => $e->getMessage()]);
    }
?>