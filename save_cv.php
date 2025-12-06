<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $userId = $_SESSION['user_id'];
    $job = trim($data['job'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $about = trim($data['about'] ?? '');
    $experiences = $data['experiences'] ?? [];
    $education = $data['education'] ?? [];
    $skills = $data['skills'] ?? [];
    
    try {
        $pdo->beginTransaction();
        
        // Sauvegarder/Mettre à jour les infos personnelles
        $stmt = $pdo->prepare("
            INSERT INTO personal_info (user_id, job_title, phone, about, skills, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
                job_title = VALUES(job_title),
                phone = VALUES(phone),
                about = VALUES(about),
                skills = VALUES(skills),
                updated_at = NOW()
        ");
        $stmt->execute([$userId, $job, $phone, $about, json_encode($skills)]);
        
        // Supprimer les anciennes expériences
        $stmt = $pdo->prepare("DELETE FROM experiences WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // Insérer les nouvelles expériences
        if (!empty($experiences)) {
            $stmt = $pdo->prepare("
                INSERT INTO experiences (user_id, company, position, dates, description) 
                VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($experiences as $exp) {
                $stmt->execute([
                    $userId,
                    $exp['company'] ?? '',
                    $exp['position'] ?? '',
                    $exp['dates'] ?? '',
                    $exp['description'] ?? ''
                ]);
            }
        }
        
        // Supprimer les anciennes formations
        $stmt = $pdo->prepare("DELETE FROM education WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // Insérer les nouvelles formations
        if (!empty($education)) {
            $stmt = $pdo->prepare("
                INSERT INTO education (user_id, school, degree, dates) 
                VALUES (?, ?, ?, ?)
            ");
            foreach ($education as $edu) {
                $stmt->execute([
                    $userId,
                    $edu['school'] ?? '',
                    $edu['degree'] ?? '',
                    $edu['dates'] ?? ''
                ]);
            }
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'CV sauvegardé avec succès!']);
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()]);
    }
}
?>