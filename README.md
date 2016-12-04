wrapper2
========

A Symfony project created on March 9, 2016, 10:06 pm.


## Eigendoku

### Entity from table

Liegen schon in AppBundle/Resources/config/unused

```php bin/console doctrine:mapping:import --force AppBundle --filter Chat xml```

```php bin/console doctrine:mapping:convert annotation ./src --filter Chat```

```php bin/console doctrine:generate:entities AcmeBlogBundle```