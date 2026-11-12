<h1>Все доступные изделия</h1>

<?php foreach ($products as $product): ?>

    <div>
        <h3>
            <?= htmlspecialchars($product['name']) ?>
        </h3>
        <p>
            <?= htmlspecialchars($product['name']) ?>
            - <a href="/products/<?= $product['id'] ?>">подробнее об изделии</a>
        </p>
    </div>

<?php endforeach; ?>