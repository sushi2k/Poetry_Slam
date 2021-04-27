<html>
	<head>
	<style>
    .container {
      position: relative;
      overflow: hidden;
      width: 100%;
      padding-top: 56.25%; /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
    }

    /* Then style the iframe to fit in the container div with full height and width */
    .responsive-iframe {
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      width: 100%;
      height: 75%;
    }

    input[type=button], input[type=submit], input[type=reset] {
      background-color: #008CBA;
      border: none;
      color: white;
      padding: 16px 32px;
      text-decoration: none;
      margin: 4px 2px;
      cursor: pointer;
      font-size: 150%;
    }

    body {
    background-image: url("IMG_1360.jpg");
    background-color: #cccccc;
    }

	</style>

	</head>

	<body>
    <center>		<text><h1 style="font-size=150%">Licht, Liebe, Leben - Poetry Slam 2021</h1>
        </text>
    </center>
    <form action="/submit.php" id="usrform" method="post">
      <textarea name="poem" rows="15" style="width:100%; padding: 12px 20px; margin: 8px 0; box-sizing: border-box; background-color: #f8f8f8;" form="usrform">Enter your poem here...</textarea><br><br>
      <center>  <input type="submit"> </center>
      </form>
    <center>		<text><h2>After your submission your poem will be transformed into an audio loop!</h2>
        </text>
    </center>
    <div class="container">
      <iframe class="responsive-iframe"  src="<URL>/player/">
    </div>


<?php
require __DIR__ . '/vendor/autoload.php';

# https://github.com/googleapis/google-cloud-php-text-to-speech
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Stichoza\GoogleTranslate\GoogleTranslate;

$poem = $_POST['poem'];


$textToSpeechClient = new TextToSpeechClient([
	# https://github.com/googleapis/google-cloud-php/blob/master/AUTHENTICATION.md
	# https://cloud.google.com/docs/authentication/production#command-line
	'credentials' => json_decode(file_get_contents('<path>/<file>.json'), true)
]);



if (!empty($poem)) {
	$tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
	$tr->setSource();
	$text = $tr->translate($poem);
	$languageCode = $tr->getLastDetectedSource();

	$input = new SynthesisInput();
	$input->setText($poem);
	$voice = new VoiceSelectionParams();

	# https://cloud.google.com/text-to-speech/docs/voices
	# https://github.com/Stichoza/google-translate-php#language-detection
	switch ($languageCode) {
	  case "en":
	    $voice->setLanguageCode('en-AU');
	    echo $languageCode; 
	    break;
	  case "de":
	    $voice->setLanguageCode('de-DE');
	    echo $languageCode; 
	    break;
	  case "zh-CN":
	    $voice->setLanguageCode('cmn-CN');
	    echo $languageCode; 
	    break;
	  case "ta":
	    $voice->setLanguageCode('ta-IN');
	    echo $languageCode; 
	    break;
	  case "fr":
	    $voice->setLanguageCode('fr-FR');
	    echo $languageCode; 
	    break;
	  default:
	    echo $languageCode;
	 
	}

	$audioConfig = new AudioConfig();
	$audioConfig->setAudioEncoding(AudioEncoding::MP3);

	$resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);

	$filename = tempnam('t2s', "$languageCode-");
	file_put_contents("$filename.mp3", $resp->getAudioContent());

	shell_exec("sox $filename.mp3 $filename-1.mp3 pad 0 5");

	unlink($filename);
	unlink("$filename.mp3");


  // $to = "<your mail address>";
  // $subject = "New Poem!";
  // $txt = $poem;
  // $headers = "From: <your mail address>" . "\r\n" .
  // "CC: <your mail address>";

  mail($to,$subject,$txt,$headers);	

}


$script_prefix=<<<JS
/*!
 *  Howler.js Audio Player Demo
 *  howlerjs.com
 *
 *  (c) 2013-2020, James Simpson of GoldFire Studios
 *  goldfirestudios.com
 *
 *  MIT License
 */

// Cache references to DOM elements.
var elms = ['track', 'timer', 'duration', 'playBtn', 'pauseBtn', 'prevBtn', 'nextBtn', 'playlistBtn', 'volumeBtn', 'progress', 'bar', 'wave', 'loading', 'playlist', 'list', 'volume', 'barEmpty', 'barFull', 'sliderBtn'];
elms.forEach(function(elm) {
  window[elm] = document.getElementById(elm);
});


var sampleID;
var executed = false;

/**
 * Player class containing the state of our playlist and where we are in it.
 * Includes all methods for playing, skipping, updating the display, etc.
 * @param {Array} playlist Array of objects with playlist song details ({title, file, howl}).
 */
