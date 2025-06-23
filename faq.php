<?php
require_once 'config.php';
$faq_list = $pdo->query("SELECT * FROM faq ORDER BY id_faq ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pusat Bantuan (FAQ)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <style>
        .hero {
            background-color: #f8f9fa;
            padding: 4rem 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #0d6efd;
        }
        body {
            background-color: #f8f9fa;
        }
        .faq-header {
            margin-top: 3rem;
            margin-bottom: 2rem;
            font-weight: 700;
            color: #0d6efd;
        }
        .accordion-button {
            font-weight: 600;
            color: #0d6efd;
        }
        .accordion-body {
            background-color: #ffffff;
            color: #212529;
        }
        .no-faq {
            font-style: italic;
            color: #6c757d;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container mb-5">
    <h1 class="text-center faq-header">Pusat Bantuan</h1>
    <p class="text-center text-muted mb-5">Temukan jawaban untuk pertanyaan yang sering diajukan di sini.</p>

    <?php if (!empty($faq_list)): ?>
    <div class="accordion" id="accordionFAQ">
        <?php foreach ($faq_list as $index => $faq): ?>
        <div class="accordion-item mb-3 shadow-sm rounded">
            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                    <?php echo htmlspecialchars($faq['pertanyaan']); ?>
                </button>
            </h2>
            <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#accordionFAQ">
                <div class="accordion-body">
                    <?php echo nl2br(htmlspecialchars($faq['jawaban'])); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p class="text-center no-faq">Belum ada pertanyaan yang ditambahkan.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
