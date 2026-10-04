<?php
require_once 'db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['ciphertext']) || !isset($data['iv'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Eksik veri gönderildi']);
        exit;
    }

    $id = bin2hex(random_bytes(16)); 

    try {
        $stmt = $pdo->prepare("INSERT INTO secrets (id, ciphertext, iv) VALUES (?, ?, ?)");
        $stmt->execute([$id, $data['ciphertext'], $data['iv']]);
        
        echo json_encode(['success' => true, 'id' => $id]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Veri kaydedilemedi']);
    }
    exit;
}

if ($method === 'GET') {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID parametresi eksik']);
        exit;
    }

    $id = $_GET['id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT ciphertext, iv FROM secrets WHERE id = ? FOR UPDATE");
        $stmt->execute([$id]);
        $secret = $stmt->fetch();

        if (!$secret) {
            $pdo->rollBack();
            http_response_code(410); 
            echo json_encode(['error' => 'Bu sır zaten okundu veya hiç var olmadı.']);
            exit;
        }

        $deleteStmt = $pdo->prepare("DELETE FROM secrets WHERE id = ?");
        $deleteStmt->execute([$id]);

        $pdo->commit();

        echo json_encode($secret);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(500);
        echo json_encode(['error' => 'Sunucu hatası oluştu']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Geçersiz metod']);