<?php

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on' && $_SERVER['HTTP_HOST'] != 'localhost') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

if(file_exists($maintenance = __DIR__ . '/../maintenance.php')) {
    require $maintenance;
}

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../config/app.php';

$app->router->namespace('Src\App\Controllers\Auth');

$app->router->group(null);
$app->router->get('/', 'AuthController:index', 'home.index', \Src\App\Middlewares\GuestMiddleware::class);
$app->router->post('/', 'AuthController:index', 'home.index', \Src\App\Middlewares\GuestMiddleware::class);

$app->router->group('login');
$app->router->post('/expired', 'AuthController:expired', 'auth.expired');
$app->router->post('/check', 'AuthController:check', 'auth.check');

$app->router->group('entrar', \Src\App\Middlewares\GuestMiddleware::class);
$app->router->get('/', 'AuthController:index', 'auth.index');
$app->router->post('/', 'AuthController:index', 'auth.index');

$app->router->group('redefinir-senha', \Src\App\Middlewares\GuestMiddleware::class);
$app->router->get('/', 'ResetPasswordController:index', 'resetPassword.index');
$app->router->post('/', 'ResetPasswordController:index', 'resetPassword.index');
$app->router->get('/{code}', 'ResetPasswordController:verify', 'resetPassword.verify');
$app->router->post('/{code}', 'ResetPasswordController:verify', 'resetPassword.verify');

$app->router->group('criar-conta', \Src\App\Middlewares\GuestMiddleware::class);
$app->router->get('/', 'RegisterController:index', 'register.index');
$app->router->post('/', 'RegisterController:index', 'register.index');

$app->router->group('logout', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'AuthController:logout', 'auth.logout');

$app->router->namespace('Src\App\Controllers');

$app->router->group('erro');
$app->router->get('/{code}', 'ErrorController:index', 'error.index');

$app->router->group('ml');
$app->router->post('/add', 'MediaLibraryController:add', 'mediaLibrary.add');
$app->router->get('/load', 'MediaLibraryController:load', 'mediaLibrary.load');
$app->router->delete('/delete', 'MediaLibraryController:delete', 'mediaLibrary.delete');

$app->router->group('language');
$app->router->get('/{lang}', 'LanguageController:index', 'language.index');

$app->router->namespace('Src\App\Controllers\Admin');

$app->router->group('admin', \Src\App\Middlewares\AdminMiddleware::class);
$app->router->get('/', 'AdminController:index', 'admin.index');
$app->router->put('/system', 'AdminController:system', 'admin.system');

$app->router->group('admin/usuarios', \Src\App\Middlewares\AdminMiddleware::class);
$app->router->get('/', 'UsersController:index', 'admin.users.index');
$app->router->post('/', 'UsersController:store', 'admin.users.store');
$app->router->get('/{user_id}', 'UsersController:edit', 'admin.users.edit');
$app->router->put('/{user_id}', 'UsersController:update', 'admin.users.update');
$app->router->delete('/{user_id}', 'UsersController:delete', 'admin.users.delete');
$app->router->get('/criar', 'UsersController:create', 'admin.users.create');
$app->router->get('/list', 'UsersController:list', 'admin.users.list');

$app->router->namespace('Src\App\Controllers\User');

$app->router->group('u', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'UserController:index', 'user.index');

$app->router->group('u/editar', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'EditController:index', 'user.edit.index');
$app->router->put('/', 'EditController:update', 'user.edit.update');

$app->router->group('u/controle-de-pecas', \Src\App\Middlewares\LeaderMiddleware::class);
$app->router->get('/', 'PiecesManagementController:index', 'user.piecesManagement.index');
$app->router->get('/list', 'PiecesManagementController:list', 'user.piecesManagement.list');

