# Core Module

## To Do
- [x] Write a folder structure document
- [x] Write how events are written inside the config directory with examples
- [x] Why do we use common directory, explain each subdirectory.
- [x] Do the same for Database subdirectories
- [x] Short paragraph about Event Driven Development
- [x] Why do we write exceptions for every single exception, what does it facilitate?
- [x] Helpers should be object oriented and shouldn't be functions only (Static methods)
- [x] Http vs Console (Draw.io graphic). Where requests are coming from and how they are handled with a graph.
- [x] Requests should be in folders/contained in subfolders
- [x] Validation
- [x] Services
- [x] Tree

## Event Driven Development

### 1. What is Event Driven Programming
It is a programming paradigm that the flow of the program is determined by events such as user actions and changes in states of the system. Systems that are designed with this paradigm waits for an external event to occur and executes a script without having to constantly apply polling. These executed procedures or set of actions are called handlers. In short, when a user action or state change occurs in the system, we can trigger a handler to execute specific set of actions. 

### 2. Examples
Any type of external interaction with a system can be handled with events. These interactions can vary from hardware input to incoming transmissions. Keyboard presses, mouse movements and actions, changes in a particular embedded sensor, network messages and so on. Some example use cases and further explanations can be found in the following links;
- https://en.wikipedia.org/wiki/Event-driven_programming
- https://www.tutorialspoint.com/concurrency_in_python/concurrency_in_python_eventdriven_programming.htm

### 3. Use Cases
Within our projects and source codes, events should be placed under the src/Events directory. Each event should be properly named such as if an event is about creation of something it should be named as SomethingCreated or if it is about deletion of an article, it should be named as ArticleDeleted. Furthermore, handlers for events should be placed inside the events directory, more specifically at src/Events/Handlers directory. 

## Core Module Directory Structure 
### 1. Directory Structure Schema
<pre>
.
├── config
├── docs
├── resources
│   └── views
|       └── ${ViewName}
├── schema
├── src
│   ├── Broadcasts
│   ├── Common
│   │   ├── Cache
│   │   │   └── ${CacheName}
│   │   ├── Enums
│   │   ├── Logger
│   │   ├── Notifications
│   │   │   └── Channels
│   │   │       └── ${ChannelName}
│   │   ├── Registry
│   │   │   ├── Drivers
│   │   │   └── Facades
│   │   └── Services
│   │       └── ${ServiceName}
│   ├── Console
│   │   └── Commands
│   ├── Database
│   │   ├── Filters
│   │   ├── GlobalScopes
│   │   ├── Migrations
│   │   ├── Models
│   │   ├── Observers
│   │   ├── Seeders
│   │   └── Traits
│   ├── Events
│   │   └── Handlers
│   ├── Exceptions
│   ├── Helpers
│   ├── Http
│   │   ├── Controllers
│   │   ├── Middleware
│   │   ├── Requests
│   │   │   └── ${RequestDefinition}
│   │   ├── Traits
│   │   │   └── Response
│   │   ├── Transformers
│   │   │   └── Traits
│   │   └── api.routes.php
│   ├── Jobs
│   ├── Notifications
│   ├── Policies
│   ├── Services
│   ├── Validation
│   │   └── Rules
│   └── ${ModuleName}ServiceProvider.php
├── tests
│   ├── rest
│   └── services
├── vendor
│   └── ${Dependencies}
└── workers
</pre>

### 2. Config
General Configurations about the project. All environment configurations should be located in the {ModuleName}.php file. "model-binding.php" binds existing database models to the module."relation.php" file is used to create pre-defined queries to fetch certain data from the database.  

### 3. Docs
Contains markdown files and documentation related materials. Used for documenting anything related to the module.

### 4. Resources
Contains blade templates files. Currently contains templates for e-mails only. For more information about blade templates please visit; https://laravel.com/docs/5.5/blade

### 5. Schema
General database related queries to create schema and insert data into those tables.

### 6. Source (src) Folder Structure
Main development is handled in here.
#### 6.1. Broadcasts
Event broadcasting is handled under this directory. Broadcasts are encapsulated under the PlusClouds\${ModuleName}\Broadcasts namespace. Each broadcast should be named as ${BroadcastEvent}Broadcast.php and should extend AbstractBroadcast while implementing "ShouldBroadcast" interface. Further details about broadcasting can be found in; https://laravel.com/docs/5.5/broadcasting

