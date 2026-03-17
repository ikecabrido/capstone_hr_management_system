<?php
require_once __DIR__ . '/modules/config.php';

try {
    // Check if career_paths has data
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM career_paths');
    $careerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    if ($careerCount === 0) {
        // Insert Career Paths
        $careerSql = "INSERT INTO career_paths (name, description, target_position, prerequisites, skills_required, duration_months, status, created_at, cover_photo) VALUES
        ('Senior Manager Track', 'Path to becoming a senior manager with focus on leadership and strategic thinking', 'Senior Manager', '3+ years management experience', '[\"Strategic Planning\", \"Leadership\", \"Budget Management\"]', 18, 'active', NOW(), NULL),
        ('Technical Expert Track', 'Career progression for technical professionals aiming for specialist roles', 'Technical Specialist', '5+ years technical experience', '[\"Deep Technical Knowledge\", \"Problem Solving\", \"Mentoring\"]', 24, 'active', NOW(), NULL),
        ('Project Manager Track', 'Dedicated path for aspiring and growing project managers', 'Senior Project Manager', '2+ years project coordination', '[\"Project Management\", \"Team Leadership\", \"Stakeholder Management\"]', 12, 'active', NOW(), NULL),
        ('Sales Executive Track', 'Career development for sales professionals targeting executive positions', 'Sales Director', '4+ years sales experience', '[\"Sales Strategy\", \"Leadership\", \"Client Relations\"]', 20, 'active', NOW(), NULL),
        ('Human Resources Specialist', 'HR career development focusing on talent management and organizational development', 'Senior HR Manager', '2+ years HR experience', '[\"Talent Management\", \"Compensation\", \"Employee Relations\"]', 15, 'active', NOW(), NULL),
        ('Financial Analyst Track', 'Career path for finance professionals aiming for senior analyst positions', 'Senior Financial Analyst', '3+ years accounting experience', '[\"Financial Analysis\", \"Reporting\", \"Risk Assessment\"]', 16, 'active', NOW(), NULL),
        ('Data Scientist Career Path', 'Career progression in data science and advanced analytics', 'Lead Data Scientist', '2+ years data analysis experience', '[\"Machine Learning\", \"Data Visualization\", \"Programming\"]', 18, 'active', NOW(), NULL),
        ('Cloud Architect Track', 'Path to becoming a cloud infrastructure architect', 'Cloud Solutions Architect', '4+ years cloud experience', '[\"Cloud Architecture\", \"System Design\", \"Security\"]', 20, 'active', NOW(), NULL),
        ('Business Analyst Track', 'Career development for business analysis and consulting roles', 'Senior Business Analyst', '2+ years BA experience', '[\"Requirements Analysis\", \"Process Improvement\", \"Communication\"]', 14, 'active', NOW(), NULL),
        ('Quality Assurance Lead', 'Leadership track for QA and quality management professionals', 'QA Manager', '3+ years QA experience', '[\"Quality Management\", \"Team Leadership\", \"Process Improvement\"]', 15, 'active', NOW(), NULL)";
        
        $pdo->exec($careerSql);
        echo "Career paths loaded successfully!<br>";
    }
    
    // Check if leadership_programs has data
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM leadership_programs');
    $leadershipCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    if ($leadershipCount === 0) {
        // Insert Leadership Programs
        $leadershipSql = "INSERT INTO leadership_programs (name, description, level, focus_area, duration_weeks, target_audience, outcomes, status, created_at, cover_photo) VALUES
        ('Executive Leadership Program', 'Comprehensive program for developing executive-level leaders', 'Executive', 'Strategic Leadership', 8, 'C-level Executives', '[\"Strategic Vision\", \"Executive Decision Making\", \"Organizational Strategy\"]', 'active', NOW(), NULL),
        ('Middle Management Excellence', 'Designed for middle managers to enhance leadership capabilities', 'Mid-Level', 'Team Management', 6, 'Middle Managers', '[\"Team Leadership\", \"Performance Management\", \"Delegation\"]', 'active', NOW(), NULL),
        ('Emerging Leaders Program', 'Program for high-potential employees identified as future leaders', 'Foundation', 'Leadership Foundations', 5, 'High Potential Employees', '[\"Leadership Skills\", \"Self Awareness\", \"Communication\"]', 'active', NOW(), NULL),
        ('Strategic Leadership Development', 'Focus on strategic thinking and long-term organizational planning', 'Executive', 'Strategic Thinking', 8, 'Senior Leaders', '[\"Strategic Planning\", \"Competitive Analysis\", \"Market Analysis\"]', 'active', NOW(), NULL),
        ('Leadership Communication Workshop', 'Advanced communication skills for leaders across all levels', 'Mid-Level', 'Communication', 4, 'All Leaders', '[\"Executive Communication\", \"Presentation Skills\", \"Persuasion\"]', 'active', NOW(), NULL),
        ('Change Management & Leadership', 'Guide organizational change as an effective leader', 'Mid-Level', 'Change Leadership', 6, 'Change Agents', '[\"Change Management\", \"Resistance Management\", \"Stakeholder Engagement\"]', 'active', NOW(), NULL),
        ('Emotional Intelligence for Leaders', 'Develop emotional intelligence to improve team dynamics', 'Foundation', 'Self Development', 5, 'Emerging Leaders', '[\"Self Awareness\", \"Empathy\", \"Relationship Management\"]', 'active', NOW(), NULL),
        ('Decision Making for Leaders', 'Learn frameworks for making strategic business decisions', 'Mid-Level', 'Critical Thinking', 5, 'Team Leads', '[\"Decision Frameworks\", \"Risk Assessment\", \"Problem Solving\"]', 'active', NOW(), NULL),
        ('Servant Leadership Model', 'Leadership approach focused on serving and supporting team members', 'Foundation', 'Leadership Philosophy', 4, 'New Leaders', '[\"Service Leadership\", \"Empowerment\", \"Team Development\"]', 'active', NOW(), NULL),
        ('Crisis Leadership & Resilience', 'Lead effectively during challenging times and organizational crises', 'Executive', 'Crisis Management', 6, 'Senior Leaders', '[\"Crisis Response\", \"Decision Making\", \"Team Stabilization\"]', 'active', NOW(), NULL),
        ('Diversity & Inclusion Leadership', 'Building inclusive teams and fostering diversity in leadership', 'Mid-Level', 'Diversity', 5, 'All Leaders', '[\"Inclusive Leadership\", \"Bias Awareness\", \"Diversity Strategy\"]', 'active', NOW(), NULL),
        ('Coaching & Mentoring Skills', 'Develop coaches and mentors for employee development', 'Mid-Level', 'Talent Development', 6, 'Managers', '[\"Coaching Skills\", \"Mentoring\", \"Employee Development\"]', 'active', NOW(), NULL),
        ('Vision & Mission Leadership', 'Creating and communicating organizational vision and values', 'Executive', 'Strategic Leadership', 6, 'C-level & Directors', '[\"Vision Creation\", \"Mission Alignment\", \"Cultural Leadership\"]', 'active', NOW(), NULL),
        ('Sustainable Leadership', 'Leadership practices for sustainable and responsible business growth', 'Executive', 'Sustainable Business', 7, 'Senior Leaders', '[\"Sustainability\", \"ESG Leadership\", \"Responsible Growth\"]', 'active', NOW(), NULL)";
        
        $pdo->exec($leadershipSql);
        echo "Leadership programs loaded successfully!<br>";
    }
    
    // Check if team_activities has data
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM team_activities');
    $activitiesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    if ($activitiesCount === 0) {
        // Insert Team Activities
        $activitiesSql = "INSERT INTO team_activities (name, description, activity_date, department, budget, participant_count, status, created_at, cover_photo) VALUES
        ('Annual Team Building Retreat', 'Full-day team building and strategic planning session for all departments', DATE_ADD(NOW(), INTERVAL 30 DAY), 'All Departments', 5000.00, 12, 'planned', NOW(), NULL),
        ('Cross-Functional Collaboration Workshop', 'Workshop to improve communication and collaboration across teams', DATE_ADD(NOW(), INTERVAL 25 DAY), 'HR & Operations', 2500.00, 8, 'planned', NOW(), NULL),
        ('Innovation Hackathon 2026', 'Interactive hackathon to generate innovative ideas for organizational improvements', DATE_ADD(NOW(), INTERVAL 45 DAY), 'Technology', 3500.00, 15, 'planned', NOW(), NULL),
        ('Diversity & Inclusion Initiative', 'Program promoting diversity and inclusive workplace culture', DATE_ADD(NOW(), INTERVAL 15 DAY), 'HR', 1500.00, 20, 'planned', NOW(), NULL),
        ('Wellness & Work-Life Balance Program', 'Comprehensive wellness initiative focusing on employee well-being', DATE_ADD(NOW(), INTERVAL 20 DAY), 'HR', 3000.00, 35, 'planned', NOW(), NULL),
        ('Mentoring Program Launch', 'Formal mentoring program connecting senior and junior staff', DATE_ADD(NOW(), INTERVAL 35 DAY), 'All Departments', 1000.00, 18, 'planned', NOW(), NULL),
        ('Quarterly Knowledge Sharing Session', 'Monthly sessions where employees share expertise and best practices', DATE_ADD(NOW(), INTERVAL 10 DAY), 'Organization', 500.00, 25, 'planned', NOW(), NULL),
        ('Community Outreach Initiative', 'Corporate social responsibility program for community involvement', DATE_ADD(NOW(), INTERVAL 50 DAY), 'CSR', 2000.00, 10, 'planned', NOW(), NULL),
        ('Environmental Sustainability Project', 'Go-green initiative to promote environmental responsibility', DATE_ADD(NOW(), INTERVAL 40 DAY), 'Operations', 4000.00, 22, 'planned', NOW(), NULL),
        ('Leadership Development Circle', 'Monthly discussion group for emerging leaders to develop professionally', DATE_ADD(NOW(), INTERVAL 5 DAY), 'Management', 800.00, 14, 'planned', NOW(), NULL),
        ('Skills Development Workshop Series', 'Multi-week workshop covering essential professional skills', DATE_ADD(NOW(), INTERVAL 22 DAY), 'Training', 3500.00, 30, 'planned', NOW(), NULL),
        ('Employee Recognition Program', 'Celebration and recognition of outstanding employee contributions', DATE_ADD(NOW(), INTERVAL 18 DAY), 'HR', 1200.00, 50, 'planned', NOW(), NULL),
        ('Tech Innovation Lab', 'Collaborative space for exploring emerging technologies', DATE_ADD(NOW(), INTERVAL 60 DAY), 'Technology', 5500.00, 16, 'planned', NOW(), NULL),
        ('Customer Experience Improvement', 'Initiative to enhance customer satisfaction and loyalty', DATE_ADD(NOW(), INTERVAL 28 DAY), 'Operations', 2200.00, 19, 'planned', NOW(), NULL),
        ('Process Improvement Kaizen', 'Continuous improvement methodology implementation sessions', DATE_ADD(NOW(), INTERVAL 32 DAY), 'Operations', 1800.00, 24, 'planned', NOW(), NULL),
        ('Health & Safety Awareness Campaign', 'Comprehensive health, safety, and wellness awareness program', DATE_ADD(NOW(), INTERVAL 12 DAY), 'Safety', 2500.00, 28, 'planned', NOW(), NULL),
        ('Digital Transformation Roadshow', 'Series of sessions introducing digital tools and processes', DATE_ADD(NOW(), INTERVAL 38 DAY), 'Technology', 3200.00, 32, 'planned', NOW(), NULL),
        ('Team Sports & Recreation', 'Organized sports activities and recreation for team bonding', DATE_ADD(NOW(), INTERVAL 8 DAY), 'HR', 2000.00, 40, 'planned', NOW(), NULL),
        ('Performance Excellence Program', 'Program focusing on highest performance standards and metrics', DATE_ADD(NOW(), INTERVAL 42 DAY), 'Management', 2800.00, 21, 'planned', NOW(), NULL),
        ('Social Responsibility Week', 'Week-long initiative for various charitable and social causes', DATE_ADD(NOW(), INTERVAL 55 DAY), 'CSR', 3300.00, 45, 'planned', NOW(), NULL)";
        
        $pdo->exec($activitiesSql);
        echo "Team activities loaded successfully!<br>";
    }
    
    echo "Data loading complete!<br>";
    echo "Career paths: " . $careerCount . "<br>";
    echo "Leadership programs: " . $leadershipCount . "<br>";
    echo "Team activities: " . $activitiesCount . "<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
