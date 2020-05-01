# Minihiker Backend v2

An update to the structure of Minihiker's backend based on the [Yii 2](http://www.yiiframework.com/) 
framework.

The application includes an API to be used by the backend itself and one to be used by clients
through a Wechat Miniprogram.

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    controllers/         contains shared api controller code
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
apiv1
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains REST controller classes
    models/              contains api-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for api application    
    web/                 contains the entry script and Web resources
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
wxapiv1
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains REST controller classes
    models/              contains api-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for wxapi application    
    web/                 contains the entry script and Web resources
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