#### 6.2. Common
This directory is used for shared code, files and data.
##### 6.2.1. Cache
Used for application caching purposes to speed up the application. Cache related codes are encapsulated under PlusClouds\${ModuleName}\Common\Cache namespace. 
##### 6.2.2. Enums
Related common enums are built here. Each enum is encapsulated under PlusClouds\${ModuleName}\Common\Enums and uses BenSampo\Enum\Enum module to implement enumerations. 
##### 6.2.3. Logger
Queue and Query loggers is defined here. Also monolog handler, a third party open source handler also used under this directory to handle logging. 
##### 6.2.4. Notifications
Sending notifications are handled in here. For each notification channel, a directory is should be created to handle any notification related development. Each channel should be placed under PlusClouds\${ModuleName}\Common\Notifications\Channels\${ChannelName} namespace. Everything related to the implementation of a specific channel should be implemented within the same namespace. 
##### 6.2.5. Registry
Used as state saver, information serializer and deserializer. In this part, we are serializing different types of data in JSON format and writing them to files to store application related information. In the case of need, a registry file can be read and deserialized to recover or continue execution of an application/data/information. Each registry driver element to store data should be named as ${Method}.php such as, File.php or Database.php. In each registry driver class should implement IDriver (namespace  NextDeveloper\Commons\Common\Registry\Drivers\IDriver) and should extend AbstractSerializer ( NextDeveloper\Commons\Common\Registry\Drivers\AbstractSerializer). Each driver class should also have a config variable to access the config\{ModuleName}.php. To set any configuration related variables, Config\{ModuleName}.php file should be used and should be called to set related variables in registry classes. The following implementation can be followed as a blueprint;

```
<?php

namespace PlusClouds\${ModuleName}\Common\Registry\Drivers;
class ${Example} extends AbstractSerializer implements IDriver
{
    /**
     * @var Config
     */
    protected $config;

    /**
    * @var string
    */
    protected $var;

    /**
    * ${Example} constructor.
    *
    * @param Config $config
    *
    * @throws Exception
    */
    public function __construct(Config $config) {
        $this->config = $config;
        $this->var = $config->get('${ModuleName}.registry.${Example}.${var}','${DefaultValue}')
    }

    /**
     * @param ${Data Type} $param
     * @return ${Data Type}
    */
    public function ${IDriverMethod}($param) {
        ...
        return ${Something}
    }
}

```

##### 6.2.6. Services
All common core services and related interfaces are placed in here. Essentially development of common services and their events should be done under this directory. Each service related implementation should be encapsulated under PlusClouds\${ModuleName}\Common\Services\${ServiceName}. Related events to a service should be placed under Events folder and should be encapsulated under  PlusClouds\${ModuleName}\Common\Services\${ServiceName}\Events namespace. Implemented events should extend AbstractEvent ( NextDeveloper\Commons\Events\AbstractEvent).

#### 6.3. Console
All requests related to console operations go here to be handled by Handlers for console commands and custom queries. Each custom console command is encapsulated under \PlusClouds\${ModuleName}\Console\Commands namespace and each command class should be named with ${CommandPurpose}Command.php. For developing custom commands, each class should extend Command (Illuminate\Console\Command) class. Examples about writing commands can be found in; https://laravel.com/docs/5.5/artisan#writing-commands

Following notation should be used to create custom commands;
```
<?php

namespace PlusClouds\${ModuleName}\Console\Commands;
use Illuminate\Console\Command;

class ${ShowExample}Command extends Command {
  /**
  * The purpose of the variable
  *
  * @var ${Data Type} $variable
   */
  ${Access Modifier} $variable;

  /**
    * Create a new ${ShowExample}Command instance.
    *
    * @param  ${Data Type}  $drip
    * @return void
    */
  public function __construct($param) {
    parent::__construct();
    ...
  }
}
```

#### 6.4. Database
Everything related to database is located in this directory.

