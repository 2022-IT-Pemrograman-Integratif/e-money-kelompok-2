<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->post('/Buskimarket-Register', 'Buskimarket/Register::index');
$routes->post('/Buskimarket-Login', 'Buskimarket/Login::index');
$routes->post('/Buskimarket-AddItem', 'Buskimarket/AddItem::index');
$routes->post('/Buskimarket-Buy', 'Buskimarket/Buy::index');
$routes->post('/Buskimarket-Buy-KCN_Pay', 'Buskimarket/Buy::KCN_Pay');
$routes->post('/Buskimarket-Buy-Buski_Coins', 'Buskimarket/Buy::Buski_Coins');
$routes->post('/Buskimarket-Buy-CuanIND', 'Buskimarket/Buy::CuanIND');
$routes->post('/Buskimarket-Buy-MoneyZ', 'Buskimarket/Buy::MoneyZ');
$routes->post('/Buskimarket-Buy-Gallecoins', 'Buskimarket/Buy::Gallecoins');
$routes->post('/Buskimarket-Buy-Talangin', 'Buskimarket/Buy::Talangin');
$routes->post('/Buskimarket-Buy-PeacePay', 'Buskimarket/Buy::PeacePay');
$routes->post('/Buskimarket-Buy-PadPay', 'Buskimarket/Buy::PadPay');
$routes->post('/Buskimarket-Buy-PayPhone', 'Buskimarket/Buy::PayPhone');
$routes->post('/Buskimarket-Buy-PayFresh', 'Buskimarket/Buy::PayFresh');
$routes->post('/Buskimarket-Buy-ECoin', 'Buskimarket/Buy::ECoin');
$routes->get('/Buskimarket-SeeItem', 'Buskimarket/SeeItem::index');
$routes->get('/Buskimarket-SeeOrder', 'Buskimarket/SeeOrder::index');
$routes->get('/Buskimarket-SeeBuytoConfirm', 'Buskimarket/SeeBuytoConfirm::index');
$routes->post('/Buskimarket-SendItem', 'Buskimarket/SendItem::index');
$routes->post('/Buskimarket-ConfirmBuy', 'Buskimarket/ConfirmBuy::index');
//$routes->resource("buskidicoin/admin"); /* admin/see_all_data */

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
