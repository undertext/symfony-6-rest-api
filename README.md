README
--------
This is a simple project to show how to build a simple REST API
using Symfony framework and Docker.
It also shows how to use Docker Compose to run the application and
how to deploy it to Amazon Container Service (ECS) and to Elastic Kubernetes Service (EKS).

## Symfony bundles used
`FrameworkBundle` - base bundle for Symfony framework, provides such functionality as routing, configuration, 
dependency injection, etc.

`DoctrineBundle` - provides integration with Doctrine ORM

`DoctrineMigrationsBundle` - provides integration with Doctrine Migrations

`FOSRestBundle` - provides REST API functionality, extends Symfony routing functionality

`SensioFrameworkExtraBundle` - provides attributes support for routing, caching, security, etc.
 Symfony 6.2 has this functionality out of the box, so this bundle should be removed.

`JMSSerializerBundle` - provides serialization functionality

`SecurityBundle` - provides security functionality

`LexikJWTAuthenticationBundle` - provides JWT authentication functionality

`SncRedisBundle` - provides Redis integration

`NelmioApiDocBundle` - provides Swagger documentation generation based on attributes

`TwigBundle` - provides Twig template engine integration, used by NelmioApiDocBundle

`MakerBundle` - provides console commands for generating code

`DoctrineFixturesBundle` - provides fixtures functionality, used for generating test data

`LiipTestFixturesBundle` -  provides base classes for functional tests to assist in setting up test-databases 
and loading fixtures.

