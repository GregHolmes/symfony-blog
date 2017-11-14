Blog, made in Symfony, secured by Auth0
=======================

A simple application to show you how to create your first Symfony application, a blog with an authenticated section secured by Auth0 and styling all handled by Bootstrap 3. Built with Symfony 3.3

## Prerequisites

### Sign up at Auth0

`https://auth0.com/signup`

1 - In the dashboard, click `Clients` on the left.
* Create Client
* Add a name
* Choose regular web applications

2 - Configure callback url.
* In your Auth0 `Client` Under the settings tab.
* Find the text box labelled `Allowed Callback URLs`
* Paste the following in: `http://localhost:8000/auth0/callback`

3 - Configure Auth0 Client to require usernames
* In the navigation bar find and click `Connections`
* Then click `Database`
* Click on `Username-Password-Authentication`
* Toggle `Requires Username` to on.

Great. Auth0 is now set up!

## Installation

### Cloning the repository

* Clone this repository `git clone git@github.com:GregHolmes/symfony-blog.git`
* Change directory into the project with the following command: `cd symfony-blog`
 
### Auth0 + symfony environment variables

* Rename or copy `.env.example` to `.env`
* Go to `https://manage.auth0.com/#/clients`
* Pick your `Client`

Copy the following parameters into your `.env` file with their specific keys:
* AUTH0_CLIENT_ID: (Client ID on Auth0)
* AUTH0_CLIENT_SECRET: (Client Secret on Auth0)
* AUTH0_DOMAIN: (Domain on Auth0)

Update the following keys with your database credentials:
* DATABASE_HOST=(Your database host, mine was localhost)
* DATABASE_PORT=3306 (3306 is default)
* DATABASE_NAME=(The name you want your database to be called)
* DATABASE_USER=(database username)
* DATABASE_PASSWORD=(database password)
 
### Configuring Symfony

* Install the third party libraries with `composer install`

Now in your Terminal client type the following command: `php bin/console server:start`

### Prepopulating the database

Ok, we're now needing the database created and populating it with some data.

* We need to create the database by running the following command: `php bin/console doctrine:database:create`

**NOTE** `php bin/console doctrine:database:create` will use the database name you've put into the parameters file above to create a database for you.

* Time to create the tables: `php bin/console doctrine:migrations:migrate`
* Running the following command will put a blog post and an author into your database: `php bin/console doctrine:fixtures:load`

**NOTE** `php bin/console doctrine:fixtures:load` won't have the author linked to auth0 so you won't be able to log in via that user. However, if you want to put more entries into it for fixtures, you'll find the code in `symfony-blog/src/AppBundle/DataFixtures/ORM/Fixtures.php`

You're now ready to go! In your browser enter the url: `http://127.0.0.1:8000`


## License

It is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).