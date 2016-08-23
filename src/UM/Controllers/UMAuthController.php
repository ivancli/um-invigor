<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/23/2016
 * Time: 11:54 AM
 */

namespace Invigor\UM\Controllers;


use App\Http\Controllers\Auth\AuthController;

class UMAuthController extends AuthController
{
    protected $redirectTo = 'um/home';
    protected $username = 'email';
    protected $redirectAfterLogout = 'um/home';
}