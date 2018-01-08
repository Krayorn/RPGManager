# RPGManager documentation

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
It is simple as killing a random goblin, you just have to add our package to your **composer.json** like this

```
"require": {
	...
	"rpgmanager/rpgmanager": "dev-master"
}
```
Then execute ``` composer install ```.

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

Once you checked the structure is correct, you have to cut/paste the files ``create.php`` and ``start.php`` from the installation/ folder into your own project directory root.
You will need these files later, to initiate your database and launch the game.


## Roles of files

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
It is where you enable or not some parameters of the game such as specific statistics, like the statistic which the damages affect.

### logs
The logs folder contains all process trace to debug if needed.

**files**:
- ``access.log``: the trace of all the methods executed during the game.
- ``request.log``: the trace of all the doctrine requests.
- ``error.log``: the trace of all the errors raised by doctrine requests.
- ``action.log``: the trace of all the actions performed by the players, which can serve as a save file.

These logs are cleared automatically, except for the ``action.log`` because of its save function.
If you want to change the clearing behaviour, look into the ``src/Model/`` directory.
 
### doctrine and dbconfig
#### *``cli-config.php``*
Enable you to use doctrine in command line.
#### *``config.php``*
Enable the connection to the database. You have to edit it with *your own database parameters* (dbname, user, password).

### setup scripts
#### *``create.php``*
This script sets your database:
- It copies our example Entities to your ``src/Entity``, and changes the namespace.
- It creates a database (or drops it if already exists) with the proper tables referred to your Entities.
- It parses ``game.json`` and push all data in the database.

/!\ Warning, the script will drop the entire database, so it will reset the party and his save. Use it only to setup your game at the start of a new party.

#### *``start.php``*
This script starts a new party!

