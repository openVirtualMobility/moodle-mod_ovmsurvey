# mod_ovmsurvey

Provides self-assessment surveys with a review page.

## Installation 

This plugin must be installed in Moodle mod directory.
See http://docs.moodle.org/en/Installing_plugins for details.

## Usage

An instance of the mod can be added like any other Moodle activity.
Each instance is responsible of the display of one part (skill) of the survey.

## Translations

Translation files are located in the `json` directory.
In order to add a new translation, 2 files must be provided : 
- questions_student_`lang`.json
- questions_teacher_`lang`.json

The Javascript bundle has then to be rebuilt using the command `npm run build`

## Dev

### development 
```
npm install
npm run start
```

### production
```
npm install
npm run build
```