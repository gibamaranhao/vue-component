# vue-component
A Simple Vue Component loader for PHP

vue-component allows you to create single-file style codes and generate component code to be added to HTML 
without the need to work with webpack, vue-cli or npm.

## Installation
The recomended way to install is via composer

composer.json

```json
{
  "minimum-stability" : "dev",
  "require" : {
    "gibamaranhao/vue-component" : "*"
  }
}
```

Execute composer update to install

## Usage

Create a component folder and a component file (the extension can be .vue, .js, .php, .html ,no matters):

components/App.vue

```html
<template>
	<h1 class="redone">
		Hello, VueComponent
	</h1>
</template>

<script>
Vue.component('App',{})
</script>
<style>
  .redone {
    color: red;
  }
</style>
```
```php
<?php
use gibamaranhao\vue\VueComponent;

$loader = require_once __DIR__.'/vendor/autoload.php';

$vc = new VueComponent();
$vc->registerComponentsFromDir(__DIR__.'/components');
?>

<html>
<head>
        <title> GibaMaranhao - VueComponent </title>
</head>
<body>

        <div id="app">
                <App />
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.21/vue.min.js"></script>
        <?= $vc->renderAllComponents()  ?>
        <script>
                new Vue({
                        el: '#app'
                });
        </script>
</body>
</html>
```
