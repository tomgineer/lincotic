<!DOCTYPE html>
<html lang="<?= esc(config('App')->defaultLocale, 'attr') ?>" data-theme="dark">
<head>
    <?= $this->include('site/partials/meta') ?>

    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/favicon/favicon.svg') ?>?v=<?= APP_VERSION ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/favicon/favicon-32.png') ?>?v=<?= APP_VERSION ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/favicon/favicon.ico') ?>?v=<?= APP_VERSION ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/favicon/apple-touch-icon.png') ?>?v=<?= APP_VERSION ?>">

    <?= $this->renderSection('headScripts') ?>
    <link rel="stylesheet" href="<?= base_url('css/app-dist.css') ?>?v=<?= APP_VERSION ?>">
    <?= $this->renderSection('styles') ?>
</head>

<body class="flex min-h-screen flex-col" id="<?= esc(body_id()) ?>">
    <?= $this->renderSection('header') ?>

    <div class="flex-1">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->renderSection('vendorScripts') ?>
    <script src="<?= base_url('js/app-dist.js') ?>?v=<?= APP_VERSION ?>" defer></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>
