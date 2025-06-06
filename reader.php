<?php
$pdf = isset($_GET['pdf']) ? $_GET['pdf'] : null;
$relativePath = 'uploads/' . basename($pdf); // secure and correct path
$fullPath = __DIR__ . '/' . $relativePath;
$webPath = $relativePath;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>📖 Book Reader</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f3e6d8;
      color: #5c4400;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }

    header {
      background-color: #b3a78c;
      color: white;
      height: 60px;
      position: relative;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    header h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 700;
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      user-select: none;
    }

        header button {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(255, 255, 255, 0.15); /* light translucent background */
        border: 1px solid rgba(255, 255, 255, 0.4);  /* soft border */
        color: white;
        font-size: 1.6rem;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.25s ease;
        font-weight: bold;
      }

      header button:hover {
        background-color: rgba(255, 255, 255, 0.25);
        transform: translateY(-50%) scale(1.05);
      }


    section#content {
      flex-grow: 1;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    iframe {
      width: 100%;
      max-width: 900px;
      height: 85vh;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(179, 167, 140, 0.4);
      border: none;
      background-color: white;
    }

    .message {
      font-size: 1.2rem;
      background: #d5bf8f;
      color: #5c4400;
      padding: 18px 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(179, 167, 140, 0.5);
      user-select: none;
      text-align: center;
      max-width: 400px;
    }
  </style>
</head>
<body>

<header>
  <button onclick="goBack()" aria-label="Go back">⬅</button>
  <h1>📖 Book Reader</h1>
</header>

<section id="content">
  <?php if ($pdf && file_exists($fullPath)): ?>
    <iframe src="<?= htmlspecialchars($webPath) ?>"></iframe>
  <?php else: ?>
    <div class="message">❌ No PDF found or file does not exist!</div>
  <?php endif; ?>
</section>

<script>
  function goBack() {
    window.history.back();
  }
</script>

</body>
</html>
