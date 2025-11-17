# Vacation Management API

The **Vacation Management API** is a PHP-based application designed to manage employee vacation requests. It provides a simple interface for employees to submit vacation requests and for administrators to manage them. The application uses **MariaDB** as its database and leverages **HTMX** for dynamic interactions.

---

## Features

- **Employee Dashboard**: View and manage vacation requests.
- **Vacation Request Submission**: Employees can submit vacation requests with start and end dates and a reason.
- **Dynamic Interactions**: Uses HTMX for seamless, dynamic updates without full page reloads.
- **CRUD Operations**: Create, read, update, and delete vacation requests.
- **Secure Password Handling**: Passwords are hashed before storage.
- **Routing**: Utilizes the `pecee/simple-router` library for clean and efficient routing.

---

## Prerequisites

Before running the project, ensure you have the following installed:

- **PHP 8.0+**
- **MariaDB or MySQL**
- **Composer** (PHP dependency manager)
- **A web server** (e.g., Apache, Nginx, or PHP's built-in server)

---

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/angelpolitis/vacation-management-api.git
   cd vacation-management-api
   ```

2. Initialise the project:

    1. If you're using Docker, build the project using:

        ```bash
        docker-composer up -d --build
        ```

    2. If you're not using Docker, you'll need to install composer via the command:

        ```bash
        vmapi composer:install
        ```

3. Install the necessary dependencies using Composer:

   ```bash
   composer install
   ```

4. Set up the database using:

    1. For Docker:

        ```bash
        docker-vmapi db:init
        ```
     
    2. For localhost:

        ```bash
        vmapi db:init
        ```

5. Access the application in your browser at [http://localhost:8080](http://localhost:8080) and the database via PHPMyAdmin at [http://localhost:8081](http://localhost:8081) (for Docker users).

---

## Usage

### Employee Dashboard

- Employees can log in to view their vacation requests.
- Submit a new vacation request by clicking the "New Request" button and filling out the form.

### Admin Panel

- Managers can view all vacation requests and approve or reject them.
- Managers can view all users and create new ones.

---

## Commands

### Install Composer Dependencies

To install the required dependencies, run:

```bash
composer install
```

### Command-line usage

The project can be used via the command line as well:

1. Create a user:

    ```bash
    vmapi user:create \
    --name="John Doe" \
    --email="john@doe.org" \
    --password="••••••••" \
    --employee_code=1147685 \
    --type="employee"
    ```

1. Update a user:

    ```bash
    vmapi user:update \
    --name="Joe Doe" \
    --email="joe@doe.org" \
    --password="••••••••" \
    --employee_code=1147685 \
    --type="manager"
    --filter="id=1"
    ```

    The `filter` option takes a value formed similarly to an HTTP query.

1. Delete a user:

    ```bash
    vmapi user:delete --id=1
    ```

    ```bash
    vmapi user:delete --employee_code=1147685
    ```

1. Fetch all users, or filter the result set by setting any of the fields:

    ```bash
    vmapi users:fetch --type="employee"
    ```

1. Create a request:

    ```bash
    vmapi request:create \
    --start_date="2025-11-17"
    --end_date="2025-11-22"
    --reason="I want to go on a trip abroad."
    --user=1
    ```

1. Update a request:

    ```bash
    vmapi request:update \
    --reason="I want to go on a binging spree."
    --filter="id=1"
    ```

1. Delete a request:

    ```bash
    vmapi request:delete --id=1
    ```

1. Approve a request:

    ```bash
    vmapi request:approve --id=11 --user=21
    ```

    The `user` must be a manager.

1. Reject a request:

    ```bash
    vmapi request:reject --id=11 --user=21
    ```

    The `user` must be a manager.

1. Fetch all requests, or filter the result set by setting any of the fields:

    ```bash
    vmapi requests:fetch
    --submission_date="2025-11-17"
    --requested_by=2
    ```

    `requested_by` must be the ID of a user.

When used via the command line, no authentication is required.

### Routing Library

This project uses the `pecee/simple-router` library for routing. Install it with:

```bash
composer require pecee/simple-router
```

---

## File Structure

- **`src/`**: Contains the core application code.
  - **`commands/`**: Contains the command definitions.
  - **`database/`**: Contains the database and table schemata.
  - **`models/`**: Contains the models used.
  - **`modules/`**: Contains the framework's classes.
  - **`public/`**: Contains all publicly accessible files.
  - **`registry/`**: Contains the file where the commands are registered.
  - **`templates/`**: Contains the PHP templates for the application.
- **`vendor/`**: Contains external dependencies.

---

## Technologies Used

- **PHP**: Backend logic and API handling.
- **MariaDB**: Database for storing vacation requests and user data.
- **HTMX**: For AJAX submitted forms without page reloading.
- **Composer**: Dependency management.
- **pecee/simple-router**: Routing library for clean URL handling.

---

## Licence

This project is intended for **educational purposes only** and is **not meant for commercial use**.