var Player = function(playlist) {
  this.playlist = playlist;
  this.index = 0;

  // Display the title of the first track.
  track.innerHTML = '1. ' + playlist[0].title;

  // Setup the playlist display.
  playlist.forEach(function(song) {
    var div = document.createElement('div');
    div.className = 'list-song';
    div.innerHTML = song.title;
    div.onclick = function() {
      player.skipTo(playlist.indexOf(song));
    };
    list.appendChild(div);
  });
};
Player.prototype = {
  /**
   * Play a song in the playlist.
   * @param  {Number} index Index of the song in the playlist (leave empty to play the first or current).
   */
  play: function(index) {
    var self = this;
    var sound;

    index = typeof index === 'number' ? index : self.index;
    var data = self.playlist[index];


	var sample = new Howl({
	  src: ['audio/Sub-Conscious-Part_21.m4a'],
	  loop: true,
	  volume: 0.6
	});

    // If we already loaded this track, use the current one.
    // Otherwise, setup and load a new Howl.
    if (data.howl) {
      sound = data.howl;

    } else {

	
	    
	sound = data.howl = new Howl({
        src: ['../t2s/' + data.file + '.mp3'],
        html5: true, // Force to HTML5 so that the audio can stream in (best for large files).
        onplay: function() {
          // Display the duration.
          duration.innerHTML = self.formatTime(Math.round(sound.duration()));

          // Start upating the progress of the track.
          requestAnimationFrame(self.step.bind(self));

          // Start the wave animation if we have already loaded
          wave.container.style.display = 'block';
          bar.style.display = 'none';
          pauseBtn.style.display = 'block';

	
	
	sampleID = sample.play();


        },
        onload: function() {
          // Start the wave animation.
          wave.container.style.display = 'block';
          bar.style.display = 'none';
          loading.style.display = 'none';
        },
        onend: function() {
          // Stop the wave animation.
          wave.container.style.display = 'none';
          bar.style.display = 'block';
          self.skip('next');
	  sample.stop(sampleID);
          
        },
        onpause: function() {
          // Stop the wave animation.
          wave.container.style.display = 'none';
          bar.style.display = 'block';
          sample.stop(sampleID);
        },
        onstop: function() {
          // Stop the wave animation.
          wave.container.style.display = 'none';
          bar.style.display = 'block';
          sample.stop(sampleID);
        },
        onseek: function() {
          // Start upating the progress of the track.
          requestAnimationFrame(self.step.bind(self));
        }
      });
    }


    // Begin playing the sound.
    sound.play();

    // Update the track display.
    track.innerHTML = (index + 1) + '. ' + data.title;

    // Show the pause button.
    if (sound.state() === 'loaded') {
      playBtn.style.display = 'none';
      pauseBtn.style.display = 'block';
    } else {
      loading.style.display = 'block';
      playBtn.style.display = 'none';
      pauseBtn.style.display = 'none';
    }

    // Keep track of the index we are currently playing.
    self.index = index;
  },

  /**
   * Pause the currently playing track.
   */
  pause: function() {
    var self = this;

    // Get the Howl we want to manipulate.
    var sound = self.playlist[self.index].howl;

    // Puase the sound.
    sound.pause();

    // Show the play button.
    playBtn.style.display = 'block';
    pauseBtn.style.display = 'none';
  },

  /**
   * Skip to the next or previous track.
   * @param  {String} direction 'next' or 'prev'.
   */
  skip: function(direction) {
    var self = this;

    // Get the next track based on the direction of the track.
    var index = 0;
    if (direction === 'prev') {
      index = self.index - 1;
      if (index < 0) {
        index = self.playlist.length - 1;
      }
    } else {
      index = self.index + 1;
      if (index >= self.playlist.length) {
        index = 0;
      }
    }

    self.skipTo(index);
  },

  /**
   * Skip to a specific track based on its playlist index.
   * @param  {Number} index Index in the playlist.
   */
  skipTo: function(index) {
    var self = this;

    // Stop the current track.
    if (self.playlist[self.index].howl) {
      self.playlist[self.index].howl.stop();
    }

    // Reset progress.
    progress.style.width = '0%';

    // Play the new track.
    self.play(index);
  },

  /**
   * Set the volume and update the volume slider display.
   * @param  {Number} val Volume between 0 and 1.
   */
  volume: function(val) {
    var self = this;

    // Update the global volume (affecting all Howls).
    Howler.volume(val);

    // Update the display on the slider.
    var barWidth = (val * 90) / 100;
    barFull.style.width = (barWidth * 100) + '%';
    sliderBtn.style.left = (window.innerWidth * barWidth + window.innerWidth * 0.05 - 25) + 'px';
  },

  /**
   * Seek to a new position in the currently playing track.
   * @param  {Number} per Percentage through the song to skip.
   */
  seek: function(per) {
    var self = this;

    // Get the Howl we want to manipulate.
    var sound = self.playlist[self.index].howl;

    // Convert the percent into a seek position.
    if (sound.playing()) {
      sound.seek(sound.duration() * per);
    }
  },

  /**
   * The step called within requestAnimationFrame to update the playback position.
   */
  step: function() {
    var self = this;

    // Get the Howl we want to manipulate.
    var sound = self.playlist[self.index].howl;

    // Determine our current seek position.
    var seek = sound.seek() || 0;
    timer.innerHTML = self.formatTime(Math.round(seek));
    progress.style.width = (((seek / sound.duration()) * 100) || 0) + '%';

    // If the sound is still playing, continue stepping.
    if (sound.playing()) {
      requestAnimationFrame(self.step.bind(self));
    }
  },

  /**
   * Toggle the playlist display on/off.
   */
  togglePlaylist: function() {
    var self = this;
    var display = (playlist.style.display === 'block') ? 'none' : 'block';

    setTimeout(function() {
      playlist.style.display = display;
    }, (display === 'block') ? 0 : 500);
    playlist.className = (display === 'block') ? 'fadein' : 'fadeout';
  },

  /**
   * Toggle the volume display on/off.
   */
  toggleVolume: function() {
    var self = this;
    var display = (volume.style.display === 'block') ? 'none' : 'block';

    setTimeout(function() {
      volume.style.display = display;
    }, (display === 'block') ? 0 : 500);
    volume.className = (display === 'block') ? 'fadein' : 'fadeout';
  },

  /**
   * Format the time from seconds to M:SS.
   * @param  {Number} secs Seconds to format.
   * @return {String}      Formatted time.
   */
  formatTime: function(secs) {
    var minutes = Math.floor(secs / 60) || 0;
    var seconds = (secs - minutes * 60) || 0;

    return minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
  }
};
// Setup our new audio player class and pass it the playlist.
var player = new Player([
JS;



$audioDir = 't2s';
$files = scandir($audioDir);
print_r($files);

$script_songs = "";

$output = array_slice($files,2); //remove the first two entries, whcih are . and ..

foreach($output as $file) {
	$file = preg_replace('/\\.[^.\\s]{3}$/', '', $file);
	$script_songs= $script_songs."{title: '$file', file: '$file', howl: null},";
}

// remove last character ","
$script_songs = substr($script_songs, 0, -1);



$script_postfix=<<<JS
]);
// Bind our player controls.
playBtn.addEventListener('click', function() {
  player.play();
});
pauseBtn.addEventListener('click', function() {
  player.pause();
});
prevBtn.addEventListener('click', function() {
  player.skip('prev');
});
nextBtn.addEventListener('click', function() {
  player.skip('next');
});
waveform.addEventListener('click', function(event) {
  player.seek(event.clientX / window.innerWidth);
});
playlistBtn.addEventListener('click', function() {
  player.togglePlaylist();
});
playlist.addEventListener('click', function() {
  player.togglePlaylist();
});
volumeBtn.addEventListener('click', function() {
  player.toggleVolume();
});
volume.addEventListener('click', function() {
  player.toggleVolume();
});

