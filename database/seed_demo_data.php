<?php
/**
 * Demo Data Seeder for GBU Grievance Management System
 * Run from project root: C:\xampp\php\php.exe database\seed_demo_data.php
 */
require_once dirname(__DIR__) . '/includes/db.php';

echo "Seeding GBU GMS demo data...\n";

$passwordHash = password_hash('password123', PASSWORD_BCRYPT);

$departments = ['SOE', 'SOICT', 'SOM', 'SOB', 'SOLJ', 'SOVS', 'SOHSS'];
$hostels = [
    'Birsa Munda Boys Hostel',
    'Guru Ghasi Das Boys Hostel',
    'Sant Ravidas Boys Hostel',
    'Sant Kabir Das Boys Hostel',
    'Tulsidas Boys Hostel',
    'Munshi Premchand Boys Hostel',
    'Savitri Bai Phule Girls Hostel',
    'Rani Laxmi Bai Girls Hostel',
    'Mahamaya Girls Hostel',
    'Mahadevi Verma Girls Hostel',
];

$students = [
    ['23ICS002', 'Ananya Verma', 'ananya.verma@gbu.ac.in', 'SOICT', 'Savitri Bai Phule Girls Hostel'],
    ['23ICS003', 'Rohan Malhotra', 'rohan.malhotra@gbu.ac.in', 'SOICT', 'Birsa Munda Boys Hostel'],
    ['23ICS004', 'Priya Singh', 'priya.singh@gbu.ac.in', 'SOE', 'Rani Laxmi Bai Girls Hostel'],
    ['23ICS005', 'Aditya Tiwari', 'aditya.tiwari@gbu.ac.in', 'SOE', 'Guru Ghasi Das Boys Hostel'],
    ['23ICS006', 'Sneha Patel', 'sneha.patel@gbu.ac.in', 'SOB', 'Mahamaya Girls Hostel'],
    ['23ICS007', 'Vikram Chauhan', 'vikram.chauhan@gbu.ac.in', 'SOM', 'Sant Ravidas Boys Hostel'],
    ['23ICS008', 'Kavya Nair', 'kavya.nair@gbu.ac.in', 'SOLJ', 'Mahadevi Verma Girls Hostel'],
    ['23ICS009', 'Arjun Mehta', 'arjun.mehta@gbu.ac.in', 'SOICT', 'Tulsidas Boys Hostel'],
    ['23ICS010', 'Ishita Joshi', 'ishita.joshi@gbu.ac.in', 'SOHSS', 'Savitri Bai Phule Girls Hostel'],
    ['23ICS011', 'Karan Yadav', 'karan.yadav@gbu.ac.in', 'SOE', 'Sant Kabir Das Boys Hostel'],
    ['23ICS012', 'Neha Gupta', 'neha.gupta@gbu.ac.in', 'SOM', 'Rani Laxmi Bai Girls Hostel'],
    ['23ICS013', 'Siddharth Rao', 'siddharth.rao@gbu.ac.in', 'SOICT', 'Munshi Premchand Boys Hostel'],
    ['23ICS015', 'Pooja Mishra', 'pooja.mishra@gbu.ac.in', 'SOB', 'Mahamaya Girls Hostel'],
    ['23ICS016', 'Rahul Saxena', 'rahul.saxena@gbu.ac.in', 'SOVS', 'Birsa Munda Boys Hostel'],
    ['23ICS017', 'Divya Bansal', 'divya.bansal@gbu.ac.in', 'SOLJ', 'Mahadevi Verma Girls Hostel'],
    ['23ICS018', 'Manish Kumar', 'manish.kumar@gbu.ac.in', 'SOE', 'Guru Ghasi Das Boys Hostel'],
    ['23ICS019', 'Riya Agarwal', 'riya.agarwal@gbu.ac.in', 'SOICT', 'Savitri Bai Phule Girls Hostel'],
    ['23ICS020', 'Nikhil Pandey', 'nikhil.pandey@gbu.ac.in', 'SOM', 'Sant Ravidas Boys Hostel'],
    ['23ICS021', 'Shreya Kulkarni', 'shreya.kulkarni@gbu.ac.in', 'SOHSS', 'Rani Laxmi Bai Girls Hostel'],
    ['23ICS022', 'Harsh Vardhan', 'harsh.vardhan@gbu.ac.in', 'SOICT', 'Tulsidas Boys Hostel'],
    ['23ICS023', 'Tanvi Deshmukh', 'tanvi.deshmukh@gbu.ac.in', 'SOB', 'Mahamaya Girls Hostel'],
    ['23ICS024', 'Abhishek Dubey', 'abhishek.dubey@gbu.ac.in', 'SOE', 'Sant Kabir Das Boys Hostel'],
    ['23ICS025', 'Meera Krishnan', 'meera.krishnan@gbu.ac.in', 'SOLJ', 'Mahadevi Verma Girls Hostel'],
    ['23ICS026', 'Yashwant Singh', 'yashwant.singh@gbu.ac.in', 'SOVS', 'Munshi Premchand Boys Hostel'],
];

