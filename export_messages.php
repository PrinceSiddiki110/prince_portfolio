<?php
require __DIR__ . '/db.php';
require_admin();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=messages.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID','From','Subject','Message','Sent At','Read']);
foreach ($pdo->query("SELECT * FROM messages ORDER BY sent_at DESC") as $row) {
  fputcsv($out, [$row['id'],$row['from_email'],$row['subject'],$row['message'],$row['sent_at'],$row['is_read']]);
}
fclose($out);
exit;
