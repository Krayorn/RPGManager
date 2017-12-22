# RPGManager documentation

//intro

### **Table of Contents**
 - [**Installation and configuration**](#installation-and-configuration)
	 - [How to install RPGManager](#how-to-install)
	 - [How to setup your environment](#how-to-setup)
 - [**Roles of files**](#roles-of-files)
    - [game config](#game-config)
        - game.json
        - settings.json
    - [doctrine and dbconfig](#doctrine-and-dbconfig)
        - cli-config.php
        - config.php
    - [setup scripts](#setup-scripts)
        - create.php
        - start.php

## Installation and configuration
### How to install RPGManager
It is simple as killing a random goblin, you just have to add our package to your **composer.json**.
Here is the link ... then ``` composer install ```.

### How to setup your environment
To use our package properly you have to respect a file structure, which is:

    project        
        └─config/
        │   └─ game.json
        │   └─ settings.json
        └─ logs/
        │   └─ access.log
        └─ src/
        └─ cli-config.php
        └─ config.php
        └─ start.php
If this structure is not the same you will probably have some problems and bugs because our scripts are based on that. 

## Roles of files
Here I will explain what is the purpose of each file in our package.

### game config
The ``config`` folder is where you will store ``game.json`` and ``settings.json``: 
#### *``game.json``* 
It is where you implement your adventure. It is where you describe your places, characters, monsters...
You just have to fill/edit the "inputs".

**tokens**:
- ``places`` (name, description, directions, monsters, npcs, items)
- ``characters`` (name, description, stats, spells, inventory, location)
- ``npcs`` (name, description, dialog)
- ``monsters`` (name, description, stats, spells, inventory)
- ``items`` (name, description, stats)
- ``spells`` (name, description, type, stats)
- ``stats`` (name, description, range)

**parameters of tokens:**
- ``name``: the name of the token.
- ``description``: a simple description of the token.
- ``directions`` (in places): 
    - ``directionName``: the name of the direction to the next step.
    - ``placeArrival``: the description of the next step.
- ``inventory``: it is what the token carry, only items.
- ``number``: the number of times you have this token in/on that token, it could be an *int* or a range of ints *[int; int]* (inclusive).
- ``value``: the value of the attribute, it have to be an *int*.
- ``range``: the number of times you have this token, it have to be a range of ints *[int; int]* (inclusive).
- ``type`` (in spells): the type of the spells, it have to be: "*damage*", "*buff*", "*debuff*", "*heal*".
#### *``settings.json``*
It is where you enable or not some parameters of the game such as ...

### doctrine and dbconfig
#### *``cli-config.php``*
Enable you to use doctrine in command line.
#### *``config.php``*
Enable the connection to the database. You have to edit it with *your own database parameters* (dbname, user, password).

### setup scripts
#### *``create.php``*
This script setup your database:
- It copy our example Entities to your ``src/Entity``, and change the namespace.
- It create a database (or drop it if already exists) with the proper tables referred to your Entities.
- It parse ``game.json`` and push all data in the database.

! Warning, the script will drop the entire database, so it will reset the party and his save. Use it only to setup your game at the start of a new party.

#### *``start.php``*
This script start a new party.