##### 6.4.1. Filters
Core database filters are defined in here. Used for creating methods to filter certain queries to get data out of the database. Each filter should be named with ${FilterName}QueryFilter.php and should be encapsulated under PlusClouds\{ModuleName}\Database\Filters namespace. Each developed filter should extend AbstractQueryFilter. Each class method should also contain informative comments including the parameter types and return types along with an informative explanation. 
```
/**
     * {Informative explanation about what this method is doing}
     *
     * @param $param
     *
     * @return ${DataType}
     */
    public function example($param) {
        $sample = sample::staticMethod($param);

        return sample;
    }
```

##### 6.4.2. GlobalScopes
Global scopes are allowing to add constraints to all queries for a model. Using global scopes, we can provide an easy way to make sure every query for a given model receives a certain constraint. Each file under this directory should be named with ${ScopeName}Scope.php and should be encapsulated under PlusClouds\{ModuleName}\Database\GlobalScopes. Each global scope class should also implement Scope interface (Illuminate\Database\Eloquent\Scope). Further details about implementing a global scope can be found in; https://laravel.com/docs/5.5/eloquent#global-scopes

##### 6.4.3. Migrations
Holds the database schemas and predefined set of database related actions. A more detailed explanation about migrations can be found in original laravel documentation in the following link; https://laravel.com/docs/migrations

##### 6.4.4. Models
Each database table has a corresponding model, that is used to interact with that table. Implemented models should extend AbstractModel and should be encapsulated under the PlusClouds\${ModuleName}\Database\Models namespace.

##### 6.4.5. Observers
Observers are event listeners for database related actions. When a change occur in the database, related event is fired in the system. Observers check these changes to execute set of actions. Each observer should extend AbstractObserver ( NextDeveloper\Commons\Database\Observers\AbstractObserver) and should be encapsulated under PlusClouds\${ModuleName}\Database\Observers. Each observer class implementation should follow the following naming schema; ${Model}Observer.php. Thus, each observer implementation should use an existing model defined under PlusClouds\${ModuleName}\Database\Models.

##### 6.4.6. Seeders
Seeders are used to insert data to the database. When executed, it can create and fill already defined database with some random or not so random values. A seeder class should extend Seeder (Illuminate\Database\Seeder) and should be encapsulated under PlusClouds\${ModuleName}\Database\Seeders namespace.

##### 6.4.7. Traits
Every database related trait is defined here. Traits are a way to reuse methods of independent classes in different scopes via "use" keyword. Each trait is encapsulated under PlusClouds\${ModuleName}\Database\Traits and should be named with ${ModelName}Trait.php. 

#### 6.5. Events
All events and handlers are implemented here. When an event occurs, handler sends a log file. Each event should extend AbstractEvent ( NextDeveloper\Commons\Events\AbstractEvent) and should be encapsulated under PlusClouds\${ModuleName}\Events namespace. Each Event class and file should be named as ${EventName}Event. The following code segment could be used as a guideline for future implementations (For ${EventName}Event.php file);

```
<?php
namespace PlusClouds\${ModuleName}\Events;

class ${EventName}Event extends AbstractEvent
{
    /**
     * @var ${DataType}
     */
    ${Modifier} $variable;

    /**
     * ${EventName} constructor.
     *
     * @param ${DataType} $var
     */
    public function __construct($var) {
      ...
    }
}
```

#### 6.6. Exceptions
All the exceptions and handlers are defined here. We create separate exceptions to make them more reusable and easier to handle variety of problems with their own solutions. Exceptions should be encapsulated within their own class and should extend AbstractCoreException. Additionally, Each exception file should be named properly and similarly to the class name where any new developer should understand the reason of having that exception defined just by reading the file name. The naming convention for exceptions files are ${ExceptionName}Exception.php. The following blueprint can be used to implement a new exception;
```
namespace PlusClouds\{ModuleName}\Exceptions;
class ${ExceptionName}Exception extends AbstractCoreException {
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report() {
        // ...
    }
 
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return mixed
     */
    public function render($request) {
        return response()...;
    }
}
```

#### 6.7. Helpers
Custom functions that are used as helper in other parts of the code or in importing projects. Each file should contain a class and each helper method should be defined within those classes as static methods. Each helper related to an operation should be named with the operation name and the helper suffix such that each file should be ${Name}Helper.php and each class name should follow the same naming. Methods implemented within these helper files should be static. The following convention can be used to create helpers;

