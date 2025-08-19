<?php
require "config.php";

// --- Inputs ---
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$perPage = 5;
$offset  = ($page - 1) * $perPage;

// --- Count total rows ---
$countSql = "SELECT COUNT(*) FROM posts";
$dataSql  = "SELECT * FROM posts";
if ($search !== '') {
    $countSql .= " WHERE title LIKE :search OR content LIKE :search";
    $dataSql  .= " WHERE title LIKE :search OR content LIKE :search";
}
$stmt = $pdo->prepare($countSql);
if ($search !== '') $stmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
$stmt->execute();
$total = (int)$stmt->fetchColumn();
$totalPages = ($total > 0) ? ceil($total / $perPage) : 1;

// --- Fetch posts ---
$dataSql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($dataSql);
if ($search !== '') $stmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
$stmt->bindValue(":limit", $perPage, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <title>Blog - Search & Pagination</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
<div class="container">
  <h1 class="mb-3">Posts</h1>

  <!-- Search -->
  <form method="get" class="mb-3">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search..." class="form-control" />
    <button class="btn btn-primary mt-2">Search</button>
  </form>

  <!-- Posts -->
  <?php if (!$posts): ?>
    <p>No posts found.</p>
  <?php else: ?>
    <?php foreach ($posts as $p): ?>
      <div class="card mb-2">
        <div class="card-body">
          <h5><?= htmlspecialchars($p['title']) ?></h5>
          <p><?= htmlspecialchars(substr($p['content'], 0, 150)) ?>...</p>
          <small class="text-muted"><?= $p['created_at'] ?></small>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- Pagination -->
  <nav>
    <ul class="pagination">
      <li class="page-item <?= ($page<=1)?'disabled':'' ?>">
        <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $page-1 ?>">Previous</a>
      </li>
      <?php for ($i=1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i==$page)?'active':'' ?>">
          <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($page>=$totalPages)?'disabled':'' ?>">
        <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $page+1 ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>
</body>
</html>