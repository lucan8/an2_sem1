# Hosplive - my web development application project

## What is hosplive?

This app represents a (imaginary) hospital chain called Hosplive and was inspired by [Medicover's web app](https://www.medicover.ro/)

## App structure

`models`: Contains classes representing each table in the database, having the role of interacting with the database

`views`: Contains the `php/html` pages that are rendered to the users

`controllers`: Contains the classes that represent the middleman between the models and views

`migrations`: contains the SQL files containing database schema changes for tasks.

`config` contains a database connection object and a router

`exceptions`: contains user defined exceptions

`generate`: contains the script files used to generate the `sql` insert files

`public`: contains the publically accesible files(`javascript` and `css` files)

`utils`: contains helper functions

`docs`: currently contains the database diagrams

## App description

### Database

In each `county` of Romania there will be a `hospital` that can employ multiple `medics`.
The `medics` having a `specialization` can also work at multiple `hospitals` at once and can serve the `patients` through `appointments` which take place in one of the `rooms` of the chosen `hospital`

### User registration

The registration process has 3 steps:

1. Normal registration: name, birth date, email, role, etc. After completion, a verification code is sent via their email
2. Email code verification: The user has to input the verification code, being restricted by the number of retries and resends.
3. Role specialization: After the user is verified, based on their role, they will be asked to provide further information(ex: medics have to provide their specialization, years of experience and CV)

### User roles

- admin
- patient
- medic
- hospital

#### Patient Role

- create, cancel, update and view their appointments

#### Medic Role

- view, cancel and update appointments (TODO)
- apply and view their job applications to hospitals (TODO: cancel job applications)

#### Hospital Role

- view, cancel and update appointments (TODO)
- view, reject and accept job applications
  
### Protection

- Check for form validity (unset parameters and invalid inputs)
- Tries to mitigate CSRF, XSS and SQL Injection
- Uses google's reCAPTCHA API to protect against bots

<!-- ## Usage

### Download a server

I'm using [XAMPP](https://www.apachefriends.org/) Apache, as this was recommende, but just downloading [Apache](https://httpd.apache.org/) will probably do as well.

### Download MySQL

[XAMPP](https://www.apachefriends.org/) already has mysql installed, but you can get it from [here](https://www.mysql.com/downloads/) as well

### Clone repository

TODO: Either move the project in a different repo or find a way to download only the poject

### Run the migrations

TODO: Set the order of the migrations
After opening a database connection run the migrations in order. -->