```
<?php

class ${Name}Helper {
  
  /**
   * ${Brief explanation about the method}
   *
   * @param ${DataType} $param1
   *
   * @return ${DataType}
   */
  public static function staticMethod1($param1) {
    ...
    return ...
  }

  /**
   * ${Brief explanation about the method}
   *
   * @param ${DataType} $param2
   * @param ${DataType} $param3
   *
   * @return ${DataType}
   */
  public static function staticMethod2($param2, $param3) {
    ...
    return ...
  }
}
```

#### 6.8. Http
Contains controllers, middlewares and requests. All of the logic to handle http requests are done in this directory.

##### 6.8.1. Controllers
Controllers are located here. Each controller group a related request handling logic into a class and encapsulated under PlusClouds\${ModuleName}\Http\Controllers namespace. Each implemented controller should reflect what it is controlling and should be named as ${Purpose}Controller. Furthermore, controllers should extend AbstractController ( NextDeveloper\Commons\Http\Controllers\AbstractController). Implemented Traits in AbstractController should also be examined before beginning to define a new controller to avoid duplicated function implementations. Further details about controllers can be found in; https://laravel.com/docs/controllers . However, as this link explains Laravel controllers, it should not be considered fully correct for our standards. 

##### 6.8.2. Middleware
Middleware checks are handled here. Middlewares provide a convenient mechanism for filtering incoming HTTP requests. For more information about Middlewares please check; https://laravel.com/docs/middleware . Each middleware class should have handle method with at least two parameters and should be encapsulated under PlusClouds\${ModuleName}\Http\Middleware namespace. Each Middleware class and file should also contain Middleware suffix such that each should be named as ${MiddlewarePurpose}Middleware. Additionally middlewares should use Closure and Illuminate\Http\Requests. The following code snippet can be used as a guideline to develop middlewares to perform some task before the request is handled by the application;

```
<?php

namespace PlusClouds\${ModuleName}\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class ${MiddlewarePurpose}Middleware
 * @package PlusClouds\${ModuleName}\Http\Middleware
 */
class ${MiddlewarePurpose}Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Do something here
        return $next($request);
    }
}
```

A middleware can also run after the request is handled and the following code snippet can be used as a guideline to develop such middleware;
```
<?php

namespace PlusClouds\${ModuleName}\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class ${MiddlewarePurpose}Middleware
 * @package PlusClouds\${ModuleName}\Http\Middleware
 */
class ${MiddlewarePurpose}Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        // Do something here
        return $response;
    }
}
```

##### 6.8.3. Requests
Http requests are handled here. Each request related to an endpoint should be handled within its own folder and should be encapsulated under PlusClouds\${ModuleName}\Http\Requests\${Endpoint} namespace. Each request should also extend AbstractFormRequest ( NextDeveloper\Commons\Http\Requests\AbstractFormRequest). Each request should also implement rules and authorize methods. Additionally, each request should be named in a way that both the endpoint and operation should be mentioned in the name with a request suffix. The following snippet can be used as a guideline;

```
<?php
namespace PlusClouds\${ModuleName}\Http\Requests\${Endpoint};

use  NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

/**
 * Class ${Endpoint}${Operation}Request.
 *
 * @package  NextDeveloper\Commons\Http\Requests
 */
class ${Endpoint}${Operation}Request extends AbstractFormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return ...;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ...
        ];
    }
}
```

##### 6.8.4. Traits
Contains traits related to incoming http requests. Traits are collection of reusable methods that are accessible in multiple classes. Re-usable functions that are called with use keyword and work like object methods inside the caller. More information about traits can be found in; https://www.w3schools.com/php/php_oop_traits.asp . Each trait implementation regarding set of actions should be clear from the file name and each trait should be named as ${Behavior}Trait and encapsulated under PlusClouds\${ModuleName}\Http\Traits namespace.

##### 6.8.5. Transformers
Each implemented transformer should be encapsulated under PlusClouds\${ModuleName}\Http\Transformers and should extend AbstractTransformer ( NextDeveloper\Commons\Http\Transformers\AbstractTransformer). The following code snippet can be used as a guideline to create a new transformer;

