-- Run this AFTER schema.sql
USE storyspill_db1;

-- Clear existing data (safe for re-seed)
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM reports;
DELETE FROM reviews;
DELETE FROM wishlist;
DELETE FROM books;
DELETE FROM genres;
DELETE FROM users;
DELETE FROM admins;
SET FOREIGN_KEY_CHECKS = 1;

-- ========== ADMIN (username: mahima, password: mahi) ==========
INSERT INTO admins (username, password) VALUES
('mahima', '$2y$10$f.VLhVyqAnb9GaNoAmmLMuwrBZBRYRL1TXcXhkAHOIf3CyWT6vru6');

-- ========== 12 USERS (password = their name) ==========
INSERT INTO users (id, name, email, password, last_activity) VALUES
(1,  'Diya',     'diya@gmail.com',     '$2y$10$KFiEXJXh14Qv3lBJ49.tHuF21PEqEa3ooCGu7NbS/fOWiMOudAYLS', NOW()),
(2,  'Neha',     'neha@gmail.com',     '$2y$10$G.LLM2F7ZjsG9TfU.j5bcOTPM0Y5zIjkOR7fRy8kRpV.cmBjseV.S', NOW()),
(3,  'Kashish',  'kashish@gmail.com',  '$2y$10$LoillzP1k/hkFySiqSXMLOo9kkPqlvlwgKg3POwRLmsh8bZLEZL2S', NOW()),
(4,  'Himani',   'himani@gmail.com',   '$2y$10$E/LQ51Ah7vZ96EBDtNCpdu1Fv32TEndMmDNIOLASkekEok1TEfeey', NOW()),
(5,  'Shruti',   'shruti@gmail.com',   '$2y$10$8grm8XiJBRTMdrpjFEZSAeeisTegx3P0VcR3pJ.0NWqFEE8OZNKE.', NOW()),
(6,  'Jeevika',  'jeevika@gmail.com',  '$2y$10$C/Gp73TifbjATLOOSRxk5OT82LISZSfXsJWxwvvMcNNaAGzPA12xq', NOW()),
(7,  'Priya',    'priya@gmail.com',    '$2y$10$VuhLyO9Tj35RQNLC7JiQ7OZcp.BbUd4FTBeUyYHpC1c2FoDLf0RJq', NOW()),
(8,  'Soni',     'soni@gmail.com',     '$2y$10$ngDO/ciFNcUb4o/7B/cIO.DE48OSI.QSAxKi0PTys9aLUkCsFENC2', NOW()),
(9,  'Brijesh',  'brijesh@gmail.com',  '$2y$10$IArKBx2grtOHucTsg9.yHOTZutZSCIUGzayha4NvB2DL7mOKdzPy6', NOW()),
(10, 'Shani',    'shani@gmail.com',    '$2y$10$t6YxJlDb2PHDh6M8ckzPXe4RyJaJH4tZJW.Osagb2Dx50OAPdPyYO', NOW()),
(11, 'Amit',     'amit@gmail.com',     '$2y$10$t2oXxhiIFMFjeoGPzcBNEuWPbOiBq/9wDE/t7KMub8yWUFGO2ZXcC', NOW()),
(12, 'Adhvik',   'adhvik@gmail.com',   '$2y$10$k8gQj04WnJh2rSgUd1Ve1u4WVd5Ails20bemkZvpwejdYOM8HzvJe', NOW());



-- ========== GENRES (master list) ==========
INSERT INTO genres (id, name, description) VALUES
(1,  'Fiction', 'Imaginative narrative literature'),
(2,  'Non-Fiction', 'Factual and informational writing'),
(3,  'Mystery', 'Suspense and detective stories'),
(4,  'Thriller', 'High-stakes suspense fiction'),
(5,  'Romance', 'Love and relationship centered stories'),
(6,  'Fantasy', 'Magic, myth, and invented worlds'),
(7,  'Science Fiction', 'Futuristic and speculative science'),
(8,  'Philosophy', 'Ideas about existence and knowledge'),
(9,  'Business', 'Entrepreneurship and management'),
(10, 'Self-Help', 'Personal growth and habits'),
(11, 'History & Science', 'Past events and scientific ideas'),
(12, 'AI & Tech', 'Artificial intelligence and technology'),
(13, 'Life & Philosophy', 'Everyday wisdom and reflection'),
(14, 'Mind & Psychology', 'Human mind and behavior'),
(15, 'Money & Economics', 'Finance and economic systems'),
(16, 'India', 'Stories and topics focused on India'),
(17, 'Culture & Society', 'Social norms and cultural study'),
(18, 'World & Politics', 'Global affairs and governance'),
(19, 'Learning & Growth', 'Education and skill building'),
(20, 'Business & Startups', 'Startup culture and innovation');


-- ========== BOOKS (each user uploads from different genres + extra books) ==========
-- Sample public videos for hover demo (short creative commons / sample clips)
-- Using reliable sample MP4 URLs

