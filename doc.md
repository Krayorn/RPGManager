# RPGManager documentation

//intro

### **Table of Contents**
 - [**Installation and configuration**](#global-file-structure)
	 - [How to install RPGManager](#how-to-install)
	 - [How to setup your environment](#how-to-setup)
 - [**Roles of files**](#roles-of-files)
    - [game.json](#game\.json)
    - [settings.json](#settings\.json)
    - [cli-config.php](#cli-config\.php)
    - [config.php](#config\.php)
    - [create.php](#create\.php)
    - [start.php](#start\.php)
	 
## How to install RPGManager
It is simple as killing a random goblin, you just have to add our package to your **composer.json**.
Here is the link ... then ``` composer install ```.

## How to setup your environment
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
- ``inventory``: it is what the token carry (only items).
- ``number``: the number of times you have this token in/on that token, it could be an *int* or a range of ints *[int; int]* (inclusive).
- ``value``: the value of the attribute, have to be an *int*.
- ``range``: the number of times you have this token, it have to be a range of ints *[int; int]* (inclusive).
- ``type`` (in spells): the type of the spells, have to be: "damage", "buff", "debuff", "heal".

#### *``settings``*


