# Hosplive - my web application development project

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

## App description

### Database

In each `county` of Romania there will be a `hospital` that can employ multiple `medics`.
The `medics` having a `specialization` can also work at multiple `hospitals` at once and can serve the `users` (especially those with the _client_ `role`) through `appointments` which take place in one of the `rooms` of the chosen `hospital`

### User registration

After the user inserts all their necessary data(like email, name, birth date...) they will be sent and email with a HID(hosplive id) and a password which will be the used for loging-in

### User roles

After the user registers, they are given a role which dictates their priviliges regarding accessing or changing data(ex: the client role can view, make or cancel appointments, but a the medic role can only view and cancel)

Possible roles

- admin
- client
- medic
- hospital

### Main functionality
Logged in the client can make an appointment at any of the hospitals available throught the country to any of the specialized medics










