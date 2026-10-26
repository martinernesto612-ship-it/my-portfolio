<?php
$d = require __DIR__ . '/data.php';
$e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$mono = fn($t) => strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(preg_split('/\s+/', trim($t)), 0, 2))));
$words = explode(' ', $d['name'], 2);

$status = ''; $errors = []; $old = ['name' => '', 'email' => '', 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($old as $k => $_) $old[$k] = trim((string)($_POST[$k] ?? ''));
  if (!empty($_POST['website'])) exit; // honeypot
  if ($old['name'] === '') $errors[] = 'Enter your name.';
  if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
  if (mb_strlen($old['message']) < 10) $errors[] = 'Write a message of at least 10 characters.';
  if (!$errors) {
    $subject = 'Portfolio message from ' . str_replace(["\r", "\n"], ' ', $old['name']);
    $headers = 'From: ' . $d['email'] . "\r\nReply-To: " . str_replace(["\r", "\n"], '', $old['email']);
    $status = @mail($d['email'], $subject, $old['message'], $headers) ? 'sent' : 'failed';
    if ($status === 'sent') $old = ['name' => '', 'email' => '', 'message' => ''];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $e($d['name']) ?> – Portfolio</title>
  <meta name="description" content="<?= $e($d['tagline']) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="hero" id="home">
  <div class="wrap bar">
    <a class="logo" href="#home" aria-label="Home">
      <i class="disc"></i><span><?= $e($d['initials']) ?> · <?= $e($d['course']) ?></span>
    </a>
    <nav><a href="#about">About</a><a href="#projects">Projects</a><a href="#contact">Contact</a></nav>
    <a class="ghost" href="#contact">Hire me</a>
  </div>

  <div class="wrap hero-body">
    <div class="hero-text">
      <h1>
        <span class="s">Portfolio of</span>
        <span class="b"><?= $e($words[0]) ?></span>
        <span class="b"><?= $e($words[1] ?? '') ?></span>
      </h1>
      <p><?= $e($d['tagline']) ?></p>
      <a class="pill big" href="#projects">See my projects</a>
    </div>
    <div class="neon" aria-hidden="true"><b></b><b></b><b></b><i></i></div>
  </div>
</header>

<section class="dark" id="about">
  <div class="wrap two">
    <div class="stack"><div class="shot"><span><?= $e($d['initials']) ?></span></div></div>
    <div>
      <p class="eyebrow">About me</p>
      <h2>Who I am</h2>
      <p class="body"><?= $e($d['about']) ?></p>
      <ul class="skills"><?php foreach ($d['skills'] as $s): ?><li><?= $e($s) ?></li><?php endforeach; ?></ul>
      <a class="pill" href="#contact">Know more</a>
    </div>
  </div>
</section>

<section class="steps">
  <div class="strings" aria-hidden="true"></div>
  <div class="wrap">
    <p class="eyebrow">How I work</p>
    <h2>My process</h2>
    <ol>
      <?php foreach ($d['steps'] as $i => $s): ?>
        <li><span class="ring"><?= $i + 1 ?></span><span><?= $e($s) ?></span></li>
      <?php endforeach; ?>
    </ol>
    <a class="pill" href="#projects">View details</a>
  </div>
</section>

<section class="dark" id="projects">
  <div class="wrap">
    <p class="eyebrow">Selected work</p>
    <h2>Projects</h2>
    <div class="cards">
      <?php foreach ($d['projects'] as $p): ?>
        <a class="card" href="<?= $e($p['url']) ?>">
          <div class="thumb"><span><?= $e($mono($p['title'])) ?></span></div>
          <h3><?= $e($p['title']) ?></h3>
          <p><?= $e($p['text']) ?></p>
          <span class="tag"><?= $e($p['tag']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="contact" id="contact">
  <div class="wrap two">
    <div>
      <p class="eyebrow">Contact</p>
      <h2>Get in touch</h2>
      <p class="body">Have a project or an opportunity in mind? Send me a message and I’ll reply as soon as I can.</p>
      <p><a class="mail" href="mailto:<?= $e($d['email']) ?>"><?= $e($d['email']) ?></a></p>
      <p class="links"><?php foreach ($d['links'] as $l => $u): ?><a href="<?= $e($u) ?>" rel="noopener"><?= $e($l) ?></a><?php endforeach; ?></p>
    </div>
    <div>
      <?php if ($status === 'sent'): ?><p class="note ok" role="status">Message sent. Thank you, I’ll reply soon.</p>
      <?php elseif ($status === 'failed'): ?><p class="note bad" role="alert">The message couldn’t be sent. Email <?= $e($d['email']) ?> instead.</p><?php endif; ?>
      <?php if ($errors): ?><div class="note bad" role="alert"><?php foreach ($errors as $err): ?><p><?= $e($err) ?></p><?php endforeach; ?></div><?php endif; ?>
      <form method="post" action="#contact">
        <label>Name <input name="name" value="<?= $e($old['name']) ?>" required></label>
        <label>Email <input type="email" name="email" value="<?= $e($old['email']) ?>" required></label>
        <label>Message <textarea name="message" rows="5" required><?= $e($old['message']) ?></textarea></label>
        <input class="hp" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
        <button class="pill" type="submit">Send message</button>
      </form>
    </div>
  </div>
</section>

<footer>© <?= date('Y') ?> <?= $e($d['name']) ?> · <?= $e($d['course']) ?></footer>
</body>
</html>
