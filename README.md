# Simple PHP-Router

Simple routing in php. 

## Setup
- Only file to include in your project is ***router.php***.
- If you use an apache webserver you need to modify your ***.htaccess*** and ***.httpd.conf*** so that the server ***(1)*** allows redirects and ***(2)*** redirects all requests to the file, where you specified your routes (in this project the index.php). 

## Usage
An example is in the index.php.

### Simple routing
- You can use "return" to return text or use "echo"
- Create simple route (Start page)
```php
Router::route('/', function() {
    // ----
    // Do stuff
    // ----
    return "Landing page!";
});
```

- Create simple route with more levels
```php
Router::route('/category', function() {
    // ----
    // Do stuff
    // ----
    echo "Categories!";
});
```

### Variables
- The variable names in the route-template do not have to match the names with the function parameters, only the order is important.

- Create route with one or more variables
```php
Router::route('/categories/{id}/{id2}', function($id, $id2) {
    return "Category with id: " . $id . " and id2: " . $id2;
});
```

### Regular Expressions
- The names you use in the route-template ***have to*** match the names you use in the regEx functions.

- Create route with variable that needs to match a regEx
```php
Router::route('/group/{id}', function($id) {
    return "Group with ID: " . $id;
})->where('id', '[0-9]+');
```

- Create route with multiple variables that need to match a specific regEx (You can also use the where function on a single route over and over again)
```php
Router::route('/user/{id}/{name}', function($id, $name) {
    return "USER with ID: " .$id . " and NAME: " . $name;
})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);
```
```php
Router::route('/user/{id}/{name}', function($id, $name) {
    return "<br>USER with ID: " .$id . " and NAME: " . $name;
})->where('id', '[0-9]+')->where('name', '[a-z]+']);
```

### Predefined regular expressions
```php
->whereNumeric('id');
->whereAlpha('name');
->whereAlphaNumeric('username');
->whereIn('type', ['type1', 'type2', 'type3']);
```

Example:
```php
Router::route('/group/{id}/{groupName}', function($id, $name) {
    return "Group with ID: " . $id . " and the name: " . $name;
})->whereNumeric('id')->whereAlphaNumeric('groupName');
```
```php
Router::route('/group/{id}/{count}', function($id, $count) {
    return "Group with ID: " . $id . " and count: " . $count;
})->whereNumeric(['id', 'count']);
```
```php
Router::route('/sports/{category}', function($category) {
    return "Sport: " . $category;
})->whereIn('category', ['football', 'basketball', 'tennis', 'swimming');
```
