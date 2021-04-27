# Poetry_Slam

This repo contains a very basic application that can be used for a poetry slam. Visitors can submit text as a poem. Once submitted the text will be transformed via a Google API (text to speech) to an audio file and added to the playlist of the JavaScript audio player Howler.js.

All submitted poems will be running as a loop.

## Pre-requisites

You need a Google Cloud Platform (GCP) account and enable the API Cloud Text-to-Speech <https://cloud.google.com/text-to-speech/docs/quickstart-client-libraries>.

The reference PHP library can be found here <https://googleapis.github.io/google-cloud-php/#/docs/google-cloud/v0.153.0/texttospeech/readme>.

You need a Linux server with Apache, PHP and composer. The following version of composer and PHP was used:

```bash
$ php --version
PHP 8.0.3 (cli) (built: Mar  5 2021 08:36:11) ( NTS )
Copyright (c) The PHP Group
Zend Engine v4.0.3, Copyright (c) Zend Technologies
    with Zend OPcache v8.0.3, Copyright (c), by Zend Technologies
$ composer --version
Composer version 2.0.12 2021-04-01 10:14:59
```

Install `text-to-speech` and also `google-translate-php` via composer:

```bash
$ composer require google/cloud-text-to-speech
$ composer require stichoza/google-translate-php
```

The tool `sox` need to be installed on your linux server with apt-get, which allows us to tweak audio files on the command line:

```bash
$ sudo apt-get install sox
```

## Get it run

- Edit line 54 in submit.php and add your URL where this repo is hosted.
- Edit line 75 in submit.php and the Google Cloud credentials as JSON file. You can fine more information around it here:
  - <https://github.com/googleapis/google-cloud-php/blob/master/AUTHENTICATION.md>
  - <https://cloud.google.com/docs/authentication/production#command-line>
- 5 languages are supported at the moment for text to speech. Another Google API is used to detect the language and according to this the switch statement in line 92 will select the language code for text2speech.
- All audio files will be saved in the folder `t2s`.
- By using the cli tool `sox` 5 seconds will be added to the end of each audio track to have a smoother transition between the poems.
- In case you have mail configured on your server, you can also trigger a mail for every new poem (line 132-136), which is commented at the moment.
- Line 143 onwards is dynamically creating the player.js file for the Howler audio player and is adding the new audio file to the playlist (line 446-460).
- An audio loop is being played in the background, which is defined in line 201 in submit.php.
