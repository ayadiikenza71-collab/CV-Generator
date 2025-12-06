<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_SESSION['user_id'];
    
    try {
        // Récupérer les infos personnelles
        $stmt = $pdo->prepare("SELECT * FROM personal_info WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $personalInfo = $stmt->fetch();
        
        // Récupérer les expériences
        $stmt = $pdo->prepare("SELECT * FROM experiences WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        $experiences = $stmt->fetchAll();
        
        // Récupérer la formation
        $stmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        $education = $stmt->fetchAll();
        
        $cvData = [
            'job' => $personalInfo['job_title'] ?? '',
            'phone' => $personalInfo['phone'] ?? '',
            'about' => $personalInfo['about'] ?? '',
            'skills' => $personalInfo ? json_decode($personalInfo['skills'], true) : [],
            'experiences' => $experiences,
            'education' => $education
        ];
        
        echo json_encode(['success' => true, 'data' => $cvData]);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération: ' . $e->getMessage()]);
    }
}
?>