$templates = [
    ['Academic', 'Lab computers not booting in Block C', 'Several desktops in the programming lab are stuck on boot loop before practical exams.'],
    ['Academic', 'Delay in internal marksheet update', 'Mid-semester internal assessment marks for two subjects are not reflected on the portal.'],
    ['Academic', 'Projector malfunction in Lecture Hall 4', 'The overhead projector flickers continuously during morning lectures.'],
    ['Academic', 'Library reference books out of stock', 'Required semester textbooks for Data Structures and DBMS are unavailable in the central library.'],
    ['Academic', 'Timetable clash for elective course', 'Two core/elective classes are scheduled in the exact same time slot this semester.'],
    ['Hostel', 'Wi-Fi connectivity drops on 3rd floor', 'Campus Wi-Fi disconnects every 10 minutes in the hostel wing after 8 PM.'],
    ['Hostel', 'Water cooler not working in hostel block', 'The drinking water dispenser on the second floor has been out of order for three days.'],
    ['Hostel', 'Bathroom plumbing leakage in Room 214', 'Continuous water seepage from the washroom pipe is damaging the corridor wall.'],
    ['Hostel', 'Ceiling fan regulator damaged in room', 'The room fan runs only at full speed and the switchboard sparks occasionally.'],
    ['Hostel', 'Corridor street lights not working', 'Four corridor lights near the hostel staircase are fused, making it dark at night.'],
    ['Infrastructure', 'Classroom AC not cooling in SOICT', 'Air conditioning in Room 302 has not been cooling properly during afternoon sessions.'],
    ['Infrastructure', 'Broken chairs in seminar hall', 'Around 12 desks and chairs in the seminar room have loose bolts and need repair.'],
    ['Infrastructure', 'Campus shuttle timing delay', 'The morning shuttle from the main gate to the academic block is running 20 minutes late.'],
    ['Finance', 'Scholarship verification pending', 'My state scholarship form has been awaiting accounts verification for over two weeks.'],
    ['Finance', 'Duplicate fee receipt not generated', 'Online semester fee was debited from my bank, but the portal receipt is still pending.'],
    ['Health', 'Dispensary stock shortage for basic meds', 'Common fever and first-aid medicines were out of stock at the campus health centre yesterday.'],
    ['Health', 'Mosquito fogging needed near hostel', 'Standing water near the hostel lawn is causing heavy mosquito breeding in the evening.'],
];

$remarks = [
    'In Progress' => [
        'Assigned to the concerned department technician for inspection.',
        'Reviewed by department admin. Maintenance team has been notified.',
        'Verification in progress with the respective section officer.',
    ],
    'Resolved' => [
        'Issue has been resolved and verified on site. Closing this grievance.',
        'Maintenance completed and tested working. Thank you for reporting.',
        'Records updated and confirmed with the department office.',
    ],
    'Rejected' => [
        'Duplicate submission; already being tracked under an earlier ticket.',
        'Insufficient details provided. Please resubmit with exact room/roll details.',
    ],
];

