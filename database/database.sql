CREATE DATABASE IF NOT EXISTS ar_tourism
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ar_tourism;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) UNIQUE NOT NULL,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE destinations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,
    name VARCHAR(160) NOT NULL,
    slug VARCHAR(180) UNIQUE NOT NULL,
    short_description VARCHAR(300),
    description TEXT,
    cover_image VARCHAR(255),
    location VARCHAR(190),
    latitude DECIMAL(10, 7) NULL,
    longitude DECIMAL(10, 7) NULL,
    maps_url VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX (status),
    INDEX (name)
);

CREATE TABLE attractions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destination_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NULL,
    name VARCHAR(160) NOT NULL,
    slug VARCHAR(180) UNIQUE NOT NULL,
    short_description VARCHAR(300),
    description TEXT,
    main_image VARCHAR(255),
    youtube_url VARCHAR(500),
    website_url VARCHAR(500),
    maps_url VARCHAR(500),
    latitude DECIMAL(10, 7) NULL,
    longitude DECIMAL(10, 7) NULL,
    opening_hours VARCHAR(255),
    entry_information VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX (status),
    INDEX (destination_id)
);

CREATE TABLE attraction_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attraction_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attraction_id) REFERENCES attractions(id) ON DELETE CASCADE
);

CREATE TABLE ar_posters (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT,
    youtube_url VARCHAR(500),
    target_file VARCHAR(255),
    target_status ENUM('NOT_COMPILED', 'READY') DEFAULT 'NOT_COMPILED',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE ar_hotspots (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    poster_id INT UNSIGNED NOT NULL,
    attraction_id INT UNSIGNED NULL,
    label VARCHAR(100) NOT NULL,
    x DECIMAL(7, 5) NOT NULL DEFAULT 0.1,
    y DECIMAL(7, 5) NOT NULL DEFAULT 0.1,
    width DECIMAL(7, 5) NOT NULL DEFAULT 0.2,
    height DECIMAL(7, 5) NOT NULL DEFAULT 0.15,
    z_index INT DEFAULT 1,
    content_type ENUM('info', 'video') DEFAULT 'info',
    video_url VARCHAR(500),
    FOREIGN KEY (poster_id) REFERENCES ar_posters(id) ON DELETE CASCADE,
    FOREIGN KEY (attraction_id) REFERENCES attractions(id) ON DELETE SET NULL
);

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    description VARCHAR(500),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password_hash)
VALUES (
    'Administrator',
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro3s5pKcN6iR8bM8Y1rXq5lK'
);

INSERT INTO categories (name, slug)
VALUES
    ('Historical', 'historical'),
    ('Nature', 'nature'),
    ('Cultural', 'cultural'),
    ('Food', 'food');

INSERT INTO destinations (
    category_id,
    name,
    slug,
    short_description,
    description,
    location,
    status
)
VALUES
    (
        1,
        'Heritage District',
        'heritage-district',
        'Stories, architecture and living culture.',
        'Explore a walkable district filled with local history, crafts and community stories.',
        'Demo City',
        'active'
    ),
    (
        2,
        'Nature Escape',
        'nature-escape',
        'Green trails and open skies.',
        'A peaceful destination for families and outdoor explorers.',
        'Demo City',
        'active'
    );

INSERT INTO attractions (
    destination_id,
    category_id,
    name,
    slug,
    short_description,
    description,
    status
)
VALUES
    (
        1,
        1,
        'Heritage Museum',
        'heritage-museum',
        'A hands-on journey through local history.',
        'Discover objects, photographs and stories preserved by the community.',
        'active'
    ),
    (
        2,
        2,
        'Forest Lookout',
        'forest-lookout',
        'A panoramic nature viewpoint.',
        'Follow the trail to a scenic lookout and enjoy the landscape.',
        'active'
    );
