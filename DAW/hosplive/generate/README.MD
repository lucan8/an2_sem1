# Data generation for database

#### All these files use [FAKER](https://github.com/fzaninotto/Faker?tab=readme-ov-file) so be sure to install locally for the script and data generation to work

- **generate_hospitals.php**

    "Randomly" generates hospital data(like id and county), assigns to each hospital it's rooms and writes the information in **the associated resource file**(resources/hospitals.txt, resources/rooms.txt)

- **generate_medics.php**
    "Randomly" generates medic data(like id and specialization),assigns to each medic the hospitals they work for and writes the information in the associated resource file(resources/medics.txt, resources/hospitals_medics.txt)

- **generate_inserts.php**
    Generates sql scripts for inserting the data into the app's database. All data used for generating these scripts can be found in the _resources_ folder