try {
    $pdo->beginTransaction();

    // 1. Insert students
    $userIns = $pdo->prepare(
        "INSERT IGNORE INTO users (roll_number, name, email, password_hash, role, department, hostel, is_active)
         VALUES (?, ?, ?, ?, 'Student', ?, ?, 1)"
    );
    foreach ($students as $s) {
        $userIns->execute([$s[0], $s[1], $s[2], $passwordHash, $s[3], $s[4]]);
    }

    $studentIds = $pdo->query("SELECT id, department, hostel FROM users WHERE role = 'Student'")->fetchAll();
    $adminIds = $pdo->query("SELECT id FROM users WHERE role != 'Student'")->fetchAll(PDO::FETCH_COLUMN);

    // Optional hostel_id lookup if hostels table exists
    $hostelMap = [];
    try {
        $hRows = $pdo->query("SELECT id, hostel_name FROM hostels")->fetchAll();
        foreach ($hRows as $hr) {
            $hostelMap[strtolower(trim($hr['hostel_name']))] = (int)$hr['id'];
        }
    } catch (PDOException $e) {
        // hostels table not migrated yet
    }
    $hasHostelIdCol = !empty($pdo->query("SHOW COLUMNS FROM grievances LIKE 'hostel_id'")->fetchAll());

    // Find current max GBU-2026-XXXX number
    $maxNum = (int)$pdo->query("SELECT MAX(CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)) FROM grievances WHERE id LIKE 'GBU-2026-%'")->fetchColumn();

    $statuses = ['Pending', 'In Progress', 'Resolved', 'Resolved', 'Resolved', 'Rejected'];
    $priorities = ['Low', 'Medium', 'Medium', 'High', 'Urgent'];
    $levels = ['Department', 'Department', 'College', 'University'];

    $gInsSql = $hasHostelIdCol
        ? "INSERT INTO grievances (id, user_id, category, department, hostel, hostel_id, priority, subject, description, status, current_level, created_at, updated_at)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        : "INSERT INTO grievances (id, user_id, category, department, hostel, priority, subject, description, status, current_level, created_at, updated_at)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $gIns = $pdo->prepare($gInsSql);
    $rIns = $pdo->prepare("INSERT INTO responses (grievance_id, admin_id, remark, status_change, created_at) VALUES (?, ?, ?, ?, ?)");
    $nIns = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, ?, ?)");
    $eIns = $pdo->prepare("INSERT INTO escalation_logs (grievance_id, escalated_from, escalated_to, escalated_at) VALUES (?, ?, ?, ?)");

    $inserted = 0;
    for ($i = 1; $i <= 120; $i++) {
        $maxNum++;
        $gid = 'GBU-2026-' . str_pad($maxNum, 4, '0', STR_PAD_LEFT);
        $stu = $studentIds[array_rand($studentIds)];
        $tpl = $templates[array_rand($templates)];
        $status = $statuses[array_rand($statuses)];
        $priority = $priorities[array_rand($priorities)];
        $level = ($status === 'Pending') ? 'Department' : $levels[array_rand($levels)];

        // Spread dates over the last 30 days
        $daysAgo = rand(0, 29);
        $hoursAgo = rand(1, 20);
        $createdAt = date('Y-m-d H:i:s', strtotime("-$daysAgo days -$hoursAgo hours"));
        $updatedAt = ($status === 'Pending')
            ? $createdAt
            : date('Y-m-d H:i:s', strtotime($createdAt . ' +' . rand(2, 48) . ' hours'));

        $hostelName = $stu['hostel'] ?: $hostels[array_rand($hostels)];
        $hostelId = $hostelMap[strtolower(trim($hostelName))] ?? null;

        if ($hasHostelIdCol) {
            $gIns->execute([
                $gid, $stu['id'], $tpl[0], $stu['department'], $hostelName, $hostelId,
                $priority, $tpl[1], $tpl[2], $status, $level, $createdAt, $updatedAt
            ]);
        } else {
            $gIns->execute([
                $gid, $stu['id'], $tpl[0], $stu['department'], $hostelName,
                $priority, $tpl[1], $tpl[2], $status, $level, $createdAt, $updatedAt
            ]);
        }

        if ($status !== 'Pending' && !empty($adminIds)) {
            $adminId = $adminIds[array_rand($adminIds)];
            $remarkList = $remarks[$status];
            $remarkText = $remarkList[array_rand($remarkList)];
            $rIns->execute([$gid, $adminId, $remarkText, $status, $updatedAt]);
            $nIns->execute([
                $stu['id'],
                "Your grievance (#$gid) status has been updated to '$status'.",
                ($status === 'Resolved' ? 1 : 0),
                $updatedAt
            ]);
        }

        if ($level === 'College') {
            $eIns->execute([$gid, 'Department', 'College', $updatedAt]);
        } elseif ($level === 'University') {
            $eIns->execute([$gid, 'College', 'University', $updatedAt]);
        }

        $inserted++;
    }

    $pdo->commit();
    echo "Successfully seeded 24 students and $inserted grievances (with responses, notifications, and escalation logs).\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "Seeding failed: " . $e->getMessage() . "\n");
    exit(1);
}
