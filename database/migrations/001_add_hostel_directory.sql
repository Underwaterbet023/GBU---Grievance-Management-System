-- GBU Smart Grievance System: hostel directory migration
-- Additive and safe to run on the existing gbu_grievance database.
-- Does NOT remove legacy grievances or legacy category values.
-- Tested conceptually against the five-table schema currently in database/schema.sql.

CREATE TABLE IF NOT EXISTS hostels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hostel_code VARCHAR(20) NOT NULL UNIQUE,
    hostel_name VARCHAR(150) NOT NULL,
    hostel_type ENUM('Girls', 'Boys', 'Married Accommodation') NOT NULL,
    official_url VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO hostels (hostel_code, hostel_name, hostel_type, official_url)
VALUES
('SBPGH', 'Savitri Bai Phule Girls Hostel', 'Girls', 'https://hostels.gbu.ac.in/sbpgh'),
('RLBGH', 'Rani Laxmi Bai Girls Hostel', 'Girls', 'https://hostels.gbu.ac.in/rlbgh'),
('RABGH', 'Rama Bai Ambedkar Girls Hostel', 'Girls', 'https://hostels.gbu.ac.in/rbagh'),
('MMGH', 'Mahamaya Girls Hostel', 'Girls', 'https://hostels.gbu.ac.in/mmgh'),
('MDVGH', 'Mahadevi Verma Girls Hostel', 'Girls', 'https://hostels.gbu.ac.in/mdvgh'),
('ICGH', 'Ismat Chughtai Girls Hostel', 'Girls', 'https://hostels.gbu.ac.in/icgh'),
('SRDBH', 'Sant Ravidas Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/srdbh'),
('SKDBH', 'Sant Kabir Das Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/skdbh'),
('BMBH', 'Birsa Munda Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/bmbh'),
('RSDBH', 'Ram Sharan Das Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/rsdbh'),
('SNGBH', 'Shri Narayan Guru Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/sngbh'),
('TDBH', 'Tulsidas Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/tdbh'),
('GGDBH', 'Guru Ghasi Das Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/ggdbh'),
('MMJBH', 'Malik Mohammad Jaysi Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/mmjbh'),
('MPCBH', 'Munshi Premchand Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/mpcbh'),
('RBH', 'Raheem Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/rbh'),
('MVBH', 'Maharshi Valmiki Boys Hostel', 'Boys', 'https://hostels.gbu.ac.in/mvbh'),
('MRSH', 'Married Research Scholars Hostel', 'Married Accommodation', 'https://hostels.gbu.ac.in/mrsh')
ON DUPLICATE KEY UPDATE
    hostel_name = VALUES(hostel_name),
    hostel_type = VALUES(hostel_type),
    official_url = VALUES(official_url),
    is_active = 1;

-- Add a nullable FK-compatible hostel_id to support gradual migration.
-- Do not drop or rename the existing grievances.hostel text column: older
-- pages and historical records may still rely on it.
ALTER TABLE grievances
    ADD COLUMN hostel_id INT NULL;

ALTER TABLE grievances
    ADD INDEX idx_grievances_hostel_id (hostel_id);

ALTER TABLE grievances
    ADD CONSTRAINT fk_grievances_hostel
    FOREIGN KEY (hostel_id) REFERENCES hostels(id)
    ON UPDATE CASCADE ON DELETE SET NULL;

-- Backfill recognized existing hostel names only. Unmatched legacy free text
-- remains in grievances.hostel for manual review.
UPDATE grievances g
JOIN hostels h ON LOWER(TRIM(g.hostel)) = LOWER(TRIM(h.hostel_name))
SET g.hostel_id = h.id
WHERE g.hostel IS NOT NULL AND g.hostel_id IS NULL;