INSERT INTO books (user_id, title, author, genre, cover_image, intro_video, pdf_path) VALUES
-- Diya - Fiction
(1, 'The Last Library on Earth', 'Elena Voss', 'Fiction',
 'https://picsum.photos/seed/book1/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Neha - Non-Fiction
(2, 'Quiet Power of Ordinary Days', 'Marcus Hale', 'Non-Fiction',
 'https://picsum.photos/seed/book2/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Kashish - Mystery
(3, 'The Clockmaker''s Secret', 'R. J. Thorne', 'Mystery',
 'https://picsum.photos/seed/book3/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Himani - Thriller
(4, 'Night Train to Nowhere', 'S. K. Mercer', 'Thriller',
 'https://picsum.photos/seed/book4/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Shruti - Romance
(5, 'Letters We Never Sent', 'Amelia Croft', 'Romance',
 'https://picsum.photos/seed/book5/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Jeevika - Fantasy
(6, 'Kingdom of Ash and Glass', 'Liora Vale', 'Fantasy',
 'https://picsum.photos/seed/book6/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Priya - Science Fiction
(7, 'Orbit of Forgotten Stars', 'Kai Nakamura', 'Science Fiction',
 'https://picsum.photos/seed/book7/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Soni - Philosophy
(8, 'The Art of Being Unreachable', 'Alexis McElroy', 'Philosophy',
 'https://picsum.photos/seed/book8/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Brijesh - Business
(9, 'Manufacturing Is a War Now', 'Rohan Mehta', 'Business',
 'https://picsum.photos/seed/book9/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WhatCarCanYouGetForAGrand.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Shani - Self-Help
(10, 'How to Force Your Brain to Crave Hard Things', 'Dr. Anya Shah', 'Self-Help',
 'https://picsum.photos/seed/book10/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Amit - History & Science style
(11, 'Defending Our Consciousness Against the Algorithms', 'Michael Pollan', 'History & Science',
 'https://picsum.photos/seed/book11/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Adhvik - AI & Tech
(12, 'Why AI Needs a Sense of Smell', 'Philip Ball', 'AI & Tech',
 'https://picsum.photos/seed/book12/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

-- Extra books for fuller shelf (same users, more genres)
(1, 'Modern Sisyphus: Corporate Absurdity', 'David Russell', 'Life & Philosophy',
 'https://picsum.photos/seed/book13/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(2, 'Eight Patterns of Miserable People', 'Nina Cole', 'Mind & Psychology',
 'https://picsum.photos/seed/book14/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(3, 'No Country Ever Got Rich From Tourism', 'E. R. Santos', 'Money & Economics',
 'https://picsum.photos/seed/book15/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(4, 'The Only Indian City Where Life Improved', 'Vikram Das', 'India',
 'https://picsum.photos/seed/book16/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(5, 'Make Nothing That Isn''t Beautiful', 'John Michael Co', 'Culture & Society',
 'https://picsum.photos/seed/book17/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(6, 'CICADA 3301: An Internet Mystery', 'Unknown', 'AI & Tech',
 'https://picsum.photos/seed/book18/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(7, 'Undercover in North Korea', 'A. Reed', 'World & Politics',
 'https://picsum.photos/seed/book19/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(8, 'Marcus Aurelius: The Man Who Solved the Universe', 'S. Grant', 'Philosophy',
 'https://picsum.photos/seed/book20/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WhatCarCanYouGetForAGrand.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(9, 'Happy Ambition', 'Tara Singh', 'Learning & Growth',
 'https://picsum.photos/seed/book21/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(10, 'The Epidemic of Constant Communication', 'L. Park', 'Culture & Society',
 'https://picsum.photos/seed/book22/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(11, 'The Strange Freedom of Knowing Nothing', 'H. Bloom', 'Life & Philosophy',
 'https://picsum.photos/seed/book23/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(12, 'Machines of Loving Grace', 'Richard Brautigan', 'AI & Tech',
 'https://picsum.photos/seed/book24/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(1, 'Why The U.S. President Is Almost Impossible To Kill', 'Security Desk', 'World & Politics',
 'https://picsum.photos/seed/book25/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(2, 'The Puzzle of Pakistan''s Poverty', 'A. Khan', 'Money & Economics',
 'https://picsum.photos/seed/book26/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(3, 'Make Something Heavy', 'Anu Atluru', 'Business & Startups',
 'https://picsum.photos/seed/book27/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(4, 'Everyone Is Numbing Out', 'Cathie Wood', 'Mind & Psychology',
 'https://picsum.photos/seed/book28/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(5, 'The Poetic Beauty of Nature', 'James Lovelock', 'History & Science',
 'https://picsum.photos/seed/book29/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'),

(6, 'You Have to Be Serious', 'Unknown', 'Learning & Growth',
 'https://picsum.photos/seed/book30/400/600',
 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4',
 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf');
-- ========== REVIEWS ==========
INSERT INTO reviews (user_id, book_id, rating, review_text) VALUES
(1, 1, 5, 'A beautiful take on libraries and memory.'),
(2, 1, 4, 'Loved the atmosphere and pacing.'),
(3, 2, 5, 'Quiet and powerful — highly recommend.'),
(4, 3, 4, 'Clever mystery with a satisfying end.'),
(5, 4, 3, 'Good thriller, a bit rushed at the finish.'),
(6, 5, 5, 'Letters that feel painfully real.'),
(7, 6, 4, 'Epic fantasy world-building.'),
(8, 7, 5, 'Sci-fi with heart.'),
(9, 8, 4, 'Thought-provoking philosophy.'),
(10, 2, 5, 'One of my favourite reads this year.'),
(11, 3, 4, 'Solid plot twists.'),
(12, 5, 5, 'Could not put it down.');

-- ========== REPORTS (sample moderation queue) ==========
INSERT INTO reports (reporter_id, book_id, reason, status) VALUES
(2, 4, 'Possible copyright concern on sample PDF', 'pending'),
(5, 7, 'Misleading genre tag', 'reviewed'),
(8, 1, 'Duplicate listing suspected', 'pending'),
(11, 6, 'Broken intro video link', 'resolved');
