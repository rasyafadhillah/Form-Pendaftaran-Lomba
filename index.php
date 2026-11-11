<?php
$nama     = '';
$email    = '';
$no_hp    = '';
$umur     = '';
$kategori = '';
$peserta  = '';

$errors  = [];
$success = false;

$daftar_kategori = [
    'web_dev'    => 'Web Development',
    'cp'         => 'Competitive Programming',
    'mobile_dev' => 'Mobile App Development',
    'ai_ds'      => 'AI & Data Science',
];

$daftar_peserta = [
    'individu' => 'Individu',
    'tim'      => 'Tim (Maksimal 3 Orang)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $no_hp    = trim($_POST['no_hp'] ?? '');
    $umur     = trim($_POST['umur'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $peserta  = $_POST['peserta'] ?? '';

    if ($nama === '') {
        $errors['nama'] = 'Nama wajib diisi.';
    } elseif (mb_strlen($nama) < 3 || mb_strlen($nama) > 50) {
        $errors['nama'] = 'Nama harus terdiri dari 3 hingga 50 karakter.';
    }

    if ($email === '') {
        $errors['email'] = 'Alamat email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format alamat email tidak valid.';
    }

    $regex_hp = '/^(?:\+62|62|0)8[1-9][0-9]{7,11}$/';
    if ($no_hp === '') {
        $errors['no_hp'] = 'Nomor HP wajib diisi.';
    } elseif (!filter_var($no_hp, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regex_hp]])) {
        $errors['no_hp'] = 'Nomor HP tidak valid. Gunakan format 08xxxxxxxxxx atau +628xxxxxxxxxx (10-14 digit).';
    }

    if ($umur === '') {
        $errors['umur'] = 'Umur wajib diisi.';
    } elseif (filter_var($umur, FILTER_VALIDATE_INT) === false) {
        $errors['umur'] = 'Umur harus berupa angka bulat.';
    } else {
        $options_umur = [
            'options' => [
                'min_range' => 17,
                'max_range' => 19,
            ],
        ];
        if (filter_var($umur, FILTER_VALIDATE_INT, $options_umur) === false) {
            $errors['umur'] = 'Peringatan: Umur peserta harus berada dalam rentang 17–19 tahun. Pendaftaran tidak dapat disimpan.';
        }
    }

    if ($kategori === '') {
        $errors['kategori'] = 'Silakan pilih kategori lomba.';
    } elseif (!in_array($kategori, array_keys($daftar_kategori), true)) {
        $errors['kategori'] = 'Kategori yang dipilih tidak terdaftar dalam whitelist.';
    }

    if ($peserta === '') {
        $errors['peserta'] = 'Silakan pilih jenis kepesertaan.';
    } elseif (!in_array($peserta, array_keys($daftar_peserta), true)) {
        $errors['peserta'] = 'Jenis kepesertaan yang dipilih tidak terdaftar dalam whitelist.';
    }

    if (empty($errors)) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Lomba Coding</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Registrasi Lomba Coding</h1>
    <p class="subtitle">Silakan lengkapi formulir pendaftaran di bawah ini dengan data yang benar.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <strong>Gagal menyimpan pendaftaran!</strong> Mohon periksa kembali isian yang ditandai di bawah ini.
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert-success">
            <strong>Pendaftaran Berhasil Disimpan!</strong>
            <p style="margin: 6px 0 10px;">Berikut adalah ringkasan data registrasi Anda:</p>
            <table class="result-table">
                <tr>
                    <th>Nama Lengkap</th>
                    <td><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th>Nomor HP</th>
                    <td><?= htmlspecialchars($no_hp, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th>Umur</th>
                    <td><?= htmlspecialchars($umur, ENT_QUOTES, 'UTF-8') ?> Tahun</td>
                </tr>
                <tr>
                    <th>Kategori Lomba</th>
                    <td><?= htmlspecialchars($daftar_kategori[$kategori], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th>Jenis Peserta</th>
                    <td><?= htmlspecialchars($daftar_peserta[$peserta], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            </table>
        </div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" method="post" novalidate>
        <div class="form-group">
            <label for="nama">Nama Lengkap <span class="required">*</span></label>
            <input
                type="text"
                id="nama"
                name="nama"
                placeholder="Masukkan nama lengkap (3-50 karakter)"
                value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                class="<?= isset($errors['nama']) ? 'input-invalid' : '' ?>"
            >
            <?php if (isset($errors['nama'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['nama'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Alamat Email <span class="required">*</span></label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="nama@domain.com"
                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                class="<?= isset($errors['email']) ? 'input-invalid' : '' ?>"
            >
            <?php if (isset($errors['email'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="no_hp">Nomor HP <span class="required">*</span></label>
            <input
                type="text"
                id="no_hp"
                name="no_hp"
                placeholder="Contoh: 081234567890"
                value="<?= htmlspecialchars($no_hp, ENT_QUOTES, 'UTF-8') ?>"
                class="<?= isset($errors['no_hp']) ? 'input-invalid' : '' ?>"
            >
            <div class="hint">Format: diawali 08, 628, atau +628 (10–14 digit).</div>
            <?php if (isset($errors['no_hp'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['no_hp'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="umur">Umur (Tahun) <span class="required">*</span></label>
            <input
                type="number"
                id="umur"
                name="umur"
                placeholder="17 - 19"
                value="<?= htmlspecialchars($umur, ENT_QUOTES, 'UTF-8') ?>"
                class="<?= isset($errors['umur']) ? 'input-invalid' : '' ?>"
            >
            <div class="hint">Khusus peserta berusia 17 hingga 19 tahun.</div>
            <?php if (isset($errors['umur'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['umur'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="kategori">Kategori Lomba <span class="required">*</span></label>
            <select
                id="kategori"
                name="kategori"
                class="<?= isset($errors['kategori']) ? 'input-invalid' : '' ?>"
            >
                <option value="">-- Pilih Kategori Lomba --</option>
                <?php foreach ($daftar_kategori as $key => $label): ?>
                    <option
                        value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($kategori === $key) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['kategori'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['kategori'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Jenis Peserta <span class="required">*</span></label>
            <div class="radio-group">
                <?php foreach ($daftar_peserta as $key => $label): ?>
                    <label class="radio-label">
                        <input
                            type="radio"
                            name="peserta"
                            value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                            <?= ($peserta === $key) ? 'checked' : '' ?>
                        >
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (isset($errors['peserta'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['peserta'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Daftar Sekarang</button>
    </form>
</div>

</body>
</html>