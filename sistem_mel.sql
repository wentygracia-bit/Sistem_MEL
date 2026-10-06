-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3310
-- Generation Time: Oct 06, 2026 at 09:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_mel`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `activity_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `project_id`, `activity_name`, `description`, `location`, `status`, `created_at`) VALUES
(1, 1, 'Penanaman 5.000 Bibit Mangrove Rhizophora', 'Kegiatan penanaman bibit mangrove bersama kelompok tani pesisir.', 'Kawasan Pesisir Muara Gembong', 'Berjalan', '2026-10-06 14:14:58'),
(2, 1, 'Pelatihan Pengolahan Sirup Mangrove bagi KWT', 'Peningkatan kapasitas Kelompok Wanita Tani untuk nilai tambah ekonomi produk pesisir.', 'Balai Desa Pantai Bahagia', 'Selesai', '2026-10-06 14:14:58');

-- --------------------------------------------------------

--
-- Table structure for table `activity_timelines`
--

CREATE TABLE `activity_timelines` (
  `timeline_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reminder_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_timelines`
--

INSERT INTO `activity_timelines` (`timeline_id`, `activity_id`, `start_date`, `end_date`, `reminder_date`, `status`) VALUES
(1, 1, '2026-02-01', '2026-04-30', '2026-04-20', 'Dalam Jadwal'),
(2, 2, '2026-03-01', '2026-03-15', '2026-03-10', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `approval_id` int(11) NOT NULL,
  `mel_result_id` int(11) NOT NULL,
  `approved_by` int(11) NOT NULL,
  `approval_date` date NOT NULL,
  `decision` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`approval_id`, `mel_result_id`, `approved_by`, `approval_date`, `decision`, `notes`) VALUES
(1, 1, 5, '2026-03-22', 'Disetujui', 'Sangat baik, teruskan ke fase pendampingan pemasaran produk olahan.');

-- --------------------------------------------------------

--
-- Table structure for table `correction_requests`
--

CREATE TABLE `correction_requests` (
  `correction_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `requested_by` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `description` text NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `response` text DEFAULT NULL,
  `response_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `update_id` int(11) NOT NULL,
  `indicator_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `evaluation_date` date NOT NULL,
  `result` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`evaluation_id`, `update_id`, `indicator_id`, `evaluator_id`, `evaluation_date`, `result`, `notes`) VALUES
(1, 1, 2, 4, '2026-03-18', 'Sesuai', 'Target terlampaui (38 dari target 35 peserta). Dokumentasi lengkap dan absensi valid.');

-- --------------------------------------------------------

--
-- Table structure for table `field_updates`
--

CREATE TABLE `field_updates` (
  `update_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `update_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `realization_value` decimal(15,2) DEFAULT NULL,
  `progress_percentage` decimal(5,2) DEFAULT NULL,
  `evidence_file` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `field_updates`
--

INSERT INTO `field_updates` (`update_id`, `activity_id`, `user_id`, `update_date`, `description`, `realization_value`, `progress_percentage`, `evidence_file`, `status`) VALUES
(1, 2, 1, '2026-03-16', 'Pelatihan telah selesai dilaksanakan selama 2 hari dengan antusiasme tinggi.', 38.00, 100.00, NULL, 'Dievaluasi');

-- --------------------------------------------------------

--
-- Table structure for table `indicators`
--

CREATE TABLE `indicators` (
  `indicator_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `indicator_name` varchar(200) NOT NULL,
  `target_value` decimal(15,2) DEFAULT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `indicators`
--

INSERT INTO `indicators` (`indicator_id`, `activity_id`, `indicator_name`, `target_value`, `unit`, `description`) VALUES
(1, 1, 'Jumlah bibit tertanam dan hidup', 5000.00, 'Bibit', 'Bibit mangrove jenis Rhizophora mucronata'),
(2, 2, 'Jumlah peserta wanita terlatih', 35.00, 'Orang', 'Peserta perempuan dari perwakilan 5 RT');

-- --------------------------------------------------------

--
-- Table structure for table `mel_results`
--

CREATE TABLE `mel_results` (
  `mel_result_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `submission_date` date NOT NULL,
  `result_summary` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mel_results`
--

INSERT INTO `mel_results` (`mel_result_id`, `activity_id`, `evaluation_id`, `submitted_by`, `submission_date`, `result_summary`, `status`) VALUES
(1, 2, 1, 3, '2026-03-20', 'Hasil evaluasi kegiatan pelatihan memenuhi standar mutu program dengan ketercapaian 108.5%. Siap untuk disahkan Direksi.', 'Disetujui');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `notification_type` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `activity_id`, `notification_type`, `message`, `sent_at`, `is_read`) VALUES
(1, 1, 1, 'Reminder', 'Pengingat Jadwal: Kegiatan \'Penanaman 5.000 Bibit Mangrove Rhizophora\' mendekati batas waktu (2026-04-30). Harap segera memperbarui data capaian lapangan.', '2026-10-06 14:21:13', 0),
(2, 4, 1, 'Reminder', 'Pengingat Jadwal: Kegiatan \'Penanaman 5.000 Bibit Mangrove Rhizophora\' mendekati batas waktu (2026-04-30). Harap segera memperbarui data capaian lapangan.', '2026-10-06 14:21:13', 0);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `project_code` varchar(50) NOT NULL,
  `project_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `public_status` varchar(50) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_code`, `project_name`, `description`, `start_date`, `end_date`, `status`, `public_status`, `created_by`, `created_at`) VALUES
(1, 'PRJ-YNKI-2026-01', 'Pemberdayaan Masyarakat Konservasi Hutan Mangrove', 'Program pemulihan ekosistem pesisir dan peningkatan ekonomi masyarakat pesisir berbasis konservasi mangrove.', '2026-01-15', '2026-12-31', 'Aktif', 'Dipublikasikan', 4, '2026-10-06 14:14:58');

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `project_member_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `member_role` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`project_member_id`, `project_id`, `user_id`, `member_role`) VALUES
(1, 1, 4, 'Project Leader'),
(2, 1, 1, 'Field Coordinator');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Staf Lapangan', 'Melaksanakan kegiatan, mengunggah laporan hasil, merespons permintaan perbaikan'),
(2, 'Staf MEL', 'Mengelola timeline kegiatan, memverifikasi laporan awal, menyusun hasil akhir MEL'),
(3, 'Project Leader', 'Mengevaluasi hasil pekerjaan terhadap indikator, meminta perbaikan ke staf lapangan'),
(4, 'Direktur', 'Memberikan pengesahan dan persetujuan akhir hasil MEL');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `organization` varchar(150) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `name`, `email`, `password`, `organization`, `is_active`, `created_at`) VALUES
(1, 1, 'Budi Santoso', 'lapangan@ynki.org', '$2y$10$z.tq5Pocn.rVHU.I5QfoI.ND/nWeFjcFwQnHd8tRphkDgE03Th1vC', 'Yayasan YNKI - Unit Lapangan', 1, '2026-10-06 14:14:58'),
(2, 1, 'Dewi Anggraini', 'lapangan2@ynki.org', '$2y$10$z.tq5Pocn.rVHU.I5QfoI.ND/nWeFjcFwQnHd8tRphkDgE03Th1vC', 'Yayasan YNKI - Unit Lapangan', 1, '2026-10-06 14:14:58'),
(3, 2, 'Siti Nurhaliza', 'mel@ynki.org', '$2y$10$z.tq5Pocn.rVHU.I5QfoI.ND/nWeFjcFwQnHd8tRphkDgE03Th1vC', 'Yayasan YNKI - Divisi MEL', 1, '2026-10-06 14:14:58'),
(4, 3, 'Rahmat Hidayat', 'leader@ynki.org', '$2y$10$z.tq5Pocn.rVHU.I5QfoI.ND/nWeFjcFwQnHd8tRphkDgE03Th1vC', 'Yayasan YNKI - Program Management', 1, '2026-10-06 14:14:58'),
(5, 4, 'Dr. Ir. Hendra Wijaya', 'direktur@ynki.org', '$2y$10$z.tq5Pocn.rVHU.I5QfoI.ND/nWeFjcFwQnHd8tRphkDgE03Th1vC', 'Yayasan YNKI - Direksi', 1, '2026-10-06 14:14:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `fk_activities_project` (`project_id`);

--
-- Indexes for table `activity_timelines`
--
ALTER TABLE `activity_timelines`
  ADD PRIMARY KEY (`timeline_id`),
  ADD KEY `fk_timelines_activity` (`activity_id`);

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `fk_approvals_mel_result` (`mel_result_id`),
  ADD KEY `fk_approvals_approver` (`approved_by`);

--
-- Indexes for table `correction_requests`
--
ALTER TABLE `correction_requests`
  ADD PRIMARY KEY (`correction_id`),
  ADD KEY `fk_corrections_evaluation` (`evaluation_id`),
  ADD KEY `fk_corrections_requested_by` (`requested_by`),
  ADD KEY `fk_corrections_assigned_to` (`assigned_to`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`evaluation_id`),
  ADD KEY `fk_evaluations_update` (`update_id`),
  ADD KEY `fk_evaluations_indicator` (`indicator_id`),
  ADD KEY `fk_evaluations_evaluator` (`evaluator_id`);

--
-- Indexes for table `field_updates`
--
ALTER TABLE `field_updates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `fk_field_updates_activity` (`activity_id`),
  ADD KEY `fk_field_updates_user` (`user_id`);

--
-- Indexes for table `indicators`
--
ALTER TABLE `indicators`
  ADD PRIMARY KEY (`indicator_id`),
  ADD KEY `fk_indicators_activity` (`activity_id`);

--
-- Indexes for table `mel_results`
--
ALTER TABLE `mel_results`
  ADD PRIMARY KEY (`mel_result_id`),
  ADD KEY `fk_mel_results_activity` (`activity_id`),
  ADD KEY `fk_mel_results_evaluation` (`evaluation_id`),
  ADD KEY `fk_mel_results_submitter` (`submitted_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notifications_user` (`user_id`),
  ADD KEY `fk_notifications_activity` (`activity_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD UNIQUE KEY `project_code` (`project_code`),
  ADD KEY `fk_projects_creator` (`created_by`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`project_member_id`),
  ADD UNIQUE KEY `uq_project_member` (`project_id`,`user_id`),
  ADD KEY `fk_project_members_user` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `activity_timelines`
--
ALTER TABLE `activity_timelines`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `correction_requests`
--
ALTER TABLE `correction_requests`
  MODIFY `correction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `field_updates`
--
ALTER TABLE `field_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `indicators`
--
ALTER TABLE `indicators`
  MODIFY `indicator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mel_results`
--
ALTER TABLE `mel_results`
  MODIFY `mel_result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `project_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `fk_activities_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `activity_timelines`
--
ALTER TABLE `activity_timelines`
  ADD CONSTRAINT `fk_timelines_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `fk_approvals_approver` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_approvals_mel_result` FOREIGN KEY (`mel_result_id`) REFERENCES `mel_results` (`mel_result_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `correction_requests`
--
ALTER TABLE `correction_requests`
  ADD CONSTRAINT `fk_corrections_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_corrections_evaluation` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`evaluation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_corrections_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `fk_evaluations_evaluator` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluations_indicator` FOREIGN KEY (`indicator_id`) REFERENCES `indicators` (`indicator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluations_update` FOREIGN KEY (`update_id`) REFERENCES `field_updates` (`update_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `field_updates`
--
ALTER TABLE `field_updates`
  ADD CONSTRAINT `fk_field_updates_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_field_updates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `indicators`
--
ALTER TABLE `indicators`
  ADD CONSTRAINT `fk_indicators_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mel_results`
--
ALTER TABLE `mel_results`
  ADD CONSTRAINT `fk_mel_results_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mel_results_evaluation` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`evaluation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mel_results_submitter` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `fk_project_members_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_project_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