// Setup the event listeners to enable dragging of volume slider.
barEmpty.addEventListener('click', function(event) {
  var per = event.layerX / parseFloat(barEmpty.scrollWidth);
  player.volume(per);
});
sliderBtn.addEventListener('mousedown', function() {
  window.sliderDown = true;
});
sliderBtn.addEventListener('touchstart', function() {
  window.sliderDown = true;
});
volume.addEventListener('mouseup', function() {
  window.sliderDown = false;
});
volume.addEventListener('touchend', function() {
  window.sliderDown = false;
});

var move = function(event) {
  if (window.sliderDown) {
    var x = event.clientX || event.touches[0].clientX;
    var startX = window.innerWidth * 0.05;
    var layerX = x - startX;
    var per = Math.min(1, Math.max(0, layerX / parseFloat(barEmpty.scrollWidth)));
    player.volume(per);
  }
};

volume.addEventListener('mousemove', move);
volume.addEventListener('touchmove', move);

// Setup the "waveform" animation.
var wave = new SiriWave({
  container: waveform,
  width: window.innerWidth,
  height: window.innerHeight * 0.3,
  cover: true,
  speed: 0.03,
  amplitude: 0.7,
  frequency: 2
});
wave.start();

// Update the height of the wave animation.
// These are basically some hacks to get SiriWave.js to do what we want.
var resize = function() {
  var height = window.innerHeight * 0.3;
  var width = window.innerWidth;
  wave.height = height;
  wave.height_2 = height / 2;
  wave.MAX = wave.height_2 - 4;
  wave.width = width;
  wave.width_2 = width / 2;
  wave.width_4 = width / 4;
  wave.canvas.height = height;
  wave.canvas.width = width;
  wave.container.style.margin = -(height / 2) + 'px auto';

  // Update the position of the slider.
  var sound = player.playlist[player.index].howl;
  if (sound) {
    var vol = sound.volume();
    var barWidth = (vol * 0.9);
    sliderBtn.style.left = (window.innerWidth * barWidth + window.innerWidth * 0.05 - 25) + 'px';
  }
};
window.addEventListener('resize', resize);
resize();
JS;

$myfile = fopen("player/player.js", "w") or die("Unable to open file!");

fwrite($myfile, $script_prefix);
fwrite($myfile, $script_songs);
fwrite($myfile, $script_postfix);
fclose($myfile);


?>


</body>
</html>

