<?php
$routes = new \Minix\Route();

$routes->get('/timestamp', Minix\Controller\ApiController::class . '@timestampAction');
$routes->post('/transaction', Minix\Controller\ApiController::class . '@transactionAction');
$routes->post('/transactionStats', Minix\Controller\ApiController::class . '@transactionStatsAction');
$routes->post('/scorePost', Minix\Controller\ApiController::class . '@scorePostAction');
$routes->get('/leaderboardGet', Minix\Controller\ApiController::class . '@leaderboardGetAction');
$routes->post('/userSave', Minix\Controller\ApiController::class . '@userSaveAction');
$routes->post('/userLoad', Minix\Controller\ApiController::class . '@userLoadAction');

return $routes;