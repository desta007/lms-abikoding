<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Level;
use App\Models\Course;
use App\Models\CourseRating;
use App\Models\CourseEnrollment;
use App\Models\CourseView;
use App\Models\Chapter;
use App\Models\ChapterMaterial;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\Broadcast;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Certificate;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ExamAttempt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Categories
        $this->call(CategorySeeder::class);
        
        // Seed Levels
        $this->call(LevelSeeder::class);

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@lms-dctech.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'whatsapp_number' => '081234567890',
        ]);

        // Create Instructor Users
        $instructors = User::factory(5)->create([
            'role' => 'instructor',
        ]);

        // Create Student Users
        $students = User::factory(20)->create([
            'role' => 'student',
        ]);

        // Get Categories and Levels
        $categories = Category::all();
        $levels = Level::all();

        // Course titles and descriptions for Flutter and Web Development
        // Expanded list with unique course names
        $flutterCourses = [
            ['title' => 'Flutter Dasar untuk Pemula', 'subtitle' => 'Pelajari dasar-dasar Flutter dari nol hingga membuat aplikasi pertama'],
            ['title' => 'Flutter State Management dengan Provider', 'subtitle' => 'Kuasi teknik state management modern dengan Provider pattern'],
            ['title' => 'Flutter UI/UX Design Patterns', 'subtitle' => 'Buat UI yang menarik dengan Material Design dan Cupertino'],
            ['title' => 'Flutter API Integration & HTTP', 'subtitle' => 'Integrasikan aplikasi Flutter dengan REST API dan backend'],
            ['title' => 'Flutter Advanced: Animations & Custom Widgets', 'subtitle' => 'Tingkatkan skill dengan animasi dan custom widgets'],
            ['title' => 'Flutter Firebase Integration', 'subtitle' => 'Gunakan Firebase untuk authentication, database, dan cloud functions'],
            ['title' => 'Flutter BLoC Pattern Mastery', 'subtitle' => 'Pelajari arsitektur BLoC untuk state management yang scalable'],
            ['title' => 'Flutter Riverpod State Management', 'subtitle' => 'Master Riverpod untuk state management yang powerful dan modern'],
            ['title' => 'Flutter Database dengan SQLite', 'subtitle' => 'Implementasi local database menggunakan SQLite di Flutter'],
            ['title' => 'Flutter Testing & Quality Assurance', 'subtitle' => 'Pelajari unit testing, widget testing, dan integration testing'],
            ['title' => 'Flutter Performance Optimization', 'subtitle' => 'Optimalkan performa aplikasi Flutter untuk pengalaman terbaik'],
            ['title' => 'Flutter CI/CD & Deployment', 'subtitle' => 'Setup continuous integration dan deploy ke Play Store & App Store'],
            ['title' => 'Flutter Architecture Patterns', 'subtitle' => 'Pelajari clean architecture, MVVM, dan design patterns terbaik'],
            ['title' => 'Flutter Multi-platform Development', 'subtitle' => 'Bangun aplikasi untuk mobile, web, dan desktop dengan satu codebase'],
            ['title' => 'Flutter Real-time Apps dengan WebSocket', 'subtitle' => 'Buat aplikasi real-time dengan WebSocket dan Socket.io'],
        ];

        $webDevCourses = [
            ['title' => 'HTML, CSS & JavaScript Fundamentals', 'subtitle' => 'Pelajari dasar-dasar web development dengan HTML, CSS, dan JavaScript'],
            ['title' => 'React.js untuk Pemula', 'subtitle' => 'Bangun aplikasi web modern dengan React.js framework'],
            ['title' => 'Vue.js Complete Guide', 'subtitle' => 'Master Vue.js dari dasar hingga advanced concepts'],
            ['title' => 'Laravel Backend Development', 'subtitle' => 'Bangun RESTful API dan backend dengan Laravel PHP framework'],
            ['title' => 'Full Stack Development dengan MERN', 'subtitle' => 'Buat aplikasi full stack dengan MongoDB, Express, React, dan Node.js'],
            ['title' => 'Next.js & TypeScript Mastery', 'subtitle' => 'Bangun aplikasi web production-ready dengan Next.js dan TypeScript'],
            ['title' => 'Angular Framework Complete Course', 'subtitle' => 'Pelajari Angular dari dasar hingga membuat aplikasi enterprise'],
            ['title' => 'Node.js & Express.js Backend', 'subtitle' => 'Bangun server-side aplikasi dengan Node.js dan Express framework'],
            ['title' => 'MongoDB Database Mastery', 'subtitle' => 'Pelajari NoSQL database MongoDB untuk aplikasi modern'],
            ['title' => 'PostgreSQL & SQL Advanced', 'subtitle' => 'Master relational database dengan PostgreSQL dan SQL queries'],
            ['title' => 'GraphQL API Development', 'subtitle' => 'Bangun API modern dengan GraphQL untuk query yang efisien'],
            ['title' => 'Docker & Kubernetes Deployment', 'subtitle' => 'Deploy aplikasi web dengan containerization dan orchestration'],
            ['title' => 'AWS Cloud Services untuk Web Apps', 'subtitle' => 'Deploy dan scale aplikasi web menggunakan Amazon Web Services'],
            ['title' => 'Web Security & Authentication', 'subtitle' => 'Implementasi keamanan web, JWT, OAuth, dan best practices'],
            ['title' => 'Progressive Web Apps (PWA)', 'subtitle' => 'Buat aplikasi web yang bekerja seperti native apps'],
        ];

        // Track all enrollments for certificate generation
        $allEnrollments = [];
        
        // Track used course titles to ensure uniqueness
        $usedFlutterTitles = [];
        $usedWebDevTitles = [];

        // Create Courses for each instructor
        foreach ($instructors as $instructor) {
            $courseCount = rand(2, 5);
            
            for ($i = 0; $i < $courseCount; $i++) {
                $category = $categories->random();
                $isFlutter = $category->name === 'Mobile Flutter Development';
                
                // Get available courses (not used yet)
                if ($isFlutter) {
                    $availableCourses = array_filter($flutterCourses, function($course) use ($usedFlutterTitles) {
                        return !in_array($course['title'], $usedFlutterTitles);
                    });
                    
                    // If all courses used, reset and start over
                    if (empty($availableCourses)) {
                        $usedFlutterTitles = [];
                        $availableCourses = $flutterCourses;
                    }
                    
                    $availableCourses = array_values($availableCourses);
                    $courseData = $availableCourses[array_rand($availableCourses)];
                    $usedFlutterTitles[] = $courseData['title'];
                } else {
                    $availableCourses = array_filter($webDevCourses, function($course) use ($usedWebDevTitles) {
                        return !in_array($course['title'], $usedWebDevTitles);
                    });
                    
                    // If all courses used, reset and start over
                    if (empty($availableCourses)) {
                        $usedWebDevTitles = [];
                        $availableCourses = $webDevCourses;
                    }
                    
                    $availableCourses = array_values($availableCourses);
                    $courseData = $availableCourses[array_rand($availableCourses)];
                    $usedWebDevTitles[] = $courseData['title'];
                }
                
                $baseSlug = Str::slug($courseData['title']);
                // Add instructor ID and random number to ensure uniqueness
                $uniqueSlug = $baseSlug . '-' . $instructor->id . '-' . uniqid();
                
                $course = Course::create([
                    'title' => $courseData['title'],
                    'subtitle' => $courseData['subtitle'],
                    'slug' => $uniqueSlug,
                    'description' => $isFlutter 
                        ? 'Kursus lengkap untuk mempelajari pengembangan aplikasi mobile dengan Flutter. Dari dasar hingga advanced, pelajari semua yang dibutuhkan untuk menjadi Flutter developer profesional.'
                        : 'Kursus komprehensif untuk mempelajari pengembangan aplikasi web modern. Pelajari teknologi terbaru dan best practices dalam web development.',
                    'about_course' => $isFlutter
                        ? 'Dalam kursus ini, Anda akan mempelajari Flutter dari dasar hingga tingkat lanjut. Mulai dari setup environment, memahami widget system, state management, hingga deployment aplikasi ke Play Store dan App Store. Kursus ini dirancang untuk pemula yang ingin menjadi Flutter developer profesional.'
                        : 'Kursus ini akan membawa Anda dari pemula hingga menjadi web developer yang handal. Pelajari HTML, CSS, JavaScript, framework modern seperti React atau Vue, serta backend development. Dengan project-based learning, Anda akan membuat aplikasi web yang nyata dan siap digunakan.',
                    'about_instructor' => fake()->paragraphs(2, true),
                    'category_id' => $category->id,
                    'level_id' => $levels->random()->id,
                    'instructor_id' => $instructor->id,
                    'price' => rand(0, 1) ? rand(50000, 500000) : 0,
                    'language' => 'Indonesian',
                    'is_published' => rand(0, 1),
                    'thumbnail' => null,
                ]);

                // Create Chapters for each course
                $chapterCount = rand(3, 8);
                for ($j = 0; $j < $chapterCount; $j++) {
                    $chapter = Chapter::create([
                        'course_id' => $course->id,
                        'title' => "Chapter " . ($j + 1) . ": " . fake()->sentence(3),
                        'description' => fake()->paragraph(),
                        'order' => $j + 1,
                        'is_published' => true,
                    ]);

                    // Create Materials for each chapter
                    $materialCount = rand(2, 5);
                    $materialTypes = ['pdf', 'image', 'video', 'audio', 'text'];
                    
                    for ($k = 0; $k < $materialCount; $k++) {
                        ChapterMaterial::create([
                            'chapter_id' => $chapter->id,
                            'material_type' => $materialTypes[array_rand($materialTypes)],
                            'title' => fake()->sentence(3),
                            'content' => fake()->paragraph(),
                            'order' => $k + 1,
                            'duration' => rand(120, 3600),
                        ]);
                    }
                }

                // Create Enrollments
                $enrollmentCount = rand(5, 15);
                $selectedStudents = $students->random(min($enrollmentCount, $students->count()));
                
                foreach ($selectedStudents as $student) {
                    $enrollment = CourseEnrollment::create([
                        'course_id' => $course->id,
                        'user_id' => $student->id,
                        'progress_percentage' => rand(0, 100),
                    ]);
                    
                    $allEnrollments[] = $enrollment;

                    // Create Course Views
                    CourseView::create([
                        'course_id' => $course->id,
                        'user_id' => $student->id,
                        'ip_address' => fake()->ipv4(),
                    ]);
                }

                // Create Course Ratings
                $ratingCount = rand(3, 10);
                $ratingStudents = $students->random(min($ratingCount, $students->count()));
                
                foreach ($ratingStudents as $student) {
                    CourseRating::create([
                        'course_id' => $course->id,
                        'user_id' => $student->id,
                        'rating' => rand(3, 5),
                        'review' => fake()->paragraph(),
                    ]);
                }

                // Create Exam for some courses
                if (rand(0, 1)) {
                    $exam = Exam::create([
                        'course_id' => $course->id,
                        'title' => 'Test: ' . $course->title,
                        'description' => fake()->paragraph(),
                        'is_active' => true,
                        'duration_minutes' => rand(30, 120),
                    ]);

                    // Create questions for exam
                    $questionCount = rand(5, 15);
                    for ($q = 0; $q < $questionCount; $q++) {
                        $question = Question::create([
                            'exam_id' => $exam->id,
                            'question_text' => fake()->sentence() . '?',
                            'question_type' => ['multiple_choice', 'true_false'][array_rand(['multiple_choice', 'true_false'])],
                            'points' => rand(1, 5),
                            'order' => $q + 1,
                        ]);

                        // Create answers
                        if ($question->question_type === 'multiple_choice') {
                            $correctAnswerIndex = rand(0, 3);
                            for ($a = 0; $a < 4; $a++) {
                                Answer::create([
                                    'question_id' => $question->id,
                                    'answer_text' => fake()->sentence(),
                                    'is_correct' => $a === $correctAnswerIndex,
                                    'order' => $a + 1,
                                ]);
                            }
                        } else {
                            Answer::create([
                                'question_id' => $question->id,
                                'answer_text' => 'True',
                                'is_correct' => rand(0, 1) === 1,
                                'order' => 1,
                            ]);
                            Answer::create([
                                'question_id' => $question->id,
                                'answer_text' => 'False',
                                'is_correct' => false,
                                'order' => 2,
                            ]);
                        }
                    }
                }
            }
        }

        // Create User Profiles
        User::all()->each(function($user) {
            if (!$user->profile) {
                \App\Models\UserProfile::create([
                    'user_id' => $user->id,
                    'bio' => fake()->paragraph(),
                    'points' => rand(0, 500),
                ]);
            }
        });

        // Create some Community Posts
        foreach ($students->take(10) as $student) {
            $postCount = rand(1, 5);
            for ($i = 0; $i < $postCount; $i++) {
                $post = Post::create([
                    'user_id' => $student->id,
                    'content' => fake()->paragraph(),
                    'post_type' => ['text', 'text', 'text', 'link'][array_rand(['text', 'text', 'text', 'link'])],
                    'is_public' => true,
                ]);

                // Create some likes
                $likeCount = rand(0, 10);
                $likers = $students->random(min($likeCount, $students->count()));
                foreach ($likers as $liker) {
                    PostLike::create([
                        'post_id' => $post->id,
                        'user_id' => $liker->id,
                    ]);
                }

                // Create some comments
                $commentCount = rand(0, 5);
                $commenters = $students->random(min($commentCount, $students->count()));
                foreach ($commenters as $commenter) {
                    PostComment::create([
                        'post_id' => $post->id,
                        'user_id' => $commenter->id,
                        'content' => fake()->sentence(),
                    ]);
                }
            }
        }

        // Create some Broadcasts
        foreach ($instructors->take(3) as $instructor) {
            Broadcast::create([
                'user_id' => $instructor->id,
                'title' => fake()->sentence(),
                'description' => fake()->paragraph(),
                'is_live' => rand(0, 1) === 1,
            ]);
        }

        // Create some Events
        foreach ($instructors->take(3) as $instructor) {
            $event = Event::create([
                'user_id' => $instructor->id,
                'title' => fake()->sentence(),
                'description' => fake()->paragraph(),
                'event_type' => ['online', 'offline', 'hybrid'][array_rand(['online', 'offline', 'hybrid'])],
                'start_date' => now()->addDays(rand(1, 30)),
                'end_date' => now()->addDays(rand(31, 60)),
                'max_attendees' => rand(10, 100),
                'is_public' => true,
            ]);

            // Create some attendees
            $attendeeCount = rand(0, 10);
            $attendees = $students->random(min($attendeeCount, $students->count()));
            foreach ($attendees as $attendee) {
                EventAttendee::create([
                    'event_id' => $event->id,
                    'user_id' => $attendee->id,
                    'status' => 'registered',
                ]);
            }
        }

        // Get all courses for comments
        $courses = Course::all();

        // Create some Comments on course materials
        foreach ($courses as $course) {
            $firstChapter = $course->chapters()->first();
            if ($firstChapter) {
                $firstMaterial = $firstChapter->materials()->first();
                if ($firstMaterial) {
                    $commentCount = rand(0, 5);
                    $commenters = $students->random(min($commentCount, $students->count()));
                    foreach ($commenters as $commenter) {
                        Comment::create([
                            'chapter_id' => $firstChapter->id,
                            'chapter_material_id' => $firstMaterial->id,
                            'user_id' => $commenter->id,
                            'content' => fake()->sentence(),
                            'is_approved' => true,
                        ]);
                    }
                }
            }
        }

        // Create some Certificates for completed courses
        foreach ($allEnrollments as $enrollment) {
            if ($enrollment->progress_percentage >= 100) {
                Certificate::create([
                    'course_enrollment_id' => $enrollment->id,
                    'user_id' => $enrollment->user_id,
                    'course_id' => $enrollment->course_id,
                ]);
            }
        }
    }
}
