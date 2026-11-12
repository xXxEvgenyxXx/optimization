<h1>Все доступные материалы</h1>

<?php foreach ($materials as $material): ?>

    <div>
        <h3>
            <?= htmlspecialchars($material['name']) ?>
        </h3>
        <p>
            <?= htmlspecialchars($material['name']) ?>
            - <a href="/materials/<?= $material['id'] ?>">подробнее о материале</a>
        </p>
    </div>

<?php endforeach; ?>