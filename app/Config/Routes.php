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


// Sa daan na manual
// $routes->get('/admin', 'AdminController::dashboard'); 
// $routes->get('/teacher', 'TeacherController::dashboard'); 
// $routes->get('/student', 'StudentController::dashboard'); 