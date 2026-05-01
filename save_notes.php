<?php
/**
 * save_notes.php
 * ─────────────────────────────────────────────────────────────────
 * AJAX endpoint — saves notepad content for the logged-in student.
 *
 * Accepts: POST  notes_content (string)  csrf_token (string)
 * Returns: JSON  { success: bool, message: string }
 *
 * Called by: notepad auto-save (JS Fetch API, 2-second debounce)
 * ─────────────────────────────────────────────────────────────────
 */

/* ── Always respond as JSON ── */
header('Content-Type: application/json; charset=utf-8');

/* ── No caching for this endpoint ── */
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/includes/auth.php';   // session_start + CSRF helpers
require_once __DIR__ . '/classes/User.php';    // User::saveNotes()

/* ── Helper: send JSON and exit ── */
function respond(bool $ok, string $msg = '', int $code = 200): never {
    http_response_code($code);
    echo json_encode(['success' => $ok, 'message' => $msg]);
    exit;
}

/* ── Only POST allowed ── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Method not allowed.', 405);
}

/* ── Must be a logged-in student ── */
if (!isLoggedIn()) {
    /*
     * Return success:false (not 401) so the JS can fall back
     * gracefully to localStorage without alarming the user.
     */
    respond(false, 'unauthenticated');
}

/* ── CSRF check ── */
if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
    respond(false, 'Security token invalid. Please refresh the page.', 403);
}

/* ── Sanitise & validate input ── */
$content = $_POST['notes_content'] ?? '';

// Hard limit: 64 KB per note is generous enough
if (mb_strlen($content, 'UTF-8') > 65536) {
    respond(false, 'Note content is too large (max 64 KB).', 413);
}

// Strip null bytes (can break some DB drivers)
$content = str_replace("\0", '', $content);

/* ── Persist via User class ── */
try {
    $userObj = new User();
    /*
     * User::saveNotes(int $userId, string $content)
     * Uses INSERT … ON DUPLICATE KEY UPDATE so it works for
     * both first-time saves and subsequent updates.
     */
    $userObj->saveNotes((int) $_SESSION['user_id'], $content);
    respond(true, 'Notes saved.');

} catch (PDOException $e) {
    // Log internally; never expose DB errors to the client
    error_log('[QuizMaster] save_notes PDO error: ' . $e->getMessage());
    respond(false, 'A database error occurred. Please try again.', 500);

} catch (Throwable $e) {
    error_log('[QuizMaster] save_notes error: ' . $e->getMessage());
    respond(false, 'An unexpected error occurred.', 500);
}
