
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal Publik | Sistem MEL YNKI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">

    <style>
        :root {
            --public-green: #B5CE88;
            --public-green-dark: #45652D;
            --public-green-light: #F0F6E8;
            --public-bg: #FAFCF8;
            --public-text: #172033;
            --public-muted: #64748B;
            --public-border: #E2E8F0;
            --public-white: #FFFFFF;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--public-bg);
            color: var(--public-text);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
        }

        .public-header {
            min-height: 82px;
            padding: 16px 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid var(--public-border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo {
            width: 54px;
            height: 54px;
            flex-shrink: 0;
            padding: 4px;
            border-radius: 15px;
            background: var(--public-green-light);
            border: 1px solid #DCE8C9;
            object-fit: contain;
        }

        .brand-name {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .brand-subtitle {
            color: var(--public-muted);
            font-size: 0.8rem;
        }

        .btn-public {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 9px;
            padding: 12px 19px;
            background: var(--public-green);
            color: #263D20;
            border: 1px solid #A9C47A;
            border-radius: 12px;
            font-weight: 700;
            transition: 0.2s ease;
            white-space: nowrap;
        }

        .btn-public:hover {
            background: #A8C578;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: white;
            border-color: var(--public-border);
            color: var(--public-text);
        }

        .btn-outline:hover {
            background: var(--public-green-light);
        }

        .container {
            width: min(1180px, 90%);
            margin: 0 auto;
        }

        /* HERO */

        .hero {
            display: grid;
            grid-template-columns: minmax(240px, 0.85fr) minmax(0, 2fr);
            align-items: center;
            gap: 48px;
            margin: 42px 0 32px;
            padding: 48px 52px;
            overflow: hidden;
            border: 1px solid #E0E9D4;
            border-radius: 28px;
            background:
                radial-gradient(circle at 12% 20%, #E7F0D9 0, transparent 42%),
                linear-gradient(135deg, #FFFFFF 0%, #F4F9EC 100%);
            box-shadow: 0 10px 30px rgba(38, 61, 32, 0.06);
        }

        .hero-brand {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-logo-wrap {
            width: 240px;
            height: 240px;
            padding: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            border: 1px solid #DFE8D2;
            border-radius: 36px;
            box-shadow: 0 14px 36px rgba(50, 75, 34, 0.10);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hero-logo-wrap:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 18px 42px rgba(50, 75, 34, 0.14);
        }

        .hero-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.04));
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 13px;
            border: 1px solid #D0E1B7;
            border-radius: 999px;
            background: #F0F6E5;
            color: var(--public-green-dark);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .hero h1 {
            margin: 18px 0 14px;
            max-width: 760px;
            font-size: clamp(2rem, 4vw, 3.1rem);
            line-height: 1.15;
            letter-spacing: -1.4px;
        }

        .hero h1 span {
            color: var(--public-green-dark);
        }

        .hero-description {
            max-width: 720px;
            margin: 0;
            color: #526176;
            font-size: 1.02rem;
            line-height: 1.85;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 25px;
        }

        .hero-note {
            margin-top: 17px;
            color: var(--public-muted);
            font-size: 0.8rem;
        }

        /* SECTION */

        .section {
            margin: 42px 0;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: clamp(1.35rem, 2.5vw, 1.8rem);
            letter-spacing: -0.5px;
        }

        .section-heading p {
            margin: 5px 0 0;
            color: var(--public-muted);
            font-size: 0.92rem;
        }

        /* METRICS */

        .metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .metric-card {
            display: flex;
            align-items: center;
            gap: 16px;
            min-height: 120px;
            padding: 23px;
            background: var(--public-white);
            border: 1px solid var(--public-border);
            border-radius: 18px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.025);
        }

        .metric-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 15px;
            background: var(--public-green-light);
            color: var(--public-green-dark);
            font-size: 1.25rem;
        }

        .metric-value {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.25;
        }

        .metric-label {
            margin-top: 4px;
            color: var(--public-muted);
            font-size: 0.85rem;
        }

        /* CONTENT CARDS */

        .content-card {
            margin-bottom: 26px;
            padding: 28px;
            background: white;
            border: 1px solid var(--public-border);
            border-radius: 20px;
            box-shadow: 0 5px 18px rgba(15, 23, 42, 0.025);
        }

        .section-count {
            flex-shrink: 0;
            padding: 7px 12px;
            border: 1px solid #D4E4BE;
            border-radius: 999px;
            background: var(--public-green-light);
            color: var(--public-green-dark);
            font-size: 0.8rem;
            font-weight: 700;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .public-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.88rem;
        }

        .public-table th {
            padding: 15px 14px;
            background: #F7F9F6;
            color: #536176;
            font-size: 0.79rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .public-table td {
            padding: 18px 14px;
            border-bottom: 1px solid #EDF0F3;
            vertical-align: middle;
        }

        .public-table tbody tr:last-child td {
            border-bottom: none;
        }

        .public-table tbody tr:hover {
            background: #FCFDFB;
        }

        .project-code {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 7px;
            background: var(--public-green-light);
            color: var(--public-green-dark);
            font-size: 0.78rem;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .item-title {
            color: var(--public-text);
            font-weight: 700;
        }

        .item-description {
            display: block;
            max-width: 330px;
            margin-top: 4px;
            color: var(--public-muted);
            font-size: 0.8rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border: 1px solid #D4E4BE;
            border-radius: 999px;
            background: var(--public-green-light);
            color: var(--public-green-dark);
            font-size: 0.78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .activity-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #E8F3FF;
            color: #1765A5;
            font-size: 0.78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .empty-state {
            padding: 42px 20px;
            text-align: center;
            color: var(--public-muted);
        }

        .empty-state i {
            display: block;
            margin-bottom: 13px;
            color: #A4B88A;
            font-size: 2rem;
        }

        .empty-state strong {
            display: block;
            margin-bottom: 5px;
            color: var(--public-text);
        }

        /* ABOUT */

        .about-panel {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: start;
            gap: 20px;
            margin: 36px 0 46px;
            padding: 28px;
            border: 1px solid #E0E9D4;
            border-radius: 20px;
            background: #F1F6E9;
        }

        .about-icon {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            background: white;
            color: var(--public-green-dark);
            font-size: 1.35rem;
        }

        .about-panel h2 {
            margin: 0 0 8px;
            font-size: 1.2rem;
        }

        .about-panel p {
            margin: 0;
            color: #526176;
            font-size: 0.91rem;
            line-height: 1.8;
        }

        /* FOOTER */

        .public-footer {
            margin-top: 60px;
            padding: 28px 5%;
            border-top: 1px solid var(--public-border);
            background: white;
        }

        .footer-inner {
            width: min(1180px, 100%);
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .footer-copy {
            color: var(--public-muted);
            font-size: 0.8rem;
        }

        .footer-brand {
            color: var(--public-text);
            font-weight: 800;
        }

        @media (max-width: 800px) {
            .hero {
                grid-template-columns: 1fr;
                gap: 25px;
                padding: 32px;
                text-align: center;
            }

            .hero-logo-wrap {
                width: 300px;
                height: 300px;
                border-radius: 28px;
            }

            .hero-actions {
                justify-content: center;
            }

            .hero-description {
                margin: 0 auto;
            }

            .metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-card {
                padding: 20px;
            }
        }

        @media (max-width: 520px) {
            .public-header {
                padding: 13px 5%;
                gap: 10px;
            }

            .brand {
                gap: 9px;
            }

            .brand-logo {
                width: 43px;
                height: 43px;
                border-radius: 12px;
            }

            .brand-name {
                font-size: 0.95rem;
            }

            .brand-subtitle {
                max-width: 165px;
                font-size: 0.67rem;
            }

            .btn-public {
                padding: 10px 11px;
                font-size: 0.78rem;
            }

            .btn-public i {
                display: none;
            }

            .container {
                width: 92%;
            }

            .hero {
                margin-top: 24px;
                padding: 25px 20px;
                border-radius: 20px;
            }

            .hero h1 {
                font-size: 2rem;
                letter-spacing: -0.8px;
            }

            .hero-description {
                font-size: 0.9rem;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .btn-public {
                width: 100%;
            }

            .metrics {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .metric-card {
                min-height: 95px;
                padding: 17px;
            }

            .section {
                margin: 32px 0;
            }

            .section-heading {
                align-items: start;
            }

            .section-heading h2 {
                font-size: 1.3rem;
            }

            .content-card {
                padding: 17px 13px;
                border-radius: 16px;
            }

            .public-table {
                min-width: 700px;
            }

            .about-panel {
                grid-template-columns: 1fr;
                padding: 22px;
            }

            .footer-inner {
                flex-direction: column;
                align-items: start;
            }
        }
    </style>
</head>

<body>

<?php
// Hanya hasil MEL yang berstatus Disetujui yang ditampilkan di tabel publik.
// Ini adalah penyaring tambahan di view, bukan pengganti pembatasan di controller.
$publishedMelResults = array_values(array_filter(
    $melResults ?? [],
    static fn($result) => ($result['status'] ?? '') === 'Disetujui'
));

$publicProjects = $publicProjects ?? [];
?>

<!-- HEADER -->
<header class="public-header">
    <a href="<?= BASE_URL ?>/" class="brand" aria-label="Beranda Sistem MEL YNKI">
        <img
            class="brand-logo"
            src="<?= BASE_URL ?>/gambar%20logo/ynki-icon.webp"
            alt="Logo YNKI"
        >
        <span>
            <span class="brand-name" style="display:block;">Sistem MEL YNKI</span>
            <span class="brand-subtitle">Monitoring, Evaluation & Learning</span>
        </span>
    </a>

    <a href="<?= BASE_URL ?>/login" class="btn-public">
        <i class="fa-solid fa-lock"></i>
        Masuk Staf / Tim
    </a>
</header>

<main class="container">

    <!-- HERO -->
    <section class="hero">
        <div class="hero-brand">
            <div class="hero-logo-wrap">
                <img
                    class="hero-logo"
                    src="<?= BASE_URL ?>/gambar%20logo/ynki-icon.webp"
                    alt="Logo Yayasan YNKI"
                >
            </div>
        </div>

        <div>
            <span class="eyebrow">
                <i class="fa-solid fa-seedling"></i>
                Akuntabilitas & Transparansi Program
            </span>

            <h1>
                Memantau Capaian,<br>
                <span>Mendorong Dampak Nyata.</span>
            </h1>

            <p class="hero-description">
                Sistem MEL YNKI menyajikan informasi mengenai pelaksanaan
                program, hasil monitoring dan evaluasi, serta capaian yang
                telah disahkan untuk mendukung transparansi dan akuntabilitas
                kepada publik.
            </p>

            <div class="hero-actions">
                <a href="#capaian" class="btn-public">
                    <i class="fa-solid fa-chart-column"></i>
                    Lihat Capaian Program
                </a>

                <a href="#tentang" class="btn-public btn-outline">
                    Tentang Sistem MEL
                    <i class="fa-solid fa-arrow-down"></i>
                </a>
            </div>

            <div class="hero-note">
                <i class="fa-solid fa-circle-info"></i>
                Informasi publik ditampilkan sesuai data yang tersedia dan
                status publikasinya.
            </div>
        </div>
    </section>

    <!-- RINGKASAN PUBLIK -->
    <section class="section" id="capaian">
        <div class="section-heading">
            <div>
                <h2>Ringkasan Informasi Publik</h2>
                <p>Gambaran singkat program dan hasil evaluasi yang telah disahkan.</p>
            </div>
        </div>

        <div class="metrics">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <div class="metric-value">
                        <?= count($publicProjects) ?>
                    </div>
                    <div class="metric-label">Proyek Dipublikasikan</div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fa-solid fa-file-circle-check"></i>
                </div>
                <div>
                    <div class="metric-value">
                        <?= count($publishedMelResults) ?>
                    </div>
                    <div class="metric-label">Hasil MEL Disahkan</div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <div class="metric-value" style="font-size:1.25rem;">
                        Transparan
                    </div>
                    <div class="metric-label">Informasi untuk Publik</div>
                </div>
            </div>
        </div>
    </section>

    <!-- DAFTAR PROYEK -->
    <section class="content-card">
        <div class="section-heading">
            <div>
                <h2>Program & Proyek Publik</h2>
                <p>Informasi program yang tersedia untuk diketahui masyarakat.</p>
            </div>

            <span class="section-count">
                <?= count($publicProjects) ?> Proyek
            </span>
        </div>

        <div class="table-responsive">
            <table class="public-table">
                <thead>
                    <tr>
                        <th>Kode Proyek</th>
                        <th>Nama Program</th>
                        <th>Periode</th>
                        <th>Kegiatan</th>
                        <th>Publikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($publicProjects)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <strong>Belum ada proyek yang dipublikasikan</strong>
                                    Informasi program akan ditampilkan di sini
                                    setelah tersedia untuk publik.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($publicProjects as $p): ?>
                            <tr>
                                <td>
                                    <span class="project-code">
                                        <?= htmlspecialchars($p['project_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="item-title">
                                        <?= htmlspecialchars($p['project_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="item-description">
                                        <?php
                                        $description = trim($p['description'] ?? '');
                                        echo htmlspecialchars(
                                            mb_strimwidth($description, 0, 110, '...'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </span>
                                </td>

                                <td>
                                    <?php
                                    $startDate = !empty($p['start_date'])
                                        ? date('d M Y', strtotime($p['start_date']))
                                        : '-';

                                    $endDate = !empty($p['end_date'])
                                        ? date('d M Y', strtotime($p['end_date']))
                                        : '-';
                                    ?>
                                    <span style="white-space:nowrap;">
                                        <?= htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span style="display:block;color:var(--public-muted);font-size:.8rem;">
                                        s/d <?= htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="activity-badge">
                                        <?= (int) ($p['total_activities'] ?? 0) ?> Kegiatan
                                    </span>
                                </td>

                                <td>
                                    <span class="status-badge">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <?= htmlspecialchars($p['public_status'] ?? 'Dipublikasikan', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- HASIL MEL YANG TELAH DISAHKAN -->
    <section class="content-card">
        <div class="section-heading">
            <div>
                <h2>Hasil Monitoring & Evaluasi</h2>
                <p>Ringkasan hasil MEL yang berstatus disahkan.</p>
            </div>

            <span class="section-count">
                <?= count($publishedMelResults) ?> Hasil
            </span>
        </div>

        <div class="table-responsive">
            <table class="public-table">
                <thead>
                    <tr>
                        <th>Kegiatan</th>
                        <th>Program</th>
                        <th>Tanggal Pengesahan</th>
                        <th>Ringkasan Hasil</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($publishedMelResults)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                    <strong>Belum ada hasil MEL yang diterbitkan</strong>
                                    Hasil evaluasi akan ditampilkan setelah
                                    memperoleh pengesahan.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($publishedMelResults as $r): ?>
                            <tr>
                                <td>
                                    <span class="item-title">
                                        <?= htmlspecialchars($r['activity_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($r['project_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td>
                                    <?php if (!empty($r['approval_date'])): ?>
                                        <?= date('d M Y', strtotime($r['approval_date'])) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="item-description" style="max-width:380px;margin:0;">
                                        <?= htmlspecialchars($r['result_summary'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="status-badge">
                                        <i class="fa-solid fa-stamp"></i>
                                        Disahkan
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- TENTANG SISTEM -->
    <section class="about-panel" id="tentang">
        <div class="about-icon">
            <i class="fa-solid fa-chart-line"></i>
        </div>

        <div>
            <h2>Tentang Sistem Monitoring, Evaluation & Learning</h2>
            <p>
                Sistem MEL membantu proses pemantauan kegiatan, penilaian
                ketercapaian indikator, dan penyusunan hasil evaluasi program.
                Portal publik ini menjadi sarana untuk melihat informasi
                program serta hasil evaluasi yang telah disahkan dan
                disiapkan untuk dipublikasikan.
            </p>
        </div>
    </section>

</main>

<!-- FOOTER -->
<footer class="public-footer">
    <div class="footer-inner">
        <div class="brand">
            <img
                class="brand-logo"
                src="<?= BASE_URL ?>/gambar%20logo/ynki-icon.webp"
                alt="Logo YNKI"
            >
            <div>
                <div class="footer-brand">Sistem MEL YNKI</div>
                <div class="footer-copy">Portal Informasi Capaian Program</div>
            </div>
        </div>

        <div class="footer-copy">
            Informasi publik berdasarkan data yang tersedia dan status pengesahan.
        </div>
    </div>
</footer>

</body>
</html>
