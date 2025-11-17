<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Vacation Management API</title>
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .login-form {
                background: #ffffff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 400px;
            }
            .login-form h2 {
                margin-bottom: 35px;
                color: #333;
                text-align: center;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                color: #555;
            }
            .form-group input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 14px;
            }
            .form-group input:focus {
                border-color: #007bff;
                outline: none;
            }
            .submit-btn {
                width: 100%;
                padding: 10px;
                background-color: #007bff;
                color: #fff;
                border: none;
                border-radius: 4px;
                font-size: 16px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .submit-btn:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <form class="login-form" action="/login" method="POST">
            <h2>Vacation Management API</h2>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="submit-btn">Login</button>

            <?php
                if (isset($_GET["error"]) && $_GET["error"] === "invalid_credentials") {
                    echo '<p style="color: red; text-align: center; margin-top: 15px;">Invalid email or password. Please try again.</p>';
                }
                elseif (isset($_GET["error"]) && $_GET["error"] === "missing_credentials") {
                    echo '<p style="color: red; text-align: center; margin-top: 15px;">"Email and password are required. Please try again.</p>';
                }
            ?>
        </form>
    </body>
</html>