$app->router->group('u/controle-de-reformados', \Src\App\Middlewares\LeaderMiddleware::class);
$app->router->get('/', 'ReformedsManagementController:index', 'user.reformedsManagement.index');
$app->router->get('/{reformation_id}', 'ReformedsManagementController:show', 'user.reformedsManagement.show');
$app->router->put('/{reformation_id}', 'ReformedsManagementController:update', 'user.reformedsManagement.update');
$app->router->delete('/{reformation_id}', 'ReformedsManagementController:delete', 'user.reformedsManagement.delete');
$app->router->post('/turn-start', 'ReformedsManagementController:turnStart', 'user.reformedsManagement.turnStart');
$app->router->post('/turn-end', 'ReformedsManagementController:turnEnd', 'user.reformedsManagement.turnEnd');
$app->router->get('/list', 'ReformedsManagementController:list', 'user.reformedsManagement.list');
$app->router->get('/export', 'ReformedsManagementController:export', 'user.reformedsManagement.export');

$app->router->group('u/produtividade-do-dia', \Src\App\Middlewares\LeaderMiddleware::class);
$app->router->get('/', 'DayProductivityController:index', 'user.dayProductivity.index');
$app->router->get('/list', 'DayProductivityController:list', 'user.dayProductivity.list');

$app->router->group('u/resumo-operacional', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'OperationalResumeController:index', 'user.operationalResume.index');
$app->router->get('/list', 'OperationalResumeController:list', 'user.operationalResume.list');

$app->router->group('u/produtos', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'ProductsController:index', 'user.products.index');
$app->router->post('/', 'ProductsController:store', 'user.products.store');
$app->router->get('/{product_id}', 'ProductsController:show', 'user.products.show');
$app->router->put('/{product_id}', 'ProductsController:update', 'user.products.update');
$app->router->delete('/{product_id}', 'ProductsController:delete', 'user.products.delete');
$app->router->get('/list', 'ProductsController:list', 'user.products.list');
$app->router->get('/export', 'ProductsController:export', 'user.products.export');

$app->router->group('u/entradas', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'ProductInputsController:index', 'user.productInputs.index');
$app->router->post('/', 'ProductInputsController:store', 'user.productInputs.store');
$app->router->get('/{product_input_id}', 'ProductInputsController:show', 'user.productInputs.show');
$app->router->put('/{product_input_id}', 'ProductInputsController:update', 'user.productInputs.update');
$app->router->delete('/{product_input_id}', 'ProductInputsController:delete', 'user.productInputs.delete');
$app->router->get('/list', 'ProductInputsController:list', 'user.productInputs.list');
$app->router->get('/export', 'ProductInputsController:export', 'user.productInputs.export');

$app->router->group('u/saidas', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'ProductOutputsController:index', 'user.productOutputs.index');
$app->router->post('/', 'ProductOutputsController:store', 'user.productOutputs.store');
$app->router->get('/{product_output_id}', 'ProductOutputsController:show', 'user.productOutputs.show');
$app->router->put('/{product_output_id}', 'ProductOutputsController:update', 'user.productOutputs.update');
$app->router->delete('/{product_output_id}', 'ProductOutputsController:delete', 'user.productOutputs.delete');
$app->router->get('/list', 'ProductOutputsController:list', 'user.productOutputs.list');
$app->router->get('/export', 'ProductOutputsController:export', 'user.productOutputs.export');

$app->router->group('u/colaboradores', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'CollaboratorsController:index', 'user.collaborators.index');
$app->router->post('/', 'CollaboratorsController:store', 'user.collaborators.store');
$app->router->get('/{collaborator_id}', 'CollaboratorsController:show', 'user.collaborators.show');
$app->router->put('/{collaborator_id}', 'CollaboratorsController:update', 'user.collaborators.update');
$app->router->delete('/{collaborator_id}', 'CollaboratorsController:delete', 'user.collaborators.delete');
$app->router->get('/list', 'CollaboratorsController:list', 'user.collaborators.list');
$app->router->get('/export', 'CollaboratorsController:export', 'user.collaborators.export');

$app->router->group('u/controle-de-estoque', \Src\App\Middlewares\UserMiddleware::class);
$app->router->get('/', 'StocksController:index', 'user.stocks.index');
$app->router->get('/list', 'StocksController:list', 'user.stocks.list');
$app->router->get('/export', 'StocksController:export', 'user.stocks.export');

$app->run();