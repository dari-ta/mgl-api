# unofficial MGL App API

This is the API for the unofficial MGL App

## Usage

NOTE: this repo is a collection of scripts for parsing 'Vertretungsplaene' at the MGL

Copy db.inc.sample.php to db.inc.php and adjust db server settings
Create a MySQL table from vpl.sql
Update constants in all PHP files

Enjoy!

## Update

call the update.php

## Get the VPL

call the get.php
with the following POST parameters:

	classes=10d

or

	classes=10d,9c,5a

or 

	classes=AG,Kurs,NDT

NDT is the Key for the news of the day
