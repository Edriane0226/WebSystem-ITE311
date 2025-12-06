<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home Page
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');

// About Page
$routes->get('/about', 'Home::about');

// Contact Page
$routes->get('/contact', 'Home::contact');

 
//Lab Exe 4

//Register
$routes->get('register', 'Auth::register'); 
$routes->post('register', 'Auth::register');

//Login
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');


//Logout
$routes->get('logout', 'Auth::logout'); 

//Dashboard
$routes->get('dashboard', 'Auth::dashboard'); 

//Course Enroll Lab 6
$routes->post('/course/enroll', 'Course::enroll');

//Midterm Exam Announcements
$routes->get('/announcements', 'Announcement::index');
$routes->group('', ['filter' => 'roleauth'], function($routes) {
    $routes->get('/teacher/dashboard', 'Teacher::dashboard');
    $routes->get('/admin/dashboard', 'Admin::dashboard');
});

//Laboratory Exercise 7 File Upload
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1'); 
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1'); 
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1'); 
$routes->get('/materials/download/(:num)', 'Materials::download/$1'); 

//Laboratory Exercise 8 Notifications
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

//Laboratory Exercise 9
$routes->get('/course/search', 'Course::search');
$routes->post('/course/search', 'Course::search');
$routes->get('/course/(:num)', 'Course::viewCourses/$1');
//Para Ma Fullfil view Details sa course
$routes->get('/courses/(:num)', 'Course::details/$1');

// Sa daan na manual
// $routes->get('/admin', 'AdminController::dashboard'); 
// $routes->get('/teacher', 'TeacherController::dashboard'); 
// $routes->get('/student', 'StudentController::dashboard'); 

//Course Management
$routes->get('/course/manage', 'Course::createCourse'); 
$routes->post('/courses/manage', 'Course::createCourse');

//SetStatus
$routes->post('/course/setStatus/(:num)/', 'Course::setStatus/$1');

//Edit Course
$routes->post('/course/update/(:num)', 'Course::updateCourse/$1');

//User Management
$routes->get('/students/studentManagement', 'UserManage::UserManagement');
$routes->post('/students/addStudent', 'UserManage::addStudent');
$routes->post('/students/update', 'UserManage::update');
$routes->get('/students/getEnrollmentData/(:num)', 'UserManage::getEnrollmentData/$1');
$routes->post('/students/enroll', 'UserManage::enroll');
$routes->delete('/students/unenroll/(:num)', 'UserManage::unenroll/$1');
$routes->post('/students/updateEnrollmentStatus', 'UserManage::updateEnrollmentStatus');
$routes->post('/students/createTeacherEnrollment', 'UserManage::createTeacherEnrollment');
$routes->get('/students/by-course/(:num)', 'UserManage::getStudentsByCourse/$1');