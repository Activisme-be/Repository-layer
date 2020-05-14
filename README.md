# Laravel Repositories

[![Build Status](https://travis-ci.org/dugajean/laravel-repositories.svg?branch=master)](https://travis-ci.org/dugajean/repositories) 
[![Latest Stable Version](https://poser.pugx.org/dugajean/repositories/v/stable)](https://packagist.org/packages/dugajean/repositories)
[![Total Downloads](https://poser.pugx.org/dugajean/repositories/downloads)](https://packagist.org/packages/dugajean/repositories)
[![License](https://poser.pugx.org/dugajean/repositories/license)](https://packagist.org/packages/dugajean/repositories)

Laravel Repositories is a package for Laravel 5 which is used to abstract the database layer. This makes applications much easier to maintain.

This package was originally created by Bosnadev, who is no longer maintaining it; therefore, I have decided to take this project over and assure its maintenance.

## Installation

Run the following command from you terminal:

 ```bash
 composer require dugajean/repositories
 ```

## Usage

First, create your repository class with this command:

```bash
php artisan make:repository Film
```

Where `Film` is the name of an existing model. If the model does not exist, it will be generated for you.

Finally, use the repository in the controller:

```php
<?php 

namespace App\Http\Controllers;

use App\Repositories\FilmRepository;

class FilmsController extends Controller {

    /**
     * @var FilmRepository 
     */
    private $filmRepository;

    public function __construct(FilmRepository $filmRepository) 
    {
        $this->filmRepository = $filmRepository;
    }

    public function index() 
    {
        return response()->json($this->filmRepository->all());
    }
}
```

###### Publishing The Configuration

If you wish to override the path where the repositories and criteria live, publish the config file:

```bash
php artisan vendor:publish --provider="Dugajean\Repositories\Providers\RepositoryProvider"
```

Then simply open `config/repositories.php` and edit away!

## Available Methods

The following methods are available:

###### Dugajean\Repositories\Contracts\RepositoryInterface

```php
public function all($columns = ['*'])
public function lists($value, $key = null)
public function paginate($perPage = 1, $columns = ['*'], $method = 'full');
public function create(array $data)
// if you use mongodb then you'll need to specify primary key $attribute
public function update(array $data, $id, $attribute = 'id')
public function delete($id)
public function find($id, $columns = ['*'])
public function findBy($field, $value, $columns = ['*'])
public function findAllBy($field, $value, $columns = ['*'])
public function findWhere($where, $columns = ['*'])
```

###### Dugajean\Repositories\Contracts\CriteriaInterface

```php
public function apply($model, Repository $repository)
```

### Example usage

Create a new film in repository:

```php
$this->filmRepository->create(Input::all());
```

Update existing film:

```php
$this->filmRepository->update(Input::all(), $film_id);
```

Delete film:

```php
$this->filmRepository->delete($id);
```

Find film by film_id;

```php
$this->filmRepository->find($id);
```

you can also chose what columns to fetch:

```php
$this->filmRepository->find($id, ['title', 'description', 'release_date']);
```

Get a single row by a single column criteria.

```php
$this->filmRepository->findBy('title', $title);
```

Or you can get all rows by a single column criteria.
```php
$this->filmRepository->findAllBy('author_id', $author_id);
```

Get all results by multiple fields

```php
$this->filmRepository->findWhere([
    'author_id' => $author_id,
    ['year', '>', $year]
]);
```

## Criteria

Criteria is a simple way to apply specific condition, or set of conditions to the repository query. 

To create a Criteria class, run the following command:

```bash
php artisan make:criteria LengthOverTwoHours --model=Film
```

Here is a sample criteria:

```php
<?php 

namespace App\Repositories\Criteria\Films;

use Dugajean\Repositories\Criteria\Criteria;
use Dugajean\Repositories\Contracts\RepositoryInterface;

class LengthOverTwoHours extends Criteria 
{
    /**
     * @param $model
     * @param RepositoryInterface $repository
     *                                       
     * @return Model
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('length', '>', 120);
    }
}
```

Now, inside you controller class you call pushCriteria method:

```php
<?php 

namespace App\Http\Controllers;

use App\Repositories\FilmRepository;
use App\Repositories\Criteria\Films\LengthOverTwoHours;

class FilmsController extends Controller 
{
    /**
     * @var FilmRepository
     */
    private $filmRepository;

    public function __construct(FilmRepository $filmRepository) 
    {
        $this->filmRepository = $filmRepository;
    }

    public function index() 
    {
        $this->filmRepository->pushCriteria(new LengthOverTwoHours());
        
        return response()->json($this->filmRepository->all());
    }
}
```

## Caching 

Add a layer of the cache easily to your repository.

### Cache usage 

Implements the interface `CacheInterface` and use the `CacheableRepository` trait. 

```php
use ActivismeBe\Repositories\Eloquent\Repository; 
use ActivismeBe\Repositories\Contracts\CacheableInterface;
use ActivismeBe\Repositories\Traits\CacheableRepository;

class ExampleRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    ...
} 
```

Done, done that your repository will be cached, and the repository cache is cleared whenever an item is created, 
modified or deleted.

### Cache config 

You can change the cache setting in the file `config/repository.php` and also directly on your repository. 

Below u can fix the available caching settings: 

```php
'cache' => [
    'enabled' => true,              // Enable or disable cache repositories
    'minutes' => 30,                // Lifetime of the cache
    'repository' => 'cache',        // Repository Cache, implementation Illuminate\Contracts\Cache\Repository
    'clean' => [                    // Sets clearing the cache
        'enabled' => true,          // Enable, disable clearing the cache on changes
        'on' => [
            'create' => true,       // Enable, disable clearing the cache when you create an item
            'update' => true,       // Enable, disable clearing the cache when you upgrading an item
            'delete' => true,       // Enable, disable clearing the cache when you delete an item
        ],
    ],
    'params' => [
        'skipCache' => 'skipCache', // Request parameter that will be used to bypass the cache repository
    ], 
    'allowed' => [
        'only' => null,             // Allow caching only for some methods
        'except' => null,           // Allow caching for all available methods, except
    ],
],
```

it is possible to override these settings directly in the repository: 

```php
use ActivismeBe\Repositories\Eloquent\Repository; 
use ActivismeBe\Repositories\Contracts\CacheableInterface;
use ActivismeBe\Repositories\Traits\CacheableRepository;

class ExampleRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    /**
     * Setting the lifetime of the cache to a repository specifically
     */
    protected int $cacheMinutes = 90;
    
    protected array $cacheOnly = ['all', ...]; 
    protected array $cacheExcept = ['find', ...];

    ...
}
```

The cacheable methods are: all, paginate, find, findByField, findWhere, getByCriteria

## Testing

```bash
$ vendor/bin/phpunit
```

## License

Pouch is released under [the MIT License](LICENSE).
