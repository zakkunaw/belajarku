CREATE DATABASE IF NOT EXISTS belajarku;

USE belajarku;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    email_verified_at timestamp NULL DEFAULT NULL,
    password varchar(255) NOT NULL,
    remember_token varchar(100) DEFAULT NULL,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
);

-- Create sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id varchar(255) NOT NULL,
    user_id bigint UNSIGNED DEFAULT NULL,
    ip_address varchar(45) DEFAULT NULL,
    user_agent text,
    payload longtext NOT NULL,
    last_activity int NOT NULL,
    PRIMARY KEY (id),
    KEY sessions_user_id_index (user_id),
    KEY sessions_last_activity_index (last_activity)
);

-- Create cache table
CREATE TABLE IF NOT EXISTS cache (
    `key` varchar(255) NOT NULL,
    value mediumtext NOT NULL,
    expiration int NOT NULL,
    PRIMARY KEY (`key`)
);

-- Create cache_locks table
CREATE TABLE IF NOT EXISTS cache_locks (
    `key` varchar(255) NOT NULL,
    owner varchar(255) NOT NULL,
    expiration int NOT NULL,
    PRIMARY KEY (`key`)
);

-- Create jobs table
CREATE TABLE IF NOT EXISTS jobs (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    queue varchar(255) NOT NULL,
    payload longtext NOT NULL,
    attempts tinyint UNSIGNED NOT NULL,
    reserved_at int UNSIGNED DEFAULT NULL,
    available_at int UNSIGNED NOT NULL,
    created_at int UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY jobs_queue_index (queue)
);

-- Create job_batches table
CREATE TABLE IF NOT EXISTS job_batches (
    id varchar(255) NOT NULL,
    name varchar(255) NOT NULL,
    total_jobs int NOT NULL,
    pending_jobs int NOT NULL,
    failed_jobs int NOT NULL,
    failed_job_ids longtext NOT NULL,
    options mediumtext,
    cancelled_at int DEFAULT NULL,
    created_at int NOT NULL,
    finished_at int DEFAULT NULL,
    PRIMARY KEY (id)
);

-- Create failed_jobs table
CREATE TABLE IF NOT EXISTS failed_jobs (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid varchar(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload longtext NOT NULL,
    exception longtext NOT NULL,
    failed_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY failed_jobs_uuid_unique (uuid)
);

-- Create study_sessions table
CREATE TABLE IF NOT EXISTS study_sessions (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id bigint UNSIGNED NOT NULL,
    subject varchar(255) NOT NULL,
    duration_minutes int NOT NULL,
    activities text,
    challenges text,
    learnings text,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY study_sessions_user_id_foreign (user_id),
    CONSTRAINT study_sessions_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Create moods table
CREATE TABLE IF NOT EXISTS moods (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id bigint UNSIGNED NOT NULL,
    session_id bigint UNSIGNED DEFAULT NULL,
    mood_before varchar(255) NOT NULL,
    mood_after varchar(255) DEFAULT NULL,
    notes text,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY moods_user_id_foreign (user_id),
    KEY moods_session_id_foreign (session_id),
    CONSTRAINT moods_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT moods_session_id_foreign FOREIGN KEY (session_id) REFERENCES study_sessions (id) ON DELETE CASCADE
);

-- Create journals table
CREATE TABLE IF NOT EXISTS journals (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id bigint UNSIGNED NOT NULL,
    session_id bigint UNSIGNED DEFAULT NULL,
    title varchar(255) NOT NULL,
    content text NOT NULL,
    ai_feedback text,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY journals_user_id_foreign (user_id),
    KEY journals_session_id_foreign (session_id),
    CONSTRAINT journals_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT journals_session_id_foreign FOREIGN KEY (session_id) REFERENCES study_sessions (id) ON DELETE SET NULL
);

-- Create goals table
CREATE TABLE IF NOT EXISTS goals (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id bigint UNSIGNED NOT NULL,
    title varchar(255) NOT NULL,
    description text,
    target_date date DEFAULT NULL,
    status varchar(255) NOT NULL DEFAULT 'active',
    progress_percentage int NOT NULL DEFAULT 0,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY goals_user_id_foreign (user_id),
    CONSTRAINT goals_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Create milestones table
CREATE TABLE IF NOT EXISTS milestones (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    goal_id bigint UNSIGNED NOT NULL,
    title varchar(255) NOT NULL,
    description text,
    target_date date DEFAULT NULL,
    status varchar(255) NOT NULL DEFAULT 'pending',
    order_index int NOT NULL DEFAULT 0,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY milestones_goal_id_foreign (goal_id),
    CONSTRAINT milestones_goal_id_foreign FOREIGN KEY (goal_id) REFERENCES goals (id) ON DELETE CASCADE
);

-- Create tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    milestone_id bigint UNSIGNED NOT NULL,
    title varchar(255) NOT NULL,
    description text,
    status varchar(255) NOT NULL DEFAULT 'pending',
    due_date date DEFAULT NULL,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY tasks_milestone_id_foreign (milestone_id),
    CONSTRAINT tasks_milestone_id_foreign FOREIGN KEY (milestone_id) REFERENCES milestones (id) ON DELETE CASCADE
);

-- Create migrations table
CREATE TABLE IF NOT EXISTS migrations (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    migration varchar(255) NOT NULL,
    batch int NOT NULL,
    PRIMARY KEY (id)
);

-- Insert migration records
INSERT IGNORE INTO
    migrations (migration, batch)
VALUES (
        '0001_01_01_000000_create_users_table',
        1
    ),
    (
        '0001_01_01_000001_create_cache_table',
        1
    ),
    (
        '0001_01_01_000002_create_jobs_table',
        1
    ),
    (
        '2025_09_01_000001_create_study_sessions_table',
        1
    ),
    (
        '2025_09_01_000002_create_moods_table',
        1
    ),
    (
        '2025_09_01_000003_create_journals_table',
        1
    ),
    (
        '2025_09_01_000004_create_goals_table',
        1
    ),
    (
        '2025_09_01_000005_create_milestones_table',
        1
    ),
    (
        '2025_09_01_000006_create_tasks_table',
        1
    );

SELECT 'All tables created successfully!' as message;