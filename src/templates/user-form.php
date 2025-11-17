<?php
    $user = $user ?? [];
?>
<div class="form-group">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" required value="<?= $user["name"] ?? "" ?>"/>
</div>
<div class="form-group">
    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" required value="<?= $user["email"] ?? "" ?>"/>
</div>
<div class="form-group">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required/>
</div>
<div class="form-group">
    <label for="employee_code">Employee Code</label>
    <input type="text" id="employee_code" name="employee_code" required value="<?= $user["employee_code"] ?? "" ?>"/>
</div>
<div class="form-group">
    <label for="type">Type</label>
    <select id="type" name="type" required>
        <option value="employee" <?= ($user["type"] ?? null) == "employee" ? "selected" : "" ?>>Employee</option>
        <option value="manager" <?= ($user["type"] ?? null) == "manager" ? "selected" : "" ?>>Manager</option>
    </select>
</div>
<button type="submit" class="submit-btn">Submit</button>