```
<?php
namespace PlusClouds\${ModuleName}\Http\Transformers;
class ${Name}Transformer extends AbstractTransformer
{
    /**
     * @param ${DataType} $var
     *
     * @return array
     */
    public function transform($var) {
        return $this->buildPayload( [
            ...
        ]);
    }
}
```

##### 6.8.6. api.routes.php
All api routes are defined here. For further information about defining routes and handling http requests, please visit; https://laravel.com/docs/routing

#### 6.9. Jobs
Queueable scheduled jobs are placed here. A job related to a group should be contained in its own folder and should be encapsulated under PlusClouds\${ModuleName}\Jobs\ ${Group} namespace. A job implementation should also extend AbstractJob ( NextDeveloper\Commons\Jobs\AbstractJob) and should implement ShouldQueue interface. The following code snippet can be used as a guideline;

```
<?php
namespace App\Jobs;
 

use Illuminate\Contracts\Queue\ShouldQueue;
use  NextDeveloper\Commons\Jobs\AbstractJob;
 
class ${Group}${Operation}Job extends AbstractJob implements ShouldQueue
{
    /**
     * ${Short Explanation for Variable}.
     *
     * @var ${DataType}
     */
    ${Identifier} $variable;
 
    /**
     * Create a new job instance.
     *
     * @param  ${DataType}  $var
     * @return void
     */
    public function __construct($var)
    {
        // ...
    }
 
    /**
     * Execute the job.
     *
     * @param  ${DataType}  $var
     * @return void
     */
    public function handle($var)
    {
        // ...
    }
}
```

#### 6.10. Notifications
Each notification is created in here and should be encapsulated under PlusClouds\${ModuleName}\Notifications. 

#### 6.11. Policies
Policies are used to authorize user actions against a given resource. Each policy is encapsulated under PlusClouds\${ModuleName}\Policies namespace and should be named as ${Resource}Policy. Also, policies use Illuminate\Auth\Access\HandlesAuthorization. 

#### 6.12. Services
Driver methods to be used by other parts of the project. All services should be implemented in here instead of developing them in controllers or other parts. Each service method should be developed as a static method and should be seperated to different classes according to their functionality. Each service should be named as ${Functionality}Service and should only contain static methods. Also, the services should be encapsulated under PlusClouds\${ModuleName}\Services namespace. 

#### 6.13. Validation
Development of creating validation rules to validate incoming data are done in this folder. Each validation rule should be developed within their own class and encapsulated under PlusClouds\${ModuleName}\Validation\Rules namespace. Each rule implement Rule (Illuminate\Contracts\Validation\Rule) interface and should be named as ${RuleName}Rule. The following code snippet can be used as a guideline to implement new validation rules;

```
<?php
namespace PlusClouds\${ModuleName}\Validation\Rules;
 
use Illuminate\Contracts\Validation\Rule;
 
class ${RuleName}Rule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // ... 
    }
 
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ${Some validation error string};
    }
}
```

#### 6.14. CoreServiceProvider.php
All service, middleware, helpers, commands, routing, jobs bindings and booting are handled in CoreServiceProvider.

### 7. Tests
Contains tests for testing the module. Tests should be splitted into subtests. Incoming requests, services and functionality of different parts should be tested and grouped under their own folder. 

### 8. Workers
Contains yaml files. Each yaml file should be named as ${ModuleName}-${WorkerOperation}.yml

## Console vs HTTP Requests
Depending on the type of request entering application, incoming requests are sent either to the console kernel or to the http kernel.

### 1. Console Kernel
All of the requests coming from command line or terminal to the application are handled by the console kernel. Upon trying to use a command, the command go through the console kernel to find if a registered event exists for that command. These registered events or actions are defined in console directory and registered in the service provider.
- #### Architectural Diagram for Console Requests
  ![Console requests diagram](console.png)
### 2. HTTP Kernel
This kernel is used to process requests that come through the web. Handled services are registered in the service provider. Incoming http requests to an endpoint is then dispatched to the router by the service provider. The router routes the requests to the controller, in the mean time the request go through the middleware and certain actions could be controlled before being send to the controller. Upon receiving the request, controller executes set of actions to send back a response to the sender.
 
- #### Architectural Diagram for HTTP Requests

![HTTP Requests Diagram](http.png)