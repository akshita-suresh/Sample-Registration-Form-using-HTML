<?php
// ---------- Server-side handling (PHP) ----------
$errors = [];
$data = ["name"=>"","email"=>"","phone"=>"","dob"=>"","gender"=>"","course"=>"","notes"=>""];
$submitted = false;

function clean($v) { return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8'); }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($data as $key => $_) {
        $data[$key] = clean($_POST[$key] ?? "");
    }
    if ($data["name"] === "")                                   $errors[] = "Full name is required.";
    if (!filter_var($_POST["email"] ?? "", FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email address.";
    if (!preg_match('/^[0-9]{10}$/', $_POST["phone"] ?? ""))    $errors[] = "Phone number must be 10 digits.";
    if ($data["course"] === "")                                 $errors[] = "Please select a course.";

    if (!$errors) {
        $submitted = true;
        // Save every application to a text file (kept outside the public web folder)
        $file  = sys_get_temp_dir() . "/applications.txt";
        $entry = date("Y-m-d H:i:s") . " | " . implode(" | ", $data) . "\n";
        file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Application Form</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script defer src="script.js"></script>
</head>
<body>
<main class="wrap">
  <h1>Online Application Form</h1>
  <p class="sub">Fill in your details and press submit. Fields marked * are required.</p>

  <?php if ($errors): ?>
    <div class="alert error" role="alert">
      <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
    </div>
  <?php endif; ?>

  <?php if ($submitted): ?>
    <div class="alert success" role="status">Application submitted successfully!</div>
    <section class="result" id="result">
      <h2>Your submitted details</h2>
      <dl>
        <dt>Name</dt><dd><?= $data["name"] ?></dd>
        <dt>Email</dt><dd><?= $data["email"] ?></dd>
        <dt>Phone</dt><dd><?= $data["phone"] ?></dd>
        <dt>Date of birth</dt><dd><?= $data["dob"] ?: "-" ?></dd>
        <dt>Gender</dt><dd><?= $data["gender"] ?: "-" ?></dd>
        <dt>Course</dt><dd><?= $data["course"] ?></dd>
        <dt>Notes</dt><dd><?= $data["notes"] ?: "-" ?></dd>
      </dl>
      <button type="button" class="btn alt" id="printBtn">Print</button>
      <a class="btn alt" href="index.php">New application</a>
    </section>
  <?php else: ?>
  <form method="POST" id="appForm" novalidate>
    <label for="name">Full name *</label>
    <input type="text" id="name" name="name" value="<?= $data["name"] ?>" required>
    <span class="err" data-for="name"></span>

    <label for="email">Email address *</label>
    <input type="email" id="email" name="email" value="<?= $data["email"] ?>" required>
    <span class="err" data-for="email"></span>

    <label for="phone">Phone number *</label>
    <input type="tel" id="phone" name="phone" maxlength="10" value="<?= $data["phone"] ?>" required>
    <span class="err" data-for="phone"></span>

    <label for="dob">Date of birth</label>
    <input type="date" id="dob" name="dob" value="<?= $data["dob"] ?>">

    <fieldset>
      <legend>Gender</legend>
      <label class="inline"><input type="radio" name="gender" value="Female"> Female</label>
      <label class="inline"><input type="radio" name="gender" value="Male"> Male</label>
      <label class="inline"><input type="radio" name="gender" value="Other"> Other</label>
    </fieldset>

    <label for="course">Course *</label>
    <select id="course" name="course" required>
      <option value="">Choose a course</option>
      <?php foreach (["BCA","BBA","B.Com","Engineering"] as $c): ?>
        <option value="<?= $c ?>" <?= $data["course"] === $c ? "selected" : "" ?>><?= $c ?></option>
      <?php endforeach; ?>
    </select>
    <span class="err" data-for="course"></span>

    <label for="notes">Additional notes <small id="count">0/200</small></label>
    <textarea id="notes" name="notes" rows="4" maxlength="200"><?= $data["notes"] ?></textarea>

    <button type="submit" class="btn">Submit application</button>
  </form>
  <?php endif; ?>
</main>
</body>
</html>
