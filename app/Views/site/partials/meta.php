<?php
    $pageTitle = trim($this->renderSection('title')) ?: 'Lincotic - Turn link chaos into order';
    $pageDescription = 'A clean, lightweight bookmark manager for organizing, searching, and accessing your favorite links.';
    $pageUrl = current_url();
    $shareImageUrl = base_url('assets/images/share.jpg');
    $shareImageAlt = 'Lincotic logo and the words Links Chaotic Lincotic in a mountain landscape.';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="dark">
<meta name="base-url" content="<?= esc(base_url()) ?>">
<meta name="theme-color" content="#1d232a">
<meta name="description" content="<?= esc($pageDescription) ?>">

<title><?= esc($pageTitle) ?></title>

<link rel="canonical" href="<?= esc($pageUrl) ?>">

<meta property="og:type" content="website">
<meta property="og:site_name" content="Lincotic">
<meta property="og:title" content="<?= esc($pageTitle) ?>">
<meta property="og:description" content="<?= esc($pageDescription) ?>">
<meta property="og:url" content="<?= esc($pageUrl) ?>">
<meta property="og:image" content="<?= esc($shareImageUrl) ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1731">
<meta property="og:image:height" content="909">
<meta property="og:image:alt" content="<?= esc($shareImageAlt) ?>">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= esc($pageTitle) ?>">
<meta name="twitter:description" content="<?= esc($pageDescription) ?>">
<meta name="twitter:image" content="<?= esc($shareImageUrl) ?>">
<meta name="twitter:image:alt" content="<?= esc($shareImageAlt) ?>">
