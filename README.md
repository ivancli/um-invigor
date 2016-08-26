# Invigor User Management Package

This package provide the fundamental functionality of user management, 
including CRUD of users, groups, roles, permissions and relationships between each of them.

Please check [System/Project requirements and Assumptions](#system-project-requirements-and-assumptions) before using this package.

---

###Table of Content
* [System/Project requirements and Assumptions](#system-project-requirements-and-assumptions)
* [Installation Guide](#installation-guide)
    * [Initial setup](#initial-setup)
        * [composer.json - repo](#composer-json-repo)
        * [composer.json - require-dev](#composer-json-require-dev)
        * [composer update](#composer-update)
        * [config/app.php - provider](#config-app-provider)
        * [config/app.php - alias](#config-app-alias)
        * [User model adds UMUserTrait](#user-model-user-trait)
        * [Generate controllers](#generate-controller)
        * [Generate migration](#generate-migration)
        * [Migrate UM DB tables](#migrate-db)
        * [Generate seeder and seeding](#generate-seeder-seeding)
        * [vendor:publish - views](#vendor-publish-view)
        * [Create view layout](#view-layout)
        * [Required front-end resources](#front-end-resources)
* [Default Package Routes](#default-package-routes)
    * [Default routes](#default-routes)
    * [User routes](#user-routes)
    * [Group routes](#group-routes)
    * [Role routes](#role-routes)
    * [Permission routes](#permission-routes)
* [Controllers](#controllers)
    * [Controllers in _UM_ package](#controllers-in-um-package)
    * [Controllers in _app_ folder](controllers-in-app-folder)
* [Middleware](#middleware)
    * [Sample usage of role in _routes_](#sample-ussage-of-role-in-routes)
    * [Sample usage of permission in _routes_](#sample-ussage-of-permission-in-routes)
    * [Sample usage of ability in _routes_](#sample-ussage-of-ability-in-routes)
    * [Sample usage of middleware in _controllers_](#sample-usage-of-middleware-in-controllers)
* [Default data](#default-data)
* [Entity relationships](#entity-relationships)

---
###System/Project Requirements and Assumptions <a name="system-project-requirements-and-assumptions"></a>
1. Project is using PHP **Laravel version-5.2.***.
2. Project is using either _**array**_, _**redis**_, _**memcached**_ or other **tagging** supported caching system
3. Project is using default User model provided by Laravel.
> Please be noticed that modification or customisation in default User model might require changes in the views, routes or controllers provided by this package. Will explain in detailed in this article.

---

###Installation Guide <a name="installation-guide"></a>
#####Initial setup <a name="initial-setup"></a>
In your Laravel project,
1. Add a repository to _composer.json_ file _repositories_ array. <a name="composer-json-repo"></a>
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "ssh://git@repo.globalit.net.au:234/srv/repo/git/invigor-user-management"
        }
    ]
}
```
---
2. Add a dev requirement to _require-dev_ in _composer.json_. <a name="composer-json-require-dev"></a>
```json
{
    "require-dev": {
      "invigor/um": "dev-master"
    }
}
```
---
3. Run `composer update` in project root folder.<a name="composer-update"></a>
---
4. Add the following code to _providers_ array in _config/app.php_ <a name="config-app-provider"></a>
```php
Invigor\UM\UMServiceProvider::class,
```
---
5. Create aliases in _config/app.php_ by adding following code to _aliases_ array <a name="config-app-alias"></a>
```php
'UM' => Invigor\UM\UMFacade::class,
'role' => Invigor\UM\Middleware\UMRole::class,
'permission' => Invigor\UM\Middleware\UMPermission::class,
'ability' => Invigor\UM\Middleware\UMAbility::class,
```
---
6. Use user trait in default User model:<a name="user-model-user-trait"></a>
```php
use Invigor\UM\Traits\UMUserTrait;

class User ...
{
    use UMUserTrait;
    
    ...
    ...
}
```
---
7. Generate controllers, in root folder of the project run: <a name="generate-controller"></a>
```sh
php artisan um:controller
```
---
8. Generate migration files, in root folder of the project run: <a name="generate-migration"></a>
```sh
php artisan um:migration
```
---
9. Migrate database tables <a name="migrate-db"></a>
```sh
php artisan migrate
```
---
10. Generate seeder and proceed seeding <a name="generate-seeder-seeding"></a>
```sh
php artisan um:seeder
```
> This seeder will insert default records into Database.
> Please refer to [Default Data](#default-data).
---
11. Include views <a name="vendor-publish-view"></a>

UM package provides comprehensive views to take care of CRUD of entities.
By running the following command in root folder of the project, 
a _um_ folder with views will be copied over to _resources/views_ folder 
```sh
php artisan vendor:publish
```

Developers are suggested to create their own layouts and extend the layouts in the view.
```sh
um
|---user
|----|---index.blade.php
|----|---create.blade.php
|----|---edit.blade.php
|----|---show.blade.php
|---group
|----|---index.blade.php
|----|---create.blade.php
|----|---edit.blade.php
|----|---show.blade.php
|---role
|----|---index.blade.php
|----|---create.blade.php
|----|---edit.blade.php
|----|---show.blade.php
|---permission
|----|---index.blade.php
|----|---create.blade.php
|----|---edit.blade.php
|----|---show.blade.php
```
---

12. View Layout <a name="view-layout"></a>
The views described in [11](#vendor-publish-view) do NOT include any CSS nor JS resources.

Therefore, a layout with required front-end resources is essential in order to use the default views.
modify the view to extend the layout you have created.

The required front-end resources will be specified in [13](#front-end-resources)

layout:
```blade
<link href="the_required_css_styles" />
<script src="the_required_js"></script>
```
views generated by vendor:publish
```blade
@extends('the_layout_you_have_created')

{{--default view content--}}
......
...
.
```

---

13. Required Front-end Resources <a name="front-end-resources"></a>

The default views of UM package required a few front-end resources.
* jQuery
* Bootstrap
* DataTables
* Select2

> If your project does not have the resource specified above ready, please proceed with the following installation.
> 
> ```sh
> #install jQuery
> $ npm install jquery
> #install Bootstrap 3 
> $ npm install bootstrap@3
> #install DataTables
> $ npm install datatables.net
> #install DataTables Bootstrap theme
> $ npm install datatables.net-bs
> #install Select2
> $ npm install https://github.com/select2/select2.git
> ```
> And make sure the following resource are included sequentially
> ```json
> [
>   "node_modules/jquery/dist/jquery.js",
>   "node_modules/bootstrap/dist/js/bootstrap.js",
>   "node_modules/datatables.net/js/jquery.dataTables.js",
>   "node_modules/datatables.net-bs/js/dataTables.bootstrap.js",
>   "node_modules/select2/dist/js/select2.js"
> ]
> ```
> ```json
> [
>   "node_modules/bootstrap/dist/css/bootstrap.css",
>   "node_modules/datatables.net-bs/css/dataTables.bootstrap.css",
>   "node_modules/select2/dist/css/select2.css"
> ]
> ```
> And make sure font files of Bootstrap is in a _fonts_ folder, a sibling folder of where `bootstrap.css` sits
---

Now the package is good to go.

---

###Default Package Routes <a name="default-package-routes"></a>
#####Default routes <a name="default-routes"></a>
`get:um/home`
is a default and temporary home page of User Management package, it includes a very plain login form if user is not yet logged in.

`get:um/login`
is a plain login form connect to a customised Authentication Controller in the package.

#####User routes <a name="user-routes"></a>
`get:um/user`
is a list page of users.

`get:um/user/{id}`
is a detail page of a selected user.

`get:um/user/create`
is the create page of user.

`post:um/user`
insert a record in users table.

`get:um/user/{id}/edit`
is the edit page of a selected user.

`put/patch:um/user/{id}`
updates a record in users table.

`delete:um/user/{id}`
deletes a record in users table.

#####Group routes <a name="group-routes"></a>
`get:um/group`
is a list page of groups.

`get:um/group/{id}`
is a detail page of a selected group.

`get:um/group/create`
is the create page of group.

`post:um/group`
insert a record in groups table.

`get:um/group/{id}/edit`
is the edit page of a selected group.

`put/patch:um/group/{id}`
updates a record in groups table.

`delete:um/group/{id}`
deletes a record in groups table.

#####Role routes <a name="role-routes"></a>
`get:um/role`
is a list page of roles.

`get:um/role/{id}`
is a detail page of a selected role.

`get:um/role/create`
is the create page of role.

`post:um/role`
insert a record in roles table.

`get:um/role/{id}/edit`
is the edit page of a selected role.

`put/patch:um/role/{id}`
updates a record in roles table.

`delete:um/role/{id}`
deletes a record in roles table.

#####Permission routes <a name="permission-routes"></a>
`get:um/permission`
is a list page of permissions.

`get:um/permission/{id}`
is a detail page of a selected permission.

`get:um/permission/create`
is the create page of permission.

`post:um/permission`
insert a record in permissions table.

`get:um/permission/{id}/edit`
is the edit page of a selected permission.

`put/patch:um/permission/{id}`
updates a record in permissions table.

`delete:um/permission/{id}`
deletes a record in permissions table.


---
###Controllers <a name="controllers"></a>
There are two sets of controllers:
1. The controllers inside UM package.
2. The controllers generate by UM package and sit in _app\Http\Controllers\UM_ folder.
#####Controllers in _UM_ package <a name="controllers-in-um-package"></a>
The controllers mainly provide functions of CRUD of _user_, _group_, _role_ and _permission_.

The `index` function of the controllers takes a parameter to indicate the expected output format: `"datatable"` or `null`. 
`"datatable"` takes corresponding request values and filters the result accordingly.
While `null` value outputs all records of the entities.

> All functions in controllers in _UM_ package do not deal with views nor routes.
>
> In order to provide good enough flexibility to this package, 
> data validations of `create` and `update` functions are in Controllers in _app_ folder.

#####Controllers in _app_ folder <a name="controllers-in-app-folder"></a>
The controllers in _app_ folder take care of the _routes_, _views_ and _representation of data_.

By default, the controllers are pointing to the views in _resources/views/um_. 
However, this can be changed directly in the controllers in _app_ folder.

---
###Middleware <a name="middleware"></a>
There are three main types of middleware available in the package.
* role
* permission
* ability (used only in a complicated situation)

All middleware can be set in _routes_ file or _controllers_. And here are the usages.

#####Sample usage of role in _routes_: <a name="sample-ussage-of-role-in-routes"></a>
```php
Route::get('url', ['middleware' => ['role:super_admin|client']])
```
> `super_admin` and `client` are the _name_ of roles (NOT _display_name_)
>
> In this case, only the users with role `super_admin` OR `client` can access this route.
> 
#####Sample usage of permission in _routes_: <a name="sample-usage-of-permission-in-routes"></a>
```php
Route::get('url', ['middleware' => ['permission:create_user|edit_user']])
```
> `create_user` and `edit_user` are the _name_ of permissions (NOT _display_name_)
> 
> This route only open to the users with permission of `create_user` OR `edit_user`.
#####Sample usage of ability in _routes_: <a name="sample-usage-of-ability-in-routes"></a>
```php
Route::get('url', ['middleware' => ['ability:super_admin|client,create_user|edit_user', true]])
```
> This middleware takes three (3) parameters, including _roles_, _permissions_ and _operator_.
>
> Operator is a boolean (true/false) indicating if middleware needs to validate both roles and permissions or not.
>
> In this case, the route only open to the `super_admin` OR `client` with the permissions of `create_user` OR `edit|user`.

#####Sample usage of middleware in _controllers_: <a name="sample-usage-of-middleware-in-controllers"></a>
```php
$this->middleware('role:super_admin, ['only' => ['create', 'store']]');
$this->middleware('permission:create_user', ['only' => ['create', 'store']]);
$this->middleware('ability:super_admin,create_user,true', ['only' => ['create', 'store']]);
```
> Syntax is pretty much same as middleware in route. 
> However, we need to indicate which functions the middleware shall be applied to by having
> `['only' => [blah blah blah]` as the second parameter in the middleware functions.

---

###Default Data<a name="default-data"></a>
Initial seeder will insert the following records:

#####_users_ table
a user with super_admin role and all parent permissions.
> email: admin@um.dev
>
> password: secret

#####_groups_ table
seeder does not insert any default group record

#####_roles_ table
seeder inserts a super_admin record to database.

#####_permissoins_ table
by default, seeder inserts CRUD permissions to 4 entities, and parent permissions for each entity:
```sh
manage_user
    |--------create_user
    |--------read_user
    |--------update_user
    |--------delete_user
manage_group
    |--------create_group
    |--------read_group
    |--------update_group
    |--------delete_group
manage_role
    |--------create_role
    |--------read_role
    |--------update_role
    |--------delete_role
manage_permission
    |--------create_permission
    |--------read_permission
    |--------update_permission
    |--------delete_permission
```

---

###Entity Relationships <a name="entity-relationships"></a>
* group-user: many to many

    a group can have multiple users, and a user can be in multiple groups.

* user-role: many to many

    a user can be in multiple roles, and multiple users can be in the same role.

* role-permission: many to many

    a role can have multiple permissions, and multiple roles can have the same permission.

* permission-permission: one to many

    multiple permissions can have a parent permission.