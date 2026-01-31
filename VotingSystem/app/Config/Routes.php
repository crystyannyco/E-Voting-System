<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Dashboard::login');
$routes->post('/login/process', 'Dashboard::processLogin');

// Apply auth filter to all routes except login related ones
$routes->group('', ['filter' => 'admin_auth'], function($routes) {
    $routes->get('dashboard', 'Dashboard::maindashboard');
    $routes->get('profile', 'Dashboard::profile');
    $routes->get('position', 'Dashboard::position');
    $routes->get('partylist', 'Dashboard::partylist');
    $routes->get('election', 'Dashboard::election');
    $routes->get('votes', 'Dashboard::votes');
    $routes->get('student', 'Dashboard::student');
    $routes->get('candidate', 'Dashboard::candidate');
    $routes->get('administrator', 'Dashboard::admin');
    
    // Routes that don't need authentication
    $routes->get('/logout', 'Dashboard::logout');

    $routes->get('/votingsystem', 'Dashboard::login');
    $routes->get('/dashboard', 'Dashboard::maindashboard');
    $routes->get('/profile', 'Dashboard::profile');
    $routes->post('profile/update', 'Dashboard::updateProfile');

    //Student CRUD
    $routes->get('/student', 'Dashboard::student');
    $routes->post('/student/add', 'Dashboard::addStudent');
    $routes->get('/student/get/(:num)', 'Dashboard::getStudent/$1');
    $routes->post('/student/update/(:num)', 'Dashboard::updateStudent/$1');
    $routes->get('/student/delete/(:num)', 'Dashboard::deleteStudent/$1');

    //Position CRUD
    $routes->get('/position', 'Dashboard::position');
    $routes->post('/position/add', 'Dashboard::addPosition');
    $routes->post('/position/update/(:num)', 'Dashboard::updatePosition/$1');
    $routes->get('/position/delete/(:num)', 'Dashboard::deletePosition/$1');

    $routes->get('/partylist', 'Dashboard::partylist');
    $routes->post('/partylist/add', 'Dashboard::addPartylist');
    $routes->post('/partylist/update/(:num)', 'Dashboard::updatePartylist/$1');
    $routes->get('/partylist/delete/(:num)', 'Dashboard::deletePartylist/$1');

    $routes->get('/candidate', 'Dashboard::candidate');
    $routes->post('/candidate/add', 'Dashboard::addCandidate');
    $routes->get('/candidate/get/(:num)', 'Dashboard::getCandidate/$1');
    $routes->post('/candidate/update/(:num)', 'Dashboard::updateCandidate/$1');
    $routes->get('/candidate/delete/(:num)', 'Dashboard::deleteCandidate/$1');

    //Election CRUD
    $routes->get('/election', 'Dashboard::election');
    $routes->post('/election/add', 'Dashboard::addElection');
    $routes->get('/election/get/(:num)', 'Dashboard::getElection/$1'); 
    $routes->post('/election/update/(:num)', 'Dashboard::updateElection/$1');
    $routes->get('/election/delete/(:num)', 'Dashboard::deleteElection/$1');

    $routes->get('/votes', 'Dashboard::votes');
    $routes->get('/dashboard/getElectionVotes/(:num)', 'Dashboard::getElectionVotes/$1');


    $routes->get('/administrator', 'Dashboard::admin');
    $routes->post('/administrator/add', 'Dashboard::addAdministrator');
    $routes->get('/administrator/get/(:num)', 'Dashboard::getAdministrator/$1');
    $routes->post('/administrator/update/(:num)', 'Dashboard::updateAdministrator/$1');
    $routes->get('/administrator/delete/(:num)', 'Dashboard::deleteAdministrator/$1');

    $routes->get('/cert', 'Dashboard::cert');

});

$routes->get('/api/students', 'StudentController::index');
$routes->get('/api/students/(:num)', 'StudentController::show/$1');
$routes->post('/api/student/login', 'StudentController::studentLogin');

$routes->post('/api/student/update-profile', 'StudentController::updateProfile');

$routes->get('/api/elections','StudentController::getAllElectionsInfo');
$routes->get('/api/election-info/(:num)', 'StudentController::getElectionInfo/$1');

$routes->get('/api/candidates','StudentController::getAllCandidatesWithElectionInfo');

$routes->get('/api/candidate/(:num)', 'StudentController::getCandidateWithElectionInfo/$1');

$routes->post('/api/cast-vote', 'StudentController::castVote');

$routes->get('api/student/check-voting-status/(:num)/(:num)', 'StudentController::checkVotingStatus/$1/$2');