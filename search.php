<?php
// search.php - receives ?q= and returns matching students as JSON
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

$q = '';
if (isset($_GET['q'])) {
    $q = trim($_GET['q']);
}

if ($q === '') {
    echo json_encode([]);
    exit;
}

try {
    if (class_exists('mysqli')) {
        $mysqli = get_db();

        // Prepare statement and bind the same term to multiple placeholders
        $sql = "SELECT id, name, roll_no, branch, email, subjects FROM students WHERE 
            name LIKE ? OR roll_no LIKE ? OR branch LIKE ? OR email LIKE ? 
            LIMIT 50";

        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $mysqli->error);
        }

        $term = '%' . $q . '%';
        // bind_param requires variables, repeat $term for each placeholder
        $stmt->bind_param('ssss', $term, $term, $term, $term);
        $stmt->execute();

        $res = $stmt->get_result();
        if ($res === false) {
            throw new Exception('Get result failed: ' . $stmt->error);
        }

        $rows = $res->fetch_all(MYSQLI_ASSOC);

        echo json_encode($rows);
    } else {
        // Fallback: use the mysql CLI if mysqli/PDO extensions are not available in this PHP build.
        // This is a pragmatic fallback for environments where PHP lacks the MySQL driver.
        require_once __DIR__ . '/db.php'; // provides DB_* constants

        // Escape single quotes for the SQL string
        $esc = str_replace("'", "\\'", $q);
        $sql = "SELECT id, name, roll_no, branch, email, subjects FROM students WHERE ";
        $sql .= "name LIKE '%" . $esc . "%' OR roll_no LIKE '%" . $esc . "%' OR branch LIKE '%" . $esc . "%' OR email LIKE '%" . $esc . "%' LIMIT 50";

        // Build mysql CLI command. Avoid exposing password as separate argv when empty.
        $cmd = '';
        // In some environments root uses socket auth; allow using sudo when DB_USER is root and no password is set.
        if (DB_USER === 'root' && DB_PASS === '') {
            // when running as root via sudo, connect using the local socket (no -h) to allow socket auth
            $cmd .= 'sudo mysql -D ' . escapeshellarg(DB_NAME) . ' -B -N -e ' . escapeshellarg($sql);
        } else {
            if (DB_USER === 'root' && DB_HOST === '127.0.0.1') {
                // avoid forcing TCP for localhost root; allow default mysql client behavior
                $hostPart = '';
            } else {
                $hostPart = ' -h ' . escapeshellarg(DB_HOST);
            }
            $cmd .= 'mysql' . $hostPart . ' -u ' . escapeshellarg(DB_USER);
            if (DB_PASS !== '') {
                // pass password inline (note: for local dev only)
                $cmd .= ' -p' . escapeshellcmd(DB_PASS);
            }
            $cmd .= ' -D ' . escapeshellarg(DB_NAME) . ' -B -N -e ' . escapeshellarg($sql);
        }

    // Log the command for debugging in server logs (no sensitive password should be present by default)
    error_log("search.php running shell command: " . $cmd);
    $out = shell_exec($cmd);
        if ($out === null) {
            throw new Exception('Failed to execute mysql CLI command.');
        }

        $lines = array_filter(array_map('trim', explode("\n", $out)));
        $rows = [];
        foreach ($lines as $line) {
            // mysql -B outputs tab-separated values
            $cols = explode("\t", $line);
            $rows[] = [
                'id' => $cols[0] ?? null,
                'name' => $cols[1] ?? null,
                'roll_no' => $cols[2] ?? null,
                'branch' => $cols[3] ?? null,
                'email' => $cols[4] ?? null,
                'subjects' => $cols[5] ?? null,
            ];
        }

        echo json_encode($rows);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
