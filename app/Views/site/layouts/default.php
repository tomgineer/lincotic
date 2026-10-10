<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <meta name="base-url" content="<?= esc(base_url()) ?>">
    <meta name="theme-color" content="#111827">

    <title>Lincotic</title>

    <link rel="stylesheet" href="<?= base_url('css/app-dist.css') ?>?v=<?= APP_VERSION ?>">
    <?= $this->renderSection('styles') ?>
</head>

<body class="flex min-h-screen flex-col" id="<?= esc(body_id()) ?>">
    <header>

    </header>

    <div class="flex-1">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="<?= base_url('js/app-dist.js') ?>?v=<?= APP_VERSION ?>" defer></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>
