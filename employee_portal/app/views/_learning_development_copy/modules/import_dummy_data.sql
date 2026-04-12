SET FOREIGN_KEY_CHECKS=0;

-- Clear existing data
DELETE FROM training_enrollments WHERE 1=1;
DELETE FROM leadership_enrollments WHERE 1=1;
DELETE FROM team_activity_participants WHERE 1=1;
DELETE FROM individual_development_plans WHERE 1=1;
DELETE FROM user_competencies WHERE 1=1;
DELETE FROM compliance_assignments WHERE 1=1;
DELETE FROM lms_enrollments WHERE 1=1;
DELETE FROM feedback_360 WHERE 1=1;
DELETE FROM performance_reviews WHERE 1=1;
DELETE FROM training_programs WHERE 1=1;
DELETE FROM career_paths WHERE 1=1;
DELETE FROM leadership_programs WHERE 1=1;
DELETE FROM team_activities WHERE 1=1;
DELETE FROM competencies WHERE 1=1;
DELETE FROM compliance_trainings WHERE 1=1;
DELETE FROM lms_courses WHERE 1=1;

-- Now import the dummy data
