LtRedisBundle
=============

Redis bundle for Symfony.

This bundle implements several Symfony2 features using Redis.

- Sessions (implementing a session storage handler that persists sessions in redis)
- Monolog logging (implementing a custom monolog processing handler that store logs in a redis list)
- SwiftMailer spooling (implementing a  spool usnig a redis list)

It also provides a DataCollector that collects all commands sent to redis through the client.

This bundle have been developed by @ronnylt sponsored by Pricebets.




