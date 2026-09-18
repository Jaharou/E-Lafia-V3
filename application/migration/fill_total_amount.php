<?php
/**
 * Script de migration PHP — Remplissage de total_amount
 * Compatible PHP 7.4 / CodeIgniter 3
 *
 * À exécuter UNE SEULE FOIS en ligne de commande :
 *   php fill_total_amount.php
 *
 * Ou via le navigateur avec protection IP si nécessaire.
 *
 * Ce script est utile pour MySQL 5.7 qui ne supporte pas JSON_TABLE().
 * Il lit chaque ligne, parse le JSON en PHP, calcule le total et met à jour la base.
 */

// Charger l'environnement CI3 si disponible, sinon connexion directe
define('BATCH_SIZE', 500); // Lignes traitées par batch pour éviter les time-outs

// --- Connexion directe (adapter les paramètres) ---
$host = 'localhost';
$db   = 'gamadadi';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

echo "=== Migration total_amount ===\n\n";

// -----------------------------------------------------------------------
// 1. Factures — invoice_entries[*].net_amount
// -----------------------------------------------------------------------
echo "--- Traitement table invoice ---\n";

$offset = 0;
$total_invoice = 0;

do {
    $stmt = $pdo->prepare(
        "SELECT invoice_id, invoice_entries FROM invoice
         WHERE total_amount IS NULL
           AND invoice_entries IS NOT NULL
           AND invoice_entries != ''
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':limit',  BATCH_SIZE, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    if (empty($rows)) {
        break;
    }

    $pdo->beginTransaction();
    $update = $pdo->prepare(
        "UPDATE invoice SET total_amount = :total WHERE invoice_id = :id"
    );

    foreach ($rows as $row) {
        $entries = json_decode($row['invoice_entries'], true);
        if (!is_array($entries)) {
            continue;
        }

        $total = 0;
        foreach ($entries as $entry) {
            $net = isset($entry['net_amount']) ? (float) $entry['net_amount'] : 0;
            $total += $net;
        }

        $update->execute([
            ':total' => round($total, 2),
            ':id'    => $row['invoice_id'],
        ]);
        $total_invoice++;
    }

    $pdo->commit();
    $offset += BATCH_SIZE;
    echo "  Factures traitées : {$total_invoice}\r";

} while (count($rows) === BATCH_SIZE);

echo "\n  Total factures migrées : {$total_invoice}\n\n";

// -----------------------------------------------------------------------
// 2. Examens — examen_entries[*].amountT
// -----------------------------------------------------------------------
echo "--- Traitement table examen ---\n";

$offset = 0;
$total_examen = 0;

do {
    $stmt = $pdo->prepare(
        "SELECT id_examen, examen_entries FROM examen
         WHERE total_amount IS NULL
           AND examen_entries IS NOT NULL
           AND examen_entries != ''
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':limit',  BATCH_SIZE, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    if (empty($rows)) {
        break;
    }

    $pdo->beginTransaction();
    $update = $pdo->prepare(
        "UPDATE examen SET total_amount = :total WHERE id_examen = :id"
    );

    foreach ($rows as $row) {
        $entries = json_decode($row['examen_entries'], true);
        if (!is_array($entries)) {
            continue;
        }

        $total = 0;
        foreach ($entries as $entry) {
            $amt = isset($entry['amountT']) ? (float) $entry['amountT'] : 0;
            $total += $amt;
        }

        $update->execute([
            ':total' => round($total, 2),
            ':id'    => $row['id_examen'],
        ]);
        $total_examen++;
    }

    $pdo->commit();
    $offset += BATCH_SIZE;
    echo "  Examens traités : {$total_examen}\r";

} while (count($rows) === BATCH_SIZE);

echo "\n  Total examens migrés : {$total_examen}\n\n";
echo "=== Migration terminée ===\n";
