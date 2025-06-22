<?php
require_once 'config.php';
$faq_list = $pdo->query("SELECT * FROM faq ORDER BY id_faq ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Bantuan (FAQ)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Pusat Bantuan</h1>
        <p class="text-center text-muted mb-5">Temukan jawaban untuk pertanyaan yang sering diajukan di sini.</p>

        <div class="accordion" id="accordionFAQ">
            <?php foreach ($faq_list as $index => $faq): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>">
                        <?php echo htmlspecialchars($faq['pertanyaan']); ?>
                    </button>
                </h2>
                <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body">
                        <?php echo nl2br(htmlspecialchars($faq['jawaban'])); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
             <?php if (empty($faq_list)): ?>
                <p class="text-center">Belum ada pertanyaan yang ditambahkan.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>