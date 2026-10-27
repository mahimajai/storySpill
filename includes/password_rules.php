<?php
/**
 * Google-style password strength rules.
 * Returns array of error messages (empty = valid).
 */
function validate_password_strength(string $password): array {
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must include at least 1 uppercase letter (A–Z).";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must include at least 1 lowercase letter (a–z).";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must include at least 1 number (0–9).";
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must include at least 1 special character (!@#$%^&* etc.).";
    }
    return $errors;
}

function password_requirements_html(): string {
    return '<ul class="password-reqs" style="margin:8px 0 0; padding-left:18px; color:#888; font-size:0.75rem; line-height:1.5;">
      <li>At least 8 characters</li>
      <li>1 uppercase letter (A–Z)</li>
      <li>1 lowercase letter (a–z)</li>
      <li>1 number (0–9)</li>
      <li>1 special character (!@#$%^&*…)</li>
    </ul>';
}